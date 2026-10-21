---
title: subitemscolumns.ini [Column_titlelength]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Column_titlelength] in subitemscolumns.ini: Name, Group, Type, Class, Field, Align, Order, Description ..."
keywords: Einstellung, subitemscolumns.ini, Column_titlelength, INI, setting, Name, Group, Type, Class, Field, Align, Order, Description, SiteAccess, TitleFormat
type: generated
generated: exp:help:examples
---
# subitemscolumns.ini [Column_titlelength]

Der Block `[Column_titlelength]` von `settings/subitemscolumns.ini`, mit 10 Einstellung(en).

Bearbeiten Sie `settings/subitemscolumns.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitemscolumns.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitemscolumns.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitemscolumns.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Page title length`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_titlelength]
Name=Page title length
*/ ?>
```

## Group

Standard: `SEO`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_titlelength]
Group=SEO
*/ ?>
```

## Type

Standard: `number`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_titlelength]
Type=number
*/ ?>
```

## Class

Standard: `expSubitemsSEOColumn`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_titlelength]
Class=expSubitemsSEOColumn
*/ ?>
```

## Field

Standard: `title_length`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_titlelength]
Field=title_length
*/ ?>
```

## Align

Standard: `right`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_titlelength]
Align=right
*/ ?>
```

## Order

Standard: `705`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_titlelength]
Order=705
*/ ?>
```

## Description

Standard: `Characters in the page title; search engines show about 60.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_titlelength]
Description=Characters in the page title; search engines show about 60.
*/ ?>
```

## SiteAccess

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_titlelength]
SiteAccess=
*/ ?>
```

## TitleFormat

Standard: `name`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_titlelength]
TitleFormat=name
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitemscolumns.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitemscolumns.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
