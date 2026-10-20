---
title: package.ini [UploadSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [UploadSettings] in package.ini: MaxArchiveSize, MaxUnpackedSize, MaxEntries, AllowedSuffixes"
keywords: Einstellung, package.ini, UploadSettings, INI, setting, MaxArchiveSize, MaxUnpackedSize, MaxEntries, AllowedSuffixes
type: generated
generated: exp:help:examples
---
# package.ini [UploadSettings]

Der Block `[UploadSettings]` von `settings/package.ini`, mit 4 Einstellung(en).

> The limits of a package archive uploaded on package/upload, the setup wizard or ezpm import  
> (eZPackageUploadInspector). An archive is also refused, whatever these say, when an entry points outside the  
> package ("..", an absolute path), is a link or a device, or its package.xml is not a well formed definition.  
> Guide: doc/eng-us/guides/packages.md  

Bearbeiten Sie `settings/package.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/package.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/package.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/package.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## MaxArchiveSize

> The largest archive accepted, in bytes (256 MiB). PHP's upload\_max\_filesize and post\_max\_size apply first.  

Standard: `268435456`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[UploadSettings]
MaxArchiveSize=268435456
*/ ?>
```

## MaxUnpackedSize

> The most its entries may unpack to, in bytes (2 GiB)  

Standard: `2147483648`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[UploadSettings]
MaxUnpackedSize=2147483648
*/ ?>
```

## MaxEntries

> The most entries (files and directories) an archive may have  

Standard: `50000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[UploadSettings]
MaxEntries=50000
*/ ?>
```

## AllowedSuffixes

> The endings an uploaded file's name may have  

Standard: eine Liste mit 3: `ezpkg`, `tar.gz`, `tgz`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[UploadSettings]
# ein leeres "AllowedSuffixes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AllowedSuffixes[]
AllowedSuffixes[]=ezpkg
AllowedSuffixes[]=tar.gz
AllowedSuffixes[]=tgz
*/ ?>
```

## Siehe auch

- [Alle Blöcke von package.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/package.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
