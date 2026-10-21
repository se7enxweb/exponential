---
title: subitemscolumns.ini [Column_translations]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Column_translations] in subitemscolumns.ini: Builtin, Name, Group, Order, Description"
keywords: Einstellung, subitemscolumns.ini, Column_translations, INI, setting, Builtin, Name, Group, Order, Description
type: generated
generated: exp:help:examples
---
# subitemscolumns.ini [Column_translations]

Der Block `[Column_translations]` von `settings/subitemscolumns.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/subitemscolumns.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitemscolumns.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitemscolumns.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitemscolumns.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Builtin

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_translations]
Builtin=true
*/ ?>
```

## Name

Standard: `Translations`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_translations]
Name=Translations
*/ ?>
```

## Group

Standard: `Translations`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_translations]
Group=Translations
*/ ?>
```

## Order

Standard: `80`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_translations]
Order=80
*/ ?>
```

## Description

Standard: `The flags of the languages the item is translated into.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_translations]
Description=The flags of the languages the item is translated into.
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitemscolumns.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitemscolumns.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
