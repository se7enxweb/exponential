---
title: subitemscolumns.ini [Column_rejectedcount]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Column_rejectedcount] in subitemscolumns.ini: Name, Group, Type, Class, Field, Align, Order, Description"
keywords: Einstellung, subitemscolumns.ini, Column_rejectedcount, INI, setting, Name, Group, Type, Class, Field, Align, Order, Description
type: generated
generated: exp:help:examples
---
# subitemscolumns.ini [Column_rejectedcount]

Der Block `[Column_rejectedcount]` von `settings/subitemscolumns.ini`, mit 8 Einstellung(en).

Bearbeiten Sie `settings/subitemscolumns.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitemscolumns.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitemscolumns.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitemscolumns.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Rejected versions`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_rejectedcount]
Name=Rejected versions
*/ ?>
```

## Group

Standard: `Workflow`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_rejectedcount]
Group=Workflow
*/ ?>
```

## Type

Standard: `number`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_rejectedcount]
Type=number
*/ ?>
```

## Class

Standard: `expSubitemsVersionColumn`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_rejectedcount]
Class=expSubitemsVersionColumn
*/ ?>
```

## Field

Standard: `rejected_count`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_rejectedcount]
Field=rejected_count
*/ ?>
```

## Align

Standard: `right`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_rejectedcount]
Align=right
*/ ?>
```

## Order

Standard: `1215`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_rejectedcount]
Order=1215
*/ ?>
```

## Description

Standard: `Versions an approver rejected.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_rejectedcount]
Description=Versions an approver rejected.
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitemscolumns.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitemscolumns.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
