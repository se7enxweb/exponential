---
title: subitemscolumns.ini [Column_missingtranslations]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Column_missingtranslations] in subitemscolumns.ini: Name, Group, Type, Class, Field, Order, Description"
keywords: Einstellung, subitemscolumns.ini, Column_missingtranslations, INI, setting, Name, Group, Type, Class, Field, Order, Description
type: generated
generated: exp:help:examples
---
# subitemscolumns.ini [Column_missingtranslations]

Der Block `[Column_missingtranslations]` von `settings/subitemscolumns.ini`, mit 7 Einstellung(en).

Bearbeiten Sie `settings/subitemscolumns.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitemscolumns.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitemscolumns.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitemscolumns.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Missing translations`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_missingtranslations]
Name=Missing translations
*/ ?>
```

## Group

Standard: `Translations`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_missingtranslations]
Group=Translations
*/ ?>
```

## Type

Standard: `list`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_missingtranslations]
Type=list
*/ ?>
```

## Class

Standard: `expSubitemsObjectColumn`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_missingtranslations]
Class=expSubitemsObjectColumn
*/ ?>
```

## Field

Standard: `missing_translations`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_missingtranslations]
Field=missing_translations
*/ ?>
```

## Order

Standard: `1115`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_missingtranslations]
Order=1115
*/ ?>
```

## Description

Standard: `The installation's languages the item is not translated into.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_missingtranslations]
Description=The installation's languages the item is not translated into.
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitemscolumns.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitemscolumns.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
