---
title: subitemscolumns.ini [Column_sectionidentifier]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Column_sectionidentifier] in subitemscolumns.ini: Name, Group, Type, Class, Field, SortField, Copy, Order ..."
keywords: Einstellung, subitemscolumns.ini, Column_sectionidentifier, INI, setting, Name, Group, Type, Class, Field, SortField, Copy, Order, Description
type: generated
generated: exp:help:examples
---
# subitemscolumns.ini [Column_sectionidentifier]

Der Block `[Column_sectionidentifier]` von `settings/subitemscolumns.ini`, mit 9 Einstellung(en).

Bearbeiten Sie `settings/subitemscolumns.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitemscolumns.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitemscolumns.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitemscolumns.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Section identifier`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_sectionidentifier]
Name=Section identifier
*/ ?>
```

## Group

Standard: `Object`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_sectionidentifier]
Group=Object
*/ ?>
```

## Type

Standard: `code`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_sectionidentifier]
Type=code
*/ ?>
```

## Class

Standard: `expSubitemsObjectColumn`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_sectionidentifier]
Class=expSubitemsObjectColumn
*/ ?>
```

## Field

Standard: `section_identifier`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_sectionidentifier]
Field=section_identifier
*/ ?>
```

## SortField

Standard: `section`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_sectionidentifier]
SortField=section
*/ ?>
```

## Copy

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_sectionidentifier]
Copy=true
*/ ?>
```

## Order

Standard: `315`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_sectionidentifier]
Order=315
*/ ?>
```

## Description

Standard: `The identifier of the item's section (sorting orders by section ID).`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_sectionidentifier]
Description=The identifier of the item's section (sorting orders by section ID).
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitemscolumns.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitemscolumns.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
