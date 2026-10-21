---
title: toolbar.ini [Tool_create_object]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Tool_create_object] in toolbar.ini: title, show_subtree, placement_node, type_classidentifier"
keywords: Einstellung, toolbar.ini, Tool_create_object, INI, setting, title, show_subtree, placement_node, type_classidentifier
type: generated
generated: exp:help:examples
---
# toolbar.ini [Tool_create_object]

Der Block `[Tool_create_object]` von `settings/toolbar.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/toolbar.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/toolbar.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/toolbar.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/toolbar.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## title

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/toolbar.ini.append.php
[Tool_create_object]
title=
*/ ?>
```

## show_subtree

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/toolbar.ini.append.php
[Tool_create_object]
show_subtree=
*/ ?>
```

## placement_node

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/toolbar.ini.append.php
[Tool_create_object]
placement_node=
*/ ?>
```

## type_classidentifier

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/toolbar.ini.append.php
[Tool_create_object]
type_classidentifier=
*/ ?>
```

## Siehe auch

- [Alle Blöcke von toolbar.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/toolbar.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
