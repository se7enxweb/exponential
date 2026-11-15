---
title: site.ini [CacheUntilSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [CacheUntilSettings] in site.ini: LiteSpeedMaxAge"
keywords: Einstellung, site.ini, CacheUntilSettings, INI, setting, LiteSpeedMaxAge
type: generated
generated: exp:help:examples
---
# site.ini [CacheUntilSettings]

Der Block `[CacheUntilSettings]` von `settings/site.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## LiteSpeedMaxAge

> The template operator cache\_until() sets when a page stops being true ({cache\_until( 'tomorrow' )},  
> {cache\_until( $event\_start )}, {cache\_until( 0 )}). Every cache honours it without a setting: the view cache,  
> the template-block cache, the HTTP cache (httpcache.ini), Velocity's response cache, and the max-age, s-maxage  
> and Expires of the response. Time strings are read in \[TimeZoneSettings\] TimeZone.  
> Guide: doc/eng-us/guides/caching-time-dependent-content.md  
>
> A page that must not be kept (cache\_until( 0 )) always gets "X-LiteSpeed-Cache-Control: no-cache", which only  
> ever keeps a page out of LiteSpeed's page cache. enabled: an anonymous visitor's page with an expiry also gets  
> "X-LiteSpeed-Cache-Control: max-age=<seconds left>", so LiteSpeed keeps it no longer than the expiry even when  
> it ignores the response's Cache-Control. LiteSpeed reads that header on every response, also one its rewrite  
> rules do not cache: enable it only when the pages that call cache\_until() are pages your LiteSpeed block caches  
> (doc/eng-us/guides/litespeed-page-cache.md).  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[CacheUntilSettings]
LiteSpeedMaxAge=disabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
