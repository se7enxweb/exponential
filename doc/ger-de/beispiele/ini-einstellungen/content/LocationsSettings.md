---
title: content.ini [LocationsSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [LocationsSettings] in content.ini: LocationsPerPage"
keywords: Einstellung, content.ini, LocationsSettings, INI, setting, LocationsPerPage
type: generated
generated: exp:help:examples
---
# content.ini [LocationsSettings]

Der Block `[LocationsSettings]` von `settings/content.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/content.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/content.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/content.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/content.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## LocationsPerPage

> Wie viele Platzierungen der Reiter Platzierungen auf einmal zeigt.  
>
> Ein Objekt kann beliebig viele Platzierungen haben, und ein vielfach wiederverwendeter  
> Inhalt kann Tausende erreichen. Sie alle zu zeichnen hieß, sie alle zu holen, mit  
> einer Pfadabfrage für jede Zeile, was die Seite lahmlegte, bevor sie  
> irgendetwas darstellte. Der Reiter blättert sie; dies ist die Seitengröße.  

Standard: `25`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[LocationsSettings]
LocationsPerPage=25
*/ ?>
```

## Siehe auch

- [Alle Blöcke von content.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/content.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
