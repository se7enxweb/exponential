---
title: fetchalias.ini [children]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [children] in fetchalias.ini: Module, FunctionName, Parameter"
keywords: Einstellung, fetchalias.ini, children, INI, setting, Module, FunctionName, Parameter
type: generated
generated: exp:help:examples
---
# fetchalias.ini [children]

Der Block `[children]` von `settings/fetchalias.ini`, mit 3 Einstellung(en).

> Fetch children  

Bearbeiten Sie `settings/fetchalias.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/fetchalias.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/fetchalias.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/fetchalias.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Module

Standard: `content`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/fetchalias.ini.append.php
[children]
Module=content
*/ ?>
```

## FunctionName

Standard: `list`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/fetchalias.ini.append.php
[children]
FunctionName=list
*/ ?>
```

## Parameter

Standard: ein Hash mit 4: `sort_by=sort_by`, `parent_node_id=parent_node_id`, `offset=offset`, `limit=limit`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/fetchalias.ini.append.php
[children]
# ein leeres "Parameter[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Parameter[]
Parameter[sort_by]=sort_by
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
