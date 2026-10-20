---
title: image.ini [AnimatedGIF]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AnimatedGIF] in image.ini: MIMEType, Match, OverrideMIMEType, DisallowedFilters, ExtraFilters"
keywords: Einstellung, image.ini, AnimatedGIF, INI, setting, MIMEType, Match, OverrideMIMEType, DisallowedFilters, ExtraFilters
type: generated
generated: exp:help:examples
---
# image.ini [AnimatedGIF]

Der Block `[AnimatedGIF]` von `settings/image.ini`, mit 5 Einstellung(en).

> Fallback if jpeg is disabled as output.  
> ConversionRules\[\]=image/jpeg;image/png  
> Override for animated GIFs, this is needed to properly  
> handle image/gif files with multiple image frames.  
> It will turn off any geometry filters to leave the images untouched.  

Bearbeiten Sie `settings/image.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/image.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/image.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/image.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## MIMEType

> The MIME type which this override works on  

Standard: `image/gif`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AnimatedGIF]
MIMEType=image/gif
*/ ?>
```

## Match

> A list of matches that needs to be satisified for this override to be used  

Standard: ein Hash mit 1: `is_animated=1`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AnimatedGIF]
# ein leeres "Match[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Match[]
Match[is_animated]=1
*/ ?>
```

## OverrideMIMEType

> Alternative MIME type for the image, currently unused.  

Standard: `animation/gif`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AnimatedGIF]
OverrideMIMEType=animation/gif
*/ ?>
```

## DisallowedFilters

> A list of filters which are not allowed to be run on this image,  
> wildcards can also be used.  

Standard: eine Liste mit 1: `*`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AnimatedGIF]
# ein leeres "DisallowedFilters[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
DisallowedFilters[]
DisallowedFilters[]=*
*/ ?>
```

## ExtraFilters

> A list of filters which are allowed to be run on this image,  
> wildcards can also be used. If this is supplied only filters that  
> are in this list will be allowed  
> Note that resizing of animated gifs is not supported using GD2, at least not until PHP 5.3.  
> See: http://www.libgd.org/FAQ\_PHP  
> AllowedFilters\[\]  
> A list of extra filters to add for this image.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AnimatedGIF]
# ein leeres "ExtraFilters[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ExtraFilters[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von image.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/image.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
