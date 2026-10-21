---
title: subitemscolumns.ini [Column_modifiediso]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Column_modifiediso] in subitemscolumns.ini: Name, Group, Type, Class, Field, SortField, Copy, Order ..."
keywords: Einstellung, subitemscolumns.ini, Column_modifiediso, INI, setting, Name, Group, Type, Class, Field, SortField, Copy, Order, Description
type: generated
generated: exp:help:examples
---
# subitemscolumns.ini [Column_modifiediso]

Der Block `[Column_modifiediso]` von `settings/subitemscolumns.ini`, mit 9 Einstellung(en).

Bearbeiten Sie `settings/subitemscolumns.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitemscolumns.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitemscolumns.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitemscolumns.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Modified (ISO 8601)`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_modifiediso]
Name=Modified (ISO 8601)
*/ ?>
```

## Group

Standard: `Dates`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_modifiediso]
Group=Dates
*/ ?>
```

## Type

Standard: `code`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_modifiediso]
Type=code
*/ ?>
```

## Class

Standard: `expSubitemsDateColumn`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_modifiediso]
Class=expSubitemsDateColumn
*/ ?>
```

## Field

Standard: `modified_iso`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_modifiediso]
Field=modified_iso
*/ ?>
```

## SortField

Standard: `modified`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_modifiediso]
SortField=modified
*/ ?>
```

## Copy

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_modifiediso]
Copy=true
*/ ?>
```

## Order

Standard: `830`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_modifiediso]
Order=830
*/ ?>
```

## Description

Standard: `The modification time as 2026-10-02T09:30:00+00:00, for copying.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_modifiediso]
Description=The modification time as 2026-10-02T09:30:00+00:00, for copying.
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitemscolumns.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitemscolumns.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
