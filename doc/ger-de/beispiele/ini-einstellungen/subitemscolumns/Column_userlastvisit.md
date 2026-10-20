---
title: subitemscolumns.ini [Column_userlastvisit]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Column_userlastvisit] in subitemscolumns.ini: Name, Group, Type, Class, Field, Policy, Order, Description"
keywords: Einstellung, subitemscolumns.ini, Column_userlastvisit, INI, setting, Name, Group, Type, Class, Field, Policy, Order, Description
type: generated
generated: exp:help:examples
---
# subitemscolumns.ini [Column_userlastvisit]

Der Block `[Column_userlastvisit]` von `settings/subitemscolumns.ini`, mit 8 Einstellung(en).

Bearbeiten Sie `settings/subitemscolumns.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitemscolumns.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitemscolumns.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitemscolumns.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Last visit`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_userlastvisit]
Name=Last visit
*/ ?>
```

## Group

Standard: `Users`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_userlastvisit]
Group=Users
*/ ?>
```

## Type

Standard: `datetime`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_userlastvisit]
Type=datetime
*/ ?>
```

## Class

Standard: `expSubitemsUserColumn`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_userlastvisit]
Class=expSubitemsUserColumn
*/ ?>
```

## Field

Standard: `last_visit`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_userlastvisit]
Field=last_visit
*/ ?>
```

## Policy

Standard: eine Liste mit 1: `role/read`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_userlastvisit]
# ein leeres "Policy[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Policy[]
Policy[]=role/read
*/ ?>
```

## Order

Standard: `1320`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_userlastvisit]
Order=1320
*/ ?>
```

## Description

Standard: `When the user's last visit began; empty when they never logged in.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_userlastvisit]
Description=When the user's last visit began; empty when they never logged in.
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitemscolumns.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitemscolumns.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
