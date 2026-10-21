---
title: subitemscolumns.ini [Column_useremail]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Column_useremail] in subitemscolumns.ini: Name, Group, Type, Class, Field, Policy, Copy, Order ..."
keywords: Einstellung, subitemscolumns.ini, Column_useremail, INI, setting, Name, Group, Type, Class, Field, Policy, Copy, Order, Description
type: generated
generated: exp:help:examples
---
# subitemscolumns.ini [Column_useremail]

Der Block `[Column_useremail]` von `settings/subitemscolumns.ini`, mit 9 Einstellung(en).

Bearbeiten Sie `settings/subitemscolumns.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitemscolumns.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitemscolumns.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitemscolumns.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `E-mail`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_useremail]
Name=E-mail
*/ ?>
```

## Group

Standard: `Users`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_useremail]
Group=Users
*/ ?>
```

## Type

Standard: `text`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_useremail]
Type=text
*/ ?>
```

## Class

Standard: `expSubitemsUserColumn`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_useremail]
Class=expSubitemsUserColumn
*/ ?>
```

## Field

Standard: `email`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_useremail]
Field=email
*/ ?>
```

## Policy

Standard: eine Liste mit 1: `role/assign`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_useremail]
# ein leeres "Policy[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Policy[]
Policy[]=role/assign
*/ ?>
```

## Copy

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_useremail]
Copy=true
*/ ?>
```

## Order

Standard: `1305`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_useremail]
Order=1305
*/ ?>
```

## Description

Standard: `The user's e-mail address. Only for users who may assign roles.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_useremail]
Description=The user's e-mail address. Only for users who may assign roles.
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitemscolumns.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitemscolumns.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
