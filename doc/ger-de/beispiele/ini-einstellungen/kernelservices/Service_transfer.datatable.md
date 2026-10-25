---
title: kernelservices.ini [Service_transfer.datatable]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Service_transfer.datatable] in kernelservices.ini: Class, Factory, Arguments, Scope, Tags, Description, Name, Direction ..."
keywords: Einstellung, kernelservices.ini, Service_transfer.datatable, INI, setting, Class, Factory, Arguments, Scope, Tags, Description, Name, Direction, Scopes, Formats, EntryURL, Permission, Modules, HelpPage, RoundTrip, Keywords, Actions, Weight
type: generated
generated: exp:help:examples
---
# kernelservices.ini [Service_transfer.datatable]

Der Block `[Service_transfer.datatable]` von `settings/kernelservices.ini`, mit 18 Einstellung(en).

Bearbeiten Sie `settings/kernelservices.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/kernelservices.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/kernelservices.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/kernelservices.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Class

Standard: `expKernelTransferTool`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.datatable]
Class=expKernelTransferTool
*/ ?>
```

## Factory

Standard: `expKernelTransferTool::fromSettings`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.datatable]
Factory=expKernelTransferTool::fromSettings
*/ ?>
```

## Arguments

Standard: eine Liste mit 1: `transfer.datatable`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.datatable]
# ein leeres "Arguments[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Arguments[]
Arguments[]=transfer.datatable
*/ ?>
```

## Scope

Standard: `worker`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.datatable]
Scope=worker
*/ ?>
```

## Tags

Standard: eine Liste mit 1: `transfer.tool`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.datatable]
# ein leeres "Tags[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Tags[]
Tags[]=transfer.tool
*/ ?>
```

## Description

Standard: `The tables of the persistent classes and the named database connections: describe, create and upgrade tables on any connection, browse their rows and see what points at content.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.datatable]
Description=The tables of the persistent classes and the named database connections: describe, create and upgrade tables on any connection, browse their rows and see what points at content.
*/ ?>
```

## Name

Standard: `Data tables`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.datatable]
Name=Data tables
*/ ?>
```

## Direction

Standard: `both`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.datatable]
Direction=both
*/ ?>
```

## Scopes

Standard: eine Liste mit 3: `data`, `class`, `subtree`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.datatable]
# ein leeres "Scopes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Scopes[]
Scopes[]=data
Scopes[]=class
Scopes[]=subtree
*/ ?>
```

## Formats

Standard: eine Liste mit 2: `SQL`, `.dba`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.datatable]
# ein leeres "Formats[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Formats[]
Formats[]=SQL
Formats[]=.dba
*/ ?>
```

## EntryURL

Standard: `datatable/dashboard`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.datatable]
EntryURL=datatable/dashboard
*/ ?>
```

## Permission

Standard: `datatable/read`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.datatable]
Permission=datatable/read
*/ ?>
```

## Modules

Standard: eine Liste mit 1: `secrets`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.datatable]
# ein leeres "Modules[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Modules[]
Modules[]=secrets
*/ ?>
```

## HelpPage

Standard: `features/6.0/data-tables.md`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.datatable]
HelpPage=features/6.0/data-tables.md
*/ ?>
```

## RoundTrip

Standard: eine Liste mit 1: `transfer.sqliimport`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.datatable]
# ein leeres "RoundTrip[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
RoundTrip[]
RoundTrip[]=transfer.sqliimport
*/ ?>
```

## Keywords

Standard: eine Liste mit 13: `custom table`, `module table`, `persistent object`, `database connection`, `external database`, `SQLite`, `MySQL`, `PostgreSQL`, `MongoDB`, `Oracle`, `SQL Server`, `eigene Tabelle` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.datatable]
# ein leeres "Keywords[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Keywords[]
Keywords[]=custom table
Keywords[]=module table
Keywords[]=persistent object
Keywords[]=database connection
Keywords[]=external database
Keywords[]=SQLite
Keywords[]=MySQL
Keywords[]=PostgreSQL
Keywords[]=MongoDB
Keywords[]=Oracle
Keywords[]=SQL Server
Keywords[]=eigene Tabelle
Keywords[]=Datenbankverbindung
*/ ?>
```

## Actions

Standard: eine Liste mit 2: `setup|import|Create or upgrade a table on a database connection…|datatable/schema|datatable/manage`, `setup|export|Browse the tables of the persistent classes…|datatable/dashboard`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.datatable]
# ein leeres "Actions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Actions[]
Actions[]=setup|import|Create or upgrade a table on a database connection…|datatable/schema|datatable/manage
Actions[]=setup|export|Browse the tables of the persistent classes…|datatable/dashboard
*/ ?>
```

## Weight

Standard: `75`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.datatable]
Weight=75
*/ ?>
```

## Siehe auch

- [Alle Blöcke von kernelservices.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/kernelservices.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
