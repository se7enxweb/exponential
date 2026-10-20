---
title: fetchalias.ini [news_list]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [news_list] in fetchalias.ini: Module, FunctionName, Constant"
keywords: Einstellung, fetchalias.ini, news_list, INI, setting, Module, FunctionName, Constant
type: generated
generated: exp:help:examples
---
# fetchalias.ini [news_list]

Der Block `[news_list]` von `settings/fetchalias.ini`, mit 3 Einstellung(en).

> Verwendung: fetch\_alias(news\_list)  

Bearbeiten Sie `settings/fetchalias.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/fetchalias.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/fetchalias.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/fetchalias.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Module

Standard: `content`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/fetchalias.ini.append.php
[news_list]
Module=content
*/ ?>
```

## FunctionName

Standard: `tree`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/fetchalias.ini.append.php
[news_list]
FunctionName=tree
*/ ?>
```

## Constant

Standard: ein Hash mit 6: `sort_by=published;0`, `class_id=2`, `parent_node_id=2`, `class_filter_type=include`, `limit=10`, `class_filter_array=2`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/fetchalias.ini.append.php
[news_list]
# ein leeres "Constant[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Constant[]
Constant[sort_by]=published;0
Constant[class_id]=2
Constant[parent_node_id]=2
Constant[class_filter_type]=include
Constant[limit]=10
Constant[class_filter_array]=2
*/ ?>
```

## Siehe auch

- [Alle Blöcke von fetchalias.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/fetchalias.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
