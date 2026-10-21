---
title: subitemscolumns.ini [Column_modifiedage]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Column_modifiedage] in subitemscolumns.ini: Name, Group, Type, Class, Field, SortField, Order, Description"
keywords: Einstellung, subitemscolumns.ini, Column_modifiedage, INI, setting, Name, Group, Type, Class, Field, SortField, Order, Description
type: generated
generated: exp:help:examples
---
# subitemscolumns.ini [Column_modifiedage]

Der Block `[Column_modifiedage]` von `settings/subitemscolumns.ini`, mit 8 Einstellung(en).

Bearbeiten Sie `settings/subitemscolumns.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitemscolumns.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitemscolumns.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitemscolumns.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Modified (age)`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_modifiedage]
Name=Modified (age)
*/ ?>
```

## Group

Standard: `Dates`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_modifiedage]
Group=Dates
*/ ?>
```

## Type

Standard: `text`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_modifiedage]
Type=text
*/ ?>
```

## Class

Standard: `expSubitemsDateColumn`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_modifiedage]
Class=expSubitemsDateColumn
*/ ?>
```

## Field

Standard: `modified_age`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_modifiedage]
Field=modified_age
*/ ?>
```

## SortField

Standard: `modified`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_modifiedage]
SortField=modified
*/ ?>
```

## Order

Standard: `810`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_modifiedage]
Order=810
*/ ?>
```

## Description

Standard: `How long ago the current version was published.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_modifiedage]
Description=How long ago the current version was published.
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitemscolumns.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitemscolumns.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
