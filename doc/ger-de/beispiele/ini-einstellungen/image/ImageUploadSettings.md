---
title: image.ini [ImageUploadSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ImageUploadSettings] in image.ini: ValidateMIMEType, AllowedMIMETypes, SanitizeSVG"
keywords: Einstellung, image.ini, ImageUploadSettings, INI, setting, ValidateMIMEType, AllowedMIMETypes, SanitizeSVG
type: generated
generated: exp:help:examples
---
# image.ini [ImageUploadSettings]

Der Block `[ImageUploadSettings]` von `settings/image.ini`, mit 3 Einstellung(en).

> Which uploaded files are accepted as images: the image datatype (edit form,  
> upload module, rich text editor upload, AJAX uploader, WebDAV, imports) and  
> the package thumbnails. The type is read from the file's content with PHP's  
> fileinfo extension, not from its name, and must be on AllowedMIMETypes\[\].  
> A refused file is not stored. Without fileinfo the type comes from  
> getimagesize() (or the file name) and one warning is logged per process.  

Bearbeiten Sie `settings/image.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/image.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/image.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/image.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ValidateMIMEType

> enabled (default) or disabled. disabled only checks that the file is an  
> image by getimagesize() or its name, as before the allow-list.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[ImageUploadSettings]
ValidateMIMEType=enabled
*/ ?>
```

## AllowedMIMETypes

> The accepted types. SVG (image/svg+xml, .svg and .svgz) is accepted  
> because it is sanitized on upload (SanitizeSVG below): only the SVG  
> elements and attributes known to be safe are stored, without script, event  
> attributes, foreignObject or external references. Every alias of an SVG is  
> the sanitized file itself; it is never scaled. Serve the files with the  
> headers in doc/eng-us/bc/6.0/svg-images.md. Remove the line to refuse SVG.  

Standard: eine Liste mit 8: `image/jpeg`, `image/png`, `image/gif`, `image/webp`, `image/avif`, `image/tiff`, `image/bmp`, `image/svg+xml`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[ImageUploadSettings]
# ein leeres "AllowedMIMETypes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AllowedMIMETypes[]
AllowedMIMETypes[]=image/jpeg
AllowedMIMETypes[]=image/png
AllowedMIMETypes[]=image/gif
AllowedMIMETypes[]=image/webp
AllowedMIMETypes[]=image/avif
AllowedMIMETypes[]=image/tiff
AllowedMIMETypes[]=image/bmp
AllowedMIMETypes[]=image/svg+xml
*/ ?>
```

## SanitizeSVG

> enabled (default) or disabled. enabled: an SVG image is stored as the  
> output of the SVG sanitizer (expSvgSanitizer); a file it cannot read, or in  
> which nothing safe remains, is refused. disabled: SVG is refused, whatever  
> AllowedMIMETypes\[\] says, so an SVG is never stored unsanitized.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[ImageUploadSettings]
SanitizeSVG=enabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von image.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/image.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
