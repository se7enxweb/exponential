---
title: subitemscolumns.ini [Column_published]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Column_published] in subitemscolumns.ini: Builtin, Name, Group, SortField, Order, Description"
keywords: Einstellung, subitemscolumns.ini, Column_published, INI, setting, Builtin, Name, Group, SortField, Order, Description
type: generated
generated: exp:help:examples
---
# subitemscolumns.ini [Column_published]

Der Block `[Column_published]` von `settings/subitemscolumns.ini`, mit 6 Einstellung(en).

Bearbeiten Sie `settings/subitemscolumns.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitemscolumns.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitemscolumns.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitemscolumns.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Builtin

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_published]
Builtin=true
*/ ?>
```

## Name

Standard: `Published`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_published]
Name=Published
*/ ?>
```

## Group

Standard: `Dates`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_published]
Group=Dates
*/ ?>
```

## SortField

Standard: `published`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_published]
SortField=published
*/ ?>
```

## Order

Standard: `70`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_published]
Order=70
*/ ?>
```

## Description

Standard: `When the item was first published.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_published]
Description=When the item was first published.
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitemscolumns.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitemscolumns.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
