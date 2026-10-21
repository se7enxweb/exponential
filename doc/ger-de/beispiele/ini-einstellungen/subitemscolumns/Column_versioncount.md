---
title: subitemscolumns.ini [Column_versioncount]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Column_versioncount] in subitemscolumns.ini: Name, Group, Type, Class, Field, Align, Order, Description"
keywords: Einstellung, subitemscolumns.ini, Column_versioncount, INI, setting, Name, Group, Type, Class, Field, Align, Order, Description
type: generated
generated: exp:help:examples
---
# subitemscolumns.ini [Column_versioncount]

Der Block `[Column_versioncount]` von `settings/subitemscolumns.ini`, mit 8 Einstellung(en).

Bearbeiten Sie `settings/subitemscolumns.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitemscolumns.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitemscolumns.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitemscolumns.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Versions`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_versioncount]
Name=Versions
*/ ?>
```

## Group

Standard: `Version`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_versioncount]
Group=Version
*/ ?>
```

## Type

Standard: `number`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_versioncount]
Type=number
*/ ?>
```

## Class

Standard: `expSubitemsVersionColumn`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_versioncount]
Class=expSubitemsVersionColumn
*/ ?>
```

## Field

Standard: `version_count`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_versioncount]
Field=version_count
*/ ?>
```

## Align

Standard: `right`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_versioncount]
Align=right
*/ ?>
```

## Order

Standard: `405`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_versioncount]
Order=405
*/ ?>
```

## Description

Standard: `Every stored version: published, archived, drafts and the rest.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_versioncount]
Description=Every stored version: published, archived, drafts and the rest.
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitemscolumns.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitemscolumns.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
