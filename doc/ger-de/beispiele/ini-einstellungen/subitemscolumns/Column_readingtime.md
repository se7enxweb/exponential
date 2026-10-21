---
title: subitemscolumns.ini [Column_readingtime]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Column_readingtime] in subitemscolumns.ini: Name, Group, Type, Class, Align, Order, Description, WordsPerMinute"
keywords: Einstellung, subitemscolumns.ini, Column_readingtime, INI, setting, Name, Group, Type, Class, Align, Order, Description, WordsPerMinute
type: generated
generated: exp:help:examples
---
# subitemscolumns.ini [Column_readingtime]

Der Block `[Column_readingtime]` von `settings/subitemscolumns.ini`, mit 8 Einstellung(en).

Bearbeiten Sie `settings/subitemscolumns.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitemscolumns.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitemscolumns.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitemscolumns.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Reading time`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_readingtime]
Name=Reading time
*/ ?>
```

## Group

Standard: `Object`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_readingtime]
Group=Object
*/ ?>
```

## Type

Standard: `number`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_readingtime]
Type=number
*/ ?>
```

## Class

Standard: `expSubitemsReadingTimeColumn`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_readingtime]
Class=expSubitemsReadingTimeColumn
*/ ?>
```

## Align

Standard: `right`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_readingtime]
Align=right
*/ ?>
```

## Order

Standard: `332`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_readingtime]
Order=332
*/ ?>
```

## Description

Standard: `Minutes a reader needs for the main text (see Word count), at WordsPerMinute words a minute. Reads the data map.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_readingtime]
Description=Minutes a reader needs for the main text (see Word count), at WordsPerMinute words a minute. Reads the data map.
*/ ?>
```

## WordsPerMinute

Standard: `200`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_readingtime]
WordsPerMinute=200
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitemscolumns.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitemscolumns.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
