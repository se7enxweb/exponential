---
title: debugbar.ini [Setting_display_included_files]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Setting_display_included_files] in debugbar.ini: File, Block, Variable, Type, Label, Group, Help"
keywords: Einstellung, debugbar.ini, Setting_display_included_files, INI, setting, File, Block, Variable, Type, Label, Group, Help
type: generated
generated: exp:help:examples
---
# debugbar.ini [Setting_display_included_files]

Der Block `[Setting_display_included_files]` von `settings/debugbar.ini`, mit 7 Einstellung(en).

Bearbeiten Sie `settings/debugbar.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/debugbar.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/debugbar.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/debugbar.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## File

Standard: `site.ini`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_display_included_files]
File=site.ini
*/ ?>
```

## Block

Standard: `DebugSettings`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_display_included_files]
Block=DebugSettings
*/ ?>
```

## Variable

Standard: `DisplayIncludedFiles`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_display_included_files]
Variable=DisplayIncludedFiles
*/ ?>
```

## Type

Standard: `bool`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_display_included_files]
Type=bool
*/ ?>
```

## Label

Standard: `Included files`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_display_included_files]
Label=Included files
*/ ?>
```

## Group

Standard: `output`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_display_included_files]
Group=output
*/ ?>
```

## Help

Standard: `Lists every PHP file the request included, in the report.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_display_included_files]
Help=Lists every PHP file the request included, in the report.
*/ ?>
```

## Siehe auch

- [Alle Blöcke von debugbar.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/debugbar.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
