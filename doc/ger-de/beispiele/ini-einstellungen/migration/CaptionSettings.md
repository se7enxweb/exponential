---
title: migration.ini [CaptionSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [CaptionSettings] in migration.ini: Before, After, ReuseUnchanged, ReuseIgnoreTableList, RestoreOnFailure, RestoreParts, CodeTreeList, BackupDirectory"
keywords: Einstellung, migration.ini, CaptionSettings, INI, setting, Before, After, ReuseUnchanged, ReuseIgnoreTableList, RestoreOnFailure, RestoreParts, CodeTreeList, BackupDirectory
type: generated
generated: exp:help:examples
---
# migration.ini [CaptionSettings]

Der Block `[CaptionSettings]` von `settings/migration.ini`, mit 8 Einstellung(en).

Bearbeiten Sie `settings/migration.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/migration.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/migration.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/migration.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Before

> Die Sicherungspunkte sind die von exp:upgrade (./console exp:upgrade:captions listet sie alle): die vollständige  
> Caption von git\_manager oder ohne git\_manager der Datenbankabzug von exp:upgrade, mit settings/override und den  
> Commits des Codes.  
>
> enabled: vor einem Lauf, der Migrationen anwendet, wird ein Sicherungspunkt angelegt (Art migration-pre). Lässt  
> sich keiner anlegen (eine Engine ohne Abzug, eine volle Platte), geht der Lauf mit einer Warnung weiter: Dann gibt  
> es keine automatische Wiederherstellung.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[CaptionSettings]
Before=enabled
*/ ?>
```

## After

> enabled: nach einem erfolgreichen Lauf wird ein Sicherungspunkt angelegt (Art migration-post, bezeichnet mit den  
> gelaufenen Migrationen): Der nächste Lauf verwendet ihn wieder, solange sich seitdem nichts geändert hat.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[CaptionSettings]
After=enabled
*/ ?>
```

## ReuseUnchanged

> enabled: der neueste Sicherungspunkt einer Migration wird statt eines neuen wiederverwendet, solange Datenbank,  
> Speicher unter var/, settings/override und der Commit der Installation so sind wie beim Anlegen. Die Prüfung ist  
> genau auf PostgreSQL (die Änderungszähler jeder Tabelle), MySQL/MariaDB (CHECKSUM TABLE jeder Tabelle) und SQLite  
> (die Zeilen jeder Tabelle); MongoDB und Oracle legen immer einen neuen an.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[CaptionSettings]
ReuseUnchanged=enabled
*/ ?>
```

## ReuseIgnoreTableList

> Die Tabellen, die die Prüfung zur Wiederverwendung auslässt: die Aufzeichnung der Läufe und Sicherungspunkte,  
> Sitzungen und der Audit-Index (jeder Sicherungspunkt schreibt ein Audit-Ereignis). Ein Name, der auf \* endet, steht  
> für jede Tabelle, die damit beginnt.  

Standard: eine Liste mit 4: `exp_migration`, `exp_migration_run`, `ezsession`, `expaudit_*`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[CaptionSettings]
# ein leeres "ReuseIgnoreTableList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ReuseIgnoreTableList[]
ReuseIgnoreTableList[]=exp_migration
ReuseIgnoreTableList[]=exp_migration_run
ReuseIgnoreTableList[]=ezsession
ReuseIgnoreTableList[]=expaudit_*
*/ ?>
```

## RestoreOnFailure

> enabled: ein gescheiterter Lauf wird sofort auf seinen Sicherungspunkt von vorher zurückgesetzt (nach einem  
> Sicherungspunkt des gescheiterten Zustands, Art migration-failure). Oracle hat keine eingebaute Wiederherstellung:  
> Der Lauf hält an und gibt die Schritte zur Wiederherstellung von Hand aus.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[CaptionSettings]
RestoreOnFailure=enabled
*/ ?>
```

## RestoreParts

> Die Teile eines Sicherungspunkts, auf die ein gescheiterter Lauf zurückgesetzt wird (exp:upgrade:rollback --parts): database, storage, settings.  

Standard: eine Liste mit 3: `database`, `storage`, `settings`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[CaptionSettings]
# ein leeres "RestoreParts[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
RestoreParts[]
RestoreParts[]=database
RestoreParts[]=storage
RestoreParts[]=settings
*/ ?>
```

## CodeTreeList

> Die Code-Bäume der Installation, die ein Lauf auf nicht committete Änderungen prüft: Ein Lauf mit --apply wird  
> abgelehnt, solange einer welche hat, es sei denn, er bekommt --include-uncommitted (die Änderungen kommen dann als  
> Patch und als Kopie der neuen Dateien in den Sicherungspunkt).  

Standard: eine Liste mit 3: `settings`, `extension`, `design`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[CaptionSettings]
# ein leeres "CodeTreeList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
CodeTreeList[]
CodeTreeList[]=settings
CodeTreeList[]=extension
CodeTreeList[]=design
*/ ?>
```

## BackupDirectory

> Der Ordner der ohne git\_manager angelegten Sicherungspunkte. Leer: var/backup/upgrade, wie bei exp:upgrade.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[CaptionSettings]
BackupDirectory=
*/ ?>
```

## Siehe auch

- [Alle Blöcke von migration.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
