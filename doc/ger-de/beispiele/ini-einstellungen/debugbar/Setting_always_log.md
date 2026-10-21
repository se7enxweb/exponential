---
title: debugbar.ini [Setting_always_log]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Setting_always_log] in debugbar.ini: File, Block, Variable, Type, Values, Label, Group, Help"
keywords: Einstellung, debugbar.ini, Setting_always_log, INI, setting, File, Block, Variable, Type, Values, Label, Group, Help
type: generated
generated: exp:help:examples
---
# debugbar.ini [Setting_always_log]

Der Block `[Setting_always_log]` von `settings/debugbar.ini`, mit 8 Einstellung(en).

Bearbeiten Sie `settings/debugbar.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/debugbar.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/debugbar.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/debugbar.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## File

Standard: `site.ini`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_always_log]
File=site.ini
*/ ?>
```

## Block

Standard: `DebugSettings`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_always_log]
Block=DebugSettings
*/ ?>
```

## Variable

Standard: `AlwaysLog`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_always_log]
Variable=AlwaysLog
*/ ?>
```

## Type

Standard: `list`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_always_log]
Type=list
*/ ?>
```

## Values

Standard: eine Liste mit 6: `error`, `warning`, `notice`, `debug`, `strict`, `deprecated`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_always_log]
# ein leeres "Values[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Values[]
Values[]=error
Values[]=warning
Values[]=notice
Values[]=debug
Values[]=strict
Values[]=deprecated
*/ ?>
```

## Label

Standard: `Always log`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_always_log]
Label=Always log
*/ ?>
```

## Group

Standard: `output`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_always_log]
Group=output
*/ ?>
```

## Help

Standard: `Message levels written to var/log even when debug output is off.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_always_log]
Help=Message levels written to var/log even when debug output is off.
*/ ?>
```

## Siehe auch

- [Alle Blöcke von debugbar.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/debugbar.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
