---
title: browse.ini [AddCustomer]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AddCustomer] in browse.ini: ReturnType, SelectionType, StartNode, TopLevelNodes"
keywords: Einstellung, browse.ini, AddCustomer, INI, setting, ReturnType, SelectionType, StartNode, TopLevelNodes
type: generated
generated: exp:help:examples
---
# browse.ini [AddCustomer]

Der Block `[AddCustomer]` von `settings/browse.ini`, mit 4 Einstellung(en).

> Aktion, wenn in der Rabattansicht ein Benutzer ausgewählt werden soll  

Bearbeiten Sie `settings/browse.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/browse.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/browse.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/browse.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ReturnType

Standard: `ObjectID`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/browse.ini.append.php
[AddCustomer]
ReturnType=ObjectID
*/ ?>
```

## SelectionType

Standard: `multiple`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/browse.ini.append.php
[AddCustomer]
SelectionType=multiple
*/ ?>
```

## StartNode

Standard: `users`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/browse.ini.append.php
[AddCustomer]
StartNode=users
*/ ?>
```

## TopLevelNodes

Standard: eine Liste mit 1: `users`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/browse.ini.append.php
[AddCustomer]
# ein leeres "TopLevelNodes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
TopLevelNodes[]
TopLevelNodes[]=users
*/ ?>
```

## Siehe auch

- [Alle Blöcke von browse.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/browse.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
