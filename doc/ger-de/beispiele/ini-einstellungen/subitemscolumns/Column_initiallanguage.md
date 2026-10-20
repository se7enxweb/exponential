---
title: subitemscolumns.ini [Column_initiallanguage]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Column_initiallanguage] in subitemscolumns.ini: Name, Group, Type, Class, Field, Order, Description"
keywords: Einstellung, subitemscolumns.ini, Column_initiallanguage, INI, setting, Name, Group, Type, Class, Field, Order, Description
type: generated
generated: exp:help:examples
---
# subitemscolumns.ini [Column_initiallanguage]

Der Block `[Column_initiallanguage]` von `settings/subitemscolumns.ini`, mit 7 Einstellung(en).

> ---------------------------------------------------------------------------------------------  
> Translations  

Bearbeiten Sie `settings/subitemscolumns.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitemscolumns.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitemscolumns.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitemscolumns.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Initial language`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_initiallanguage]
Name=Initial language
*/ ?>
```

## Group

Standard: `Translations`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_initiallanguage]
Group=Translations
*/ ?>
```

## Type

Standard: `code`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_initiallanguage]
Type=code
*/ ?>
```

## Class

Standard: `expSubitemsObjectColumn`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_initiallanguage]
Class=expSubitemsObjectColumn
*/ ?>
```

## Field

Standard: `initial_language`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_initiallanguage]
Field=initial_language
*/ ?>
```

## Order

Standard: `1100`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_initiallanguage]
Order=1100
*/ ?>
```

## Description

Standard: `The language the item was first written in.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_initiallanguage]
Description=The language the item was first written in.
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitemscolumns.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitemscolumns.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
