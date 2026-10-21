---
title: subitemscolumns.ini [Column_relatednames]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Column_relatednames] in subitemscolumns.ini: Name, Group, Type, Class, Field, Order, Description, Limit"
keywords: Einstellung, subitemscolumns.ini, Column_relatednames, INI, setting, Name, Group, Type, Class, Field, Order, Description, Limit
type: generated
generated: exp:help:examples
---
# subitemscolumns.ini [Column_relatednames]

Der Block `[Column_relatednames]` von `settings/subitemscolumns.ini`, mit 8 Einstellung(en).

Bearbeiten Sie `settings/subitemscolumns.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitemscolumns.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitemscolumns.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitemscolumns.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Related object names`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_relatednames]
Name=Related object names
*/ ?>
```

## Group

Standard: `Relations`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_relatednames]
Group=Relations
*/ ?>
```

## Type

Standard: `list`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_relatednames]
Type=list
*/ ?>
```

## Class

Standard: `expSubitemsRelationColumn`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_relatednames]
Class=expSubitemsRelationColumn
*/ ?>
```

## Field

Standard: `related_names`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_relatednames]
Field=related_names
*/ ?>
```

## Order

Standard: `1005`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_relatednames]
Order=1005
*/ ?>
```

## Description

Standard: `The names of the first related objects you may read, by name.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_relatednames]
Description=The names of the first related objects you may read, by name.
*/ ?>
```

## Limit

> Die meisten angezeigten Namen  

Standard: `10`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_relatednames]
Limit=10
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitemscolumns.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitemscolumns.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
