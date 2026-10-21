---
title: fetchalias.ini [comments]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [comments] in fetchalias.ini: Module, FunctionName, Constant, Parameter"
keywords: Einstellung, fetchalias.ini, comments, INI, setting, Module, FunctionName, Constant, Parameter
type: generated
generated: exp:help:examples
---
# fetchalias.ini [comments]

Der Block `[comments]` von `settings/fetchalias.ini`, mit 4 Einstellung(en).

> Kommentare zu Artikeln holen  

Bearbeiten Sie `settings/fetchalias.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/fetchalias.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/fetchalias.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/fetchalias.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Module

Standard: `content`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/fetchalias.ini.append.php
[comments]
Module=content
*/ ?>
```

## FunctionName

Standard: `list`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/fetchalias.ini.append.php
[comments]
FunctionName=list
*/ ?>
```

## Constant

Standard: ein Hash mit 3: `sort_by=published;0`, `class_filter_type=include`, `class_filter_array=comment`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/fetchalias.ini.append.php
[comments]
# ein leeres "Constant[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Constant[]
Constant[sort_by]=published;0
Constant[class_filter_type]=include
Constant[class_filter_array]=comment
*/ ?>
```

## Parameter

Standard: ein Hash mit 1: `parent_node_id=parent_node_id`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/fetchalias.ini.append.php
[comments]
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
