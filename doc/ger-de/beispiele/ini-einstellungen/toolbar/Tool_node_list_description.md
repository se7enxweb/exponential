---
title: toolbar.ini [Tool_node_list_description]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Tool_node_list_description] in toolbar.ini: parent_node, title, show_subtree, limit, sort_by, treelist_check"
keywords: Einstellung, toolbar.ini, Tool_node_list_description, INI, setting, parent_node, title, show_subtree, limit, sort_by, treelist_check
type: generated
generated: exp:help:examples
---
# toolbar.ini [Tool_node_list_description]

Der Block `[Tool_node_list_description]` von `settings/toolbar.ini`, mit 6 Einstellung(en).

Bearbeiten Sie `settings/toolbar.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/toolbar.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/toolbar.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/toolbar.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## parent_node

Standard: `Item source`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/toolbar.ini.append.php
[Tool_node_list_description]
parent_node=Item source
*/ ?>
```

## title

Standard: `Title`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/toolbar.ini.append.php
[Tool_node_list_description]
title=Title
*/ ?>
```

## show_subtree

Standard: `Show in subtree`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/toolbar.ini.append.php
[Tool_node_list_description]
show_subtree=Show in subtree
*/ ?>
```

## limit

Standard: `Number of items`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/toolbar.ini.append.php
[Tool_node_list_description]
limit=Number of items
*/ ?>
```

## sort_by

Standard: `Sort on attribute`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/toolbar.ini.append.php
[Tool_node_list_description]
sort_by=Sort on attribute
*/ ?>
```

## treelist_check

Standard: `Fetch whole tree?`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/toolbar.ini.append.php
[Tool_node_list_description]
treelist_check=Fetch whole tree?
*/ ?>
```

## Siehe auch

- [Alle Blöcke von toolbar.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/toolbar.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
