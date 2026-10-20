---
title: contentcheck.ini [StorageSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [StorageSettings] in contentcheck.ini: ScanDirectoryList, IgnorePathList, ScanBatchSize, StepSeconds, MaxListed, MinAge, ExpectedOwner, FileMode ..."
keywords: Einstellung, contentcheck.ini, StorageSettings, INI, setting, ScanDirectoryList, IgnorePathList, ScanBatchSize, StepSeconds, MaxListed, MinAge, ExpectedOwner, FileMode, QuarantineDirectory, MaxActionBatch
type: generated
generated: exp:help:examples
---
# contentcheck.ini [StorageSettings]

Der Block `[StorageSettings]` von `settings/contentcheck.ini`, mit 10 Einstellung(en).

Bearbeiten Sie `settings/contentcheck.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/contentcheck.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/contentcheck.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/contentcheck.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ScanDirectoryList

> The storage files (doc/eng-us/features/6.0/content-checks.md, "Storage files"): files below  
> var/<site>/storage that no content uses, and the file-system problems of those it uses.  
> The directories below the storage that are scanned  

Standard: eine Liste mit 3: `images`, `images-versioned`, `original`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[StorageSettings]
# ein leeres "ScanDirectoryList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ScanDirectoryList[]
ScanDirectoryList[]=images
ScanDirectoryList[]=images-versioned
ScanDirectoryList[]=original
*/ ?>
```

## IgnorePathList

> Paths below the storage that are never listed (prefixes): caches other code keeps there  

Standard: eine Liste mit 1: `images/_video-thumbnails`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[StorageSettings]
# ein leeres "IgnorePathList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
IgnorePathList[]
IgnorePathList[]=images/_video-thumbnails
*/ ?>
```

## ScanBatchSize

> Files read in one step of the scan (50 to 50000), and how long a step in the browser may  
> take before it stops at the end of a batch (seconds, 1 to 25); the CLI runs the whole scan  

Standard: `2000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[StorageSettings]
ScanBatchSize=2000
*/ ?>
```

## StepSeconds

Standard: `4`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[StorageSettings]
StepSeconds=4
*/ ?>
```

## MaxListed

> Orphans and problems kept in the result of a scan (100 to 100000); the totals count on  

Standard: `5000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[StorageSettings]
MaxListed=5000
*/ ?>
```

## MinAge

> A file younger than this (seconds) is never an orphan: an upload writes the file before  
> its row in the database  

Standard: `86400`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[StorageSettings]
MinAge=86400
*/ ?>
```

## ExpectedOwner

> The owner every used file should have: a user name or id; empty: the owner of the storage  
> directory. The mode every used file should have at least (octal); empty: site.ini  
> \[FileSettings\] StorageFilePermissions  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[StorageSettings]
ExpectedOwner=
*/ ?>
```

## FileMode

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[StorageSettings]
FileMode=
*/ ?>
```

## QuarantineDirectory

> Where orphans are moved: var/<site>/<QuarantineDirectory>/<date>/<path below the storage>  

Standard: `storage-quarantine`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[StorageSettings]
QuarantineDirectory=storage-quarantine
*/ ?>
```

## MaxActionBatch

> At most this many files are taken from one confirmation (1 to 1000)  

Standard: `200`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[StorageSettings]
MaxActionBatch=200
*/ ?>
```

## Siehe auch

- [Alle Blöcke von contentcheck.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/contentcheck.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
