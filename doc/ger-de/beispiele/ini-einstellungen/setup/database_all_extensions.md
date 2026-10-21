---
title: setup.ini [database_all_extensions]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [database_all_extensions] in setup.ini: Extensions, Require"
keywords: Einstellung, setup.ini, database_all_extensions, INI, setting, Extensions, Require
type: generated
generated: exp:help:examples
---
# setup.ini [database_all_extensions]

Der Block `[database_all_extensions]` von `settings/setup.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/setup.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/setup.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/setup.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/setup.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Extensions

Standard: `sqlite3;mysqli;pgsql;mongodb`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/setup.ini.append.php
[database_all_extensions]
Extensions=sqlite3;mysqli;pgsql;mongodb
*/ ?>
```

## Require

Standard: `all`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/setup.ini.append.php
[database_all_extensions]
Require=all
*/ ?>
```

## Siehe auch

- [Alle Blöcke von setup.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/setup.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
