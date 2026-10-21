---
title: toolbar.ini [Tool_node_list]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Tool_node_list] in toolbar.ini: parent_node, title, show_subtree, limit, sort_by, treelist_check"
keywords: Einstellung, toolbar.ini, Tool_node_list, INI, setting, parent_node, title, show_subtree, limit, sort_by, treelist_check
type: generated
generated: exp:help:examples
---
# toolbar.ini [Tool_node_list]

Der Block `[Tool_node_list]` von `settings/toolbar.ini`, mit 6 Einstellung(en).

Bearbeiten Sie `settings/toolbar.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/toolbar.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/toolbar.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/toolbar.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## parent_node

Standard: `2`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/toolbar.ini.append.php
[Tool_node_list]
parent_node=2
*/ ?>
```

## title

Standard: `Latest items`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/toolbar.ini.append.php
[Tool_node_list]
title=Latest items
*/ ?>
```

## show_subtree

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/toolbar.ini.append.php
[Tool_node_list]
show_subtree=
*/ ?>
```

## limit

Standard: `5`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/toolbar.ini.append.php
[Tool_node_list]
limit=5
*/ ?>
```

## sort_by

Standard: `published`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/toolbar.ini.append.php
[Tool_node_list]
sort_by=published
*/ ?>
```

## treelist_check

Standard: `yes`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/toolbar.ini.append.php
[Tool_node_list]
treelist_check=yes
*/ ?>
```

## Siehe auch

- [Alle Blöcke von toolbar.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/toolbar.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
