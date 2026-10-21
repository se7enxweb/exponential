---
title: site.ini [SiteDataSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [SiteDataSettings] in site.ini: TemplateReadable"
keywords: Einstellung, site.ini, SiteDataSettings, INI, setting, TemplateReadable
type: generated
generated: exp:help:examples
---
# site.ini [SiteDataSettings]

Der Block `[SiteDataSettings]` von `settings/site.ini`, mit 1 Einstellung(en).

> Neben der Website, die der Globus öffnet, wird ein Siteaccess nur aufgeführt, wo sich der  
> angemeldete Benutzer anmelden darf (user/login und seine Einschränkung SiteAccess, wie die  
> Anmeldung sie prüft).  
> Die Cache-Blöcke des Kopfes werden nach der Liste und der Anfrage unterschieden; ein geänderter  
> SiteName, Locale oder SiteDesign eines aufgeführten Siteaccess erscheint, sobald die Template-  
> Caches des Administrations-Siteaccess geleert sind.  
> Website-Daten: die Name/Wert-Zeilen der Tabelle ezsite\_data (eZSiteData), etwa  
> ezpublish-version. Setup > Website-Daten (setup/sitedata, Richtlinie setup/system\_info)  
> listet sie auf. Templates lesen eine Zeile mit  
>   fetch( 'content', 'site\_data', hash( 'name', 'my-flag' ) )  
> (das Objekt eZSiteData, .value ist also der Wert; false für einen Namen, der hier nicht aufgeführt  
> oder nicht gespeichert ist), aber nur die hier aufgeführten Namen, als ganze Namen: Nichts  
> ist lesbar, bis ein Name hinzugefügt wird. Nur lesend: Templates schreiben nie eine Zeile.  
> Teile aus LoveStack übernommen (mugoweb/ezpublish-legacy, Commits a679a13176  
> und 94b7b6c239), Copyright (C) Mugo Web, GPL v2.  

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## TemplateReadable

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteDataSettings]
# ein leeres "TemplateReadable[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
TemplateReadable[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
