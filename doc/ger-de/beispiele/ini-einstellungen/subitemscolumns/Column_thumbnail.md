---
title: subitemscolumns.ini [Column_thumbnail]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Column_thumbnail] in subitemscolumns.ini: Builtin, Name, Group, Order, Description"
keywords: Einstellung, subitemscolumns.ini, Column_thumbnail, INI, setting, Builtin, Name, Group, Order, Description
type: generated
generated: exp:help:examples
---
# subitemscolumns.ini [Column_thumbnail]

Der Block `[Column_thumbnail]` von `settings/subitemscolumns.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/subitemscolumns.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitemscolumns.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitemscolumns.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitemscolumns.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Builtin

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_thumbnail]
Builtin=true
*/ ?>
```

## Name

Standard: `Thumbnail`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_thumbnail]
Name=Thumbnail
*/ ?>
```

## Group

Standard: `Basic`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_thumbnail]
Group=Basic
*/ ?>
```

## Order

Standard: `10`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_thumbnail]
Order=10
*/ ?>
```

## Description

Standard: `A small preview of the item's image, or its class icon.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_thumbnail]
Description=A small preview of the item's image, or its class icon.
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitemscolumns.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitemscolumns.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
