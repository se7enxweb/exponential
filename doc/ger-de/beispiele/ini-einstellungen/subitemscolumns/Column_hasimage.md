---
title: subitemscolumns.ini [Column_hasimage]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Column_hasimage] in subitemscolumns.ini: Name, Group, Type, Class, Field, Order, Description"
keywords: Einstellung, subitemscolumns.ini, Column_hasimage, INI, setting, Name, Group, Type, Class, Field, Order, Description
type: generated
generated: exp:help:examples
---
# subitemscolumns.ini [Column_hasimage]

Der Block `[Column_hasimage]` von `settings/subitemscolumns.ini`, mit 7 Einstellung(en).

> ---------------------------------------------------------------------------------------------  
> Media: images and files  

Bearbeiten Sie `settings/subitemscolumns.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitemscolumns.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitemscolumns.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitemscolumns.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Has image`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_hasimage]
Name=Has image
*/ ?>
```

## Group

Standard: `Media`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_hasimage]
Group=Media
*/ ?>
```

## Type

Standard: `bool`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_hasimage]
Type=bool
*/ ?>
```

## Class

Standard: `expSubitemsMediaColumn`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_hasimage]
Class=expSubitemsMediaColumn
*/ ?>
```

## Field

Standard: `has_image`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_hasimage]
Field=has_image
*/ ?>
```

## Order

Standard: `1400`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_hasimage]
Order=1400
*/ ?>
```

## Description

Standard: `Whether the item has an image; empty when its class has no image attribute.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_hasimage]
Description=Whether the item has an image; empty when its class has no image attribute.
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitemscolumns.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitemscolumns.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
