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

> How many locations the locations tab shows at a time.  
>
> An object can be placed in any number of locations, and a widely reused piece  
> of content can reach thousands. Drawing them all meant fetching them all, and  
> a path lookup for every row, which took the page down before it rendered  
> anything. The tab pages them; this is the page size.  

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
