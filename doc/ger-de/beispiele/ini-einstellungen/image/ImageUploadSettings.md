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

> Welche hochgeladenen Dateien als Bilder akzeptiert werden: der Datentyp Bild (Bearbeitungsformular,  
> Modul upload, Upload im Rich-Text-Editor, AJAX-Uploader, WebDAV, Importe) und  
> die Vorschaubilder der Pakete. Der Typ wird mit der PHP-Erweiterung fileinfo aus dem Inhalt der Datei gelesen,  
> nicht aus ihrem Namen, und muss in AllowedMIMETypes\[\] stehen.  
> Eine abgelehnte Datei wird nicht gespeichert. Ohne fileinfo kommt der Typ aus  
> getimagesize() (oder dem Dateinamen), und pro Prozess wird eine Warnung protokolliert.  

Bearbeiten Sie `settings/image.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/image.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/image.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/image.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ValidateMIMEType

> enabled (Standard) oder disabled. disabled prüft nur, ob die Datei ein  
> Bild ist, per getimagesize() oder ihren Namen, wie vor der Positivliste.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[ImageUploadSettings]
ValidateMIMEType=enabled
*/ ?>
```

## AllowedMIMETypes

> Die akzeptierten Typen. SVG (image/svg+xml, .svg und .svgz) wird akzeptiert,  
> weil es beim Hochladen bereinigt wird (SanitizeSVG unten): Nur die SVG-  
> Elemente und -Attribute, die als sicher bekannt sind, werden gespeichert, ohne Skripte, Event-  
> Attribute, foreignObject oder externe Verweise. Jeder Alias eines SVG ist  
> die bereinigte Datei selbst; sie wird nie skaliert. Liefern Sie die Dateien mit den  
> Headern aus doc/eng-us/bc/6.0/svg-images.md aus. Entfernen Sie die Zeile, um SVG abzulehnen.  

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

> enabled (Standard) oder disabled. enabled: Ein SVG-Bild wird als die  
> Ausgabe des SVG-Bereinigers (expSvgSanitizer) gespeichert; eine Datei, die er nicht lesen kann, oder in  
> der nichts Sicheres übrig bleibt, wird abgelehnt. disabled: SVG wird abgelehnt, egal was  
> AllowedMIMETypes\[\] sagt, damit ein SVG nie unbereinigt gespeichert wird.  

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
