---
title: subitemscolumns.ini [Column_allaliases]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Column_allaliases] in subitemscolumns.ini: Name, Group, Type, Class, Field, Copy, Order, Description"
keywords: Einstellung, subitemscolumns.ini, Column_allaliases, INI, setting, Name, Group, Type, Class, Field, Copy, Order, Description
type: generated
generated: exp:help:examples
---
# subitemscolumns.ini [Column_allaliases]

Der Block `[Column_allaliases]` von `settings/subitemscolumns.ini`, mit 8 Einstellung(en).

Bearbeiten Sie `settings/subitemscolumns.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitemscolumns.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitemscolumns.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitemscolumns.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `All URL aliases`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_allaliases]
Name=All URL aliases
*/ ?>
```

## Group

Standard: `URLs`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_allaliases]
Group=URLs
*/ ?>
```

## Type

Standard: `list`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_allaliases]
Type=list
*/ ?>
```

## Class

Standard: `expSubitemsURLColumn`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_allaliases]
Class=expSubitemsURLColumn
*/ ?>
```

## Field

Standard: `all_aliases`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_allaliases]
Field=all_aliases
*/ ?>
```

## Copy

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_allaliases]
Copy=true
*/ ?>
```

## Order

Standard: `602`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_allaliases]
Order=602
*/ ?>
```

## Description

Standard: `Every own URL alias of the item, in all its languages (/ is the site root). Shares the alias query with the other alias columns.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_allaliases]
Description=Every own URL alias of the item, in all its languages (/ is the site root). Shares the alias query with the other alias columns.
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitemscolumns.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitemscolumns.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
