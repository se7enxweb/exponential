---
title: subitemscolumns.ini [Column_dayssincemodified]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Column_dayssincemodified] in subitemscolumns.ini: Name, Group, Type, Class, Field, SortField, Align, Order ..."
keywords: Einstellung, subitemscolumns.ini, Column_dayssincemodified, INI, setting, Name, Group, Type, Class, Field, SortField, Align, Order, Description
type: generated
generated: exp:help:examples
---
# subitemscolumns.ini [Column_dayssincemodified]

Der Block `[Column_dayssincemodified]` von `settings/subitemscolumns.ini`, mit 9 Einstellung(en).

Bearbeiten Sie `settings/subitemscolumns.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitemscolumns.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitemscolumns.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitemscolumns.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Days since modified`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_dayssincemodified]
Name=Days since modified
*/ ?>
```

## Group

Standard: `Dates`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_dayssincemodified]
Group=Dates
*/ ?>
```

## Type

Standard: `number`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_dayssincemodified]
Type=number
*/ ?>
```

## Class

Standard: `expSubitemsDateColumn`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_dayssincemodified]
Class=expSubitemsDateColumn
*/ ?>
```

## Field

Standard: `days_since_modified`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_dayssincemodified]
Field=days_since_modified
*/ ?>
```

## SortField

Standard: `modified`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_dayssincemodified]
SortField=modified
*/ ?>
```

## Align

Standard: `right`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_dayssincemodified]
Align=right
*/ ?>
```

## Order

Standard: `820`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_dayssincemodified]
Order=820
*/ ?>
```

## Description

Standard: `Whole days since the current version was published.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_dayssincemodified]
Description=Whole days since the current version was published.
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitemscolumns.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitemscolumns.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
