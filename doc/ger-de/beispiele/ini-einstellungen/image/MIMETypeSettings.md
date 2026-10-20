---
title: image.ini [MIMETypeSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [MIMETypeSettings] in image.ini: OverrideList, Quality, ConversionRules"
keywords: Einstellung, image.ini, MIMETypeSettings, INI, setting, OverrideList, Quality, ConversionRules
type: generated
generated: exp:help:examples
---
# image.ini [MIMETypeSettings]

Der Block `[MIMETypeSettings]` von `settings/image.ini`, mit 3 Einstellung(en).

> Gemäß der Spezifikation von RSS 1.0 / RSS 2.0:  
> Höchstwert für die Breite ist 144, Standard 88,  
> Höchstwert für die Höhe ist 400, Standard 31.  
> Optionaler Bild-Alias für Einträge im RSS-Feed, wenn Sie ein Bild-  
> attribut als Quelle des Tags description angeben; wenn deaktiviert, wird stattdessen 'rss' verwendet  
>
> \[rssitem\]  
> Reference=reference  
> Filters\[\]=geometry/scaledownonly=128;128  
> Ein Beispiel-Alias, der das Bild in Graustufen umwandelt  
>
> \[gray\]  
> Reference=large  
> Filters\[\]  
> Filters\[\]=colorspace/gray  
> Ein Beispiel-Alias, der das Bild auf eine bestimmte Größe zuschneidet  
> und einen Rahmen hinzufügt.  
>
> \[zoom\]  
> Reference=reference  
> Filters\[\]  
> Filters\[\]=geometry/crop=200;200;155;155  
> Filters\[\]=border/color=0;0;0  
> Filters\[\]=border=4;4  
>
> -------- Einstellungen der MIME-Typen von Bildern --------  

Bearbeiten Sie `settings/image.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/image.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/image.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/image.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## OverrideList

> Eine Liste von Einstellungen, die bestimmte MIME-Typen überschreiben  
> Die Overrides können erlaubte und nicht erlaubte Filter  
> sowie zusätzliche Filter festlegen.  

Standard: eine Liste mit 2: `AnimatedGIF`, `LayeredPhotoshop`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[MIMETypeSettings]
# ein leeres "OverrideList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
OverrideList[]
OverrideList[]=AnimatedGIF
OverrideList[]=LayeredPhotoshop
*/ ?>
```

## Quality

> Eine Liste von MIME-Typen und ihrem Wert für die Bildqualität  
> Der Wert ist je MIME-Typ verschieden.  
> JPEG-Qualität von 0 (schlechteste Qualität, kleinste Datei) bis 100 (beste Qualität, größte Datei) setzen  

Standard: eine Liste mit 1: `image/jpeg;75`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[MIMETypeSettings]
# ein leeres "Quality[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Quality[]
Quality[]=image/jpeg;75
*/ ?>
```

## ConversionRules

> Die globalen Umwandlungsregeln  
> Die meisten werden in jpeg umgewandelt, außer gif und xpm  

Standard: eine Liste mit 4: `image/webp;image/png`, `image/gif;image/png`, `image/x-xpixmap;image/png`, `*;image/jpeg`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[MIMETypeSettings]
# ein leeres "ConversionRules[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ConversionRules[]
ConversionRules[]=image/webp;image/png
ConversionRules[]=image/gif;image/png
ConversionRules[]=image/x-xpixmap;image/png
ConversionRules[]=*;image/jpeg
*/ ?>
```

## Siehe auch

- [Alle Blöcke von image.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/image.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
