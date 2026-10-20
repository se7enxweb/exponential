---
title: subitemscolumns.ini [Column_teaser]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Column_teaser] in subitemscolumns.ini: Name, Group, Type, Template, Order, Description, Attributes, Length"
keywords: Einstellung, subitemscolumns.ini, Column_teaser, INI, setting, Name, Group, Type, Template, Order, Description, Attributes, Length
type: generated
generated: exp:help:examples
---
# subitemscolumns.ini [Column_teaser]

Der Block `[Column_teaser]` von `settings/subitemscolumns.ini`, mit 8 Einstellung(en).

> ---------------------------------------------------------------------------------------------  
> Custom: examples of the three kinds of custom column (see the guide, "Adding a column in  
> 3 minutes"). Copy one, give it a key of your own, and change what it does.  

Bearbeiten Sie `settings/subitemscolumns.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitemscolumns.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitemscolumns.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitemscolumns.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Teaser`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_teaser]
Name=Teaser
*/ ?>
```

## Group

Standard: `Custom`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_teaser]
Group=Custom
*/ ?>
```

## Type

Standard: `html`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_teaser]
Type=html
*/ ?>
```

## Template

Standard: `design:subitems/columns/teaser.tpl`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_teaser]
Template=design:subitems/columns/teaser.tpl
*/ ?>
```

## Order

Standard: `1600`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_teaser]
Order=1600
*/ ?>
```

## Description

Standard: `The first words of the intro, description or body (a template column).`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_teaser]
Description=The first words of the intro, description or body (a template column).
*/ ?>
```

## Attributes

> Attribute identifiers tried in this order  

Standard: eine Liste mit 4: `intro`, `short_description`, `description`, `body`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_teaser]
# ein leeres "Attributes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Attributes[]
Attributes[]=intro
Attributes[]=short_description
Attributes[]=description
Attributes[]=body
*/ ?>
```

## Length

> The most characters shown  

Standard: `120`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_teaser]
Length=120
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitemscolumns.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitemscolumns.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
