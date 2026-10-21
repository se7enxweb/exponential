---
title: subitemscolumns.ini [Column_userrolecount]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Column_userrolecount] in subitemscolumns.ini: Name, Group, Type, Class, Field, Policy, Align, Order ..."
keywords: Einstellung, subitemscolumns.ini, Column_userrolecount, INI, setting, Name, Group, Type, Class, Field, Policy, Align, Order, Description
type: generated
generated: exp:help:examples
---
# subitemscolumns.ini [Column_userrolecount]

Der Block `[Column_userrolecount]` von `settings/subitemscolumns.ini`, mit 9 Einstellung(en).

Bearbeiten Sie `settings/subitemscolumns.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitemscolumns.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitemscolumns.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitemscolumns.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Role count`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_userrolecount]
Name=Role count
*/ ?>
```

## Group

Standard: `Users`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_userrolecount]
Group=Users
*/ ?>
```

## Type

Standard: `number`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_userrolecount]
Type=number
*/ ?>
```

## Class

Standard: `expSubitemsUserColumn`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_userrolecount]
Class=expSubitemsUserColumn
*/ ?>
```

## Field

Standard: `role_count`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_userrolecount]
Field=role_count
*/ ?>
```

## Policy

Standard: eine Liste mit 1: `role/read`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_userrolecount]
# ein leeres "Policy[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Policy[]
Policy[]=role/read
*/ ?>
```

## Align

Standard: `right`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_userrolecount]
Align=right
*/ ?>
```

## Order

Standard: `1340`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_userrolecount]
Order=1340
*/ ?>
```

## Description

Standard: `How many roles apply to the user.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_userrolecount]
Description=How many roles apply to the user.
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitemscolumns.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitemscolumns.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
