---
title: staticcache.ini [CacheSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [CacheSettings] in staticcache.ini: HostName, SourceProtocol, StaticStorageDir, MaxCacheDepth, CachedURLArray, CachedSiteAccesses, AlwaysUpdateArray, CronjobCacheClear ..."
keywords: Einstellung, staticcache.ini, CacheSettings, INI, setting, HostName, SourceProtocol, StaticStorageDir, MaxCacheDepth, CachedURLArray, CachedSiteAccesses, AlwaysUpdateArray, CronjobCacheClear, AppendGeneratedTime
type: generated
generated: exp:help:examples
---
# staticcache.ini [CacheSettings]

Der Block `[CacheSettings]` von `settings/staticcache.ini`, mit 9 Einstellung(en).

> Exponential configuration file.  
>
> NOTE: It is not recommended to edit this file directly, instead  
>       a file in override should be created for setting the  
>       values that is required for your site. Either create  
>       a file called settings/override/staticcache.ini.append or  
>       settings/override/staticcache.ini.append.php for more security  
>       in non-virtualhost modes (the .php file may already be present  
>       and can be used for this purpose).  

Bearbeiten Sie `settings/staticcache.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/staticcache.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/staticcache.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/staticcache.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## HostName

> The host to fetch pages from when generating the cache.  
>
> Leave this empty. It is deprecated since 4.4 and empty is the correct  
> value: each siteaccess is then fetched from its own  
> site.ini.\[SiteSettings\].SiteURL, which is what a multi site installation  
> needs. It shipped as "localhost" for years, which sent every fetch to  
> whatever the web server answers for that name - usually the hosting  
> panel's placeholder, never this site - and the run failed silently.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[CacheSettings]
HostName=
*/ ?>
```

## SourceProtocol

> The protocol used to fetch pages, http or https.  
>
> http is fine on a site that redirects to https, because the fetch follows  
> redirects. Set https where plain http is refused outright rather than  
> redirected.  

Standard: `http`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[CacheSettings]
SourceProtocol=http
*/ ?>
```

## StaticStorageDir

> Where generated pages are written, relative to the var directory of the  
> siteaccess unless the path is absolute or already begins with var/.  
>
> The default therefore resolves to var/<site>/static, alongside  
> var/<site>/cache and var/<site>/storage. Generated pages are cache and  
> belong with the rest of it, not in the installation root.  

Standard: `static`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[CacheSettings]
StaticStorageDir=static
*/ ?>
```

## MaxCacheDepth

> Urls deeper than this many segments are never cached, and never refreshed  
> when an editor publishes. The old default of 3 allowed two path segments,  
> which excludes most of a real content tree.  

Standard: `12`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[CacheSettings]
MaxCacheDepth=12
*/ ?>
```

## CachedURLArray

> A list of url's to cache. You can use the \* wildcard to include a whole  
> subtree. A bare /\* caches the entire site, subject to MaxCacheDepth.  

Standard: eine Liste mit 2: `/`, `/*`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[CacheSettings]
# ein leeres "CachedURLArray[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
CachedURLArray[]
CachedURLArray[]=/
CachedURLArray[]=/*
*/ ?>
```

## CachedSiteAccesses

> A list of site accesses to generate static content for.  
>
> Left empty, the siteaccesses this installation serves are used: every  
> entry of site.ini.\[SiteAccessSettings\].RelatedSiteAccessList except those  
> that require a login, whose pages are per user and must not be shared.  
> Name siteaccesses here to override that.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[CacheSettings]
# ein leeres "CachedSiteAccesses[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
CachedSiteAccesses[]
*/ ?>
```

## AlwaysUpdateArray

> A list of locations that will be updated whenever an object is published. You  
> can NOT use a wildcard here.  

Standard: eine Liste mit 1: `/`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[CacheSettings]
# ein leeres "AlwaysUpdateArray[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AlwaysUpdateArray[]
AlwaysUpdateArray[]=/
*/ ?>
```

## CronjobCacheClear

> Defer cache generation to cronjob.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[CacheSettings]
CronjobCacheClear=disabled
*/ ?>
```

## AppendGeneratedTime

> Controls whenever <!-- Generated: YYY-MM-DD HH:MM:SS --> comment  
> should be appended into the generated static cache file.  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[CacheSettings]
AppendGeneratedTime=true
*/ ?>
```

## Siehe auch

- [Alle Blöcke von staticcache.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/staticcache.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
