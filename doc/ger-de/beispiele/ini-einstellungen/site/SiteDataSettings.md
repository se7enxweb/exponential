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

> Besides the site the globe opens, a siteaccess is listed only where the  
> signed-in user may log in (user/login and its SiteAccess limitation, as the  
> login checks it).  
> The header's cache-blocks are keyed by the list and the request; a changed  
> SiteName, Locale or SiteDesign of a listed siteaccess shows once the template  
> caches of the administration siteaccess are cleared.  
> Site data: the name/value rows of the table ezsite\_data (eZSiteData), such as  
> ezpublish-version. Setup > Site data (setup/sitedata, policy setup/system\_info)  
> lists them. Templates read a row with  
>   fetch( 'content', 'site\_data', hash( 'name', 'my-flag' ) )  
> (the eZSiteData object, so .value is the value; false for a name not listed  
> here or not stored), but only the names listed here, as whole names: nothing  
> is readable until a name is added. Read only: templates never write a row.  
> Portions derived from LoveStack (mugoweb/ezpublish-legacy, commits a679a13176  
> and 94b7b6c239), Copyright (C) Mugo Web, GPL v2.  

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
