---
title: migration.ini [MigrationSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [MigrationSettings] in migration.ini: Enabled, DirectoryList, ExtensionDirectory"
keywords: Einstellung, migration.ini, MigrationSettings, INI, setting, Enabled, DirectoryList, ExtensionDirectory
type: generated
generated: exp:help:examples
---
# migration.ini [MigrationSettings]

Der Block `[MigrationSettings]` von `settings/migration.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/migration.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/migration.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/migration.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/migration.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Enabled

> enabled: in den Verzeichnissen unten wird nach Migrationen gesucht. disabled: es werden keine gefunden.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[MigrationSettings]
Enabled=enabled
*/ ?>
```

## DirectoryList

> Die Migrationsverzeichnisse der Site, relativ zur Wurzel der Installation oder absolut. Jedes enthält  
> Dateien namens YYYYMMDDHHMMSS\_name.<type> (php, yml, sql, <engine>.sql, mongodb.json, ini); Unterverzeichnisse werden nicht  
> durchsucht.  

Standard: eine Liste mit 1: `migrations`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[MigrationSettings]
# ein leeres "DirectoryList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
DirectoryList[]
DirectoryList[]=migrations
*/ ?>
```

## ExtensionDirectory

> Das Verzeichnis in jeder aktiven Erweiterung (extension/<name>/migrations) und jedem installierten  
> Paket, das seine Migrationen enthält.  

Standard: `migrations`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[MigrationSettings]
ExtensionDirectory=migrations
*/ ?>
```

## Siehe auch

- [Alle Blöcke von migration.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
