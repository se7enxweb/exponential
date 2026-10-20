---
title: subitems.ini [Defaults_users]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Defaults_users] in subitems.ini: Subtree, ParentClassIdentifiers, Columns"
keywords: Einstellung, subitems.ini, Defaults_users, INI, setting, Subtree, ParentClassIdentifiers, Columns
type: generated
generated: exp:help:examples
---
# subitems.ini [Defaults_users]

Der Block `[Defaults_users]` von `settings/subitems.ini`, mit 3 Einstellung(en).

> The users tree (content.ini \[NodeSettings\] UserRootNode=5); the list's former user defaults  

Bearbeiten Sie `settings/subitems.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitems.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitems.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitems.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Subtree

Standard: eine Liste mit 1: `5`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitems.ini.append.php
[Defaults_users]
# ein leeres "Subtree[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Subtree[]
Subtree[]=5
*/ ?>
```

## ParentClassIdentifiers

Standard: eine Liste mit 1: `user_group`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitems.ini.append.php
[Defaults_users]
# ein leeres "ParentClassIdentifiers[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ParentClassIdentifiers[]
ParentClassIdentifiers[]=user_group
*/ ?>
```

## Columns

Standard: eine Liste mit 3: `name`, `modifier`, `published`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitems.ini.append.php
[Defaults_users]
# ein leeres "Columns[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Columns[]
Columns[]=name
Columns[]=modifier
Columns[]=published
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitems.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitems.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
