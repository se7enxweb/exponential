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

> Die Engines, die für eine Verbindung verwendet werden dürfen (sqlite, mysql, postgresql, oracle, sqlserver, mongodb)  

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

> Die Verzeichnisse, in denen die Datei einer SQLite-Verbindung liegen darf, relativ zur Installation; die Datei wird dort angelegt,  
> wenn sie noch nicht existiert. Ein Pfad außerhalb davon wird abgelehnt.  

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

> Hosts, die eine Server-Verbindung erreichen darf; ist die Liste leer, jeder (Hostnamen oder Adressen; \* für jeden)  

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

> Sekunden, die ein Verbindungstest auf den Server wartet  

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
