---
title: staticcache.ini [CacheSettings]
audience: developer, admin
category: examples
summary: "The settings of [CacheSettings] in staticcache.ini: HostName, SourceProtocol, StaticStorageDir, MaxCacheDepth, CachedURLArray, CachedSiteAccesses, AlwaysUpdateArray, CronjobCacheClear ..."
keywords: staticcache.ini, CacheSettings, INI, setting, HostName, SourceProtocol, StaticStorageDir, MaxCacheDepth, CachedURLArray, CachedSiteAccesses, AlwaysUpdateArray, CronjobCacheClear, AppendGeneratedTime
type: generated
generated: exp:help:examples
---
# staticcache.ini [CacheSettings]

The block `[CacheSettings]` of `settings/staticcache.ini`, with 9 setting(s).

> Exponential configuration file.  
>
> NOTE: It is not recommended to edit this file directly, instead  
>       a file in override should be created for setting the  
>       values that is required for your site. Either create  
>       a file called settings/override/staticcache.ini.append or  
>       settings/override/staticcache.ini.append.php for more security  
>       in non-virtualhost modes (the .php file may already be present  
>       and can be used for this purpose).  

Never edit `settings/staticcache.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/staticcache.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/staticcache.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/staticcache.ini.append.php` in an extension (read when the extension is active).

## HostName

> The host to fetch pages from when generating the cache.  
>
> Leave this empty. It is deprecated since 4.4 and empty is the correct  
> value: each siteaccess is then fetched from its own  
> site.ini.\[SiteSettings\].SiteURL, which is what a multi site installation  
> needs. It shipped as "localhost" for years, which sent every fetch to  
> whatever the web server answers for that name - usually the hosting  
> panel's placeholder, never this site - and the run failed silently.  

Default: empty.

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

Default: `http`.

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

Default: `static`.

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

Default: `12`.

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

Default: a list of 2: `/`, `/*`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[CacheSettings]
# an empty "CachedURLArray[]" first replaces the shipped list; without it the items are added
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

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[CacheSettings]
# an empty "CachedSiteAccesses[]" first replaces the shipped list; without it the items are added
CachedSiteAccesses[]
*/ ?>
```

## AlwaysUpdateArray

> A list of locations that will be updated whenever an object is published. You  
> can NOT use a wildcard here.  

Default: a list of 1: `/`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[CacheSettings]
# an empty "AlwaysUpdateArray[]" first replaces the shipped list; without it the items are added
AlwaysUpdateArray[]
AlwaysUpdateArray[]=/
*/ ?>
```

## CronjobCacheClear

> Defer cache generation to cronjob.  

Default: `disabled`.

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

Default: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[CacheSettings]
AppendGeneratedTime=true
*/ ?>
```

## Related

- [All blocks of staticcache.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/staticcache.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
