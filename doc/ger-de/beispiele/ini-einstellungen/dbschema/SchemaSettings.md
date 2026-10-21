---
title: dbschema.ini [SchemaSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [SchemaSettings] in dbschema.ini: SchemaPaths, SchemaHandlerClasses"
keywords: Einstellung, dbschema.ini, SchemaSettings, INI, setting, SchemaPaths, SchemaHandlerClasses
type: generated
generated: exp:help:examples
---
# dbschema.ini [SchemaSettings]

Der Block `[SchemaSettings]` von `settings/dbschema.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/dbschema.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/dbschema.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/dbschema.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/dbschema.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## SchemaPaths

Standard: ein Hash mit 6: `mysql=lib/ezdbschema/classes/ezmysqlschema.php`, `mysqli=lib/ezdbschema/classes/ezmysqlschema.php`, `postgresql=lib/ezdbschema/classes/ezpgsqlschema.php`, `sqlite=lib/ezdbschema/classes/ezsqliteschema.php`, `sqlite3=lib/ezdbschema/classes/ezsqliteschema.php`, `mongo=lib/ezdbschema/classes/expmongoschema.php`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/dbschema.ini.append.php
[SchemaSettings]
# ein leeres "SchemaPaths[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SchemaPaths[]
SchemaPaths[mysql]=lib/ezdbschema/classes/ezmysqlschema.php
SchemaPaths[mysqli]=lib/ezdbschema/classes/ezmysqlschema.php
SchemaPaths[postgresql]=lib/ezdbschema/classes/ezpgsqlschema.php
SchemaPaths[sqlite]=lib/ezdbschema/classes/ezsqliteschema.php
SchemaPaths[sqlite3]=lib/ezdbschema/classes/ezsqliteschema.php
SchemaPaths[mongo]=lib/ezdbschema/classes/expmongoschema.php
*/ ?>
```

## SchemaHandlerClasses

Standard: ein Hash mit 6: `mysql=eZMysqlSchema`, `mysqli=eZMysqlSchema`, `postgresql=eZPgsqlSchema`, `sqlite=eZSQLiteSchema`, `sqlite3=eZSQLiteSchema`, `mongo=expMongoSchema`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/dbschema.ini.append.php
[SchemaSettings]
# ein leeres "SchemaHandlerClasses[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SchemaHandlerClasses[]
SchemaHandlerClasses[mysql]=eZMysqlSchema
SchemaHandlerClasses[mysqli]=eZMysqlSchema
SchemaHandlerClasses[postgresql]=eZPgsqlSchema
SchemaHandlerClasses[sqlite]=eZSQLiteSchema
SchemaHandlerClasses[sqlite3]=eZSQLiteSchema
SchemaHandlerClasses[mongo]=expMongoSchema
*/ ?>
```

## Siehe auch

- [Alle Blöcke von dbschema.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/dbschema.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
