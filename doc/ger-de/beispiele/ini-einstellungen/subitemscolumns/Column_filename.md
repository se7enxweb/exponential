---
title: subitemscolumns.ini [Column_filename]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Column_filename] in subitemscolumns.ini: Name, Group, Type, Class, Field, Copy, Order, Description"
keywords: Einstellung, subitemscolumns.ini, Column_filename, INI, setting, Name, Group, Type, Class, Field, Copy, Order, Description
type: generated
generated: exp:help:examples
---
# subitemscolumns.ini [Column_filename]

Der Block `[Column_filename]` von `settings/subitemscolumns.ini`, mit 8 Einstellung(en).

Bearbeiten Sie `settings/subitemscolumns.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitemscolumns.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitemscolumns.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitemscolumns.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `File name`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_filename]
Name=File name
*/ ?>
```

## Group

Standard: `Media`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_filename]
Group=Media
*/ ?>
```

## Type

Standard: `text`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_filename]
Type=text
*/ ?>
```

## Class

Standard: `expSubitemsMediaColumn`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_filename]
Class=expSubitemsMediaColumn
*/ ?>
```

## Field

Standard: `file_name`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_filename]
Field=file_name
*/ ?>
```

## Copy

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_filename]
Copy=true
*/ ?>
```

## Order

Standard: `1440`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_filename]
Order=1440
*/ ?>
```

## Description

Standard: `The name the file (binary file or media) was uploaded with.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_filename]
Description=The name the file (binary file or media) was uploaded with.
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitemscolumns.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitemscolumns.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
