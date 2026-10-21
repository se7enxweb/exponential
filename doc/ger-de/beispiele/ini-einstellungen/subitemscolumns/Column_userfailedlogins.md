---
title: subitemscolumns.ini [Column_userfailedlogins]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Column_userfailedlogins] in subitemscolumns.ini: Name, Group, Type, Class, Field, Policy, Align, Order ..."
keywords: Einstellung, subitemscolumns.ini, Column_userfailedlogins, INI, setting, Name, Group, Type, Class, Field, Policy, Align, Order, Description
type: generated
generated: exp:help:examples
---
# subitemscolumns.ini [Column_userfailedlogins]

Der Block `[Column_userfailedlogins]` von `settings/subitemscolumns.ini`, mit 9 Einstellung(en).

Bearbeiten Sie `settings/subitemscolumns.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitemscolumns.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitemscolumns.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitemscolumns.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Failed logins`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_userfailedlogins]
Name=Failed logins
*/ ?>
```

## Group

Standard: `Users`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_userfailedlogins]
Group=Users
*/ ?>
```

## Type

Standard: `number`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_userfailedlogins]
Type=number
*/ ?>
```

## Class

Standard: `expSubitemsUserColumn`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_userfailedlogins]
Class=expSubitemsUserColumn
*/ ?>
```

## Field

Standard: `failed_logins`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_userfailedlogins]
Field=failed_logins
*/ ?>
```

## Policy

Standard: eine Liste mit 1: `role/assign`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_userfailedlogins]
# ein leeres "Policy[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Policy[]
Policy[]=role/assign
*/ ?>
```

## Align

Standard: `right`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_userfailedlogins]
Align=right
*/ ?>
```

## Order

Standard: `1330`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_userfailedlogins]
Order=1330
*/ ?>
```

## Description

Standard: `Failed login attempts since the last successful one.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_userfailedlogins]
Description=Failed login attempts since the last successful one.
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitemscolumns.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitemscolumns.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
