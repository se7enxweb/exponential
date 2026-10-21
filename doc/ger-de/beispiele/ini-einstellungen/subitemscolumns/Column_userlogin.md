---
title: subitemscolumns.ini [Column_userlogin]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Column_userlogin] in subitemscolumns.ini: Name, Group, Type, Class, Field, Policy, Copy, Order ..."
keywords: Einstellung, subitemscolumns.ini, Column_userlogin, INI, setting, Name, Group, Type, Class, Field, Policy, Copy, Order, Description
type: generated
generated: exp:help:examples
---
# subitemscolumns.ini [Column_userlogin]

Der Block `[Column_userlogin]` von `settings/subitemscolumns.ini`, mit 9 Einstellung(en).

> ---------------------------------------------------------------------------------------------  
> Users: Benutzerkonten (Zeilen unter einer Benutzergruppe; für alles andere leer). Personenbezogene Daten, daher  
> braucht jede Spalte eine Richtlinie.  

Bearbeiten Sie `settings/subitemscolumns.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitemscolumns.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitemscolumns.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitemscolumns.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Login`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_userlogin]
Name=Login
*/ ?>
```

## Group

Standard: `Users`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_userlogin]
Group=Users
*/ ?>
```

## Type

Standard: `code`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_userlogin]
Type=code
*/ ?>
```

## Class

Standard: `expSubitemsUserColumn`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_userlogin]
Class=expSubitemsUserColumn
*/ ?>
```

## Field

Standard: `login`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_userlogin]
Field=login
*/ ?>
```

## Policy

Standard: eine Liste mit 1: `role/read`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_userlogin]
# ein leeres "Policy[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Policy[]
Policy[]=role/read
*/ ?>
```

## Copy

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_userlogin]
Copy=true
*/ ?>
```

## Order

Standard: `1300`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_userlogin]
Order=1300
*/ ?>
```

## Description

Standard: `The user's login name.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_userlogin]
Description=The user's login name.
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitemscolumns.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitemscolumns.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
