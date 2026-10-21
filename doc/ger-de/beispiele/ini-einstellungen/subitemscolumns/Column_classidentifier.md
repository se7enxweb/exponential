---
title: subitemscolumns.ini [Column_classidentifier]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Column_classidentifier] in subitemscolumns.ini: Name, Group, Type, Class, Field, SortField, Copy, Order ..."
keywords: Einstellung, subitemscolumns.ini, Column_classidentifier, INI, setting, Name, Group, Type, Class, Field, SortField, Copy, Order, Description
type: generated
generated: exp:help:examples
---
# subitemscolumns.ini [Column_classidentifier]

Der Block `[Column_classidentifier]` von `settings/subitemscolumns.ini`, mit 9 Einstellung(en).

> ---------------------------------------------------------------------------------------------  
> Object: das Inhaltsobjekt, seine Klasse und Sektion  

Bearbeiten Sie `settings/subitemscolumns.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitemscolumns.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitemscolumns.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitemscolumns.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Class identifier`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_classidentifier]
Name=Class identifier
*/ ?>
```

## Group

Standard: `Object`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_classidentifier]
Group=Object
*/ ?>
```

## Type

Standard: `code`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_classidentifier]
Type=code
*/ ?>
```

## Class

Standard: `expSubitemsObjectColumn`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_classidentifier]
Class=expSubitemsObjectColumn
*/ ?>
```

## Field

Standard: `class_identifier`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_classidentifier]
Field=class_identifier
*/ ?>
```

## SortField

Standard: `class_identifier`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_classidentifier]
SortField=class_identifier
*/ ?>
```

## Copy

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_classidentifier]
Copy=true
*/ ?>
```

## Order

Standard: `300`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_classidentifier]
Order=300
*/ ?>
```

## Description

Standard: `The identifier of the item's class, as templates and fetches use it.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_classidentifier]
Description=The identifier of the item's class, as templates and fetches use it.
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitemscolumns.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitemscolumns.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
