---
title: setup.ini [DatabaseSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [DatabaseSettings] in setup.ini: DefaultType, DefaultServer, DefaultPort, DefaultName, DefaultUser, DefaultPassword, DefaultPort_pgsql, DefaultUser_pgsql ..."
keywords: Einstellung, setup.ini, DatabaseSettings, INI, setting, DefaultType, DefaultServer, DefaultPort, DefaultName, DefaultUser, DefaultPassword, DefaultPort_pgsql, DefaultUser_pgsql, SQLSchema, CleanSQLData, DemoSQLData
type: generated
generated: exp:help:examples
---
# setup.ini [DatabaseSettings]

Der Block `[DatabaseSettings]` von `settings/setup.ini`, mit 11 Einstellung(en).

Bearbeiten Sie `settings/setup.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/setup.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/setup.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/setup.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## DefaultType

> The database system the setup wizard lists first and preselects.  
> One of sqlite3, mysqli, pgsql, mongodb. SQLite needs no database server;  
> when the PHP sqlite3 extension is missing the wizard falls back to the  
> first available system and says so.  

Standard: `sqlite3`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/setup.ini.append.php
[DatabaseSettings]
DefaultType=sqlite3
*/ ?>
```

## DefaultServer

Standard: `localhost`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/setup.ini.append.php
[DatabaseSettings]
DefaultServer=localhost
*/ ?>
```

## DefaultPort

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/setup.ini.append.php
[DatabaseSettings]
DefaultPort=
*/ ?>
```

## DefaultName

Standard: `exponential`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/setup.ini.append.php
[DatabaseSettings]
DefaultName=exponential
*/ ?>
```

## DefaultUser

Standard: `root`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/setup.ini.append.php
[DatabaseSettings]
DefaultUser=root
*/ ?>
```

## DefaultPassword

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/setup.ini.append.php
[DatabaseSettings]
DefaultPassword=
*/ ?>
```

## DefaultPort_pgsql

> Default<Setting>\_<type> replaces the value above on the database page for  
> that system: a PostgreSQL server has no user root and listens on 5432.  

Standard: `5432`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/setup.ini.append.php
[DatabaseSettings]
DefaultPort_pgsql=5432
*/ ?>
```

## DefaultUser_pgsql

Standard: `postgres`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/setup.ini.append.php
[DatabaseSettings]
DefaultUser_pgsql=postgres
*/ ?>
```

## SQLSchema

> CleanSQL=kernel\_clean.sql  
> DemoSQL=demokernel.sql  

Standard: `kernel_schema.sql`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/setup.ini.append.php
[DatabaseSettings]
SQLSchema=kernel_schema.sql
*/ ?>
```

## CleanSQLData

Standard: `cleandata.sql`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/setup.ini.append.php
[DatabaseSettings]
CleanSQLData=cleandata.sql
*/ ?>
```

## DemoSQLData

Standard: `demodata.sql`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/setup.ini.append.php
[DatabaseSettings]
DemoSQLData=demodata.sql
*/ ?>
```

## Siehe auch

- [Alle Blöcke von setup.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/setup.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
