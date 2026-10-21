---
title: rest.ini [PreRoutingFilters]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [PreRoutingFilters] in rest.ini: Filters"
keywords: Einstellung, rest.ini, PreRoutingFilters, INI, setting, Filters
type: generated
generated: exp:help:examples
---
# rest.ini [PreRoutingFilters]

Der Block `[PreRoutingFilters]` von `settings/rest.ini`, mit 1 Einstellung(en).

> (Auth-)Filter für jede Aktion in 'myController' der API-Version 2 überspringen  
> SkipFilter\[\]=myController\_\*;2  
> Für künftige Verwendung  
> RunFilter\[\]  

Bearbeiten Sie `settings/rest.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/rest.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/rest.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/rest.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Filters

> Eigene Filter, die ezpRestPreRoutingFilterInterface implementieren und ausgeführt werden,  
> bevor die Routen erzeugt werden.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[PreRoutingFilters]
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
