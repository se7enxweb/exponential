---
title: subitemscolumns.ini [Column_objectremoteid]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Column_objectremoteid] in subitemscolumns.ini: Builtin, Name, Group, Copy, Order, Description"
keywords: Einstellung, subitemscolumns.ini, Column_objectremoteid, INI, setting, Builtin, Name, Group, Copy, Order, Description
type: generated
generated: exp:help:examples
---
# subitemscolumns.ini [Column_objectremoteid]

Der Block `[Column_objectremoteid]` von `settings/subitemscolumns.ini`, mit 6 Einstellung(en).

Bearbeiten Sie `settings/subitemscolumns.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitemscolumns.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitemscolumns.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitemscolumns.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Builtin

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_objectremoteid]
Builtin=true
*/ ?>
```

## Name

Standard: `Object remote ID`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_objectremoteid]
Name=Object remote ID
*/ ?>
```

## Group

Standard: `Object`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_objectremoteid]
Group=Object
*/ ?>
```

## Copy

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_objectremoteid]
Copy=true
*/ ?>
```

## Order

Standard: `130`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_objectremoteid]
Order=130
*/ ?>
```

## Description

Standard: `The remote ID of the content object, stable across installations.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_objectremoteid]
Description=The remote ID of the content object, stable across installations.
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitemscolumns.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitemscolumns.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
