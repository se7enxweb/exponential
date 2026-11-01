---
title: site.ini [ContentFieldMarkerSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ContentFieldMarkerSettings] in site.ini: Handlers"
keywords: Einstellung, site.ini, ContentFieldMarkerSettings, INI, setting, Handlers
type: generated
generated: exp:help:examples
---
# site.ini [ContentFieldMarkerSettings]

Der Block `[ContentFieldMarkerSettings]` von `settings/site.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Handlers

> Markers around every rendered attribute ({attribute\_view\_gui}) for a tool that edits the page in place:  
>   <!--exp-field data-exp-field="title" data-exp-object="61" data-exp-version="7" data-exp-attribute="402" data-exp-language="eng-US"-->  
>   ... the attribute's output ... <!--/exp-field-->  
> HTML comments, so a page looks and matches CSS selectors the same with or without them. Only when one of  
> these classes says so for the request (a static active(); the website sidebar expwt adds  
> expwtFieldMarkerHandler: a signed-in user who may edit with the sidebar). None (the default): nothing is  
> emitted, and visitors' pages and their caches stay exactly as they are.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentFieldMarkerSettings]
# ein leeres "Handlers[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Handlers[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
