---
title: subitemscolumns.ini [Column_tagcount]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Column_tagcount] in subitemscolumns.ini: Name, Group, Type, Class, Field, Align, Order, Description"
keywords: Einstellung, subitemscolumns.ini, Column_tagcount, INI, setting, Name, Group, Type, Class, Field, Align, Order, Description
type: generated
generated: exp:help:examples
---
# subitemscolumns.ini [Column_tagcount]

Der Block `[Column_tagcount]` von `settings/subitemscolumns.ini`, mit 8 Einstellung(en).

Bearbeiten Sie `settings/subitemscolumns.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitemscolumns.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitemscolumns.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitemscolumns.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Tag count`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_tagcount]
Name=Tag count
*/ ?>
```

## Group

Standard: `Relations`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_tagcount]
Group=Relations
*/ ?>
```

## Type

Standard: `number`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_tagcount]
Type=number
*/ ?>
```

## Class

Standard: `expSubitemsRelationColumn`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_tagcount]
Class=expSubitemsRelationColumn
*/ ?>
```

## Field

Standard: `tag_count`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_tagcount]
Field=tag_count
*/ ?>
```

## Align

Standard: `right`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_tagcount]
Align=right
*/ ?>
```

## Order

Standard: `1025`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_tagcount]
Order=1025
*/ ?>
```

## Description

Standard: `How many tags the item has (needs the eztags extension).`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_tagcount]
Description=How many tags the item has (needs the eztags extension).
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitemscolumns.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitemscolumns.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
