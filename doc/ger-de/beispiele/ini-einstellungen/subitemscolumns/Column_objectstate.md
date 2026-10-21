---
title: subitemscolumns.ini [Column_objectstate]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Column_objectstate] in subitemscolumns.ini: Builtin, Name, Group, Order, Description"
keywords: Einstellung, subitemscolumns.ini, Column_objectstate, INI, setting, Builtin, Name, Group, Order, Description
type: generated
generated: exp:help:examples
---
# subitemscolumns.ini [Column_objectstate]

Der Block `[Column_objectstate]` von `settings/subitemscolumns.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/subitemscolumns.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitemscolumns.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitemscolumns.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitemscolumns.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Builtin

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_objectstate]
Builtin=true
*/ ?>
```

## Name

Standard: `Object state`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_objectstate]
Name=Object state
*/ ?>
```

## Group

Standard: `Workflow`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_objectstate]
Group=Workflow
*/ ?>
```

## Order

Standard: `140`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_objectstate]
Order=140
*/ ?>
```

## Description

Standard: `The item's object states, as group/state identifiers.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitemscolumns.ini.append.php
[Column_objectstate]
Description=The item's object states, as group/state identifiers.
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitemscolumns.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitemscolumns.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
