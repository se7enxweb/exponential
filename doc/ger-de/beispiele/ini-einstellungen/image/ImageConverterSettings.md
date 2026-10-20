---
title: image.ini [ImageConverterSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ImageConverterSettings] in image.ini: ImageConverters, LockTimeout"
keywords: Einstellung, image.ini, ImageConverterSettings, INI, setting, ImageConverters, LockTimeout
type: generated
generated: exp:help:examples
---
# image.ini [ImageConverterSettings]

Der Block `[ImageConverterSettings]` von `settings/image.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/image.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/image.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/image.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/image.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ImageConverters

> List of converters to use, each entry is the name of the converter  
> which is defined in a ini block.  
> You can use ImageMagick and GD handlers by default.  
> The order of the convertes are important since the first  
> applicable converter will be used. The last handler is  
> usually a fallback converter which can take care of most formats.  
>
> GD comes first: it is the image extension built into PHP, so it  
> converts in process, without starting an external program for  
> every image, and needs nothing installed beside PHP. It reads and  
> writes JPEG, PNG, GIF and WBMP and supports every geometry filter  
> the default aliases use.  
> ImageMagick stays in the list as the fallback. It is used for the  
> formats GD cannot read (for example PSD, TIFF, PDF or WebP) and for  
> the filters only it supports (for example flatten, resize,  
> filter/swirl or filter/noise), and it takes over completely when the  
> GD extension is missing or \[GD\] IsEnabled=false.  
> To prefer ImageMagick again, override the list in  
> settings/override/image.ini.append.php:  
>   ImageConverters\[\]  
>   ImageConverters\[\]=ImageMagick  
>   ImageConverters\[\]=GD  
> The list can also contain converters which cannot work  
> on the installed server, the converter should check this  
> and tell the image system to not use it.  
>
> For any other converter make sure you have a INI section  
> with the same name as your converter's  
>
> For example :  
> ImageConverters\[\]=MyCustomConverter  
> \[MyCustomConverter\]  
> with the same INI variables as GD or ImageMagick INI sections  

Standard: eine Liste mit 2: `GD`, `ImageMagick`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[ImageConverterSettings]
# ein leeres "ImageConverters[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ImageConverters[]
ImageConverters[]=GD
ImageConverters[]=ImageMagick
*/ ?>
```

## LockTimeout

> Waiting time for processes in queue to get an exclusive writing lock  
> for writing a specific image alias.  
>
> If a process spends longer than this time waiting, the process holding the lock  
> will be ended, and the waiting process will continue, generating the image alias.  

Standard: `60`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[ImageConverterSettings]
LockTimeout=60
*/ ?>
```

## Siehe auch

- [Alle Blöcke von image.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/image.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
