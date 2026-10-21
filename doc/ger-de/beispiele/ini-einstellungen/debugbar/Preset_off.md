---
title: debugbar.ini [Preset_off]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Preset_off] in debugbar.ini: Name, Description, Values"
keywords: Einstellung, debugbar.ini, Preset_off, INI, setting, Name, Description, Values
type: generated
generated: exp:help:examples
---
# debugbar.ini [Preset_off]

Der Block `[Preset_off]` von `settings/debugbar.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/debugbar.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/debugbar.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/debugbar.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/debugbar.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Off`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Preset_off]
Name=Off
*/ ?>
```

## Description

Standard: `Every debug output off again.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Preset_off]
Description=Every debug output off again.
*/ ?>
```

## Values

Standard: ein Hash mit 13: `debug_output=off`, `debug_redirection=off`, `display_debug_warnings=off`, `display_included_files=off`, `template_debug=off`, `template_show_used_templates=off`, `template_path_comments=off`, `template_show_method_debug=off`, `template_development_mode=off`, `sql_output=off`, `sql_query_analysis=off`, `sql_debug_transactions=off` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Preset_off]
# ein leeres "Values[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Values[]
Values[debug_output]=off
Values[debug_redirection]=off
Values[display_debug_warnings]=off
Values[display_included_files]=off
Values[template_debug]=off
Values[template_show_used_templates]=off
Values[template_path_comments]=off
Values[template_show_method_debug]=off
Values[template_development_mode]=off
Values[sql_output]=off
Values[sql_query_analysis]=off
Values[sql_debug_transactions]=off
Values[regional_debug]=off
*/ ?>
```

## Siehe auch

- [Alle Blöcke von debugbar.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/debugbar.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
