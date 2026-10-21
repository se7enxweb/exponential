---
title: subitemscolumns.ini [Column_invisible]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Column_invisible] in subitemscolumns.ini: Name, Group, Type, Class, Field, SortField, Order, Description"
keywords: Einstellung, subitemscolumns.ini, Column_invisible, INI, setting, Name, Group, Type, Class, Field, SortField, Order, Description
type: generated
generated: exp:help:examples
---
# subitemscolumns.ini [Column_invisible]

Der Block `[Column_invisible]` von `settings/subitemscolumns.ini`, mit 8 Einstellung(en).

Bearbeiten Sie `settings/subitemscolumns.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitemscolumns.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitemscolumns.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitemscolumns.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Invisible`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_invisible]
Name=Invisible
*/ ?>
```

## Group

Standard: `Node`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_invisible]
Group=Node
*/ ?>
```

## Type

Standard: `bool`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_invisible]
Type=bool
*/ ?>
```

## Class

Standard: `expSubitemsNodeColumn`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_invisible]
Class=expSubitemsNodeColumn
*/ ?>
```

## Field

Standard: `is_invisible`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_invisible]
Field=is_invisible
*/ ?>
```

## SortField

Standard: `visibility`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_invisible]
SortField=visibility
*/ ?>
```

## Order

Standard: `240`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_invisible]
Order=240
*/ ?>
```

## Description

Standard: `Not shown to visitors: hidden itself or below a hidden location.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_invisible]
Description=Not shown to visitors: hidden itself or below a hidden location.
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitemscolumns.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitemscolumns.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
