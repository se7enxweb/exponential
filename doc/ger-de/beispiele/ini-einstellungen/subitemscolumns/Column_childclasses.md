---
title: subitemscolumns.ini [Column_childclasses]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Column_childclasses] in subitemscolumns.ini: Name, Group, Type, Class, Field, Order, Description"
keywords: Einstellung, subitemscolumns.ini, Column_childclasses, INI, setting, Name, Group, Type, Class, Field, Order, Description
type: generated
generated: exp:help:examples
---
# subitemscolumns.ini [Column_childclasses]

Der Block `[Column_childclasses]` von `settings/subitemscolumns.ini`, mit 7 Einstellung(en).

Bearbeiten Sie `settings/subitemscolumns.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitemscolumns.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitemscolumns.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitemscolumns.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Child classes`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_childclasses]
Name=Child classes
*/ ?>
```

## Group

Standard: `Node`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_childclasses]
Group=Node
*/ ?>
```

## Type

Standard: `list`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_childclasses]
Type=list
*/ ?>
```

## Class

Standard: `expSubitemsNodeColumn`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_childclasses]
Class=expSubitemsNodeColumn
*/ ?>
```

## Field

Standard: `child_classes`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_childclasses]
Field=child_classes
*/ ?>
```

## Order

Standard: `260`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_childclasses]
Order=260
*/ ?>
```

## Description

Standard: `The classes of the direct children with their numbers, most frequent first. One grouped query per row; counts every child.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_childclasses]
Description=The classes of the direct children with their numbers, most frequent first. One grouped query per row; counts every child.
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitemscolumns.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitemscolumns.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
