---
title: subitemscolumns.ini [Column_publicpath]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Column_publicpath] in subitemscolumns.ini: Name, Group, Type, Class, Field, Copy, Order, Description ..."
keywords: Einstellung, subitemscolumns.ini, Column_publicpath, INI, setting, Name, Group, Type, Class, Field, Copy, Order, Description, SiteAccess
type: generated
generated: exp:help:examples
---
# subitemscolumns.ini [Column_publicpath]

Der Block `[Column_publicpath]` von `settings/subitemscolumns.ini`, mit 9 Einstellung(en).

Bearbeiten Sie `settings/subitemscolumns.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitemscolumns.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitemscolumns.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitemscolumns.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Public path`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_publicpath]
Name=Public path
*/ ?>
```

## Group

Standard: `URLs`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_publicpath]
Group=URLs
*/ ?>
```

## Type

Standard: `code`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_publicpath]
Type=code
*/ ?>
```

## Class

Standard: `expSubitemsURLColumn`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_publicpath]
Class=expSubitemsURLColumn
*/ ?>
```

## Field

Standard: `public_path`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_publicpath]
Field=public_path
*/ ?>
```

## Copy

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_publicpath]
Copy=true
*/ ?>
```

## Order

Standard: `610`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_publicpath]
Order=610
*/ ?>
```

## Description

Standard: `The path visitors use on the public siteaccess: the URL alias without that siteaccess's PathPrefix.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_publicpath]
Description=The path visitors use on the public siteaccess: the URL alias without that siteaccess's PathPrefix.
*/ ?>
```

## SiteAccess

> Der öffentliche Siteaccess; leer: site.ini \[SiteSettings\] DefaultAccess  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_publicpath]
SiteAccess=
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitemscolumns.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitemscolumns.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
