---
title: rest.ini [ResponseFilters]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ResponseFilters] in rest.ini: Filters"
keywords: Einstellung, rest.ini, ResponseFilters, INI, setting, Filters
type: generated
generated: exp:help:examples
---
# rest.ini [ResponseFilters]

Der Block `[ResponseFilters]` von `settings/rest.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/rest.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/rest.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/rest.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/rest.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Filters

> Custom filters implementing ezpRestResponseFilterInterface, to be executed,  
> after the view has generated a response.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[ResponseFilters]
# ein leeres "Filters[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Filters[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von rest.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/rest.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
