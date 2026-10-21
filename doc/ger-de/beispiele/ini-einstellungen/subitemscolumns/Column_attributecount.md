---
title: subitemscolumns.ini [Column_attributecount]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Column_attributecount] in subitemscolumns.ini: Name, Group, Type, Class, Field, Align, Order, Description"
keywords: Einstellung, subitemscolumns.ini, Column_attributecount, INI, setting, Name, Group, Type, Class, Field, Align, Order, Description
type: generated
generated: exp:help:examples
---
# subitemscolumns.ini [Column_attributecount]

Der Block `[Column_attributecount]` von `settings/subitemscolumns.ini`, mit 8 Einstellung(en).

Bearbeiten Sie `settings/subitemscolumns.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitemscolumns.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitemscolumns.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitemscolumns.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Attributes`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_attributecount]
Name=Attributes
*/ ?>
```

## Group

Standard: `Technical`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_attributecount]
Group=Technical
*/ ?>
```

## Type

Standard: `number`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_attributecount]
Type=number
*/ ?>
```

## Class

Standard: `expSubitemsObjectColumn`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_attributecount]
Class=expSubitemsObjectColumn
*/ ?>
```

## Field

Standard: `attribute_count`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_attributecount]
Field=attribute_count
*/ ?>
```

## Align

Standard: `right`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_attributecount]
Align=right
*/ ?>
```

## Order

Standard: `1515`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_attributecount]
Order=1515
*/ ?>
```

## Description

Standard: `Attributes of the current version in the shown language. Reads the data map.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_attributecount]
Description=Attributes of the current version in the shown language. Reads the data map.
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitemscolumns.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitemscolumns.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
