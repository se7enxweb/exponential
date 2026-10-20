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

> Which subdirectory of var/cache to use for temporary image files.  

Standard: `imagetmp`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[FileSettings]
TemporaryDir=imagetmp
*/ ?>
```

## PublishedImages

> Wich subdirectory of var/storage to use for publish images  

Standard: `images`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[FileSettings]
PublishedImages=images
*/ ?>
```

## VersionedImages

> Wich subdirectory of var/storage to use for non-published (drafts, archived) images  

Standard: `images-versioned`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[FileSettings]
VersionedImages=images-versioned
*/ ?>
```

## DirPermissions

> Permission for new image directories, setting it to 0777 means that anyone  
> can read and write the files and can be considered a security risk.  
> It's preferred to use 0770 and make sure the web server has correct  
> user/group access.  

Standard: `0777`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[FileSettings]
DirPermissions=0777
*/ ?>
```

## ImagePermissions

> Permission for new images, setting it to 0666 means that anyone  
> can read and write the files and can be considered a security risk.  
> It's preferred to use 0660 and make sure the web server has correct  
> user/group access.  

Standard: `0666`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[FileSettings]
ImagePermissions=0666
*/ ?>
```

## Siehe auch

- [Alle Blöcke von image.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/image.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
