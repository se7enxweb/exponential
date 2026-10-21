---
title: syscard.ini [Inventory]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Inventory] in syscard.ini: Settings"
keywords: Einstellung, syscard.ini, Inventory, INI, setting, Settings
type: generated
generated: exp:help:examples
---
# syscard.ini [Inventory]

Der Block `[Inventory]` von `settings/syscard.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/syscard.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/syscard.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/syscard.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/syscard.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Settings

> Die Einstellungen, die exp:info zeigt, als <file>/<block>/<name>. Eine Einstellung, deren Name wie ein Geheimnis aussieht (password, key,  
> token, user, login ...), wird nie angezeigt, auch wenn sie hier aufgeführt ist.  

Standard: eine Liste mit 18: `site.ini/SiteSettings/SiteName`, `site.ini/SiteSettings/DefaultAccess`, `site.ini/SiteAccessSettings/MatchOrder`, `site.ini/RegionalSettings/Locale`, `site.ini/RegionalSettings/SiteLanguageList`, `site.ini/TimeZoneSettings/TimeZone`, `site.ini/ContentSettings/ViewCaching`, `site.ini/ContentSettings/StaticCache`, `site.ini/TemplateSettings/TemplateCompile`, `site.ini/TemplateSettings/TemplateCache`, `site.ini/DebugSettings/DebugOutput`, `site.ini/MailSettings/Transport` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Inventory]
# ein leeres "Settings[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Settings[]
Settings[]=site.ini/SiteSettings/SiteName
Settings[]=site.ini/SiteSettings/DefaultAccess
Settings[]=site.ini/SiteAccessSettings/MatchOrder
Settings[]=site.ini/RegionalSettings/Locale
Settings[]=site.ini/RegionalSettings/SiteLanguageList
Settings[]=site.ini/TimeZoneSettings/TimeZone
Settings[]=site.ini/ContentSettings/ViewCaching
Settings[]=site.ini/ContentSettings/StaticCache
Settings[]=site.ini/TemplateSettings/TemplateCompile
Settings[]=site.ini/TemplateSettings/TemplateCache
Settings[]=site.ini/DebugSettings/DebugOutput
Settings[]=site.ini/MailSettings/Transport
Settings[]=site.ini/SessionSettings/Handler
Settings[]=file.ini/ClusteringSettings/FileHandler
Settings[]=velocity.ini/ServerSettings/Engine
Settings[]=velocity.ini/ServerSettings/Workers
Settings[]=velocity.ini/ServerSettings/ForkPerRequest
Settings[]=exphelp.ini/DocLanguages/Default
*/ ?>
```

## Siehe auch

- [Alle Blöcke von syscard.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/syscard.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
