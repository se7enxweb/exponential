---
title: subitemscolumns.ini [Column_pagetitle]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Column_pagetitle] in subitemscolumns.ini: Name, Group, Type, Class, Field, Copy, Order, Description ..."
keywords: Einstellung, subitemscolumns.ini, Column_pagetitle, INI, setting, Name, Group, Type, Class, Field, Copy, Order, Description, SiteAccess, TitleFormat
type: generated
generated: exp:help:examples
---
# subitemscolumns.ini [Column_pagetitle]

Der Block `[Column_pagetitle]` von `settings/subitemscolumns.ini`, mit 10 Einstellung(en).

> ---------------------------------------------------------------------------------------------  
> SEO  

Bearbeiten Sie `settings/subitemscolumns.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitemscolumns.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitemscolumns.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitemscolumns.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Page title in the browser`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_pagetitle]
Name=Page title in the browser
*/ ?>
```

## Group

Standard: `SEO`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_pagetitle]
Group=SEO
*/ ?>
```

## Type

Standard: `text`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_pagetitle]
Type=text
*/ ?>
```

## Class

Standard: `expSubitemsSEOColumn`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_pagetitle]
Class=expSubitemsSEOColumn
*/ ?>
```

## Field

Standard: `page_title`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_pagetitle]
Field=page_title
*/ ?>
```

## Copy

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_pagetitle]
Copy=true
*/ ?>
```

## Order

Standard: `700`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_pagetitle]
Order=700
*/ ?>
```

## Description

Standard: `The public site's title for the item's page, worked out as its pagelayout does, without rendering the page.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_pagetitle]
Description=The public site's title for the item's page, worked out as its pagelayout does, without rendering the page.
*/ ?>
```

## SiteAccess

> Der öffentliche Siteaccess; leer: site.ini \[SiteSettings\] DefaultAccess  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_pagetitle]
SiteAccess=
*/ ?>
```

## TitleFormat

> Die Regel des öffentlichen Designs: name = "<meta title or name> - <SiteName>" (das Design media),  
> path = "<name> / <parent> / ... - <SiteName>" (design/standard und page\_head.tpl von ezwebin)  

Standard: `name`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_pagetitle]
TitleFormat=name
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitemscolumns.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitemscolumns.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
