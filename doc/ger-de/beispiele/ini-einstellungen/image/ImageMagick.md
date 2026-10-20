---
title: image.ini [ImageMagick]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ImageMagick] in image.ini: Name, IsEnabled, Handler, ExecutablePath, Executable, PreParameters, PostParameters, UseTypeTag ..."
keywords: Einstellung, image.ini, ImageMagick, INI, setting, Name, IsEnabled, Handler, ExecutablePath, Executable, PreParameters, PostParameters, UseTypeTag, InputMIMEList, OutputMIMEList, QualityParameters, FrameRangeParameters, Filters, MIMETagMap
type: generated
generated: exp:help:examples
---
# image.ini [ImageMagick]

Der Block `[ImageMagick]` von `settings/image.ini`, mit 14 Einstellung(en).

> The following filters are supported by GD  
> geometry/scale  
> geometry/scalewidth  
> geometry/scaleheight  
> geometry/scaledownonly  
> geometry/scalewidthdownonly  
> geometry/scaleheightdownonly  
> geometry/scaleexact  
> geometry/scalepercent  
> geometry/crop  
> colorspace/gray  
> luminance/gray  
> luminance/sepia  
> color/monochrome  
> border  
> border/color  
> border/width  

Bearbeiten Sie `settings/image.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/image.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/image.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/image.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `ImageMagick`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[ImageMagick]
Name=ImageMagick
*/ ?>
```

## IsEnabled

> If set to true then this handler will be used,  
> The setup wizard will turn this off it ImageMagick is not found on the system  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[ImageMagick]
IsEnabled=true
*/ ?>
```

## Handler

> Which PHP handler to use for the conversion,  
> ezimageshell means to use the generic shell handler which  
> creates a command line and executes it with system().  

Standard: `eZImageShellFactory`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[ImageMagick]
Handler=eZImageShellFactory
*/ ?>
```

## ExecutablePath

> The path to the executable, can be empty for global path.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[ImageMagick]
ExecutablePath=
*/ ?>
```

## Executable

> Name of the executable  

Standard: `convert`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[ImageMagick]
Executable=convert
*/ ?>
```

## PreParameters

> Name of the executable for windows,  
> uncomment ExecutableMac for Mac specific converter and  
> ExecutableUnix for Unix/Linux specific converter  
> ExecutableWin32=convertim  
> ExecutableMac=convert  
> ExecutableUnix=convert  
> Parameters that must be run before the filenames and filters.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[ImageMagick]
PreParameters=
*/ ?>
```

## PostParameters

> Parameters that must be run after the filenames and filters.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[ImageMagick]
PostParameters=
*/ ?>
```

## UseTypeTag

> Whether the destination name should be prefixed with a TAG name (see MIMETagMap below)  
> The variable contains the separator between the TAG and the filename.  
> This is needed for ImageMagick to provide proper conversions of some formats.  

Standard: `:`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[ImageMagick]
UseTypeTag=:
*/ ?>
```

## InputMIMEList

> MIME types that the converter can get as input  

Standard: eine Liste mit 1: `*`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[ImageMagick]
# ein leeres "InputMIMEList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
InputMIMEList[]
InputMIMEList[]=*
*/ ?>
```

## OutputMIMEList

> MIME types that the converter can generate  

Standard: eine Liste mit 1: `*`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[ImageMagick]
# ein leeres "OutputMIMEList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
OutputMIMEList[]
OutputMIMEList[]=*
*/ ?>
```

## QualityParameters

> A list of mime types and their quality parameters  

Standard: eine Liste mit 1: `image/jpeg;-quality %1`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[ImageMagick]
# ein leeres "QualityParameters[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
QualityParameters[]
QualityParameters[]=image/jpeg;-quality %1
*/ ?>
```

## FrameRangeParameters

> This setting will provide the possibility to postpend options to the source file  filename.  
> This behavior is needed to address a specific layer in PSD files.  
> This array needs a semicolon seperated value which will build like this:  
> <mimetype>;<option>  
> Set frame range to handle PSD files with ImageMagick  

Standard: eine Liste mit 1: `application/x-photoshop;[0]`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[ImageMagick]
# ein leeres "FrameRangeParameters[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
FrameRangeParameters[]
FrameRangeParameters[]=application/x-photoshop;[0]
*/ ?>
```

## Filters

> A list of filter ImageMagick supports, add more if needed  
> The same filter names are used in aliases and MIME type overrides with optional parameters.  

Standard: eine Liste mit 19: `geometry/scale=-geometry %1x%2`, `geometry/scalewidth=-geometry %1`, `geometry/scaleheight=-geometry x%1`, `geometry/scaledownonly=-geometry %1x%2>`, `geometry/scalewidthdownonly=-geometry %1>`, `geometry/scaleheightdownonly=-geometry x%1>`, `geometry/scaleexact=-geometry %1x%2!`, `geometry/scalepercent=-geometry %1x%2%`, `geometry/crop=-crop %1x%2+%3+%4`, `filter/noise=-noise %1`, `filter/swirl=-swirl %1`, `colorspace/gray=-colorspace GRAY` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[ImageMagick]
# ein leeres "Filters[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Filters[]
Filters[]=geometry/scale=-geometry %1x%2
Filters[]=geometry/scalewidth=-geometry %1
Filters[]=geometry/scaleheight=-geometry x%1
Filters[]=geometry/scaledownonly=-geometry %1x%2>
Filters[]=geometry/scalewidthdownonly=-geometry %1>
Filters[]=geometry/scaleheightdownonly=-geometry x%1>
Filters[]=geometry/scaleexact=-geometry %1x%2!
Filters[]=geometry/scalepercent=-geometry %1x%2%
Filters[]=geometry/crop=-crop %1x%2+%3+%4
Filters[]=filter/noise=-noise %1
Filters[]=filter/swirl=-swirl %1
Filters[]=colorspace/gray=-colorspace GRAY
Filters[]=colorspace/transparent=-colorspace Transparent
Filters[]=colorspace=-colorspace %1
Filters[]=border=-border %1x%2
Filters[]=border/color=-bordercolor rgb(%1,%2,%3)
Filters[]=border/width=-borderwidth %1
Filters[]=flatten=-flatten
Filters[]=resize=-resize %1
*/ ?>
```

## MIMETagMap

> Maps a MIME type to a tag name which is prepended to the destination name for ImageMagick  

Standard: eine Liste mit 19: `image/x-xpixmap;XPM`, `image/png;PNG`, `image/webp;WEBP`, `image/jpeg;JPEG`, `image/bmp;BMP`, `image/gif;GIF`, `image/x-portable-bitmap;PBM`, `image/tiff;TIFF`, `image/pcx;PCX`, `image/x-pict;PICT`, `image/svg+xml;SVG`, `image/tga;TGA` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[ImageMagick]
# ein leeres "MIMETagMap[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
MIMETagMap[]
MIMETagMap[]=image/x-xpixmap;XPM
MIMETagMap[]=image/png;PNG
MIMETagMap[]=image/webp;WEBP
MIMETagMap[]=image/jpeg;JPEG
MIMETagMap[]=image/bmp;BMP
MIMETagMap[]=image/gif;GIF
MIMETagMap[]=image/x-portable-bitmap;PBM
MIMETagMap[]=image/tiff;TIFF
MIMETagMap[]=image/pcx;PCX
MIMETagMap[]=image/x-pict;PICT
MIMETagMap[]=image/svg+xml;SVG
MIMETagMap[]=image/tga;TGA
MIMETagMap[]=image/vnd.wap.wbmp;WBMP
MIMETagMap[]=image/x-xbitmap;XBM
MIMETagMap[]=image/x-xcf-gimp;XCF
MIMETagMap[]=application/x-photoshop;PSD
MIMETagMap[]=application/pdf;PDF
MIMETagMap[]=application/postscript;PS
MIMETagMap[]=text/plain;TEXT
*/ ?>
```

## Siehe auch

- [Alle Blöcke von image.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/image.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
