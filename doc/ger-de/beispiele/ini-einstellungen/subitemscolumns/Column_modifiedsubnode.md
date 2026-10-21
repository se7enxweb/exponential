---
title: subitemscolumns.ini [Column_modifiedsubnode]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Column_modifiedsubnode] in subitemscolumns.ini: Name, Group, Type, Class, Field, SortField, Order, Description"
keywords: Einstellung, subitemscolumns.ini, Column_modifiedsubnode, INI, setting, Name, Group, Type, Class, Field, SortField, Order, Description
type: generated
generated: exp:help:examples
---
# subitemscolumns.ini [Column_modifiedsubnode]

Der Block `[Column_modifiedsubnode]` von `settings/subitemscolumns.ini`, mit 8 Einstellung(en).

> ---------------------------------------------------------------------------------------------  
> Dates: Daten  

Bearbeiten Sie `settings/subitemscolumns.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitemscolumns.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitemscolumns.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitemscolumns.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Subtree modified`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_modifiedsubnode]
Name=Subtree modified
*/ ?>
```

## Group

Standard: `Dates`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_modifiedsubnode]
Group=Dates
*/ ?>
```

## Type

Standard: `datetime`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_modifiedsubnode]
Type=datetime
*/ ?>
```

## Class

Standard: `expSubitemsNodeColumn`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_modifiedsubnode]
Class=expSubitemsNodeColumn
*/ ?>
```

## Field

Standard: `modified_subnode`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_modifiedsubnode]
Field=modified_subnode
*/ ?>
```

## SortField

Standard: `modified_subnode`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_modifiedsubnode]
SortField=modified_subnode
*/ ?>
```

## Order

Standard: `800`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_modifiedsubnode]
Order=800
*/ ?>
```

## Description

Standard: `When anything in the item's subtree last changed.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_modifiedsubnode]
Description=When anything in the item's subtree last changed.
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitemscolumns.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitemscolumns.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
