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

> Die Speicherdateien (doc/eng-us/features/6.0/content-checks.md, "Storage files"): Dateien unter  
> var/<site>/storage, die kein Inhalt verwendet, und die Dateisystemprobleme der verwendeten.  
> Die Verzeichnisse unter dem Speicher, die durchsucht werden  

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

> Pfade unter dem Speicher, die nie aufgeführt werden (Präfixe): Caches, die anderer Code dort hält  

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

> In einem Schritt des Durchlaufs gelesene Dateien (50 bis 50000) und wie lange ein Schritt im Browser dauern darf,  
> bevor er am Ende eines Stapels anhält (Sekunden, 1 bis 25); die Kommandozeile führt den ganzen Durchlauf aus  

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

> Verwaiste Dateien und Probleme, die im Ergebnis eines Durchlaufs behalten werden (100 bis 100000); die Summen zählen weiter  

Standard: `5000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[StorageSettings]
MaxListed=5000
*/ ?>
```

## MinAge

> Eine Datei, die jünger ist (Sekunden), ist nie verwaist: Ein Upload schreibt die Datei vor  
> ihrer Zeile in der Datenbank  

Standard: `86400`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[StorageSettings]
MinAge=86400
*/ ?>
```

## ExpectedOwner

> Der Eigentümer, den jede verwendete Datei haben sollte: ein Benutzername oder eine ID; leer: der Eigentümer des Speicher-  
> verzeichnisses. Der Modus, den jede verwendete Datei mindestens haben sollte (oktal); leer: site.ini  
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

> Wohin verwaiste Dateien verschoben werden: var/<site>/<QuarantineDirectory>/<date>/<path below the storage>  

Standard: `storage-quarantine`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[StorageSettings]
QuarantineDirectory=storage-quarantine
*/ ?>
```

## MaxActionBatch

> Höchstens so viele Dateien werden aus einer Bestätigung übernommen (1 bis 1000)  

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
