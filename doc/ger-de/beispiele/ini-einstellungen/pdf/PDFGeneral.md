---
title: pdf.ini [PDFGeneral]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [PDFGeneral] in pdf.ini: Format, Orientation, TopMargin, BottomMargin, LeftMargin, RightMargin, OutputCharset, Transliterate ..."
keywords: Einstellung, pdf.ini, PDFGeneral, INI, setting, Format, Orientation, TopMargin, BottomMargin, LeftMargin, RightMargin, OutputCharset, Transliterate, LinkUnderline, MaxImageWidth, MaxImageHeight, ImageScaling, Font, FontEncoding
type: generated
generated: exp:help:examples
---
# pdf.ini [PDFGeneral]

Der Block `[PDFGeneral]` von `settings/pdf.ini`, mit 14 Einstellung(en).

Bearbeiten Sie `settings/pdf.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/pdf.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/pdf.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/pdf.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Format

Standard: `A4`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/pdf.ini.append.php
[PDFGeneral]
Format=A4
*/ ?>
```

## Orientation

Standard: `portrait`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/pdf.ini.append.php
[PDFGeneral]
Orientation=portrait
*/ ?>
```

## TopMargin

Standard: `80`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/pdf.ini.append.php
[PDFGeneral]
TopMargin=80
*/ ?>
```

## BottomMargin

Standard: `100`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/pdf.ini.append.php
[PDFGeneral]
BottomMargin=100
*/ ?>
```

## LeftMargin

Standard: `80`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/pdf.ini.append.php
[PDFGeneral]
LeftMargin=80
*/ ?>
```

## RightMargin

Standard: `80`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/pdf.ini.append.php
[PDFGeneral]
RightMargin=80
*/ ?>
```

## OutputCharset

> Der Zeichensatz, in dem der PDF-Text geschrieben wird.  
>
> Ignoriert, wenn Font unten eine .ttf-Datei nennt, weil eine zusammengesetzte Schrift gar keine  
> Umwandlung braucht, siehe Font.  
>
> Sonst muss es ein Ein-Byte-Zeichensatz sein. Die mitgelieferten Schriften haben eine Kodierung mit 256  
> Einträgen, ein Mehrbyte-Zeichensatz würde also Byte für Byte gezeichnet und  
> als Zeichensalat gelesen: "cafÃ©" statt "café". utf-8 hier zu setzen, während eine  
> dieser Schriften verwendet wird, wird abgelehnt, mit einem Fehler im Debug-Log, und  
> stattdessen windows-1252 verwendet. Zeigen Sie mit Font auf eine .ttf, um echtes utf-8 zu erhalten.  
>
> Ausgeliefert wurde dies als iso-8859-1, was die falsche Wahl war: Die Schriften werden  
> mit /Encoding /WinAnsiEncoding geschrieben, und WinAnsi und iso-8859-1 unterscheiden sich bei  
> 0x80-0x9F. Dieser Bereich enthält den Geviertstrich, das Eurozeichen, den Aufzählungspunkt und die  
> typografischen Anführungszeichen; die Schrift konnte sie also zeichnen, die Umwandlung warf sie aber weg  
> und schrieb ein Fragezeichen. windows-1252 ist das, was WinAnsiEncoding ist, es behält sie also. Ändern Sie dies nur zusammen mit FontEncoding unten.  

Standard: `windows-1252`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/pdf.ini.append.php
[PDFGeneral]
OutputCharset=windows-1252
*/ ?>
```

## Transliterate

> Ignoriert, wenn Font eine .ttf-Datei nennt, wo nichts vereinfacht werden muss.  
>
> Ein Zeichen, das der Zeichensatz oben nicht aufnehmen kann, zum nächstliegenden ASCII-Zeichen vereinfachen  
> statt zu einem Fragezeichen, sodass ein polnischer oder tschechischer Name lesbar bleibt:  
> Lódz statt ód. Zeichen ohne jede Ähnlichkeit mit ASCII, zum Beispiel Japanisch,  
> können diese Schriften weiterhin nicht zeichnen.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/pdf.ini.append.php
[PDFGeneral]
Transliterate=enabled
*/ ?>
```

## LinkUnderline

> Unter einem Link zusätzlich zur blauen Farbe eine Linie zeichnen.  
>
> Die Linie wurde früher viel zu kurz gezeichnet, weil das Ende eines Links vom  
> Zeilenanfang statt vom Anfang des Links aus gemessen wurde.  
> Das ist behoben, daher ist dies an. Setzen Sie es auf disabled für Links, die farbig  
> und anklickbar, aber nicht unterstrichen sind.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/pdf.ini.append.php
[PDFGeneral]
LinkUnderline=enabled
*/ ?>
```

## MaxImageWidth

> Wie groß ein Bild gezeichnet werden darf, in Punkt, 72 pro Zoll.  
>
> Die Templates übergeben Bilder in ihrer Pixelgröße, und diese Größe wird dann  
> als Punkt verwendet, ein Foto mit 1600 Pixeln Breite verlangte also 1600 Punkt auf einer Seite  
> mit 595 Punkt Breite und lief in jede Richtung über das Papier. Bilder werden jetzt  
> unter Beibehaltung ihrer Proportionen passend verkleinert. Nur verkleinert: Was schon  
> klein genug ist, bleibt, wie es ist.  
>
> Leer gelassen fällt jeder Wert auf den Platz zurück, den die Seite hat (das Papier abzüglich seiner  
> Ränder), was die sinnvolle Obergrenze ist. Setzen Sie sie, um Bilder deutlich innerhalb  
> des Textes zu halten, zum Beispiel 360 für eine halbe Seitenbreite auf A4.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/pdf.ini.append.php
[PDFGeneral]
MaxImageWidth=
*/ ?>
```

## MaxImageHeight

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/pdf.ini.append.php
[PDFGeneral]
MaxImageHeight=
*/ ?>
```

## ImageScaling

> Auf disabled setzen, um Bilder in der Größe zu zeichnen, in der sie übergeben werden,  
> wie früher, samt Überlauf über die Seite.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/pdf.ini.append.php
[PDFGeneral]
ImageScaling=enabled
*/ ?>
```

## Font

> Die verwendete Schrift, sofern ein Template keine andere wählt.  
>
> Ein Name ohne Endung ist eine der Schriften, die neben der Bibliothek mitgeliefert werden:  
> Helvetica, Times-Roman, Courier. Das sind einfache Schriften: 256 Plätze, ein Byte  
> pro Zeichen, der Text muss also zuerst in OutputCharset gepresst werden, und  
> was nicht passt, wird transliteriert oder geht verloren.  
>
> Geben Sie stattdessen den vollen Pfad einer .ttf-Datei an, wird die Schrift als  
> zusammengesetzte Schrift eingebettet, die ihre Glyphen über Nummern statt über eine  
> Tabelle mit 256 Einträgen anspricht. Dann muss nichts gepresst werden: utf-8 geht  
> unverändert durch, OutputCharset und Transliterate gelten nicht mehr, und jedes  
> Zeichen, das die Schrift tatsächlich hat, wird gezeichnet. Der Text bleibt markierbar und  
> durchsuchbar, weil die Rückzuordnung zu Unicode mitgeschrieben wird.  
>
> Was Sie erhalten, entscheidet dann die Schrift und nicht diese Software.  
> DejaVuSans deckt Lateinisch, erweitertes Lateinisch, Griechisch, Kyrillisch, Hebräisch, Armenisch  
> und Georgisch ab. Für Chinesisch, Japanisch oder Koreanisch zeigen Sie auf eine Schrift, die sie  
> hat: DroidSansFallback oder eine der Noto-CJK-Schriften.  
>
> Font=/usr/share/fonts/dejavu-sans-fonts/DejaVuSans.ttf  

Standard: `lib/ezpdf/classes/fonts/Helvetica`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/pdf.ini.append.php
[PDFGeneral]
Font=lib/ezpdf/classes/fonts/Helvetica
*/ ?>
```

## FontEncoding

> Welche Glyphe jeder der 256 Plätze der Schrift enthält. Muss zu OutputCharset passen,  
> sonst landen Akzente auf den falschen Zeichen. WinAnsiEncoding ist windows-1252,  
> MacRomanEncoding ist mac-roman, und StandardEncoding ist die eigene Belegung der Schriften,  
> die keines von beiden ist.  

Standard: `WinAnsiEncoding`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/pdf.ini.append.php
[PDFGeneral]
FontEncoding=WinAnsiEncoding
*/ ?>
```

## Siehe auch

- [Alle Blöcke von pdf.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/pdf.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
