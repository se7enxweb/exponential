---
title: site.ini [Event]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Event] in site.ini: Listeners"
keywords: Einstellung, site.ini, Event, INI, setting, Listeners
type: generated
generated: exp:help:examples
---
# site.ini [Event]

Der Block `[Event]` von `settings/site.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Listeners

> (This is private api and might change in future versions)  
> List of global event listeners/filters in the form:  
> Listeners\[\]=<event>@<callback>  
> eg:  
> Listeners\[\]=request/input@ezxFormHandler::input  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Event]
# ein leeres "Listeners[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Listeners[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
