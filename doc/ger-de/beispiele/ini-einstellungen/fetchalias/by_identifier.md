---
title: fetchalias.ini [by_identifier]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [by_identifier] in fetchalias.ini: Module, FunctionName, Parameter"
keywords: Einstellung, fetchalias.ini, by_identifier, INI, setting, Module, FunctionName, Parameter
type: generated
generated: exp:help:examples
---
# fetchalias.ini [by_identifier]

Der Block `[by_identifier]` von `settings/fetchalias.ini`, mit 3 Einstellung(en).

> usage : fetch\_alias(by\_identifier, hash(attr\_id, <unique text> ))  

Bearbeiten Sie `settings/fetchalias.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/fetchalias.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/fetchalias.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/fetchalias.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Module

Standard: `content`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/fetchalias.ini.append.php
[by_identifier]
Module=content
*/ ?>
```

## FunctionName

Standard: `object_by_attribute`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/fetchalias.ini.append.php
[by_identifier]
FunctionName=object_by_attribute
*/ ?>
```

## Parameter

Standard: ein Hash mit 1: `identifier=attr_id`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/fetchalias.ini.append.php
[by_identifier]
# ein leeres "Parameter[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Parameter[]
Parameter[identifier]=attr_id
*/ ?>
```

## Siehe auch

- [Alle Blöcke von fetchalias.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/fetchalias.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
