---
title: subitemscolumns.ini [Column_parentnodeid]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Column_parentnodeid] in subitemscolumns.ini: Name, Group, Type, Class, Field, Copy, Align, Order ..."
keywords: Einstellung, subitemscolumns.ini, Column_parentnodeid, INI, setting, Name, Group, Type, Class, Field, Copy, Align, Order, Description
type: generated
generated: exp:help:examples
---
# subitemscolumns.ini [Column_parentnodeid]

Der Block `[Column_parentnodeid]` von `settings/subitemscolumns.ini`, mit 9 Einstellung(en).

> ---------------------------------------------------------------------------------------------  
> Node: the location itself and its children  

Bearbeiten Sie `settings/subitemscolumns.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitemscolumns.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitemscolumns.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitemscolumns.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Parent node ID`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_parentnodeid]
Name=Parent node ID
*/ ?>
```

## Group

Standard: `Node`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_parentnodeid]
Group=Node
*/ ?>
```

## Type

Standard: `number`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_parentnodeid]
Type=number
*/ ?>
```

## Class

Standard: `expSubitemsNodeColumn`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_parentnodeid]
Class=expSubitemsNodeColumn
*/ ?>
```

## Field

Standard: `parent_node_id`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_parentnodeid]
Field=parent_node_id
*/ ?>
```

## Copy

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_parentnodeid]
Copy=true
*/ ?>
```

## Align

Standard: `right`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_parentnodeid]
Align=right
*/ ?>
```

## Order

Standard: `200`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_parentnodeid]
Order=200
*/ ?>
```

## Description

Standard: `The node ID of the item's parent location.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_parentnodeid]
Description=The node ID of the item's parent location.
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitemscolumns.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitemscolumns.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
