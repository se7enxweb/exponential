---
title: browse.ini [AssignRole]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AssignRole] in browse.ini: StartNode, SelectionType, ReturnType, Class, TopLevelNodes"
keywords: Einstellung, browse.ini, AssignRole, INI, setting, StartNode, SelectionType, ReturnType, Class, TopLevelNodes
type: generated
generated: exp:help:examples
---
# browse.ini [AssignRole]

Der Block `[AssignRole]` von `settings/browse.ini`, mit 5 Einstellung(en).

> Action used when assigning a role to a user or user group  

Bearbeiten Sie `settings/browse.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/browse.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/browse.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/browse.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## StartNode

Standard: `users`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/browse.ini.append.php
[AssignRole]
StartNode=users
*/ ?>
```

## SelectionType

Standard: `multiple`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/browse.ini.append.php
[AssignRole]
SelectionType=multiple
*/ ?>
```

## ReturnType

Standard: `ObjectID`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/browse.ini.append.php
[AssignRole]
ReturnType=ObjectID
*/ ?>
```

## Class

Standard: eine Liste mit 2: `user`, `user_group`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/browse.ini.append.php
[AssignRole]
# ein leeres "Class[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Class[]
Class[]=user
Class[]=user_group
*/ ?>
```

## TopLevelNodes

Standard: eine Liste mit 1: `users`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/browse.ini.append.php
[AssignRole]
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
