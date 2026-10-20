---
title: debugbar.ini [Setting_sql_slow_queries]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Setting_sql_slow_queries] in debugbar.ini: File, Block, Variable, Type, Label, Group, Help"
keywords: Einstellung, debugbar.ini, Setting_sql_slow_queries, INI, setting, File, Block, Variable, Type, Label, Group, Help
type: generated
generated: exp:help:examples
---
# debugbar.ini [Setting_sql_slow_queries]

Der Block `[Setting_sql_slow_queries]` von `settings/debugbar.ini`, mit 7 Einstellung(en).

Bearbeiten Sie `settings/debugbar.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/debugbar.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/debugbar.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/debugbar.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## File

Standard: `site.ini`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_sql_slow_queries]
File=site.ini
*/ ?>
```

## Block

Standard: `DatabaseSettings`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_sql_slow_queries]
Block=DatabaseSettings
*/ ?>
```

## Variable

Standard: `SlowQueriesOutput`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_sql_slow_queries]
Variable=SlowQueriesOutput
*/ ?>
```

## Type

Standard: `text`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_sql_slow_queries]
Type=text
*/ ?>
```

## Label

Standard: `Slow queries only (ms)`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_sql_slow_queries]
Label=Slow queries only (ms)
*/ ?>
```

## Group

Standard: `sql`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_sql_slow_queries]
Group=sql
*/ ?>
```

## Help

Standard: `Lists only statements slower than this many milliseconds; 0 lists all.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_sql_slow_queries]
Help=Lists only statements slower than this many milliseconds; 0 lists all.
*/ ?>
```

## Siehe auch

- [Alle Blöcke von debugbar.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/debugbar.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
