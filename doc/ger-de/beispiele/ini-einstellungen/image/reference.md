---
title: image.ini [reference]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [reference] in image.ini: Filters"
keywords: Referenz, Einstellung, image.ini, reference, INI, setting, Filters
type: generated
generated: exp:help:examples
---
# image.ini [reference]

Der Block `[reference]` von `settings/image.ini`, mit 1 Einstellung(en).

> AliasList\[\]=rssitem  
> AliasList\[\]=gray  
> AliasList\[\]=zoom  
> Example of an image alias, the group is called the same as the alias name  
> \[myalias\]  
>
> Tells the system to use the original alias as reference image. The reference  
> image is the base of conversion and filtering. original is the default and  
> can be omitted  
> Reference=reference  
>
> The MIME type of the output image. Can be specified if you want a specific  
> format for the alias, if not skip it and let the image system decide.  
> A useful application for this is screenshots, normally you don't want  
> to use image/jpeg for screenshot but rather image/png or image/gif.  
> MIMEType=image/png  
>
> Override quality by MIME type for this alias only  
> This follows the pattern from the global \[MIMETypeSettings\] settings  
> Quality\[\]  
> Quality\[\]=image/jpeg;80  
>
> A list of filters to run, each filter must be supported by the active image converters  
> Filters\[\]=geometry/scale=300;300  
> reference, small, medium and large for backwards compatability  
> You can for instance remove the reference if don't want the extra disk usage  
> By defining the 'original' alias and setting some filters  
> or changing the MIME type you can even force a conversion of  
> the originally uploaded image.  
> This is useful to save space or make sure the image is in a specific format  
> \[original\]  
> Filters\[\]  
> Filters\[\]=colorspace/gray  
> \[original\]  
> Filters\[\]  
> Filters\[\]=filter/swirl=210  
> # MIMEType=image/png  

Bearbeiten Sie `settings/image.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/image.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/image.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/image.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Filters

Standard: eine Liste mit 1: `geometry/scaledownonly=600;600`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[reference]
# ein leeres "Filters[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Filters[]
Filters[]=geometry/scaledownonly=600;600
*/ ?>
```

## Siehe auch

- [Alle Blöcke von image.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/image.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
