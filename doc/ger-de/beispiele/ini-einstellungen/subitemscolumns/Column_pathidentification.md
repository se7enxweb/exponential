---
title: subitemscolumns.ini [Column_pathidentification]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Column_pathidentification] in subitemscolumns.ini: Name, Group, Type, Class, Field, SortField, Copy, Order ..."
keywords: Einstellung, subitemscolumns.ini, Column_pathidentification, INI, setting, Name, Group, Type, Class, Field, SortField, Copy, Order, Description
type: generated
generated: exp:help:examples
---
# subitemscolumns.ini [Column_pathidentification]

Der Block `[Column_pathidentification]` von `settings/subitemscolumns.ini`, mit 9 Einstellung(en).

Bearbeiten Sie `settings/subitemscolumns.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitemscolumns.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitemscolumns.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitemscolumns.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Path identification string`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_pathidentification]
Name=Path identification string
*/ ?>
```

## Group

Standard: `Node`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_pathidentification]
Group=Node
*/ ?>
```

## Type

Standard: `code`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_pathidentification]
Type=code
*/ ?>
```

## Class

Standard: `expSubitemsNodeColumn`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_pathidentification]
Class=expSubitemsNodeColumn
*/ ?>
```

## Field

Standard: `path_identification`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_pathidentification]
Field=path_identification
*/ ?>
```

## SortField

Standard: `path_string`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_pathidentification]
SortField=path_string
*/ ?>
```

## Copy

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_pathidentification]
Copy=true
*/ ?>
```

## Order

Standard: `215`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_pathidentification]
Order=215
*/ ?>
```

## Description

Standard: `The location's path as identifiers (ezcontentobject_tree.path_identification_string).`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_pathidentification]
Description=The location's path as identifiers (ezcontentobject_tree.path_identification_string).
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitemscolumns.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitemscolumns.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
