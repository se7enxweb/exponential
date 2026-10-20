---
title: fetchalias.ini [children_count]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [children_count] in fetchalias.ini: Module, FunctionName, Parameter"
keywords: Einstellung, fetchalias.ini, children_count, INI, setting, Module, FunctionName, Parameter
type: generated
generated: exp:help:examples
---
# fetchalias.ini [children_count]

Der Block `[children_count]` von `settings/fetchalias.ini`, mit 3 Einstellung(en).

> Unterknoten zählen  

Bearbeiten Sie `settings/fetchalias.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/fetchalias.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/fetchalias.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/fetchalias.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Module

Standard: `content`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/fetchalias.ini.append.php
[children_count]
Module=content
*/ ?>
```

## FunctionName

Standard: `list_count`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/fetchalias.ini.append.php
[children_count]
FunctionName=list_count
*/ ?>
```

## Parameter

Standard: ein Hash mit 1: `parent_node_id=parent_node_id`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/fetchalias.ini.append.php
[children_count]
# ein leeres "Parameter[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Parameter[]
Parameter[parent_node_id]=parent_node_id
*/ ?>
```

## Siehe auch

- [Alle Blöcke von fetchalias.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/fetchalias.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
