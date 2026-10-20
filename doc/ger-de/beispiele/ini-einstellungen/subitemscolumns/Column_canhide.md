---
title: subitemscolumns.ini [Column_canhide]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Column_canhide] in subitemscolumns.ini: Name, Group, Type, Class, Field, Order, Description"
keywords: Einstellung, subitemscolumns.ini, Column_canhide, INI, setting, Name, Group, Type, Class, Field, Order, Description
type: generated
generated: exp:help:examples
---
# subitemscolumns.ini [Column_canhide]

Der Block `[Column_canhide]` von `settings/subitemscolumns.ini`, mit 7 Einstellung(en).

Bearbeiten Sie `settings/subitemscolumns.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitemscolumns.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitemscolumns.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitemscolumns.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `You can hide`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_canhide]
Name=You can hide
*/ ?>
```

## Group

Standard: `Workflow`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_canhide]
Group=Workflow
*/ ?>
```

## Type

Standard: `bool`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_canhide]
Type=bool
*/ ?>
```

## Class

Standard: `expSubitemsPermissionColumn`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_canhide]
Class=expSubitemsPermissionColumn
*/ ?>
```

## Field

Standard: `can_hide`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_canhide]
Field=can_hide
*/ ?>
```

## Order

Standard: `1240`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_canhide]
Order=1240
*/ ?>
```

## Description

Standard: `Whether you may hide and reveal the item.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_canhide]
Description=Whether you may hide and reveal the item.
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitemscolumns.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitemscolumns.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
