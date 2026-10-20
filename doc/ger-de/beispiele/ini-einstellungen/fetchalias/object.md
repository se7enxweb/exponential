---
title: fetchalias.ini [object]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [object] in fetchalias.ini: Module, FunctionName, Parameter"
keywords: Einstellung, fetchalias.ini, object, INI, setting, Module, FunctionName, Parameter
type: generated
generated: exp:help:examples
---
# fetchalias.ini [object]

Der Block `[object]` von `settings/fetchalias.ini`, mit 3 Einstellung(en).

> usage : fetch\_alias(object, hash(id, <object\_id>))  

Bearbeiten Sie `settings/fetchalias.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/fetchalias.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/fetchalias.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/fetchalias.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Module

Standard: `content`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/fetchalias.ini.append.php
[object]
Module=content
*/ ?>
```

## FunctionName

Standard: `object`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/fetchalias.ini.append.php
[object]
FunctionName=object
*/ ?>
```

## Parameter

Standard: ein Hash mit 1: `object_id=id`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/fetchalias.ini.append.php
[object]
# ein leeres "Parameter[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Parameter[]
Parameter[object_id]=id
*/ ?>
```

## Siehe auch

- [Alle Blöcke von fetchalias.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/fetchalias.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
