# Auf Abruf erzeugte Bild-Aliase

Lesen Sie diese Seite, wenn Sie wissen möchten, wie Exponential 6.0.15 Bild-Aliase erzeugt: warum eine Seite beim
Erzeugen keine Bilder mehr umwandelt, welche Adressen die Bilder bekommen, was ein Besucher sieht, während ein Alias
erzeugt wird, wie WebP und AVIF angeboten werden, wie Aliase nach dem Veröffentlichen vorab erzeugt werden, wie eine
Entwicklungs- oder Staging-Website die Bilder, die ihr fehlen, von der Produktion holt, und wie Sie all das aus der
Administration und von der Kommandozeile aus steuern und beobachten. Die letzten Abschnitte nennen die gemessene
Leistung und die Grenzen.

Verwandt: [Webserver-Rezepte für auf Abruf erzeugte Bild-Aliase](../../beispiele/webserver/bild-aliase-auf-abruf.md)
(Beispiele), [Aktualisierung auf 6.0.15: auf Abruf erzeugte Bild-Aliase](../../bc/6.0/bild-aliase-auf-abruf.md)
(Hinweise zur Aktualisierung), [Bild-Aliase](../../hilfe/bild-aliase.md) (Hilfe zu Setup > Bild-Aliase),
[`exp:image:alias`](../../beispiele/befehle/expimagealias.md) (erzeugte Befehlsreferenz),
[image.ini \[AliasSettings\]](../../beispiele/ini-einstellungen/image/AliasSettings.md),
[\[AliasStaging\]](../../beispiele/ini-einstellungen/image/AliasStaging.md) und
[\[AliasSource_pro\]](../../beispiele/ini-einstellungen/image/AliasSource_pro.md) (erzeugte Referenz der Einstellungen).

## Kurz gesagt

- Ein **Bild-Alias** ist eine Variante eines hochgeladenen Bildes, definiert in `settings/image.ini` (`small`, `large`,
  das `i160` des Themes und so weiter). Bis 6.0.14 wandelte der Template-Aufruf `$attribute.content[alias]` jeden
  fehlenden Alias **beim Erzeugen der Seite** um: Eine Seite mit 200 neuen Bildern wartete nach dem Leeren des Caches
  auf 200 Umwandlungen.
- Im **Modus auf Abruf**, dem Standard von 6.0.15, **berechnet** der Template-Aufruf den Alias nur: seine URL, den
  Dateinamen, den MIME-Typ, Breite und Höhe, ohne etwas umzuwandeln. Der Browser fragt nach dem Bild, und dann wird
  der Alias erzeugt, von der View `content/image` oder vom leichten Einstiegsskript `index_image.php`. Templates
  ändern sich nicht.
- **Eine Einstellung schaltet zurück**: `image.ini [AliasSettings] AliasGeneration=render` ist das ursprüngliche
  Verhalten. Der Schalter wirkt jederzeit in beide Richtungen; keine Daten werden migriert, und in einem Modus
  erzeugte Aliase werden vom anderen genutzt.
- Die Adresse, die ein Template für einen noch nicht erzeugten Alias bekommt, ist **signiert**, sodass die Anfrage
  keine Sitzung, keinen Benutzer und keine Datenbank braucht, bis der Alias erzeugt wird. Unsignierte Adressen liefern
  nur Dateien aus, die vorhanden sind.
- Während ein Alias erzeugt wird, bekommt der Browser einen **Platzhalter**: ein neutrales SVG in der richtigen
  Größe, ohne Text, ohne Logo, das dem Dunkelmodus des Browsers folgt. Ein kleines Skript, `exp_image_swap.js`, fragt
  erneut und tauscht das echte Bild ein.
- Aliase können **WebP- und AVIF-Varianten** haben, ausgewählt nach dem `Accept`-Header des Browsers. Verwenden Sie
  sie nur für große Aliase.
- Nach dem Veröffentlichen werden die Aliase der neuen Bilder von der Job-Warteschlange (expqueue) **im Hintergrund**
  erzeugt, sodass der erste Besucher sie fertig vorfindet.
- Eine **Entwicklungs- oder Staging-Website** holt die Originale und Aliase, die ihr fehlen, über HTTPS von der
  Produktion und braucht daher keine Kopie von `var/storage`. Ein Schutzschalter und eine Warn-E-Mail schützen sie
  vor einer fehlerhaften Quelle.
- **Setup > Bild-Aliase** (`setup/aliases`) zeigt den Zustand und steuert alles; `./console exp:image:alias` tut
  dasselbe auf der Kommandozeile. Jedes Ereignis wird in `imagealias.log` geschrieben.
- **Leistung, ehrlich gesagt**: Eine Seite mit 500 Bildern, deren Aliase es gibt, lädt in **1,7 s**
  (Apache/PHP-FPM) und **1,9 s** (Velocity). Eine Seite mit 500 **neuen** Bildern ist nicht in 3 Sekunden
  vollständig: 4,6 s mit Apache/PHP-FPM (vorher 20,7 s) und 6,6 s mit Velocity (vorher 40,7 s) auf einem stark
  ausgelasteten Server, einmal 2,6 s, und ihre echten Bilder folgen innerhalb
  etwa einer halben Minute; allein das Umwandeln von 500 Originalen in voller Größe braucht mehr Rechenzeit als 3
  Sekunden eines Servers mit 12 Kernen. Was lange Bildlisten schnell macht, ist die Vorab-Erzeugung nach dem
  Veröffentlichen; siehe [Leistung und Abstimmung](#leistung-und-abstimmung).

## Inhalt

1. [Warum auf Abruf](#warum-auf-abruf)
2. [Der Modusschalter](#der-modusschalter)
3. [Wie ein Alias erzeugt wird](#wie-ein-alias-erzeugt-wird)
4. [Die Adressen und ihre Signatur](#die-adressen-und-ihre-signatur)
5. [Platzhalter und das Austauschskript](#platzhalter-und-das-austauschskript)
6. [WebP und AVIF](#webp-und-avif)
7. [Die Warteschlange, die Vorab-Erzeugung und die Umwandlungsplätze](#die-warteschlange-die-vorab-erzeugung-und-die-umwandlungsplätze)
8. [Staging von der Produktion](#staging-von-der-produktion)
9. [Webserver](#webserver)
10. [Setup > Bild-Aliase](#setup--bild-aliase)
11. [Die Kommandozeile: exp:image:alias](#die-kommandozeile-expimagealias)
12. [Das Log](#das-log)
13. [Alle Einstellungen](#alle-einstellungen)
14. [Leistung und Abstimmung](#leistung-und-abstimmung)
15. [Grenzen und was nicht getestet wurde](#grenzen-und-was-nicht-getestet-wurde)

## Warum auf Abruf

Ein Bild umzuwandeln dauert einige zehn Millisekunden (GD dekodiert das ganze Original, oft 1600 bis 2000 Pixel
breit). Im Modus render summieren sich diese Millisekunden innerhalb einer einzigen Seitenanfrage: Im Prozess auf dem
Testserver gemessen, verbringt ein Template, das 500 fehlende `i160`-Aliase zeigt, **24,8 Sekunden** mit Umwandeln,
bevor das erste Byte den Server verlässt. Nach dem Leeren des Caches, mit einem neuen Theme oder einem geänderten Alias
bezahlt das jede Seite mit Bildern, ein Besucher nach dem anderen.

Auf Abruf ist das HTML derselben Seite in **0,4 bis 0,6 Sekunden** draußen; die Aliase erzeugen die Bildanfragen, die
der Browser parallel sendet, einige wenige gleichzeitig je Server, und für ein Bild, das länger als `MaxWait` braucht,
zeigt der Browser einen Platzhalter.

## Der Modusschalter

```ini
# settings/override/image.ini.append.php
[AliasSettings]
# on-demand (the default) or render (the original: the template call converts while rendering)
AliasGeneration=on-demand
```

- **on-demand** (Standard für neue und aktualisierte Websites): Templates bekommen berechnete Einträge. Ein Alias, der
  schon vorhanden ist, behält seine URL in der Ablage (eine statische Datei); nur ein fehlender oder veralteter bekommt
  eine Adresse für die Erzeugung auf Abruf.
- **render**: das ursprüngliche Verhalten, unveränderter Codepfad. Verwenden Sie es, um zurückzugehen, zum Beispiel
  wenn sich die Webserver-Regeln aus [Webserver](#webserver) noch nicht installieren lassen. Schalten Sie jederzeit
  wieder auf `on-demand`.
- **Kommandozeilenskripte arbeiten immer im Modus render** (Cronjobs, Mailer, Exporte, PDF- und Feed-Generatoren lesen
  die Dateien, nach denen sie fragen), was immer die Einstellung sagt. Die Worker von Exponential Velocity sind in
  diesem Sinn keine Kommandozeilenskripte: Sie folgen der Einstellung wie PHP-FPM.
- Die berechnete Größe ist die Größe der Datei, die erzeugt wird: Sie wird je Umwandlungsschritt mit der eigenen
  Arithmetik des Konverters berechnet (GD schneidet ab, ImageMagick rundet), Fokuspunkt und Zuschnitt eingeschlossen.
  Ein auf Abruf erzeugter Alias ist Byte für Byte die Datei, die der Modus render mit demselben Konverter erzeugt
  (geprüft für 330 Aliase echter Bilder). Aliase, die ein Konverter ohne Größenarithmetik erzeugt (zum Beispiel ein
  eigener Imagine-Handler eines Themes), haben keine berechnete Breite und Höhe.

## Wie ein Alias erzeugt wird

1. Das Template fragt nach einem Alias. Im Modus auf Abruf gibt der Handler den berechneten Eintrag zurück: `url`,
   `filename`, `mime_type`, `width`, `height`, `full_path` (der Pfad, den die Datei in der Ablage haben wird). Nichts
   wird umgewandelt, nichts geschrieben, und die Datenbank wird nicht gefragt, ob die Datei vorhanden ist.
2. Der Browser fragt die URL an. `content/image` (oder `index_image.php` für einen Pfad der Ablage, den der Webserver
   nicht gefunden hat) prüft die Signatur, dann:
   - die Datei ist vorhanden: eine **Weiterleitung 302** auf die URL in der Ablage (der Webserver sendet die Datei)
     oder, wo der Webserver die Ablage nicht ausliefern kann (ein Datenbank-Cluster ohne Gateway, Entwürfe), die
     Datei selbst (`Delivery`);
   - die Datei fehlt: Der Alias wird erzeugt, mit derselben Kette von Konvertern, demselben Fokuspunkt und Zuschnitt
     wie im Modus render und denselben Schreibvorgängen in die Alias-Liste des Attributs und in `ezimagefile`, dann
     wie oben beantwortet;
   - alle Umwandlungsplätze sind belegt (eine Seite voller neuer Bilder): sofort der **Platzhalter** (200), und der
     Alias kommt in den **Rückstau**, der gleich nach der Antwort erzeugt wird (`Backlog`, siehe
     [Der Rückstau](#der-rückstau-gleich-nach-der-antwort-erzeugt));
   - das Erzeugen dauert länger als `MaxWait` (standardmäßig 2 Sekunden; wo es keinen Rückstau gibt): der
     **Platzhalter** (200), und der Alias wird für die Job-Warteschlange eingereiht, sodass er fertig ist, wenn der
     Browser erneut fragt.
3. Sperren gelten **je Alias-Datei**, nicht je Original: Die Aliase eines Bildes werden parallel erzeugt, und zwei
   Anfragen nach demselben Alias ergeben eine Umwandlung. `[ImageConverterSettings] LockTimeout` (standardmäßig 60
   Sekunden) begrenzt das Warten auf einen anderen Prozess; auf einem einzelnen Server ist die Sperre eine
   Dateisperre in `var/<site>/cache/imagealias-locks`, auf einem Datenbank-Cluster (eZDFS) der Eintrag `.generating`
   der Alias-Datei.
4. Eine begonnene Umwandlung wird immer zu Ende geführt (GD und ImageMagick lassen sich nicht unterbrechen, ohne die
   Arbeit zu verlieren); nach `MaxWait` beginnt in dieser Anfrage keine neue Umwandlung. Der ungünstigste Fall für
   eine Anfrage ist `MaxWait` plus eine Umwandlung.

Echte Aliase werden mit `Cache-Control: public, max-age=CacheMaxAge, immutable` gesendet (standardmäßig ein Jahr) und
dürfen vom Antwort-Cache von Velocity und vom HTTP-Cache behalten werden. Platzhalter werden mit `no-store` gesendet
und nie zwischengespeichert. `content/image` startet **keine Sitzung**, auch dann nicht, wenn der Besucher ein
Sitzungs-Cookie hat.

## Die Adressen und ihre Signatur

`[AliasSettings] OnDemandURL` wählt die Adresse, die ein Template für einen noch nicht erzeugten Alias bekommt:

| Wert | Adresse | Erzeugt einen fehlenden Alias |
|---|---|---|
| `token` (Standard) | `/content/image/<token>/<file name>` | ja: Das signierte Token trägt Attribut, Version, Sprache, Alias und den Pfad des Originals in der Ablage, sodass die Anfrage keine Datenbank braucht, bis der Alias erzeugt wird |
| `ids-token` | `/content/image/<attribute>/<version>/<language>/<alias>/<token>/<file name>` | ja (signiert) |
| `ids` | `/content/image/<attribute>/<version>/<language>/<alias>/<file name>` | nein: liefert einen vorhandenen Alias aus, sonst einen 404-Platzhalter |
| `node` | `/content/image/<node>/<attribute identifier>/<alias>` | nein, wie `ids` |
| `storage` | der einfache Pfad in der Ablage, wie ihn der Modus render liefert | ja, wenn der Pfad einen konfigurierten Alias eines veröffentlichten Bildes nennt; ein Fehltreffer erreicht `index_image.php` über die Regel des Webservers |

Die Argumente sind View-Parameter des Moduls, nie ein Query-String. Ein URI-Siteaccess davor funktioniert
(`/eng/content/image/...`).

**Regeln der Signatur:**

- Der Schlüssel ist das Geheimnis `image_alias_signing` (Setup > Geheimnisse). Es wird bei der ersten Verwendung
  angelegt (32 Zufallsbytes) und nie ersetzt, weil ein Lesen fehlgeschlagen ist.
- Eine **gültige Signatur gilt als Leserecht**: keine Abfrage von Benutzer, Sitzung oder Richtlinie, und sie darf den
  Alias erzeugen oder holen.
- Eine fehlende, falsche oder alte Signatur **liefert eine vorhandene Datei** trotzdem aus; sie erzeugt nie eine
  (404-Platzhalter).
- **Schlüsselwechsel**: Setup > Bild-Aliase > Übersicht, **Schlüssel wechseln**. Der vorherige Schlüssel wird als
  `image_alias_signing_previous` behalten und `SigningKeyGrace` Sekunden lang akzeptiert (standardmäßig 604800, eine
  Woche), sodass Seiten, die mit den alten URLs zwischengespeichert sind, weiter funktionieren. Velocity-Worker lesen
  den Schlüssel höchstens alle 300 Sekunden neu.
- **Entwürfe und Vorschauen** (`images-versioned`) bekommen Signaturen, die nach `DraftTokenLifetime` Sekunden ablaufen
  (standardmäßig 3600). Veröffentlichte Bilder bekommen URLs, die nicht ablaufen.

## Platzhalter und das Austauschskript

`Placeholder=enabled` (Standard): Statt eines kaputten Bildes antwortet ein fehlendes Bild mit einem neutralen SVG in
der berechneten Größe, in einem sanften Ton, der dem Dunkelmodus des Browsers folgt, ohne Text und ohne Logo. Das gilt
für **jedes** fehlende Bild, nicht nur für Aliase. Ein ausstehendes Bild (signiert, wird gerade erzeugt) antwortet mit
200; ein abgewiesenes oder unbekanntes mit 404. Beide tragen den Header `X-Image-Placeholder: 1` und
`Server-Timing: exp-image-placeholder` sowie `Cache-Control: no-store`.

`SwapScript=enabled` (Standard) fügt `design/standard/javascript/exp_image_swap.js` zu `design.ini
[JavaScriptSettings] FrontendJavaScriptList[]` hinzu. Es findet die Bilder, die als Platzhalter zurückkamen (an der
Markierung in `Server-Timing`) und tauscht `src`/`srcset` aus, sobald das echte Bild da ist. Es fragt in Runden: die
wartenden Bilder in der Reihenfolge der Seite, vier auf einmal, bis zum ersten, das noch ein Platzhalter ist (der
Server erzeugt sie in der Reihenfolge, in der sie angefragt wurden, die folgenden sind also auch noch nicht fertig).
Die Pause zwischen den Runden beginnt bei 0,5 s und wächst auf 3 s, solange nichts kommt; nach einer Minute ohne ein
fertiges Bild gibt es auf. Eine Seite mit 500 neuen Bildern kostet etwa eine Anfrage je fertigem Bild und eine je
Runde.

### Seiten-Platzhalter: eine Seite mit vielen neuen Bildern

`PagePlaceholders=enabled` (Standard). Jedes noch nicht erzeugte Bild ist eine Anfrage an PHP (`content/image`), und
auf einer Seite mit Hunderten neuer Bilder entscheiden diese Anfragen, nicht die Umwandlungen, wann die Seite geladen
ist. Deshalb werden die ersten `PagePlaceholderAfter` (standardmäßig 24) noch nicht erzeugten Bilder einer Seite wie
immer über `content/image` verlinkt, und die übrigen auf einen **statischen Platzhalter** ihrer Größe, den der
Webserver ohne PHP beantwortet und den der Browser einmal je Größe anfragt:

```
var/<site>/cache/public/imagealias-placeholder/160x106.svg#exp-src=<die content/image-Adresse>&exp-depth=6
```

- Das Fragment (`#...`) wird nie an den Server geschickt. Es nennt dem Austauschskript das echte Bild; das Skript
  fragt es in der Reihenfolge der Seite an und setzt es ein, sobald es erzeugt ist. `exp-depth` sagt dem Skript, wie
  viele Pfadsegmente der Platzhalter unter dem Wurzelverzeichnis der Installation liegt, damit eine relative Adresse so
  aufgelöst wird, wie es `ezroot` im Template tat.
- Die Aliase kommen in den Rückstau, während die Seite erzeugt wird, in der Reihenfolge der Seite, und die
  Abarbeiter starten gleich nach der Antwort der Seite; sie werden also schon erzeugt, bevor der Browser die Seite hat.
- Eine Seite mit solchen Platzhaltern bleibt höchstens `PagePlaceholderTime` Sekunden (standardmäßig 60) im
  Ansichts-Cache, damit die nächsten Besucher die echten Adressen bekommen.
- Ohne das Austauschskript behalten diese Bilder den Platzhalter, bis die Seite neu geladen wird: Ein Theme mit
  eigenem Seitenlayout muss es selbst laden (siehe oben).
- Nur wo der Rückstau gilt (ein Server, der zuerst antwortet), und nur für Aliase, die noch nicht erzeugt sind; ein
  veralteter Alias behält seine Adresse (seine alte Datei wird gezeigt, bis die neue erzeugt ist). `disabled`: Jedes
  noch nicht erzeugte Bild wird wie bisher über `content/image` verlinkt.

**Ein Theme mit eigenem Pagelayout muss das Skript selbst laden**, sonst bleiben die Platzhalter bis zum nächsten
Seitenaufruf. Die Designs des Kernels laden `FrontendJavaScriptList[]`; ein Theme, das seine eigenen `<script>`-Tags
schreibt, tut das nicht. Das Media-Theme (`sevenx_themes_media`) tut es am Ende seiner Seite, in
`design/media/templates/pagelayout/script_bottom.tpl`:

```smarty
{if ezini( 'AliasSettings', 'SwapScript', 'image.ini' )|ne( 'disabled' )}{ezscript( array( 'exp_image_swap.js' ) )}{/if}
```

Ein Template-Operator eines Themes, der die Alias-Liste liest und Bild-URLs selbst baut, muss die URL vom Handler
nehmen (`$handler->imageAlias( $alias )['url']`), statt nach einer Datei zu sehen und auf das Original
zurückzufallen; sonst bekommen seine Seiten weder Adressen für die Erzeugung auf Abruf noch ausgehandelte URLs. Der
Operator `ng_image_alias` des Media-Themes wurde so geändert.

## WebP und AVIF

Ein Alias-Block kann zusätzliche Formate für die Browser auflisten, die sie akzeptieren:

```ini
# settings/override/image.ini.append.php
[i1320]
Formats[]=webp
Formats[]=avif
```

- `content/image` antwortet mit einer Variante, wenn der `Accept`-Header den Typ ausdrücklich nennt (`image/webp`,
  `image/avif`; `image/*` und `*/*` zählen nicht; `q=0` lehnt ab) und `Negotiate=enabled` ist. Die Reihenfolge von
  `Formats[]` entscheidet bei gleicher Präferenz. Jede Antwort für einen solchen Alias trägt `Vary: Accept`.
- Eine Variante wird neben ihrem Alias als `<alias file>.<format>` gespeichert (`foo_large.jpg.webp`), aus der
  Alias-Datei erzeugt und neu erzeugt, wenn der Alias neuer ist. Sie steht weder im XML des Attributs noch in
  `ezimagefile`, sodass die Datenbank genau so bleibt, wie der Modus render sie schreibt; sie wird mit ihrem Alias
  entfernt. Aliase im Format GIF (Animation) und SVG bekommen keine Varianten.
- Der Konverter für Varianten ist `PreferredConverter` (`auto`: die Extension imagick, dann der Befehl ImageMagick,
  solange `[ImageMagick] IsEnabled=true`, dann GD; ein benannter wird zuerst versucht, dann die anderen). Ein Format,
  das kein Konverter schreiben kann, wird übersprungen. Die Aliase aus `AliasList[]` selbst verwenden in beiden Modi
  weiter `[ImageConverterSettings] ImageConverters[]`, sodass sich ihre Bytes nicht ändern.
- Qualität: `Quality[]=image/webp;80` des Alias-Blocks, sonst `[MIMETypeSettings] Quality[]`, sonst 80 für WebP und
  60 für AVIF.
- Der Pfad einer Variante in der Ablage funktioniert auch für sich (`index_image.php` erzeugt sie), für Webserver,
  die selbst aushandeln.

**Verwenden Sie `Formats[]` nur für große Aliase**, die wenige Male je Seite gezeigt werden (Hero, Artikelbild, volle
Breite). Ein Alias mit auszuhandelnden Formaten wird immer über `content/image` verlinkt, auch wenn er erzeugt ist,
sodass jede Ansicht eine PHP-Anfrage plus eine Weiterleitung ist. Auf einer Liste von 500 Vorschaubildern kostet das
bei jedem Seitenaufruf Sekunden (siehe [Leistung und Abstimmung](#leistung-und-abstimmung)). Für Vorschaubilder
lassen Sie die Formate weg oder lassen den Webserver aushandeln (zum Beispiel mit einer nginx-`map $http_accept` auf
den Pfad der Variante in der Ablage).

Der Webserver muss `.avif` und `.webp` mit ihren Typen senden; eine Variante namens `foo.jpg.avif` wird sonst nach
ihrem inneren `.jpg` als `image/jpeg` gesendet. `.htaccess_root` enthält `AddType image/avif .avif`; siehe die
[Rezepte](../../beispiele/webserver/bild-aliase-auf-abruf.md#die-typen-für-avif-und-webp).

## Die Warteschlange, die Vorab-Erzeugung und die Umwandlungsplätze

Der Kernel liefert drei Jobtypen für die Job-Warteschlange (expqueue) in `settings/expqueue.ini` mit:
`image.alias.generate` (Fehltreffer von Besuchern und Vorab-Erzeugung), `image.alias.bulk` (Erzeugen, Neu erzeugen
oder Entfernen über einen Bereich) und `image.alias.resync` (Staging). Eine Website überschreibt ihre Klasse und ihre
Zeitlimits in `settings/override/expqueue.ini.append.php`. Ohne expqueue funktioniert alles weiterhin: Nichts wird
eingereiht, Massenläufe laufen in der Anfrage oder auf der Kommandozeile, und ein fehlender Alias wird erzeugt, wenn
nach ihm gefragt wird.

**Prioritäten.** Fehltreffer von Besuchern gehen allem voran (Klasse interactive, Priorität 80), dann das
Veröffentlichen (editorial, 60), dann die Läufe der Administration (interactive, 60), dann Massenarbeit (bulk, 40).
Eine Klasse, die eine Website nicht hat, fällt auf die nächste passende zurück.

**Vorab-Erzeugung nach dem Veröffentlichen** (`PreGenerate=enabled`, nur im Modus auf Abruf): Wenn ein Objekt mit
Bildern veröffentlicht wird, erzeugt ein Job die Aliase der neuen Bilder.

- Standardmäßig jeden Alias aus der **Vereinigung** von `AliasList[]` des aktuellen Siteaccess und seiner
  `RelatedSiteAccessList[]`, jeden so erzeugt, wie ihn der Siteaccess definiert, der ihn auflistet (ein Alias, den nur
  die Website hat, wird mit der `image.ini` der Website erzeugt), dazu die Varianten aus `Formats[]`.
- `PreGenerateAliasList[]` beschränkt das auf einige Aliase (`PreGenerateAliasList[]=i1320`). Ein Name, den kein
  verwandter Siteaccess definiert, wird übersprungen, mit einer Warnung und einer Zeile in `imagealias.log` je
  Veröffentlichung (`event=queue result=unknown-alias`). Jeder andere Alias wird weiterhin auf Abruf erzeugt.
- `PreGenerateFormats=disabled` erzeugt die Aliase ohne ihre WebP-/AVIF-Varianten.
- Ohne die Warteschlange wird beim Veröffentlichen nichts umgewandelt; `imagealias.log` sagt `result=no-queue`.
- Auf dem Testserver hatte ein neues Bild 35 Aliase (26 des Admin-Siteaccess, 9 nur der Website) und 6 Varianten:
  etwa 2 bis 3,6 Sekunden Worker-Zeit je Bild.

**Umwandlungsplätze** (`MaxParallel`, standardmäßig 2): Höchstens so viele Umwandlungen (Aliase und Varianten) laufen
gleichzeitig je Server, gezählt über alle Prozesse: PHP-FPM, Velocity und die Kommandozeile. Ein Platz ist eine
Sperrdatei in `<cache dir>/imagealias-slots/<server name>/`. Eine Anfrage, die alle Plätze belegt findet, antwortet
sofort mit dem Platzhalter und überlässt den Alias dem Rückstau (unten); ohne Rückstau wartet sie innerhalb von
`MaxWait`, antwortet dann mit dem Platzhalter und reiht den Alias mit der Priorität der Besucher ein. Plätze gelten nur
im Modus auf Abruf.

### Der Rückstau: gleich nach der Antwort erzeugt

`Backlog=enabled` (Standard). Eine Seite mit Hunderten neuer Bilder fragt alle auf einmal an. Eine Anfrage wartet nie
auf einen Platz: Ist einer frei, erzeugt sie ihren Alias sofort; sonst antwortet sie in wenigen Millisekunden und ohne
Datenbankabfrage mit dem Platzhalter und schreibt den Alias in den Rückstau des Servers, eine kleine Datei je Alias in
`<cache dir>/imagealias-backlog/<server name>/`. Sobald ihre Antwort hinaus ist (PHP-FPM, LiteSpeed und FrankenPHP
beenden die Anfrage vorher; Velocity führt es nach dem Senden der Antwort aus), erzeugt eine Anfrage, die einen freien
Platz bekommt, den Rückstau, die ältesten zuerst, höchstens `BacklogTime` Sekunden lang (standardmäßig 30), und gibt
den Platz dann zurück. So arbeiten höchstens `MaxParallel` Prozesse gleichzeitig am Rückstau, alle anderen sind sofort
für die nächste Anfrage frei, und es wird nicht für jedes Bild ein Job in die Datenbank geschrieben.

- Jeder Alias wird so erzeugt, wie ein Job der Warteschlange ihn erzeugt: das veröffentlichte Bild, die `image.ini`
  des Siteaccess, in dem die Seite gezeigt wurde, dieselbe Sperre und dieselben Plätze; Dateien und
  Datenbankschreibvorgänge sind also die des Modus render.
- Der Cronjob-Teil `imagealiasbacklog.php` (Gruppe `frequent`) erzeugt, was keine Anfrage übernommen hat (ein Server,
  der keine Anfragen mehr bekommt), und gibt einen Eintrag zurück, dessen Prozess gestorben ist (nach 5 Minuten).
- **Abarbeiter-Prozesse** (`BacklogDrainer=process`, Standard): Nach ihrer Antwort erzeugt die Anfrage den Rückstau
  nicht selbst; sie startet einen Abarbeiter, `bin/php/expimagealiasdrain.php --slot=<n>`, losgelöst (eigene Sitzung,
  Ausgabe verworfen), und ist sofort für die nächste Anfrage frei. Ein Abarbeiter je vier wartende Aliase, höchstens
  `MaxParallel` je Server (je eine Sperrdatei in `<backlog directory>/.drainers/`); jeder erzeugt den Rückstau auf einem
  Umwandlungsplatz, bis er leer ist oder `BacklogTime` Sekunden vergangen sind. Von root gestartet (Velocity), wird ein
  Abarbeiter zuerst zum Eigentümer von `var/`. Warum: Velocity gibt einem Worker seine nächste Anfrage, sobald er
  geantwortet hat; ein Worker, der den Rückstau selbst erzeugte, ließ diese Anfrage bis zu `BacklogTime` warten
  (gemessen: 19 bis 30 s für ein Bild während einer kalten Seite); die Kinder von PHP-FPM wurden genauso festgehalten.
  Braucht `proc_open()` und die PHP-Kommandozeile; ohne sie, und mit `BacklogDrainer=request`, erzeugt die Anfrage den
  Rückstau wie bisher nach ihrer Antwort.
- Das Apache-Modul (`mod_php`) und die Kommandozeile können nicht zuerst antworten: Dort wartet die Anfrage innerhalb
  von `MaxWait` und reiht einen Job ein, wie mit `Backlog=disabled`.
- Mit Staging (`[AliasStaging] SyncFrom`) holt eine Anfrage weiterhin innerhalb von `MaxWait`; erst eine Anfrage, die
  danach alle Plätze belegt findet, nutzt den Rückstau.

**Massenläufe** senken die Priorität ihres Workers auf den nice-Wert `BulkNice` (standardmäßig 10), sodass sie dem
Webserver den Vortritt lassen, und lassen sich für alle Server auf einmal **pausieren und fortsetzen** (Setup >
Bild-Aliase, oder `exp:image:alias status --pause` / `--resume`); ein pausierter Lauf macht dort weiter, wo er
angehalten hat. Das Entfernen von Aliasen (Entfernen, Neu erzeugen) leert auch den Antwort-Cache von Velocity.

## Staging von der Produktion

Eine Website, die nicht die Produktion ist, kann die Bilder, die ihr fehlen, von einer anderen Installation holen;
eine Entwicklungs- oder Staging-Kopie braucht also die Datenbank, aber **keine Kopie von `var/storage`**.

```ini
# settings/override/image.ini.append.php on the staging site
[AliasStaging]
Environment=stage
SyncFrom=pro

[AliasSource_pro]
BaseURL=https://www.example.org
# the name of a secret, never the value: Setup > Secrets, or env:<VARIABLE>
Credential=image_staging_pro
```

- **Umgebungen**: `alpha` (früher Test neuen Codes), `dev` (die Installation eines Entwicklers), `beta` (Vorschau für
  Tester und Redakteure), `stage` (Vorproduktion, die letzte Prüfung vor einem Release), `pro` (Produktion: die Quelle
  der anderen, **holt nie**), `post` (eine Kopie nach einem Release, für Prüfungen und Hotfixes), `arc` (ein
  eingefrorenes Archiv). Standard `pro`; ein unbekannter Wert gilt als `pro`. Die meisten Websites brauchen nur eine
  Installation `dev` oder `stage`, die von `pro` abgleicht.
- **Quellen**: ein Block `[AliasSource_<environment>]` je Umgebung, die eine Quelle sein kann, mit `BaseURL` (http
  oder https, ohne Benutzernamen, Passwort oder Query darin) und `Credential`. `SyncFrom` nennt den zu verwendenden
  Block.
- **Zugangsdaten** liegen in **expSecrets** (Setup > Geheimnisse) oder in einer Umgebungsvariablen
  (`env:<VARIABLE>`): Das Geheimnis enthält `user:password` (Basic-Authentifizierung) oder `Bearer <token>`. Der Wert
  wird nie in eine Einstellung, das Log, eine E-Mail oder die Seite geschrieben; fehlt ein benanntes Geheimnis, wird
  überhaupt keine Anfrage gesendet.
- **Pfadzuordnung**: `PathMap[]=<local prefix>;<source prefix>` für eine Quelle, deren Pfade in der Ablage abweichen
  (das Präfix der Quelle ist ein Pfad unterhalb von `BaseURL` oder eine ganze URL; das längste lokale Präfix gewinnt).
- **Bei einem Seitenaufruf**: Ein fehlender Alias wird zuerst von der Quelle geholt; hat die Quelle ihn auch nicht,
  wird sein Original geholt und der Alias hier erzeugt. Ein fehlendes Original, nach dem über seinen Pfad in der
  Ablage gefragt wird, wird geholt. Alles innerhalb von `MaxWait`; danach der Platzhalter, und der Browser fragt
  erneut. Nur veröffentlichte Bilder werden geholt, nie Entwürfe. Eine geholte Datei wird geprüft (nur 200, der
  richtige Bildtyp, keine HTML-Fehlerseite, höchstens `MaxFileSize`, standardmäßig 64 MB) und über den
  Cluster-Datei-Handler gespeichert. Geholte Aliase werden in der eigenen Alias-Liste der Staging-Website so
  verzeichnet, wie der Modus render sie verzeichnen würde.
- **Abgleich** holt die Dateien vieler Bilder auf einmal (Setup > Bild-Aliase > Massenläufe, oder
  `exp:image:alias resync`). Bereiche: die ganze Website, ein Knoten oder ein Teilbaum, Klassen, Attribute, Aliase
  (`original` für die Originale) und „geändert seit“. Je Lauf: **nur fehlende** (Standard) oder abweichende Dateien
  **überschreiben** (Größe, dann SHA-256), und ein Probelauf. Ein Abgleich holt, was die gespeicherten Alias-Listen
  nennen; er schreibt nie in die Datenbank. Nur auf einer Staging-Installation.
- **Schutzschalter**: Nach `FailuresBeforePause` Fehlern in Folge (standardmäßig 5: keine Antwort, eine
  Zeitüberschreitung, 401/403, eine ungültige Antwort wie eine HTML-Fehlerseite, eine Datei, die die Quelle weder als
  Alias noch als Original hat) wird die Quelle für `PauseSeconds` pausiert (standardmäßig 300): Fehlende Bilder
  bekommen sofort den Platzhalter, niemand wartet auf die Quelle. Eine Datei, die durchkommt, beendet die Zählung. Ein
  einzelner Alias, den die Quelle nicht hat, ist kein Fehler (sein Original kann noch kommen). Setup > Bild-Aliase
  zeigt die Pause, den Grund und den letzten Fehler und kann sie zurücksetzen.
- **Warn-E-Mail**: Wenn eine Quelle pausiert wird, sendet `AlertEmail=enabled` eine E-Mail an `AlertRecipients[]`
  (sonst `site.ini [MailSettings] AdminEmail`), höchstens einmal pro Stunde je Quelle, über den eigenen E-Mail-Versand
  der Website.

Lassen Sie nie zwei Installationen eine Datenbank teilen, wenn Staging eingeschaltet ist: Die Staging-Website
verzeichnet die Aliase, die sie geholt oder erzeugt hat, in der Datenbank, und die Produktion würde dann Dateien
verlinken, die sie nicht hat.

## Webserver

Ein fehlendes Bild der Ablage und `content/image` müssen `index_image.php` erreichen, das leichte Einstiegsskript
(keine Sitzung, kein Seitenkernel, keine Template-Engine). Jedes Rezept steht auf der Seite
[Webserver-Rezepte für auf Abruf erzeugte Bild-Aliase](../../beispiele/webserver/bild-aliase-auf-abruf.md):

- **Apache**: `.htaccess_root` und `.htaccess_root_static` enthalten die Regeln, aktiv. Ein virtueller Host mit
  `AllowOverride None` braucht sie in seiner eigenen Konfiguration, und eine Liste erlaubter Skripte im
  Wurzelverzeichnis braucht `_image`.
- **nginx**: `try_files $uri /index_image.php$is_args$args;` in der Location der Ablage, eine Location für
  `content/image` und eine FastCGI-Location für `index_image.php`. nginx vor Apache fällt auf Apache zurück.
- **LiteSpeed** liest `.htaccess`; OpenLiteSpeed bekommt dieselben Regeln in seinem virtuellen Host.
- **FrankenPHP/Caddy**: Das Caddyfile, das die Engine frankenphp von Velocity schreibt, enthält sie; ein von Hand
  geschriebenes braucht denselben Block.
- **Exponential Velocity**: nichts zu tun; seine Front-Controller-Routen schicken beides an `index_image.php` im
  Worker.
- **Datenbank-Cluster (eZDFS/eZDB)**: Das Cluster-Gateway gibt ein Bild, für das es keine Datei hat, an
  `index_image.php` weiter (`CLUSTER_IMAGE_ALIAS_ON_DEMAND`, standardmäßig an).
- **Request Shield**: `content/image`, `index_image.php` und Fehltreffer in der Ablage brauchen ein eigenes Budget,
  sonst löst eine Seite mit Hunderten neuer Bilder das Tempolimit für Seitenaufrufe aus.

Ohne die Regeln funktioniert die Website im Modus auf Abruf mit dem Standard `OnDemandURL=token` trotzdem: Fehlende
Aliase werden als `content/image`-URLs verlinkt, die auch `index.php` beantwortet (mit einem vollen Start des Kernels
je Bild, also langsamer). Nur `OnDemandURL=storage` hängt von der Regel für Fehltreffer in der Ablage ab.

## Setup > Bild-Aliase

In der Administration: **Setup > Bild-Aliase** (`setup/aliases`), in der Gruppe System direkt nach **Cache**. Die
Seite zu sehen braucht die Richtlinie `setup/aliases`; jede Aktion braucht `setup/managealiases`. Administratoren haben
beide. Die Seite hat fünf Teile:

- **Übersicht**: Modus, Adressform, Auslieferung, `MaxWait`, belegte Umwandlungsplätze, Formatauswahl, Platzhalter
  und Austauschskript, Vorab-Erzeugung (mit der Alias-Liste, wenn gesetzt), die Zahl der Aliase; die Konverter der
  Formatvarianten in der Reihenfolge, in der sie versucht werden, mit WebP/AVIF; Umgebung und Quelle (Zustand des
  Schutzschalters, Fehler, letzter Fehler, eine Schaltfläche zum Zurücksetzen); wartende, laufende und
  fehlgeschlagene Jobs je Jobtyp und ob Massenläufe pausiert sind; die letzten 24 Stunden aus dem Log (erzeugt,
  Platzhalter, Fehler, an einen Job übergeben, geholt, Läufe, jeweils ein Link in den Verlauf; ein an einen Job
  übergebener Alias ist kein Fehler und löst keine Warnung aus); das Alter des Signaturschlüssels, die
  Gültigkeit des vorherigen Schlüssels und **Schlüssel wechseln**; Prüfungen, die Aufmerksamkeit brauchen; „So
  funktioniert es“.
- **Massenläufe**: Erzeugen, Neu erzeugen oder Entfernen (und Abgleich auf einer Staging-Installation) über die ganze
  Website, einen Knoten oder einen Teilbaum, mit Klassen, Attributen, Aliasen, „Geändert seit“ und einer Obergrenze;
  Probelauf; Überschreiben beim Abgleich. Läufe werden als Jobs eingereiht, mit einer Fortschrittskarte, die sich live
  aktualisiert (abbrechen, pausieren, fortsetzen); ohne Warteschlange laufen höchstens 50 Bilder in der Anfrage. Die
  letzten zehn Läufe werden aufgelistet.
- **Testwerkzeuge**: **Ein Bild** nach Knoten- oder Objekt-ID: je Bildfeld und Sprache das Original und jeden Alias
  mit der Adresse, die ein Template jetzt bekommt, Größe, Typ, ob die Datei vorhanden ist oder auf Abruf erzeugt wird,
  und die Varianten; einen Alias **Erzeugen** oder **Prüfen**, mit seiner Zeit. **Verbindung zu einer Quelle**: eine
  Anfrage an die Basisadresse der Quelle oder einen Pfad in der Ablage, mit gesendeten Zugangsdaten (nie angezeigt):
  Status, Typ, Größe, Zeit, Urteil.
- **Verlauf**: die neuesten Zeilen von `imagealias.log`, gefiltert nach Ereignis, Lauf-ID, Alias oder Text im Pfad;
  der Modus Verfolgen aktualisiert alle 3 Sekunden.
- **Einstellungen**: jeder Schlüssel von `[AliasSettings]` und `[AliasStaging]` sowie `BaseURL`/`Credential` der
  Quellblöcke, mit Hilfetext und Standard. **Änderungen vorab ansehen** zeigt, was das Speichern ändern würde; das
  Speichern schreibt `settings/override/image.ini.append.php` über den INI-Editor (mit einer Sicherung und einem
  Audit-Eintrag), nach „Bestätigen Sie, dass Sie es sind“. Zugangsdaten sind der Name eines Geheimnisses, nie sein
  Wert.

Das Kontextmenü des Inhaltsbaums hat **Fortgeschritten > Bild-Aliase**: die Aliase dieses Elements oder seines
Teilbaums erzeugen, neu erzeugen oder entfernen, als Job (mit `setup/managealiases`). Hilfe zur Seite:
[Bild-Aliase](../../hilfe/bild-aliase.md).

## Die Kommandozeile: exp:image:alias

`./console exp:image:alias <subcommand> [options]` (auch `php bin/php/expimagealias.php`) tut, was die Seite tut. Als
root gestartet, wird er zuerst zum Eigentümer von `var/`, sodass die Dateien, die er schreibt, dem Benutzer des
Webservers gehören.

| Unterbefehl | Was er tut |
|---|---|
| `status` (Standard) | Modus, Einstellungen, Konverter, Staging-Quelle, Warteschlange, Pause, die letzten 24 Stunden des Logs (Fehler und an einen Job übergebene Aliase getrennt; json: `dashboard.counts.failures` und `.deferred`), Prüfungen und die letzten Läufe. `--job=<id>` ein Job mit seinem Fortschritt; `--pause[=<reason>]` und `--resume` pausieren die Massenläufe aller Server oder lassen sie weiterlaufen; `--cancel=<id>` bricht einen Job für Bild-Aliase ab |
| `generate` | die fehlenden Aliase erzeugen |
| `regenerate` | die Aliase entfernen und neu erzeugen |
| `purge` | die Aliase entfernen (die Originale bleiben); ein Seitenaufruf erzeugt sie auf Abruf neu |
| `resync` | fehlende Originale und Aliase von der Staging-Quelle holen (nur Staging-Installationen) |
| `test` | `--node=<id>` oder `--object=<id>`: je Alias die Adresse, die ein Template bekommt, Größe, Typ, Datei vorhanden oder auf Abruf erzeugt, Varianten; `--alias=<name>` erzeugt ihn jetzt (`--dry-run`: prüft nur); `--source[=<env>]` testet die Verbindung zu einer Staging-Quelle, `--path=<p>` mit einem Pfad in der Ablage |

| Option | Bedeutung |
|---|---|
| `--node=<id>`, `--subtree=<id>` | die Bilder des Objekts an diesem Knoten, oder jedes Objekts darunter (den Knoten eingeschlossen) |
| `--object=<id>` | `test`: die Bilder dieses Objekts |
| `--class=a,b`, `--attribute=a,b` | nur Objekte dieser Klassen, nur diese Bildattribute (Bezeichner) |
| `--alias=a,b` | nur diese Aliase (`resync`: `original` für die Originale); `test`: der Alias, der erzeugt oder geprüft werden soll |
| `--since=<when>` | nur Objekte, die seit einem Datum (`2026-11-01`, `"2026-11-01 14:30"`) oder seit einer Zeitspanne (`2d`, `12h`, `30m`, `"1 week"`) geändert wurden |
| `--limit=<n>` | höchstens so viele Bilder |
| `--dry-run` | generate, regenerate, purge: nur die Bilder zählen; resync: sagen, was geholt würde; `test --alias`: nur berichten |
| `--overwrite` | resync: auch Dateien ersetzen, die von der Quelle abweichen |
| `--queue` | den Lauf als Job einreihen und seine ID ausgeben, statt ihn hier auszuführen |
| `--wait` | ein Lauf hier wartet, solange Massenläufe pausiert sind, statt anzuhalten |
| `--all` | regenerate, purge: einen Lauf über jedes Bild der Installation erlauben |
| `--format=text\|json` | Text (Standard) oder die maschinenlesbare Ausgabe `exp-image-alias/1` |

Ohne Bereich umfasst ein Lauf jedes Bild; `regenerate` und `purge` brauchen dann `--all`. Die Aliase sind die des
Siteaccess (`-s`) und seiner `RelatedSiteAccessList[]`. Ein Lauf im Prozess erzeugt Aliase genau so wie die Jobs
(dieselben Sperren, Plätze, Varianten, gespeicherten Daten und dasselbe Log). **Exit-Codes**: 0 erledigt; 1 ein Lauf
oder Test hatte Fehler; 2 falsche Optionen; 3 nicht verfügbar (keine Job-Warteschlange für `--queue`, keine
Staging-Installation, kein solches Bild und kein solcher Job); 4 angehalten, weil Massenläufe pausiert sind.

```bash
./console exp:image:alias status
./console exp:image:alias generate --subtree=93 --dry-run
./console exp:image:alias generate --subtree=93
./console exp:image:alias regenerate --node=93 --alias=i160,i320 --queue
./console exp:image:alias status --job=12898
./console exp:image:alias purge --class=image --since=2d
./console exp:image:alias resync --subtree=2 --since=1w --overwrite
./console exp:image:alias test --node=93
./console exp:image:alias status --pause="nightly import"
```

`exp:cache imagealias` (alle Aliase auf einmal leeren) und Setup > Cache bleiben, wie sie waren; ihre Hilfe verweist
hierher.

## Das Log

Jedes Ereignis ist eine Zeile in `imagealias.log` im Log-Verzeichnis der Website (`var/<site>/log/` oder `var/log/`),
geschrieben über das Log des Kernels, sodass es nach `site.ini [FileSettings] LogFileMaxSize`, `LogFileRotateCount`
und `LogFileCompress` rotiert:

```
[ Nov 13 2026 09:12:01 ][ site ][ <request> ] event=generate result=ok ms=84 alias=small run=- path=var/site/storage/...
```

Ereignisse: `generate`, `fail`, `defer`, `placeholder`, `stage`, `queue`, `run-start`, `run-end`, `pause`, `resume`
(und `serve`, das nicht geschrieben wird: eine Zeile je Bildanfrage würde das Log fluten). `defer` (`result=timeout`)
heißt: Innerhalb der Wartezeit wurde kein Umwandlungsplatz und keine Sperre frei; die Anfrage hat den Platzhalter
geliefert, und ein Job erzeugt den Alias oder die Variante. Das ist kein Fehler. Logs von vor `defer` enthalten dafür
`event=fail result=timeout`; Übersicht und Verlauf lesen diese Zeilen als `defer` (die Datei bleibt unverändert). Ein
Abruf beim Staging, der in eine Zeitüberschreitung lief, nennt seine Quelle (`source=`) und bleibt ein `fail`. `run-start` und `run-end` eines
Massenlaufs tragen die Lauf-ID, die die Seite und der Befehl ausgeben; `run-end` trägt die Zählungen. Zeilen einer
Umwandlung tragen ihren Platz, wie lange sie auf den Platz gewartet und wie lange sie ihn gehalten hat. Der Verlauf in
Setup > Bild-Aliase und Setup > Logdateien lesen es live.

Eine belebte Seite mit neuen Bildern füllt die standardmäßigen 2 MB einer Log-Datei in etwa einer Minute; erhöhen Sie
`LogFileMaxSize`, wenn der Verlauf auf einer solchen Website mehr als die letzten Minuten abdecken soll.

## Alle Einstellungen

`settings/image.ini`, überschrieben in `settings/override/image.ini.append.php` (oder je Siteaccess). Jeder Schlüssel
ist in der Datei und in der erzeugten Referenz dokumentiert
([\[AliasSettings\]](../../beispiele/ini-einstellungen/image/AliasSettings.md),
[\[AliasStaging\]](../../beispiele/ini-einstellungen/image/AliasStaging.md),
[\[AliasSource_pro\]](../../beispiele/ini-einstellungen/image/AliasSource_pro.md)).

| Einstellung | Standard | Was sie tut |
|---|---|---|
| `[AliasSettings] AliasGeneration` | `on-demand` | `on-demand` oder `render` (das ursprüngliche Verhalten) |
| `OnDemandURL` | `token` | `token`, `ids-token`, `ids`, `node` oder `storage` ([Adressen](#die-adressen-und-ihre-signatur)) |
| `Delivery` | `auto` | `auto` (Weiterleitung, wenn der Webserver die Ablage ausliefert, sonst die Datei), `redirect`, `stream` |
| `MaxWait` | `2` | Sekunden, die eine Anfrage mit dem Erzeugen oder Holen eines Alias verbringen darf, Wartezeiten eingeschlossen; dann der Platzhalter |
| `DraftTokenLifetime` | `3600` | Sekunden, die eine signierte URL eines Entwurfs- oder Vorschaubildes gültig bleibt |
| `SigningKeyGrace` | `604800` | Sekunden, die der vorherige Signaturschlüssel nach einem Wechsel noch akzeptiert wird |
| `PreferredConverter` | `auto` | Konverter der WebP-/AVIF-Varianten: `auto`, `imagick`, `imagemagick`, `gd` |
| `Negotiate` | `enabled` | mit einer WebP-/AVIF-Variante antworten, wenn der Browser sie akzeptiert und der Alias sie auflistet |
| `Placeholder` | `enabled` | ein neutrales SVG in passender Größe, solange ein Bild fehlt |
| `SwapScript` | `enabled` | das Skript, das Platzhalter gegen das echte Bild austauscht |
| `PreGenerate` | `enabled` | die Aliase der neuen Bilder nach dem Veröffentlichen im Hintergrund erzeugen |
| `PreGenerateAliasList[]` | leer (alle) | die Vorab-Erzeugung auf diese Aliase beschränken |
| `PreGenerateFormats` | `enabled` | die Vorab-Erzeugung erzeugt auch die WebP-/AVIF-Varianten |
| `MaxParallel` | `2` | gleichzeitige Umwandlungen je Server, über alle Prozesse |
| `Backlog` | `enabled` | eine Anfrage, die alle Plätze belegt findet, antwortet sofort; der Alias wird gleich nach der Antwort erzeugt |
| `BacklogTime` | `30` | Sekunden, die eine Anfrage nach ihrer Antwort weiter am Rückstau arbeitet (1 bis 600) |
| `BacklogDrainer` | `process` | wer den Rückstau nach der Antwort erzeugt: Abarbeiter-Prozesse, oder `request` (die Anfrage selbst) |
| `PagePlaceholders` | `enabled` | nach `PagePlaceholderAfter` noch nicht erzeugten Bildern verlinkt eine Seite die übrigen auf einen statischen Platzhalter |
| `PagePlaceholderAfter` | `24` | noch nicht erzeugte Bilder, die eine Seite zuerst über `content/image` verlinkt (0 bis 10000) |
| `PagePlaceholderTime` | `60` | Sekunden, die eine Seite mit solchen Platzhaltern im Ansichts-Cache bleiben darf (10 bis 3600) |
| `BulkNice` | `10` | nice-Wert des Workers eines Massenlaufs (0 lässt ihn unverändert) |
| `CacheMaxAge` | `31536000` | max-age in `Cache-Control` eines echten Alias (Platzhalter: `no-store`) |
| `LogFileMaxSize` | `20971520` | Bytes, die `var/log/imagealias.log` erreichen darf, bevor es rotiert wird: eine eigene Größe, nicht site.ini `LogFileMaxSize` für alle Logs, damit ein Massenlauf seinen Verlauf behält (1 MB bis 1 GB) |
| `[<alias>] Formats[]` | keine | `webp`, `avif`: Varianten für die Formatauswahl |
| `[ImageConverterSettings] LockTimeout` | `60` | Sekunden, die ein Prozess auf die Sperre eines anderen Prozesses für eine Alias-Datei wartet (0: kein Warten) |
| `[AliasStaging] Environment` | `pro` | `alpha`, `dev`, `beta`, `stage`, `pro`, `post`, `arc`; `pro` holt nie |
| `SyncFrom` | leer | die Umgebung, von der geholt wird; leer: kein Staging |
| `PathMap[]` | keine | `<local prefix>;<source prefix>` |
| `Timeout` | `5` | Sekunden, die ein Download dauern darf (auf einer Seite nie mehr, als von `MaxWait` übrig ist) |
| `MaxFileSize` | `67108864` | die größte geholte Datei, in Bytes |
| `FailuresBeforePause` | `5` | Fehler in Folge, bevor die Quelle pausiert wird |
| `PauseSeconds` | `300` | wie lange eine pausierte Quelle nicht gefragt wird |
| `AlertEmail` | `enabled` | E-Mail, wenn eine Quelle pausiert wird (höchstens einmal pro Stunde je Quelle) |
| `AlertRecipients[]` | keine | die Adressen; keine: `site.ini [MailSettings] AdminEmail` |
| `[AliasSource_<env>] BaseURL` | leer | die Quellinstallation, z. B. `https://www.example.org` |
| `Credential` | leer | der Name eines Geheimnisses (oder `env:<VARIABLE>`), das `user:password` oder `Bearer <token>` enthält |

`config.cluster.php`: `define( 'CLUSTER_IMAGE_ALIAS_ON_DEMAND', false );` hindert das Cluster-Gateway daran, fehlende
Bilder an `index_image.php` weiterzugeben (Standard: es tut es).

## Leistung und Abstimmung

**Erneut gemessen am 19. November 2026**, nachdem die Rückstau-Einträge einer Seite erst nach ihrer Antwort
geschrieben werden (sie kosteten eine Seite mit 500 neuen Bildern etwa 0,3 s vor ihrem ersten Byte) und Abarbeiter, deren
Zeit mit wartenden Einträgen abläuft, die nächsten starten. Dieselbe Seite, `MaxParallel=6`, je drei Läufe, Last 18 bis
35 auf 12 Kernen, der Browser auf demselben Server:

| Seite | Server | Zustand | Laden (Median) | Vollständig | Echt | Erstes Byte |
|---|---|---|---|---|---|---|
| 500 x i160 | Apache/PHP-FPM | kalt | **2,8 s** | 2,6 s | 32,6 s | 0,8 s (vorher 1,1 bis 1,3 s) |
| 500 x i160 | Velocity | kalt | 4,2 s | 4,1 s | 37,3 s | 1,2 s |
| 500 x i160 | Apache/PHP-FPM | warm | 2,7 s | 2,7 s | 2,7 s | 0,2 s |
| 500 x i160 | Velocity | warm | 4,3 s | 4,3 s | 4,3 s | 0,5 s |

- Das Ziel (kalt in unter 3 s vollständig) ist mit Apache/PHP-FPM erreicht. Mit Velocity ist eine warme Seite mit 500
  gespeicherten Bildern langsamer als die kalte, die nur einige Dutzend Platzhalterdateien abruft: Dort begrenzt sein
  HTTP/2-Weg für viele Streams auf einer Verbindung, nicht die Aliase.
- Etwa 1,1 bis 1,5 s jedes Ladens braucht der Browser, um 500 Bilder anzuordnen; der Alias-Code nimmt davon wenige
  Millisekunden.

**Erneut gemessen am 17. November 2026**, nach zwei Änderungen: Seiten-Platzhalter (nach 24 noch nicht erzeugten
Bildern verlinkt eine Seite die übrigen auf einen statischen Platzhalter, ohne PHP-Anfrage; siehe
[Seiten-Platzhalter](#seiten-platzhalter-eine-seite-mit-vielen-neuen-bildern)) und Abarbeiter-Prozesse (der Rückstau wird
von eigenen Prozessen erzeugt, nicht von denen des Webservers; mit Velocity ließ ein Worker, der den Rückstau erzeugte,
die nächste Anfrage, die er bekam, bis zu 30 s warten). Derselbe Server und dieselbe Seite, `MaxParallel=6`, Mediane
aus drei Läufen; der Server war viel stärker ausgelastet als am 15. November (Last 85 bis 124 auf 12 Kernen während
dieser Läufe, 13 bis 36 während der Läufe vor den Änderungen am selben Tag):

| Seite | Server | Zustand | Laden vorher (selber Tag) | Laden jetzt | Vollständig jetzt | Echt jetzt |
|---|---|---|---|---|---|---|
| 500 x i160 | Apache/PHP-FPM | kalt | 18,2 s | 4,6 s | 4,3 s | 50,6 s |
| 500 x i160 | Velocity | kalt | 30,7 s | 6,6 s | 6,3 s | über 60 s |
| 500 x i160 | Apache/PHP-FPM | warm (HTML vom kalten Besuch) | 3,1 s | 5,3 s | 5,3 s | 5,3 s |
| 500 x i160 | Velocity | warm (HTML vom kalten Besuch) | 2,8 s | 3,5 s | 3,5 s | 3,5 s |

- Ein kalter Lauf mit Apache/PHP-FPM war bei geringerer Last am selben Morgen in **2,6 s** vollständig. Die warmen
  Werte sind nur durch die Last langsamer: Am warmen Weg hat sich nichts geändert.
- Die Seite kostet jetzt 24 PHP-Anfragen und eine statische Anfrage je Platzhaltergröße statt 500 PHP-Anfragen; die
  Abarbeiter erzeugen die Aliase auf diesem Server mit etwa 15 bis 30 je Sekunde, und das Austauschskript zeigt sie in
  der Reihenfolge der Seite.
- "Echt" hängt an den Umwandlungen (etwa 40 s Rechenzeit für 500 Originale in voller Größe), bei dieser Last umso mehr;
  dafür ist die Vorab-Erzeugung nach dem Veröffentlichen da.

**Erneut gemessen am 15. November 2026**, nach drei Änderungen: der Rückstau (eine Anfrage wartet nie auf einen Platz),
die Signaturschlüssel zwischen den Anfragen von PHP-FPM aufbewahrt (keine Datenbank für eine Bildanfrage) und das
Erweiterungsverzeichnis einmal je Anfrage gelesen (`index_image.php` beantwortete mit PHP-FPM vorher etwa 30 Anfragen
pro Sekunde, danach etwa 150). Derselbe Server, dieselbe Seite mit 500 `i160`-Bildern, `MaxParallel=6`, Last 10 bis
38 durch andere Arbeit, Mediane aus drei Läufen (vorher: zwei):

| Seite | Server | Zustand | Laden vorher | Laden jetzt | Vollständig jetzt | Echt jetzt |
|---|---|---|---|---|---|---|
| 500 x i160 | Apache/PHP-FPM | kalt | 25,2 s | 8,2 s | 7,4 s | 32,7 s |
| 500 x i160 | Velocity | kalt | 30,8 s | 24,4 s | 24,4 s | 48,7 s |
| 500 x i160 | Apache/PHP-FPM | warm (HTML vom kalten Besuch) | 2,8 s | 1,7 s | 1,7 s | 1,7 s |
| 500 x i160 | Velocity | warm (HTML vom kalten Besuch) | 10,4 s | 1,9 s | 1,9 s | 1,9 s |
| 500 x i160 | Apache/PHP-FPM | warm, frisches HTML | | 2,1 s | 2,1 s | 2,1 s |
| 500 x i160 | Velocity | warm, frisches HTML | | 2,5 s | 2,5 s | 2,5 s |

Das Ziel von 3 Sekunden ist also für eine Seite erreicht, deren Aliase es gibt, auf beiden Servern, und nicht für eine
Seite mit 500 neuen Bildern:

- **Die Umwandlungen selbst**: 500 `i160` aus Originalen mit 800 bis 1920 Pixeln Breite brauchen etwa 40 Sekunden
  Rechenzeit (GD dekodiert das ganze Original); 6 Plätze schafften hier 13 bis 21 pro Sekunde. „Echt“ kann auf 12
  Kernen nicht in 3 Sekunden kommen, was immer der Server tut; dafür ist die Vorab-Erzeugung da.
- **500 Anfragen an PHP**: Jeder Platzhalter ist weiterhin eine PHP-Anfrage. Apache/PHP-FPM hinter nginx beantwortete
  hier je nach Last etwa 75 bis 180 pro Sekunde über eine HTTP/2-Verbindung; die Seite ist vollständig, wenn die
  letzte beantwortet ist.
- **Velocity über HTTP/2**: Eine HTTP/2-Verbindung zu Velocity beantwortete hier 37 bis 84 Bildanfragen pro Sekunde
  (Apache hinter nginx im selben Moment 76 bis 177), 32 einzelne Verbindungen bekommen über 200. Das liegt im
  HTTP/2-Pfad der Engine, nicht im Kernel, und ist der größte Teil des Unterschieds zwischen den beiden Servern.

Die Messungen vom 13. November, vor diesen Änderungen (zuerst mit `MaxParallel=2`):

Gemessen am 13. November 2026 auf dem Testserver alpha.se7enx.com: 12 CPU-Kerne, Last 7 bis 30 durch andere Arbeit
während jedes Laufs (die Zahlen sind für einen untätigen Server pessimistisch), SQLite-Datenbank, eine Seite mit 500
Bildobjekten (Kopien von 204 echten Originalen, 800 bis 1920 Pixel breit), Chromium ohne Cache, Mediane aus drei
Läufen, Einstellungen wie ausgeliefert (`MaxParallel=2`, `MaxWait=2`, `i160` mit WebP und AVIF). „Vollständig“
heißt, dass jedes Bild etwas zeigt (ein Platzhalter zählt); „echt“ heißt, dass jedes Bild das echte Bild zeigt.

| Seite | Server | Zustand | Erstes Byte | Vollständig | Echt |
|---|---|---|---|---|---|
| 500 x i160 | Apache/PHP-FPM | kalt | 0,59 s | 20,7 s | 21,1 s |
| 500 x i160 | Velocity | kalt | 1,10 s | 40,6 s | 53,9 s |
| 500 gemischte Aliase | Apache/PHP-FPM | kalt | 0,74 s | 21,1 s | 30,2 s |
| 500 gemischte Aliase | Velocity | kalt | 1,88 s | 35,3 s | 42,4 s |
| 110 x i160 | Apache/PHP-FPM | kalt | 0,21 s | 4,52 s | 4,62 s |
| 500 x i160 | Apache/PHP-FPM | warm | 0,40 s | 6,7 s | 6,7 s |
| 500 x i160 | Velocity | warm | 0,40 s | 22,1 s | 22,1 s |
| 500 gemischte Aliase | Apache/PHP-FPM | warm | 0,28 s | 4,39 s | 4,39 s |
| 500 gemischte Aliase | Velocity | warm | 0,44 s | 4,47 s | 4,47 s |
| 110 x i160 | Apache/PHP-FPM | warm | 0,37 s | 1,76 s | 1,76 s |

Zum Vergleich: Im Modus render brauchen dieselben 500 fehlenden Aliase 24,8 Sekunden innerhalb der Seitenanfrage,
vor dem ersten Byte. **Das Ziel von 3 Sekunden für eine Seite mit 500 neuen Bildern ist nicht erreicht**, auf keinem
der beiden Server, weder kalt noch warm. Eine Seite mit 110 Bildern erreicht es warm unter Apache/PHP-FPM (1,8 s). Ein
signierter Treffer eines vorhandenen Alias kostet im Prozess 0,05 ms, keine Datenbankabfrage und keine Sitzung; über
HTTPS 70 bis 80 ms je Bild, vor allem TLS und der Start des Kernels.

Was begrenzt, geordnet nach Wirkung, und was hilft:

1. **`MaxParallel`**: 2 gleichzeitige Umwandlungen begrenzen eine kalte Seite auf etwa 20 bis 35 Bilder pro Sekunde.
   Setzen Sie es auf etwa die Hälfte der CPU-Kerne eines Servers, dessen Seiten viele neue Bilder zeigen (der
   Testserver läuft jetzt mit 6 von 12). Gemessener Durchsatz von `i160` plus WebP: 19 pro Sekunde mit 1 oder 2
   Plätzen, 33 mit 4, 45 mit 12. Auch das Schreiben jedes Alias in die Datenbank (SQLite: ein Schreiber zur Zeit)
   begrenzt ihn.
2. **`Formats[]` auf Vorschaubildern**: Ein ausgehandelter Alias ist bei jeder Ansicht eine PHP-Anfrage und eine
   Weiterleitung, warm wie kalt. 500 ausgehandelte Vorschaubilder warm: 6,7 s (Apache), 22 s (Velocity); mit 84 von
   500 ausgehandelt: 4,4 s. Setzen Sie `Formats[]` nur auf große Aliase (der Testserver hat sie von `i160` auf
   `i770`, `i1320` und `i1920` verlegt).
3. **Vorab-Erzeugung**: Nach dem Veröffentlichen erzeugte Aliase sind beim ersten Aufruf URLs in der Ablage, statische
   Dateien. Auf Abruf ist das Sicherheitsnetz, nicht der Plan für Seiten mit 500 Bildern. Lassen Sie
   `PreGenerate=enabled` und die Job-Warteschlange laufen; beschränken Sie `PreGenerateAliasList[]` auf die Aliase,
   die die Seiten wirklich zeigen, wenn das Veröffentlichen zu viel Arbeit erzeugt.
4. **Die Regeln für `index_image.php`**: Ohne sie läuft `content/image` über `index.php` (ein voller Start des Kernels
   je Bild). Installieren Sie sie und geben Sie PHP-FPM genug Kindprozesse: Anfragen, die eingereiht sind, bevor PHP
   startet, liegen außerhalb von `MaxWait`, sodass die Seite wartet, statt Platzhalter zu bekommen.
5. **Der Request Shield** muss Bildern ein eigenes Budget geben (siehe die Rezepte); sonst bekommt eine Seite mit
   Hunderten neuer Bilder Antworten 429 und wird nie vollständig.
6. **Das Austauschskript** muss auf der Seite sein, sonst bleiben Platzhalter bis zum Neuladen.
7. **Velocity mit vielen Bildern auf einer HTTP/2-Verbindung** beantwortet etwa 23 Bildanfragen pro Sekunde, wenn der
   Browser sie auf einer Verbindung bündelt (180 pro Sekunde über 32 Verbindungen); seine Zahlen oben leiden darunter.
   Das ist eine Sache der Velocity-Engine, nicht des Kernels.

## Grenzen und was nicht getestet wurde

- **Nur die in `image.ini` definierten Aliase** werden erzeugt. Es gibt keine freien Größen
  (`/content/image/.../300x200`): Ein Name, der nicht konfiguriert ist, wird abgewiesen.
- Umwandeln lässt sich nicht unterbrechen: Eine langsame Umwandlung kann eine Anfrage `MaxWait` plus diese Umwandlung
  dauern lassen.
- Das Austauschskript gibt nach einer Minute ohne ein fertiges Bild auf; ein Bild, das dann noch nicht erzeugt ist,
  bleibt bis zum nächsten Seitenaufruf ein Platzhalter.
- Aliase, die ein Konverter ohne Größenarithmetik erzeugt (zum Beispiel der Imagine-Handler von `cjw_themes_jumper`),
  haben keine berechnete Breite und Höhe.
- Der Antwort-Cache von Velocity beachtet `Vary` nicht; eine ausgehandelte Antwort, die direkt gesendet wird, wird
  deshalb als `private` gesendet, und nur die Weiterleitungen werden zwischengespeichert.
- **Nicht getestet**: ein echter eZDFS-/eZDB-Cluster (die Übergabe durch das Gateway wurde mit einem Gateway ohne
  Metadaten auf eZFS-Ablage getestet; die Sperren verwenden konstruktionsbedingt die `.generating`-Einträge des
  Handlers); LiteSpeed und OpenLiteSpeed (kein Server verfügbar; die Regeln sind die unter Apache getesteten);
  FrankenPHP im Worker-Modus; nginx vor Apache wurde auf dem Testserver konfiguriert und geprüft, nicht gemessen; eine
  Staging-Website, die von PHP-FPM oder Velocity ausgeliefert wird (die Staging-Tests liefen auf dem eingebauten
  Server von PHP mit dem Router des Kernels); die Warn-E-Mail über SMTP oder sendmail (nur Datei-Versand); eine
  Website ohne expqueue (die Pfade ohne Warteschlange sind nur mit Unit-Tests getestet); das Abbrechen eines
  laufenden Massenjobs.
