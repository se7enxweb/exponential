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

> According to the RSS 1.0 / RSS 2.0 specification:  
> maximum value for width is 144, default value is 88,  
> maximum value for height is 400, default value is 31.  
> Optional image alias used in rss feed items if you specify image  
> attribute as source for description tag, 'rss' is used instead if disabled  
>
> \[rssitem\]  
> Reference=reference  
> Filters\[\]=geometry/scaledownonly=128;128  
> An example alias which converts the image to grayscale  
>
> \[gray\]  
> Reference=large  
> Filters\[\]  
> Filters\[\]=colorspace/gray  
> An example alias which crops the image to specific size  
> and adds a border.  
>
> \[zoom\]  
> Reference=reference  
> Filters\[\]  
> Filters\[\]=geometry/crop=200;200;155;155  
> Filters\[\]=border/color=0;0;0  
> Filters\[\]=border=4;4  
>
> -------- Image MIME type settings --------  

Bearbeiten Sie `settings/image.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/image.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/image.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/image.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## OverrideList

> A list of settings which override certain MIME types  
> The overrides can define allowed and disallowed filters  
> as well as extra filters.l  

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

> A list of mimetypes and their image quality value  
> The value varies from mime type to mime type.  
> Set JPEG quality from 0 (worst quality, smallest file) to 100 (best quality, biggest file)  

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

> The global conversion rules  
> Most will be converted to jpeg except gif and xpms  

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
