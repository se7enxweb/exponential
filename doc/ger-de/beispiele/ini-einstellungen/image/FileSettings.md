---
title: image.ini [FileSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [FileSettings] in image.ini: TemporaryDir, PublishedImages, VersionedImages, DirPermissions, ImagePermissions"
keywords: Einstellung, image.ini, FileSettings, INI, setting, TemporaryDir, PublishedImages, VersionedImages, DirPermissions, ImagePermissions
type: generated
generated: exp:help:examples
---
# image.ini [FileSettings]

Der Block `[FileSettings]` von `settings/image.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/image.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/image.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/image.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/image.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## TemporaryDir

> Welches Unterverzeichnis von var/cache für temporäre Bilddateien verwendet wird.  

Standard: `imagetmp`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[FileSettings]
TemporaryDir=imagetmp
*/ ?>
```

## PublishedImages

> Welches Unterverzeichnis von var/storage für veröffentlichte Bilder verwendet wird  

Standard: `images`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[FileSettings]
PublishedImages=images
*/ ?>
```

## VersionedImages

> Welches Unterverzeichnis von var/storage für nicht veröffentlichte Bilder (Entwürfe, archivierte) verwendet wird  

Standard: `images-versioned`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[FileSettings]
VersionedImages=images-versioned
*/ ?>
```

## DirPermissions

> Berechtigung für neue Bildverzeichnisse; 0777 bedeutet, dass jeder  
> die Dateien lesen und schreiben kann, was als Sicherheitsrisiko gelten kann.  
> Besser ist 0770, wobei Sie sicherstellen, dass der Webserver den richtigen  
> Benutzer-/Gruppenzugriff hat.  

Standard: `0777`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[FileSettings]
DirPermissions=0777
*/ ?>
```

## ImagePermissions

> Berechtigung für neue Bilder und ihre Varianten. Leer: dieselbe wie für  
> jede andere gespeicherte Datei, site.ini \[FileSettings\] StorageFilePermissions. Ein Wert  
> hier gilt nur für Bilder; 0666 bedeutet, dass jeder die Dateien lesen und  
> schreiben kann, was als Sicherheitsrisiko gelten kann.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[FileSettings]
ImagePermissions=
*/ ?>
```

## Siehe auch

- [Alle Blöcke von image.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/image.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
