---
title: debugbar.ini [Setting_debug_mode]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Setting_debug_mode] in debugbar.ini: File, Block, Variable, Type, Values, Label, Group, Help"
keywords: Einstellung, debugbar.ini, Setting_debug_mode, INI, setting, File, Block, Variable, Type, Values, Label, Group, Help
type: generated
generated: exp:help:examples
---
# debugbar.ini [Setting_debug_mode]

Der Block `[Setting_debug_mode]` von `settings/debugbar.ini`, mit 8 Einstellung(en).

Bearbeiten Sie `settings/debugbar.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/debugbar.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/debugbar.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/debugbar.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## File

Standard: `site.ini`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_debug_mode]
File=site.ini
*/ ?>
```

## Block

Standard: `DebugSettings`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_debug_mode]
Block=DebugSettings
*/ ?>
```

## Variable

Standard: `Debug`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_debug_mode]
Variable=Debug
*/ ?>
```

## Type

Standard: `enum`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_debug_mode]
Type=enum
*/ ?>
```

## Values

Standard: eine Liste mit 2: `inline`, `popup`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_debug_mode]
# ein leeres "Values[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Values[]
Values[]=inline
Values[]=popup
*/ ?>
```

## Label

Standard: `Report placement`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_debug_mode]
Label=Report placement
*/ ?>
```

## Group

Standard: `output`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_debug_mode]
Group=output
*/ ?>
```

## Help

Standard: `Inline puts the report under the page, popup opens it in a window of its own.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_debug_mode]
Help=Inline puts the report under the page, popup opens it in a window of its own.
*/ ?>
```

## Siehe auch

- [Alle Blöcke von debugbar.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/debugbar.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
