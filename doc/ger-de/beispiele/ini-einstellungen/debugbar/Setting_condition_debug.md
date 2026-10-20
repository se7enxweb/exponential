---
title: debugbar.ini [Setting_condition_debug]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Setting_condition_debug] in debugbar.ini: File, Block, Variable, Type, Label, Group, Help"
keywords: Einstellung, debugbar.ini, Setting_condition_debug, INI, setting, File, Block, Variable, Type, Label, Group, Help
type: generated
generated: exp:help:examples
---
# debugbar.ini [Setting_condition_debug]

Der Block `[Setting_condition_debug]` von `settings/debugbar.ini`, mit 7 Einstellung(en).

> --------------------------------------------------------------------- Bedingungen, REST, Caching  

Bearbeiten Sie `settings/debugbar.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/debugbar.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/debugbar.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/debugbar.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## File

Standard: `debug.ini`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_condition_debug]
File=debug.ini
*/ ?>
```

## Block

Standard: `DebugSettings`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_condition_debug]
Block=DebugSettings
*/ ?>
```

## Variable

Standard: `ConditionDebug`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_condition_debug]
Variable=ConditionDebug
*/ ?>
```

## Type

Standard: `bool`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_condition_debug]
Type=bool
*/ ?>
```

## Label

Standard: `Conditional debug`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_condition_debug]
Label=Conditional debug
*/ ?>
```

## Group

Standard: `conditions`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_condition_debug]
Group=conditions
*/ ?>
```

## Help

Standard: `Master switch of the debug conditions below (debug.ini GeneralCondition).`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_condition_debug]
Help=Master switch of the debug conditions below (debug.ini GeneralCondition).
*/ ?>
```

## Siehe auch

- [Alle Blöcke von debugbar.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/debugbar.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
