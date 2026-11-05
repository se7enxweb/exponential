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

> Markierungen um jedes gerenderte Attribut ({attribute\_view\_gui}) für ein Werkzeug, das die Seite an Ort und Stelle bearbeitet:  
>   <!--exp-field data-exp-field="title" data-exp-object="61" data-exp-version="7" data-exp-attribute="402" data-exp-language="eng-US"-->  
>   ... die Ausgabe des Attributs ... <!--/exp-field-->  
> HTML-Kommentare, sodass eine Seite mit oder ohne sie gleich aussieht und auf CSS-Selektoren gleich passt. Nur wenn eine  
> dieser Klassen es für die Anfrage sagt (ein statisches active(); die Website-Seitenleiste expwt fügt  
> expwtFieldMarkerHandler hinzu: ein angemeldeter Benutzer, der mit der Seitenleiste bearbeiten darf). Keine (der Standard): Es wird nichts  
> ausgegeben, und die Seiten der Besucher und ihre Caches bleiben genau, wie sie sind.  

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
