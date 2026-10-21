---
title: subitemscolumns.ini [Column_dayssincepublished]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Column_dayssincepublished] in subitemscolumns.ini: Name, Group, Type, Class, Field, SortField, Align, Order ..."
keywords: Einstellung, subitemscolumns.ini, Column_dayssincepublished, INI, setting, Name, Group, Type, Class, Field, SortField, Align, Order, Description
type: generated
generated: exp:help:examples
---
# subitemscolumns.ini [Column_dayssincepublished]

Der Block `[Column_dayssincepublished]` von `settings/subitemscolumns.ini`, mit 9 Einstellung(en).

Bearbeiten Sie `settings/subitemscolumns.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitemscolumns.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitemscolumns.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitemscolumns.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Days since published`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_dayssincepublished]
Name=Days since published
*/ ?>
```

## Group

Standard: `Dates`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_dayssincepublished]
Group=Dates
*/ ?>
```

## Type

Standard: `number`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_dayssincepublished]
Type=number
*/ ?>
```

## Class

Standard: `expSubitemsDateColumn`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_dayssincepublished]
Class=expSubitemsDateColumn
*/ ?>
```

## Field

Standard: `days_since_published`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_dayssincepublished]
Field=days_since_published
*/ ?>
```

## SortField

Standard: `published`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_dayssincepublished]
SortField=published
*/ ?>
```

## Align

Standard: `right`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_dayssincepublished]
Align=right
*/ ?>
```

## Order

Standard: `815`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_dayssincepublished]
Order=815
*/ ?>
```

## Description

Standard: `Whole days since the item was first published.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_dayssincepublished]
Description=Whole days since the item was first published.
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitemscolumns.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitemscolumns.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
