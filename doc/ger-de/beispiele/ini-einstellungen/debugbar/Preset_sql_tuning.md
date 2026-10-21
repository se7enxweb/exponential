---
title: debugbar.ini [Preset_sql_tuning]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Preset_sql_tuning] in debugbar.ini: Name, Description, Values"
keywords: Einstellung, debugbar.ini, Preset_sql_tuning, INI, setting, Name, Description, Values
type: generated
generated: exp:help:examples
---
# debugbar.ini [Preset_sql_tuning]

Der Block `[Preset_sql_tuning]` von `settings/debugbar.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/debugbar.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/debugbar.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/debugbar.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/debugbar.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `SQL tuning`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Preset_sql_tuning]
Name=SQL tuning
*/ ?>
```

## Description

Standard: `Every statement with its time, the analysis, and transaction debug.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Preset_sql_tuning]
Description=Every statement with its time, the analysis, and transaction debug.
*/ ?>
```

## Values

Standard: ein Hash mit 4: `debug_output=on`, `sql_output=on`, `sql_query_analysis=on`, `sql_debug_transactions=on`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Preset_sql_tuning]
# ein leeres "Values[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Values[]
Values[debug_output]=on
Values[sql_output]=on
Values[sql_query_analysis]=on
Values[sql_debug_transactions]=on
*/ ?>
```

## Siehe auch

- [Alle Blöcke von debugbar.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/debugbar.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
