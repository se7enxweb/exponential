---
title: site.ini [Cache]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Cache] in site.ini: CacheItems"
keywords: Einstellung, site.ini, Cache, INI, setting, CacheItems
type: generated
generated: exp:help:examples
---
# site.ini [Cache]

Der Block `[Cache]` von `settings/site.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## CacheItems

> Array with cache items to extend ezcache  
> Example of use:  
> CacheItems\[\]=custom  
>
> \[Cache\_custom\]  
>
> \[optional\] Name of the cache item, key (custom) is used camel-cased if not set  
> name=Custom cache  
> \[optional\] Id of cache item, to be used from command line, key (custom) is used if not set  
> id=custom  
> \[optional\] If cache item should be cleared using cluster instead of plain file handler, def: false  
> isClustered=true  
> \[optional\] tags that will trigger clearing of this item, default: empty array  
> tags\[\]=content  
> tags\[\]=custom  
> \[optional\] If cache uses eZExpiryHandler, then this is the key to get expiry time  
> expiryKey=global-custom-cache  
> \[optional\] (bool) If this cache is enabled or not, default: true  
> enabled=false  
> \[optional\] Path where cache is stored, either directory or file if class/purgeClass  
> variables are unset, relative to current cache directory is assumed  
> path=custom\_cache.php  
> \[optional\] custom cache clear function "<class>::clearCache()" to be called  
> class=eZSomeClassName  
> \[optional\] custom cache purge function "<class>::purgeCache()" to be called  
> purgeClass=eZSomeClassName  

Standard: eine Liste mit 2: `rest`, `restRoutes`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Cache]
# ein leeres "CacheItems[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
CacheItems[]
CacheItems[]=rest
CacheItems[]=restRoutes
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
