---
title: subitemscolumns.ini [Column_systemurl]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Column_systemurl] in subitemscolumns.ini: Name, Group, Type, Class, Field, Copy, Order, Description"
keywords: Einstellung, subitemscolumns.ini, Column_systemurl, INI, setting, Name, Group, Type, Class, Field, Copy, Order, Description
type: generated
generated: exp:help:examples
---
# subitemscolumns.ini [Column_systemurl]

Der Block `[Column_systemurl]` von `settings/subitemscolumns.ini`, mit 8 Einstellung(en).

Bearbeiten Sie `settings/subitemscolumns.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitemscolumns.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitemscolumns.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitemscolumns.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `System URL`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_systemurl]
Name=System URL
*/ ?>
```

## Group

Standard: `URLs`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_systemurl]
Group=URLs
*/ ?>
```

## Type

Standard: `code`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_systemurl]
Type=code
*/ ?>
```

## Class

Standard: `expSubitemsURLColumn`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_systemurl]
Class=expSubitemsURLColumn
*/ ?>
```

## Field

Standard: `system_url`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_systemurl]
Field=system_url
*/ ?>
```

## Copy

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_systemurl]
Copy=true
*/ ?>
```

## Order

Standard: `605`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_systemurl]
Order=605
*/ ?>
```

## Description

Standard: `content/view/full/<node id>: the address that always works, whatever the aliases.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_systemurl]
Description=content/view/full/<node id>: the address that always works, whatever the aliases.
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitemscolumns.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitemscolumns.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
