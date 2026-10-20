---
title: httpcache.ini [HttpCacheSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [HttpCacheSettings] in httpcache.ini: Enabled, CachedSiteAccesses, MaxAge, StaleWhileRevalidate, TagHeader, APCu, MaxBodySize, QueryStringParameters ..."
keywords: Einstellung, httpcache.ini, HttpCacheSettings, INI, setting, Enabled, CachedSiteAccesses, MaxAge, StaleWhileRevalidate, TagHeader, APCu, MaxBodySize, QueryStringParameters, ContentChangePurges
type: generated
generated: exp:help:examples
---
# httpcache.ini [HttpCacheSettings]

Der Block `[HttpCacheSettings]` von `settings/httpcache.ini`, mit 9 Einstellung(en).

Bearbeiten Sie `settings/httpcache.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/httpcache.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/httpcache.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/httpcache.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Enabled

> enabled or disabled. The early exit and Velocity only serve what exists, and  
> nothing is stored while this is disabled.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/httpcache.ini.append.php
[HttpCacheSettings]
Enabled=disabled
*/ ?>
```

## CachedSiteAccesses

> Siteaccesses whose pages are cached. Matched by host, by URI (URIMatchType  
> element or map) or by host and URI (host\_uri); a siteaccess found by another  
> rule (port, servervar, index, the text and regexp types) is never served  
> early, since that cannot be known before the kernel starts.  

Standard: eine Liste mit 1: `site`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/httpcache.ini.append.php
[HttpCacheSettings]
# ein leeres "CachedSiteAccesses[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
CachedSiteAccesses[]
CachedSiteAccesses[]=site
*/ ?>
```

## MaxAge

> Seconds a cached page may be served at most, even if nothing purged it.  

Standard: `3600`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/httpcache.ini.append.php
[HttpCacheSettings]
MaxAge=3600
*/ ?>
```

## StaleWhileRevalidate

> Seconds an expired (not purged) page may still be served, marked STALE, while  
> one request renders it again. 0 serves nothing expired.  

Standard: `60`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/httpcache.ini.append.php
[HttpCacheSettings]
StaleWhileRevalidate=60
*/ ?>
```

## TagHeader

> The purge tags of each page (its nodes, objects and layouts) in one response  
> header, for a purging proxy in front that reads them, or to see them while  
> debugging: xkey (Varnish xkey vmod), Surrogate-Key (Fastly), Cache-Tag  
> (Cloudflare), or another header name. disabled (the default) sends none:  
> the tags name internal ids, so no visitor should see them otherwise.  
> ProxyHeaders=enabled, the setting before this one, still means xkey.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/httpcache.ini.append.php
[HttpCacheSettings]
TagHeader=disabled
*/ ?>
```

## APCu

> Keep hot entries in APCu in front of the files (when APCu is usable).  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/httpcache.ini.append.php
[HttpCacheSettings]
APCu=enabled
*/ ?>
```

## MaxBodySize

> Largest page body, in bytes, that is stored.  

Standard: `2097152`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/httpcache.ini.append.php
[HttpCacheSettings]
MaxBodySize=2097152
*/ ?>
```

## QueryStringParameters

> Query string parameters a cached page may carry. A request with any other  
> parameter is not cached (every random query string would add an entry).  
> Exponential passes view parameters in the path, /(offset)/10.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/httpcache.ini.append.php
[HttpCacheSettings]
# ein leeres "QueryStringParameters[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
QueryStringParameters[]
*/ ?>
```

## ContentChangePurges

> What a content change purges. A whole page also holds what the view cache  
> never did (the pagelayout's menus, layout blocks that run queries), and the  
> tags of a change cannot name all of those yet.  
>   all  - every cached page (safe; the default)  
>   tags - only pages tagged with the changed objects, their nodes and parents,  
>          and pages with layout blocks that run queries (their items are tagged)  
> Changes made in the layout editor always purge every page.  

Standard: `all`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/httpcache.ini.append.php
[HttpCacheSettings]
ContentChangePurges=all
*/ ?>
```

## Siehe auch

- [Alle Blöcke von httpcache.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/httpcache.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
