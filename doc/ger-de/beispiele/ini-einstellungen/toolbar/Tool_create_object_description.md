---
title: toolbar.ini [Tool_create_object_description]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Tool_create_object_description] in toolbar.ini: title, show_subtree, placement_node, type_classidentifier"
keywords: Einstellung, toolbar.ini, Tool_create_object_description, INI, setting, title, show_subtree, placement_node, type_classidentifier
type: generated
generated: exp:help:examples
---
# toolbar.ini [Tool_create_object_description]

Der Block `[Tool_create_object_description]` von `settings/toolbar.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/toolbar.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/toolbar.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/toolbar.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/toolbar.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## title

Standard: `Title`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/toolbar.ini.append.php
[Tool_create_object_description]
title=Title
*/ ?>
```

## show_subtree

Standard: `Show in subtree`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/toolbar.ini.append.php
[Tool_create_object_description]
show_subtree=Show in subtree
*/ ?>
```

## placement_node

Standard: `Where to place`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/toolbar.ini.append.php
[Tool_create_object_description]
placement_node=Where to place
*/ ?>
```

## type_classidentifier

Standard: `Type`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/toolbar.ini.append.php
[Tool_create_object_description]
type_classidentifier=Type
*/ ?>
```

## Siehe auch

- [Alle Blöcke von toolbar.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/toolbar.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
