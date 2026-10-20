---
title: fetchalias.ini [subtree]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [subtree] in fetchalias.ini: Module, FunctionName, Constant, Parameter"
keywords: Einstellung, fetchalias.ini, subtree, INI, setting, Module, FunctionName, Constant, Parameter
type: generated
generated: exp:help:examples
---
# fetchalias.ini [subtree]

Der Block `[subtree]` von `settings/fetchalias.ini`, mit 4 Einstellung(en).

> Fetch subtree  

Bearbeiten Sie `settings/fetchalias.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/fetchalias.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/fetchalias.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/fetchalias.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Module

Standard: `content`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/fetchalias.ini.append.php
[subtree]
Module=content
*/ ?>
```

## FunctionName

Standard: `tree`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/fetchalias.ini.append.php
[subtree]
FunctionName=tree
*/ ?>
```

## Constant

Standard: ein Hash mit 1: `sort_by=priority;0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/fetchalias.ini.append.php
[subtree]
# ein leeres "Constant[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Constant[]
Constant[sort_by]=priority;0
*/ ?>
```

## Parameter

Standard: ein Hash mit 3: `parent_node_id=parent_node_id`, `offset=offset`, `limit=limit`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/fetchalias.ini.append.php
[subtree]
# ein leeres "Parameter[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Parameter[]
Parameter[parent_node_id]=parent_node_id
Parameter[offset]=offset
Parameter[limit]=limit
*/ ?>
```

## Siehe auch

- [Alle Blöcke von fetchalias.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/fetchalias.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
