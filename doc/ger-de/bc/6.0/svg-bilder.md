# SVG-Bilder

Ab dem 15. Oktober 2026 akzeptiert der Bild-Datentyp SVG-Bilder (`.svg`, und `.svgz`, mit gzip komprimiert) auf jeder
Website, auch nach einem Upgrade. Ein SVG ist ein Dokument, aus dem ein Browser Skripte ausführen kann, daher wird es
nie so gespeichert, wie es hochgeladen wurde: Der SVG-Bereiniger des Kernels, `expSvgSanitizer`, behält nur, was als
sicher bekannt ist, und die gespeicherte Datei ist seine Ausgabe. Als zweite Verteidigungslinie wird die Datei außerdem
mit Headern ausgeliefert, die verhindern, dass sie etwas ausführt oder lädt, wenn sie direkt geöffnet wird.

> **Warnung**
> **`.htaccess` bremst jede Anfrage.** Sind `.htaccess`-Dateien erlaubt (`AllowOverride` alles außer `None`), geschweige
> denn genutzt, sucht Apache sie bei jeder Anfrage in jedem Verzeichnis des Pfads und liest und wertet sie aus, und
> die Ladezeiten der Seiten steigen drastisch ([gemessen](../../anleitungen/setup-dateien-von-der-konsole.md#gemessen-htaccess-gegen-regeln-im-virtuellen-host)). Die einzige wirkliche Abhilfe ist, die
> Funktion für den virtuellen Host abzuschalten: `AllowOverride None` im virtuellen HTTPS-Host, und die Regeln in
> der Konfiguration des virtuellen Hosts selbst als mod_rewrite-Regeln. Das ist in Geschwindigkeitstests im
> Produktivbetrieb weit schneller und die Zeit für die Optimierung einer Produktivkonfiguration unbedingt wert.
> Stellen Sie sofort um: Übernehmen Sie die Regeln des [Beispiels für einen virtuellen HTTPS-Host](../../beispiele/webserver/apache-virtueller-host.md) in
> Ihren virtuellen Host (mit dem statischen Cache auch [`staticcache_offload.conf`](../../../eng-us/examples/staticcache_offload.conf)), setzen Sie
> `AllowOverride None`, führen Sie `apachectl configtest` aus, laden Sie Apache neu und testen Sie die Website.
> Dieselben Regeln für andere Server: [nginx](../../beispiele/webserver/nginx.md), [LiteSpeed](../../beispiele/webserver/litespeed.md), [Caddy](../../beispiele/webserver/caddy.md), [IIS](../../beispiele/webserver/iis.md),
> [Velocity](../../beispiele/webserver/velocity.md) (eingebaut); wohin Regeln für virtuelle Hosts in einem Control Panel gehören:
> [Plesk](../../hilfe/hosting/control-panels/plesk.md), [cPanel](../../hilfe/hosting/control-panels/cpanel.md), [DirectAdmin](../../hilfe/hosting/control-panels/directadmin.md), [ISPConfig](../../hilfe/hosting/control-panels/ispconfig.md).
> **Unter LiteSpeed oder OpenLiteSpeed** kann zusätzlich der Seiten-Cache des Servers anonyme Besucher und Crawler ohne PHP bedienen: [Seiten-Caching mit LiteSpeed und OpenLiteSpeed](../../anleitungen/seiten-cache-mit-litespeed.md).

## Wo es gilt

Jeder Weg, auf dem ein Bild gespeichert wird: das Bearbeitungsformular, `content/upload` und das Upload-Modul, das
Werkzeug für Mehrfach-Uploads, der Upload-Dialog des Rich-Text-Editors, der AJAX-Uploader der Objektrelationsliste,
WebDAV, der Paketimport und Importe über `fromString()`. Sie alle speichern über
`eZImageAliasHandler::initializeFromHTTPFile()` oder `initializeFromFile()`, die jede Datei bereinigen, die ihrem
Inhalt nach ein SVG ist (ihr Wurzelelement ist `<svg>`, egal wie ihr Name lautet oder welchen Typ der Browser gesendet
hat). Eine Datei, die diese nach Name oder Typ als SVG speichern würden, deren Inhalt aber nicht als SVG erkannt wird
(zum Beispiel UTF-16), wird nicht gespeichert: Eine als `image/svg+xml` ausgelieferte Datei ist immer die Ausgabe des
Bereinigers. Die Prüfungen vor dem Speichern (`eZImageType::imageFileError()`, verwendet vom Bearbeitungsformular,
von `eZContentUpload` und vom AJAX-Uploader) lehnen ein SVG, das der Bereiniger ablehnen würde, mit einer Meldung ab.

Die Vorschaubilder und Stilbilder des Paket-Assistenten bleiben Rasterbilder: Ein SVG wird dort abgelehnt.

## Die Einstellungen

`settings/image.ini`:

```ini
[ImageUploadSettings]
AllowedMIMETypes[]=image/svg+xml
# enabled (default) or disabled
SanitizeSVG=enabled
```

- `image/svg+xml` in `AllowedMIMETypes[]` und `SanitizeSVG=enabled` (beides ausgeliefert): SVG wird akzeptiert und
  bereinigt gespeichert.
- `SanitizeSVG=disabled`, oder `image/svg+xml` nicht in der Liste: SVG wird wie bisher mit „Bilder vom Typ
  image/svg+xml werden nicht angenommen.“ abgelehnt. Es gibt keine Einstellung, die ein SVG unbereinigt speichert.

`eZImageType::svgAccepted()` sagt, ob SVG akzeptiert wird.

## Was der Bereiniger behält

Der Bereiniger parst die Datei mit `DOMDocument`, ohne Netzwerkzugriff, ohne Ersetzung von Entitäten und ohne eine DTD
zu laden, und baut aus einer Liste erlaubter Elemente ein neues Dokument (die SVG-Profile von svg-sanitize und
DOMPurify waren die Referenz):

- **Elemente**: `svg`, `g`, `defs`, `symbol`, `use`, `switch`, `title`, `desc`, `metadata`, `view`, `a`, `path`,
  `rect`, `circle`, `ellipse`, `line`, `polyline`, `polygon`, `image`, `style`, `text`, `tspan`, `textPath`,
  `linearGradient`, `radialGradient`, `stop`, `pattern`, `clipPath`, `mask`, `marker`, `filter` und die
  Filter-Primitive (`feBlend` bis `feTurbulence`), `animate`, `animateTransform`, `animateMotion`, `mpath`, `set`.
  Immer im SVG-Namensraum geschrieben; ein SVG ohne den Namensraum erhält ihn, damit es als Bild dargestellt wird.
- **Attribute**: die Geometrie- und Darstellungsattribute von SVG (`d`, `points`, `viewBox`, `fill`, `stroke`,
  `transform`, die Attribute für Verläufe, Filter und Text ...), `id`, `class`, `style`, `role`, `aria-*`,
  `xml:space`, `xml:lang`.
- **Verweise**: `href` und `xlink:href` auf ein internes `#fragment`. `<a>` behält außerdem Links mit `http:`,
  `https:` und `mailto:`, und `<image>` behält ein eingebettetes `data:image/png`, `jpeg`, `gif` oder `webp` in
  base64, das wirklich ein solches Bild ist.
- **CSS**, in `style`-Attributen und `<style>`-Elementen, nachdem CSS-Escapes und Kommentare aufgelöst sind:
  Deklarationen ohne Skript- oder `data:`-URL und mit `url()` nur auf ein internes `#fragment`. In einem
  `<style>`-Element wird jedes Stück geprüft, das ein Browser als Deklaration lesen könnte, auch neben einer
  verschachtelten Regel, in einem zuletzt offen gelassenen Block und vor der ersten Regel.

## Was er entfernt

- `<script>`, `<foreignObject>` (mit jedem HTML darin), `iframe`, `handler`, `listener`, XInclude und jedes Element
  eines anderen Namensraums (XHTML, Inkscape, Sodipodi, Illustrator), mit seinem Inhalt.
- Jedes Ereignisattribut `on*` und jedes Attribut eines anderen Namensraums.
- URLs mit `javascript:`, `vbscript:` und `data:` (außer den oben genannten eingebetteten Rasterbildern), überall.
- Verweise von `<use>`, `<image>`, `<feImage>`, `<mpath>` und `<textPath>` auf etwas anderes als ein internes Fragment
  (ein `<image>`, `<use>`, `<feImage>` oder `<mpath>` ohne sicheren Verweis wird ganz entfernt).
- `url(...)` auf etwas anderes als ein internes Fragment, in Stil- und Darstellungsattributen (`fill`, `mask`,
  `filter`, `clip-path`, `marker-*` ...).
- `@import`, `expression()`, `behavior` und `-moz-binding` in CSS, und die Bildfunktionen, die aus einer einfachen
  Zeichenkette oder einem Attribut laden: `image-set()` (auch `-webkit-image-set()`), `image()`, `cross-fade()`,
  `element()`, `src()`, `attr()`.
- Eine Animation (`animate`, `set` ...) von `href`, `xlink:href`, `style` oder einem Ereignisattribut, oder mit einem
  Wert, der entfernt würde.
- Kommentare, Verarbeitungsanweisungen (`<?xml-stylesheet?>`), CDATA-Hüllen und den DOCTYPE.

Er lehnt die Datei ab (und der Upload nennt den Grund), wenn:

- sie kein wohlgeformtes XML ist oder ihr Wurzelelement nicht `<svg>` ist;
- sie einen DOCTYPE mit internem Subset oder einer Entitätsdeklaration hat (XXE, „billion laughs“);
- sie UTF-16 oder UTF-32 ist (gelesen werden nur UTF-8 und ASCII-basierte Kodierungen);
- sie nach dem Dekomprimieren größer als 5 MB ist, mehr als 50 000 Knoten hat oder tiefer als 128 Elemente
  verschachtelt ist;
- sich eine `.svgz` nicht dekomprimieren lässt oder auf mehr als 5 MB dekomprimiert (eine gzip-Bombe);
- nichts übrig bleibt, das zeichnet.

Ein Upload als `.svgz` wird als einfaches `.svg` gespeichert.

```php
$clean = expSvgSanitizer::sanitize( $markup );          // the safe markup, or false
echo expSvgSanitizer::lastError();                       // why it was refused
expSvgSanitizer::sanitizeFile( $path, $targetPath );     // writes plain SVG; false leaves the file unchanged
list( $width, $height ) = expSvgSanitizer::dimensions( $path );
```

## Aliase

Ein SVG wird nie skaliert und nie an GD oder ImageMagick übergeben (`eZImageManager::createImageAlias()` und
`convert()` lehnen es ab). Jeder Alias eines SVG-Bildes (`small`, `medium`, `large`, `reference`, ein eigener Alias)
ist die Originaldatei: dieselbe URL, mit Breite und Höhe des Originals, gelesen aus seinen Attributen `width` und
`height` (`px`, `pt`, `pc`, `mm`, `cm`, `in`) oder, ohne diese, aus seiner `viewBox`. Daher zeigt
`{attribute_view_gui attribute=$node.data_map.image image_class=medium}` das SVG in der Größe des Originals;
skalieren Sie es im Template oder mit CSS, wo eine kleinere Größe gewünscht ist. Die Aliase werden nicht in das XML
des Attributs geschrieben, sodass Verschieben, Kopieren und Entfernen des Bildes eine einzige Datei betreffen.

## Header bei der Auslieferung

Ein mit `<img>` oder aus einem Stylesheet angezeigtes SVG führt nie Skripte aus. Direkt geöffnet (seine URL in der
Adressleiste, ein `<object>` oder `<iframe>`) ist es ein Dokument mit dem Ursprung der Website. Als zweite
Verteidigungslinie werden die SVG-Dateien ausgeliefert mit:

```
Content-Security-Policy: sandbox; default-src 'none'; style-src 'unsafe-inline'
X-Content-Type-Options: nosniff
```

Was ausgeliefert wird:

- **`.htaccess_root`** setzt sie für Dateien `.svg` und `.svgz` (braucht `mod_headers`).
- **Exponential Velocity** setzt sie für jedes `.svg` und `.svgz`, das er als statische Datei ausliefert, über
  HTTP/1.1 und HTTP/2 (ab v0.0.4.53; `Q.webserver.svgSandbox false` schaltet es aus).
- **`content/download`** (`eZFile::downloadHeaders()`) und das Cluster-Gateway (`index_cluster.php`) senden sie für
  ein SVG.

Eine Installation, die über die Konfiguration eines virtuellen Hosts statt über `.htaccess` ausgeliefert wird, braucht
die Zeilen dort.

**Apache** (im `<VirtualHost>` oder `<Directory>` der Website, mit `mod_headers`):

```apache
<FilesMatch "(?i)\.svgz?$">
    Header always set Content-Security-Policy "sandbox; default-src 'none'; style-src 'unsafe-inline'"
    Header always set X-Content-Type-Options "nosniff"
</FilesMatch>
```

**nginx** (im `server`-Block, vor der Location, die den Speicher ausliefert):

```nginx
location ~* \.svgz?$ {
    add_header Content-Security-Policy "sandbox; default-src 'none'; style-src 'unsafe-inline'" always;
    add_header X-Content-Type-Options "nosniff" always;
    try_files $uri =404;
}
```

In nginx ersetzt ein `add_header` in einer Location die des `server`-Blocks: Wiederholen Sie dort jeden Header, den
der `server`-Block für jede Antwort setzt.

## Für Erweiterungsentwickler

- Ein eigener Upload-Weg, der eine Bilddatei direkt speichert, ohne den Bild-Datentyp, muss für eine Datei, die
  `expSvgSanitizer::isSVGFile( $path )` erkennt, `eZImageType::sanitizeSVGFile( $path )` aufrufen und die Datei
  ablehnen, wenn das false zurückgibt.
- Ein Upload-Werkzeug, das Dateitypen Klassen zuordnet (`upload.ini [CreateSettings] MimeClassMap[]`), ordnet
  `image/svg+xml` über das ausgelieferte `MimeClassMap[image]=image` der Bildklasse zu; ein Dateitypfilter des
  Werkzeugs braucht `*.svg` (und `*.svgz`).

## Tests

- `tests/tests/lib/ezimage/expSvgSanitizerTest.php`: 75 schädliche SVGs (Skript, Ereignisattribute an jeder Art von
  Element, foreignObject und iframe, Skript- und Data-Links, Entitätstricks, XXE, billion laughs, CSS-`url()`,
  `@import`, `expression()`, `image-set()` und `attr()`, CSS außerhalb der innersten Regelblöcke, Animation von
  `href`, gemischte Groß- und Kleinschreibung, Namensräume, CDATA, Kommentare, base64, verschachteltes `<svg>`) kommen
  alle ohne etwas heraus, das ausgeführt oder geladen wird; 12 Bootstrap Icons und 3 Zeichnungen behalten jedes
  Element und Attribut (und werden in Chromium Pixel für Pixel gleich dargestellt).
- `tests/tests/kernel/datatypes/ezimage/expImageSvgTest.php`: Annahme und Ablehnungen des Datentyps, die Einstellung,
  die Paketvorschaubilder, die Aliase und der Bildmanager.
