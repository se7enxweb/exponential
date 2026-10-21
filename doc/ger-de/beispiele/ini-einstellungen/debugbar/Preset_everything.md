---
title: debugbar.ini [Preset_everything]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Preset_everything] in debugbar.ini: Name, Description, Values"
keywords: Einstellung, debugbar.ini, Preset_everything, INI, setting, Name, Description, Values
type: generated
generated: exp:help:examples
---
# debugbar.ini [Preset_everything]

Der Block `[Preset_everything]` von `settings/debugbar.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/debugbar.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/debugbar.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/debugbar.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/debugbar.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Everything`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Preset_everything]
Name=Everything
*/ ?>
```

## Description

Standard: `Every debug output there is (slow: for one look, then switch it off).`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Preset_everything]
Description=Every debug output there is (slow: for one look, then switch it off).
*/ ?>
```

## Values

Standard: ein Hash mit 10: `debug_output=on`, `display_debug_warnings=on`, `display_included_files=on`, `template_debug=on`, `template_show_xhtml_code=on`, `template_show_used_templates=on`, `sql_output=on`, `sql_query_analysis=on`, `sql_debug_transactions=on`, `regional_debug=on`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Preset_everything]
# ein leeres "Values[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Values[]
Values[debug_output]=on
Values[display_debug_warnings]=on
Values[display_included_files]=on
Values[template_debug]=on
Values[template_show_xhtml_code]=on
Values[template_show_used_templates]=on
Values[sql_output]=on
Values[sql_query_analysis]=on
Values[sql_debug_transactions]=on
Values[regional_debug]=on
*/ ?>
```

## Siehe auch

- [Alle Blöcke von debugbar.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/debugbar.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
