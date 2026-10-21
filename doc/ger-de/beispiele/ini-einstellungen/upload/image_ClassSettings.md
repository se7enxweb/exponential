---
title: upload.ini [image_ClassSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [image_ClassSettings] in upload.ini: FileAttribute, NameAttribute, NamePattern"
keywords: Einstellung, upload.ini, image_ClassSettings, INI, setting, FileAttribute, NameAttribute, NamePattern
type: generated
generated: exp:help:examples
---
# upload.ini [image_ClassSettings]

Der Block `[image_ClassSettings]` von `settings/upload.ini`, mit 3 Einstellung(en).

> Besteht aus dem Namen des Klassenbezeichners mit angehängtem \_ClassSettings  
> Dieses Beispiel ist für den Bezeichner 'image'.  
> \[image\_ClassSettings\]  
>
> Der Bezeichner des Klassenattributs, das die Dateidaten erhält  
> FileAttribute=image  
>
> Der Bezeichner des Klassenattributs, das den Namen erhält;  
> der Name wird mit NamePattern berechnet  
> NameAttribute=name  
>
> Ein Muster, das den Namen des Objekts festlegt; es  
> enthält Text und Tags in < und >.  
> Verfügbare Tags sind:  
> - original\_filename:        der Dateiname der hochgeladenen Datei, wie er auf dem lokalen Rechner war  
> - original\_filename\_base:   wie original\_filename, aber nur der Stamm des Namens (d. h. ohne Endung)  
> - original\_filename\_suffix: wie original\_filename, aber nur die Endung  
> - mime\_type:                der erkannte MIME-Typ der Datei  
> NamePattern=<original\_filename\_base>  

Bearbeiten Sie `settings/upload.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/upload.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/upload.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/upload.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## FileAttribute

Standard: `image`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/upload.ini.append.php
[image_ClassSettings]
FileAttribute=image
*/ ?>
```

## NameAttribute

Standard: `name`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/upload.ini.append.php
[image_ClassSettings]
NameAttribute=name
*/ ?>
```

## NamePattern

Standard: `<original_filename_base>`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/upload.ini.append.php
[image_ClassSettings]
NamePattern=<original_filename_base>
*/ ?>
```

## Siehe auch

- [Alle Blöcke von upload.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/upload.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
