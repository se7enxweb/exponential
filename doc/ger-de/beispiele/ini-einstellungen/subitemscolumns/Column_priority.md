---
title: subitemscolumns.ini [Column_priority]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Column_priority] in subitemscolumns.ini: Builtin, Name, Group, SortField, Align, Order, Description"
keywords: Einstellung, subitemscolumns.ini, Column_priority, INI, setting, Builtin, Name, Group, SortField, Align, Order, Description
type: generated
generated: exp:help:examples
---
# subitemscolumns.ini [Column_priority]

Der Block `[Column_priority]` von `settings/subitemscolumns.ini`, mit 7 Einstellung(en).

Bearbeiten Sie `settings/subitemscolumns.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitemscolumns.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitemscolumns.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitemscolumns.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Builtin

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_priority]
Builtin=true
*/ ?>
```

## Name

Standard: `Priority`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_priority]
Name=Priority
*/ ?>
```

## Group

Standard: `Basic`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_priority]
Group=Basic
*/ ?>
```

## SortField

Standard: `priority`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_priority]
SortField=priority
*/ ?>
```

## Align

Standard: `right`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_priority]
Align=right
*/ ?>
```

## Order

Standard: `150`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_priority]
Order=150
*/ ?>
```

## Description

Standard: `The priority within the parent; editable in place when the parent sorts by priority.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_priority]
Description=The priority within the parent; editable in place when the parent sorts by priority.
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitemscolumns.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitemscolumns.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
