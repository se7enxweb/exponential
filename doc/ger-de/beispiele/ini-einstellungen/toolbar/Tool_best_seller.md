---
title: toolbar.ini [Tool_best_seller]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Tool_best_seller] in toolbar.ini: show_subtree, product_parent_node"
keywords: Einstellung, toolbar.ini, Tool_best_seller, INI, setting, show_subtree, product_parent_node
type: generated
generated: exp:help:examples
---
# toolbar.ini [Tool_best_seller]

Der Block `[Tool_best_seller]` von `settings/toolbar.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/toolbar.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/toolbar.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/toolbar.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/toolbar.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## show_subtree

Standard: `shop`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/toolbar.ini.append.php
[Tool_best_seller]
show_subtree=shop
*/ ?>
```

## product_parent_node

Standard: `2`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/toolbar.ini.append.php
[Tool_best_seller]
product_parent_node=2
*/ ?>
```

## Siehe auch

- [Alle Blöcke von toolbar.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/toolbar.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
