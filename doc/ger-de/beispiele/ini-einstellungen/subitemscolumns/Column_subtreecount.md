---
title: subitemscolumns.ini [Column_subtreecount]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Column_subtreecount] in subitemscolumns.ini: Name, Group, Type, Class, Field, Align, Order, Description"
keywords: Einstellung, subitemscolumns.ini, Column_subtreecount, INI, setting, Name, Group, Type, Class, Field, Align, Order, Description
type: generated
generated: exp:help:examples
---
# subitemscolumns.ini [Column_subtreecount]

Der Block `[Column_subtreecount]` von `settings/subitemscolumns.ini`, mit 8 Einstellung(en).

Bearbeiten Sie `settings/subitemscolumns.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitemscolumns.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitemscolumns.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitemscolumns.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Subtree size`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_subtreecount]
Name=Subtree size
*/ ?>
```

## Group

Standard: `Node`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_subtreecount]
Group=Node
*/ ?>
```

## Type

Standard: `number`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_subtreecount]
Type=number
*/ ?>
```

## Class

Standard: `expSubitemsNodeColumn`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_subtreecount]
Class=expSubitemsNodeColumn
*/ ?>
```

## Field

Standard: `subtree_count`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_subtreecount]
Field=subtree_count
*/ ?>
```

## Align

Standard: `right`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_subtreecount]
Align=right
*/ ?>
```

## Order

Standard: `255`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_subtreecount]
Order=255
*/ ?>
```

## Description

Standard: `Every item below, all depths, that you may read. One count query per row over the whole subtree: slower for big trees.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_subtreecount]
Description=Every item below, all depths, that you may read. One count query per row over the whole subtree: slower for big trees.
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitemscolumns.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitemscolumns.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
