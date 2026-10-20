---
title: debugbar.ini [Preset_template_work]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Preset_template_work] in debugbar.ini: Name, Description, Values"
keywords: Einstellung, debugbar.ini, Preset_template_work, INI, setting, Name, Description, Values
type: generated
generated: exp:help:examples
---
# debugbar.ini [Preset_template_work]

Der Block `[Preset_template_work]` von `settings/debugbar.ini`, mit 3 Einstellung(en).

> --------------------------------------------------------------------- presets  

Bearbeiten Sie `settings/debugbar.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/debugbar.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/debugbar.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/debugbar.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Template work`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Preset_template_work]
Name=Template work
*/ ?>
```

## Description

Standard: `Template debug with names and the list of used templates, compiler kept on.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Preset_template_work]
Description=Template debug with names and the list of used templates, compiler kept on.
*/ ?>
```

## Values

Standard: ein Hash mit 5: `debug_output=on`, `template_debug=on`, `template_show_xhtml_code=on`, `template_show_used_templates=on`, `template_development_mode=on`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Preset_template_work]
# ein leeres "Values[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Values[]
Values[debug_output]=on
Values[template_debug]=on
Values[template_show_xhtml_code]=on
Values[template_show_used_templates]=on
Values[template_development_mode]=on
*/ ?>
```

## Siehe auch

- [Alle Blöcke von debugbar.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/debugbar.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
