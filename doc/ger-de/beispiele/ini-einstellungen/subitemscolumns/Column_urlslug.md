---
title: subitemscolumns.ini [Column_urlslug]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Column_urlslug] in subitemscolumns.ini: Name, Group, Type, Class, Field, Copy, Order, Description"
keywords: Einstellung, subitemscolumns.ini, Column_urlslug, INI, setting, Name, Group, Type, Class, Field, Copy, Order, Description
type: generated
generated: exp:help:examples
---
# subitemscolumns.ini [Column_urlslug]

Der Block `[Column_urlslug]` von `settings/subitemscolumns.ini`, mit 8 Einstellung(en).

Bearbeiten Sie `settings/subitemscolumns.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitemscolumns.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitemscolumns.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitemscolumns.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `URL slug`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_urlslug]
Name=URL slug
*/ ?>
```

## Group

Standard: `URLs`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_urlslug]
Group=URLs
*/ ?>
```

## Type

Standard: `code`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_urlslug]
Type=code
*/ ?>
```

## Class

Standard: `expSubitemsURLColumn`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_urlslug]
Class=expSubitemsURLColumn
*/ ?>
```

## Field

Standard: `url_slug`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_urlslug]
Field=url_slug
*/ ?>
```

## Copy

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_urlslug]
Copy=true
*/ ?>
```

## Order

Standard: `620`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_urlslug]
Order=620
*/ ?>
```

## Description

Standard: `The last element of the URL alias.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_urlslug]
Description=The last element of the URL alias.
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitemscolumns.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitemscolumns.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
