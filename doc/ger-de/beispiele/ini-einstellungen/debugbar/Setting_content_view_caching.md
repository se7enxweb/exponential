---
title: debugbar.ini [Setting_content_view_caching]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Setting_content_view_caching] in debugbar.ini: File, Block, Variable, Type, Label, Group, Help"
keywords: Einstellung, debugbar.ini, Setting_content_view_caching, INI, setting, File, Block, Variable, Type, Label, Group, Help
type: generated
generated: exp:help:examples
---
# debugbar.ini [Setting_content_view_caching]

Der Block `[Setting_content_view_caching]` von `settings/debugbar.ini`, mit 7 Einstellung(en).

Bearbeiten Sie `settings/debugbar.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/debugbar.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/debugbar.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/debugbar.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## File

Standard: `site.ini`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_content_view_caching]
File=site.ini
*/ ?>
```

## Block

Standard: `ContentSettings`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_content_view_caching]
Block=ContentSettings
*/ ?>
```

## Variable

Standard: `ViewCaching`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_content_view_caching]
Variable=ViewCaching
*/ ?>
```

## Type

Standard: `bool`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_content_view_caching]
Type=bool
*/ ?>
```

## Label

Standard: `View cache`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_content_view_caching]
Label=View cache
*/ ?>
```

## Group

Standard: `caching`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_content_view_caching]
Group=caching
*/ ?>
```

## Help

Standard: `The content view cache. Off renders every node view each time.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[Setting_content_view_caching]
Help=The content view cache. Off renders every node view each time.
*/ ?>
```

## Siehe auch

- [Alle Blöcke von debugbar.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/debugbar.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
