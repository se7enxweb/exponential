---
title: debugbar.ini [Setting_regional_development_mode]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Setting_regional_development_mode] in debugbar.ini: File, Block, Variable, Type, Label, Group, Help"
keywords: Einstellung, debugbar.ini, Setting_regional_development_mode, INI, setting, File, Block, Variable, Type, Label, Group, Help
type: generated
generated: exp:help:examples
---
# debugbar.ini [Setting_regional_development_mode]

Der Block `[Setting_regional_development_mode]` von `settings/debugbar.ini`, mit 7 Einstellung(en).

Bearbeiten Sie `settings/debugbar.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/debugbar.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/debugbar.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/debugbar.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## File

Standard: `site.ini`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_regional_development_mode]
File=site.ini
*/ ?>
```

## Block

Standard: `RegionalSettings`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_regional_development_mode]
Block=RegionalSettings
*/ ?>
```

## Variable

Standard: `DevelopmentMode`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_regional_development_mode]
Variable=DevelopmentMode
*/ ?>
```

## Type

Standard: `bool`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_regional_development_mode]
Type=bool
*/ ?>
```

## Label

Standard: `Bork translation`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_regional_development_mode]
Label=Bork translation
*/ ?>
```

## Group

Standard: `regional`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_regional_development_mode]
Group=regional
*/ ?>
```

## Help

Standard: `Shows untranslated texts in bork, so they stand out.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_regional_development_mode]
Help=Shows untranslated texts in bork, so they stand out.
*/ ?>
```

## Siehe auch

- [Alle Blöcke von debugbar.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/debugbar.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
