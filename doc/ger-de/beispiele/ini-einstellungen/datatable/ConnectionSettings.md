---
title: datatable.ini [ConnectionSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ConnectionSettings] in datatable.ini: Engines, SQLiteDirectories, AllowedHosts, ConnectTimeout"
keywords: Einstellung, datatable.ini, ConnectionSettings, INI, setting, Engines, SQLiteDirectories, AllowedHosts, ConnectTimeout
type: generated
generated: exp:help:examples
---
# datatable.ini [ConnectionSettings]

Der Block `[ConnectionSettings]` von `settings/datatable.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/datatable.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/datatable.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/datatable.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/datatable.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Engines

> The engines that may be used for a connection (sqlite, mysql, postgresql, oracle, sqlserver, mongodb)  

Standard: eine Liste mit 6: `sqlite`, `mysql`, `postgresql`, `oracle`, `sqlserver`, `mongodb`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/datatable.ini.append.php
[ConnectionSettings]
# ein leeres "Engines[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Engines[]
Engines[]=sqlite
Engines[]=mysql
Engines[]=postgresql
Engines[]=oracle
Engines[]=sqlserver
Engines[]=mongodb
*/ ?>
```

## SQLiteDirectories

> The directories a SQLite connection's file may be in, relative to the installation; the file is created there  
> when it does not exist yet. A path outside them is refused.  

Standard: eine Liste mit 1: `var/datatable`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/datatable.ini.append.php
[ConnectionSettings]
# ein leeres "SQLiteDirectories[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SQLiteDirectories[]
SQLiteDirectories[]=var/datatable
*/ ?>
```

## AllowedHosts

> Hosts a server connection may reach, besides any when the list is empty (host names or addresses; \* for any)  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/datatable.ini.append.php
[ConnectionSettings]
# ein leeres "AllowedHosts[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AllowedHosts[]
*/ ?>
```

## ConnectTimeout

> Seconds a connection test waits for the server  

Standard: `5`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/datatable.ini.append.php
[ConnectionSettings]
ConnectTimeout=5
*/ ?>
```

## Siehe auch

- [Alle Blöcke von datatable.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/datatable.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
