---
title: Bild-Operatoren
audience: developer, designer, app-creator
category: development
order: 270
summary: texttoimage, image und imagefile aus eZTemplateImageOperator - Text mit einer TrueType-Schrift aus texttoimage.ini als Bild rendern, Ebenen zusammensetzen und die Größe einer Bilddatei lesen - welche davon Dateien unter var/cache/texttoimage schreiben, und Beispiele von alpha.
keywords: texttoimage, image, imagefile, GD, Schrift, Schriftart, Bild, Ebene, font, texttoimage.ini, eZImageLayer, eZImageTextLayer, eZImageObject, eZTemplateImageOperator
type: help
---
# Bild-Operatoren

Drei Operatoren von `eZTemplateImageOperator` (`lib/eztemplate/classes/eztemplateimageoperator.php`), registriert
über `lib/eztemplate/classes/eztemplateautoload.php`, arbeiten mit Bildern über die GD-Erweiterung von PHP (die Klasse
prüft auf `ImageTTFBBox`, `ImageCreate`, `ImageTTFText`, `ImagePNG` und die anderen Funktionen, die sie braucht, und
protokolliert einen Fehler, statt zu arbeiten, wenn eine davon fehlt):

- `texttoimage` rendert einen Text mit einer TrueType-Schrift als PNG-Bild;
- `image` fügt Ebenen zu einem Bild zusammen;
- `imagefile` liest eine vorhandene Bilddatei.

Mit den Bildern von Inhaltsobjekten, die aus dem Bild-Datentyp und seinen Aliasen kommen, haben sie nichts zu tun.
Alle drei geben ein Bildobjekt zurück (`eZImageLayer`, `eZImageTextLayer` oder `eZImageObject`), das als `<img>`-Tag
ausgegeben wird und die Attribute `filepath`, `filename`, `width`, `height`, `alternative_text`, `imagepath` und
`has_size` bietet.

## Inhalt

1. [Einstellungen: texttoimage.ini](#einstellungen-texttoimageini)
2. [texttoimage](#texttoimage)
3. [image](#image)
4. [imagefile](#imagefile)
5. [Verwandte Seiten](#verwandte-seiten)

## Einstellungen: texttoimage.ini

`settings/texttoimage.ini` enthält die Pfade und die Schriftklassen:

| Einstellung | Wert auf alpha | Bedeutung |
|---|---|---|
| `[ImageSettings] UseCache` | `enabled` | ein bereits mit demselben Text und denselben Einstellungen gerendertes Bild wiederverwenden |
| `[PathSettings] FontDir[]` | `design/standard/fonts` | wo nach den `.ttf`-Schriften gesucht wird |
| `[PathSettings] CacheDir` | `var/cache/texttoimage` | wohin gerenderte Bilder geschrieben werden |
| `[DefaultSettings]` | `Family=1942`, `PointSize=40`, weißer Hintergrund, schwarzer Text | gilt, wenn eine Klasse nichts anderes angibt |
| `[1942]`, `[a_d_mono]`, `[sketchy]`, `[smartie]` ... | Schriftfamilie und Anpassungen | die Klassen, die ein Template nennen kann |

## texttoimage

### texttoimage

Rendert den gepipten Text mit den Schrifteinstellungen einer Klasse, überschrieben durch jeden angegebenen Parameter.
Das Bild wird nach `<CacheDir>/imagetext/` unter einem Namen geschrieben, der aus einem MD5 des Textes und der
Einstellungen gebildet wird; mit `UseCache` wird eine vorhandene Datei wiederverwendet.

| Parameter | Typ | Erforderlich | Standardwert |
|---|---|---|---|
| `class` | Zeichenkette, ein Abschnitt von `texttoimage.ini` | nein | `'default'` (fällt auf `[DefaultSettings]` zurück) |
| `family` | Zeichenkette, Name der Schriftdatei ohne `.ttf` | nein | aus der Klasse |
| `pointsize` | Ganzzahl | nein | aus der Klasse |
| `angle` | Ganzzahl | nein | aus der Klasse |
| `bgcolor` | Farbe (`#rrggbb` oder ein Array aus drei Zahlen) | nein | aus der Klasse |
| `textcolor` | Farbe | nein | aus der Klasse |
| `x`, `y`, `w`, `h` | ganzzahlige Anpassungen | nein | aus der Klasse |
| `usecache` | boolean oder eine Zeichenkette als Cache-Name | nein | `[ImageSettings] UseCache` |
| `storeimage` | boolean | nein | `true` |

Eingabe: der Text, über die Pipe. Gibt ein `eZImageTextLayer` zurück. Wird die Schrift nicht gefunden, gibt der
Operator nichts zurück; kann die Schrift nicht geöffnet werden, protokolliert er "Could not open font". Generierte
Referenz: [texttoimage](../../beispiele/template-operatoren/texttoimage.md).

**Er schreibt Dateien.** Das folgende Template wurde beim Vorbereiten dieser Seite einmal auf alpha gerendert und hat
das Verzeichnis `var/cache/texttoimage/imagetext/` mit der PNG-Datei angelegt (ein Cache-Verzeichnis: Es kann geleert
werden, und das Bild wird bei der nächsten Anfrage neu erzeugt):

```tpl
{'Fit & Healthy'|texttoimage( '1942' )}
```

Seine Ausgabe auf alpha:

```text

<img border="0" src="/var/cache/texttoimage/imagetext/b/d/3/bd3fdc7ef41d4f843b93d8d9703be5d4/Fit_amp_Healthy.png" alt="Fit &amp;amp; Healthy" width="450" height="52" />
```

Beachten Sie den `alt`-Text: Der Operator maskiert den Text beim Anlegen der Ebene mit `htmlspecialchars()`
(`$alternativeText`), und die `<img>`-Ausgabe maskiert ihn erneut, sodass `&` als `&amp;amp;` ankommt. Übergeben Sie
Text ohne Zeichen, die maskiert werden müssen, oder schreiben Sie das `<img>`-Tag selbst aus `imagepath`, `width` und
`height`.

## image

### image

Baut ein Bild aus Ebenen. Jeder Parameter ist entweder eine Zeichenkette, die zum Alternativtext wird, oder ein Array
aus einer Ebene und einem Hash mit Platzierungsoptionen: `halign` (`left`, `right`, `center`), `valign` (`top`,
`bottom`, `center`), `x`, `y`, `xrel`, `yrel` und `transparency`. Das Ergebnis wird nach `<CacheDir>/imageobject/`
geschrieben (mit `UseCache` wiederverwendet).

| Parameter | Typ | Erforderlich | Standardwert |
|---|---|---|---|
| Ebenen und Alternativtext | `array( layer, hash( options ) )` oder Zeichenkette, beliebig viele | ja | keiner |

Eingabe: keine. Gibt ein `eZImageObject` zurück. Generierte Referenz: [image](../../beispiele/template-operatoren/image.md).

**Er schreibt Dateien**, wie `texttoimage`. Dieses Template, ebenfalls einmal auf alpha gerendert, hat
`var/cache/texttoimage/imageobject/` angelegt:

```tpl
{image( array( 'Fit & Healthy'|texttoimage( '1942' ), hash( 'halign', 'center', 'valign', 'center' ) ), 'Fit and Healthy' )}
```

Seine Ausgabe auf alpha:

```text

<img border="0" src="/var/cache/texttoimage/imageobject/5/e/1/5e1e308e65728a71d3bcfdcfd00dbd81/Fit_and_Healthy.png" alt="Fit and Healthy" width="450" height="52" />
```

## imagefile

### imagefile

Liest eine Bilddatei und gibt sie als Ebene zurück: `eZImageLayer::createForFile()` liest die Größe mit
`getimagesize()` und lädt das Bild in den Speicher. Es wird nichts geschrieben. Der Alternativtext ist der Dateiname
ohne Endung.

| Parameter | Typ | Erforderlich | Standardwert |
|---|---|---|---|
| `filename` | Zeichenkette, ein Pfad relativ zur Installation | ja | keiner |
| `options` | Array | nein | `array()` |

Eingabe: keine. Gibt ein `eZImageLayer` zurück, das als Ebene von `image` verwendet werden kann. Generierte Referenz:
[imagefile](../../beispiele/template-operatoren/imagefile.md).

```tpl
{def $img = imagefile( 'design/standard/images/add.png' )}
filepath: {$img.filepath}
filename: {$img.filename}
size: {$img.width} x {$img.height} (has_size: {$img.has_size|choose( 'no', 'yes' )})
alternative_text: {$img.alternative_text}
```

Ausgabe auf alpha:

```text
filepath: design/standard/images
filename: add.png
size: 16 x 16 (has_size: yes)
alternative_text: add
```

Das Beispiel für `imagefile` lieferte kompiliert auf alpha dieselbe Ausgabe. Die beiden schreibenden Beispiele wurden
kein zweites Mal ausgeführt, weder interpretiert noch kompiliert.

## Verwandte Seiten

- [Icon-Operatoren](icon-operatoren.md): Icons für Klassen, MIME-Typen und Flaggen
- [Beispiele zum Bild-Datentyp](../../beispiele/datentypen/ezimage.md): Bilder von Inhaltsobjekten
- [Alle Template-Operatoren (generiert)](../../beispiele/template-operatoren/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
