---
title: subitemscolumns.ini [Column_urlalias]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Column_urlalias] in subitemscolumns.ini: Name, Group, Type, Class, Field, SortField, Copy, Order ..."
keywords: Einstellung, subitemscolumns.ini, Column_urlalias, INI, setting, Name, Group, Type, Class, Field, SortField, Copy, Order, Description
type: generated
generated: exp:help:examples
---
# subitemscolumns.ini [Column_urlalias]

Der Block `[Column_urlalias]` von `settings/subitemscolumns.ini`, mit 9 Einstellung(en).

> ---------------------------------------------------------------------------------------------  
> URLs  

Bearbeiten Sie `settings/subitemscolumns.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitemscolumns.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitemscolumns.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitemscolumns.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `URL alias`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_urlalias]
Name=URL alias
*/ ?>
```

## Group

Standard: `URLs`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_urlalias]
Group=URLs
*/ ?>
```

## Type

Standard: `code`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_urlalias]
Type=code
*/ ?>
```

## Class

Standard: `expSubitemsURLColumn`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_urlalias]
Class=expSubitemsURLColumn
*/ ?>
```

## Field

Standard: `url_alias`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_urlalias]
Field=url_alias
*/ ?>
```

## SortField

Standard: `path_string`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_urlalias]
SortField=path_string
*/ ?>
```

## Copy

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_urlalias]
Copy=true
*/ ?>
```

## Order

Standard: `600`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_urlalias]
Order=600
*/ ?>
```

## Description

Standard: `The item's URL alias as the admin uses it (the path from the content root); / for the node at the site root.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_urlalias]
Description=The item's URL alias as the admin uses it (the path from the content root); / for the node at the site root.
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitemscolumns.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitemscolumns.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
