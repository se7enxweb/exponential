---
title: subitemscolumns.ini [Column_publicurl]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Column_publicurl] in subitemscolumns.ini: Name, Group, Type, Class, Field, Copy, Order, Description ..."
keywords: Einstellung, subitemscolumns.ini, Column_publicurl, INI, setting, Name, Group, Type, Class, Field, Copy, Order, Description, SiteAccess, Scheme
type: generated
generated: exp:help:examples
---
# subitemscolumns.ini [Column_publicurl]

Der Block `[Column_publicurl]` von `settings/subitemscolumns.ini`, mit 10 Einstellung(en).

Bearbeiten Sie `settings/subitemscolumns.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitemscolumns.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitemscolumns.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitemscolumns.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Public URL`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_publicurl]
Name=Public URL
*/ ?>
```

## Group

Standard: `URLs`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_publicurl]
Group=URLs
*/ ?>
```

## Type

Standard: `link`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_publicurl]
Type=link
*/ ?>
```

## Class

Standard: `expSubitemsURLColumn`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_publicurl]
Class=expSubitemsURLColumn
*/ ?>
```

## Field

Standard: `public_url`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_publicurl]
Field=public_url
*/ ?>
```

## Copy

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_publicurl]
Copy=true
*/ ?>
```

## Order

Standard: `615`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_publicurl]
Order=615
*/ ?>
```

## Description

Standard: `The full address on the public site: the public siteaccess's SiteURL and the public path.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_publicurl]
Description=The full address on the public site: the public siteaccess's SiteURL and the public path.
*/ ?>
```

## SiteAccess

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_publicurl]
SiteAccess=
*/ ?>
```

## Scheme

> Schema, das SiteURL vorangestellt wird, wenn sie keines hat: https oder http  

Standard: `https`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_publicurl]
Scheme=https
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitemscolumns.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitemscolumns.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
