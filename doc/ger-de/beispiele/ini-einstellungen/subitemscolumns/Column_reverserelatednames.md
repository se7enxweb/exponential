---
title: subitemscolumns.ini [Column_reverserelatednames]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Column_reverserelatednames] in subitemscolumns.ini: Name, Group, Type, Class, Field, Order, Description, Limit"
keywords: Einstellung, subitemscolumns.ini, Column_reverserelatednames, INI, setting, Name, Group, Type, Class, Field, Order, Description, Limit
type: generated
generated: exp:help:examples
---
# subitemscolumns.ini [Column_reverserelatednames]

Der Block `[Column_reverserelatednames]` von `settings/subitemscolumns.ini`, mit 8 Einstellung(en).

Bearbeiten Sie `settings/subitemscolumns.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitemscolumns.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitemscolumns.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitemscolumns.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Reverse related names`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_reverserelatednames]
Name=Reverse related names
*/ ?>
```

## Group

Standard: `Relations`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_reverserelatednames]
Group=Relations
*/ ?>
```

## Type

Standard: `list`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_reverserelatednames]
Type=list
*/ ?>
```

## Class

Standard: `expSubitemsRelationColumn`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_reverserelatednames]
Class=expSubitemsRelationColumn
*/ ?>
```

## Field

Standard: `reverse_related_names`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_reverserelatednames]
Field=reverse_related_names
*/ ?>
```

## Order

Standard: `1015`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_reverserelatednames]
Order=1015
*/ ?>
```

## Description

Standard: `The names of the first objects that relate to the item and that you may read.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_reverserelatednames]
Description=The names of the first objects that relate to the item and that you may read.
*/ ?>
```

## Limit

Standard: `10`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_reverserelatednames]
Limit=10
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitemscolumns.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitemscolumns.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
