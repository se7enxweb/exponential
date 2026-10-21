---
title: subitemscolumns.ini [Column_editlink]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Column_editlink] in subitemscolumns.ini: Name, Group, Type, Handler, Copy, Order, Description"
keywords: Einstellung, subitemscolumns.ini, Column_editlink, INI, setting, Name, Group, Type, Handler, Copy, Order, Description
type: generated
generated: exp:help:examples
---
# subitemscolumns.ini [Column_editlink]

Der Block `[Column_editlink]` von `settings/subitemscolumns.ini`, mit 7 Einstellung(en).

Bearbeiten Sie `settings/subitemscolumns.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitemscolumns.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitemscolumns.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitemscolumns.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Edit link`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_editlink]
Name=Edit link
*/ ?>
```

## Group

Standard: `Custom`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_editlink]
Group=Custom
*/ ?>
```

## Type

Standard: `link`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_editlink]
Type=link
*/ ?>
```

## Handler

Standard: `expSubitemsColumnHandlers::editLink`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_editlink]
Handler=expSubitemsColumnHandlers::editLink
*/ ?>
```

## Copy

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_editlink]
Copy=true
*/ ?>
```

## Order

Standard: `1605`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_editlink]
Order=1605
*/ ?>
```

## Description

Standard: `The address of the item's edit page, when you may edit it (a Handler column).`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_editlink]
Description=The address of the item's edit page, when you may edit it (a Handler column).
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitemscolumns.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitemscolumns.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
