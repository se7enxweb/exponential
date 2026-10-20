---
title: rest.ini [CacheSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [CacheSettings] in rest.ini: ApplicationCache, ApplicationCacheDefault, DefaultCacheTTL, RouteApcCache, RouteApcCacheTTL"
keywords: Einstellung, rest.ini, CacheSettings, INI, setting, ApplicationCache, ApplicationCacheDefault, DefaultCacheTTL, RouteApcCache, RouteApcCacheTTL
type: generated
generated: exp:help:examples
---
# rest.ini [CacheSettings]

Der Block `[CacheSettings]` von `settings/rest.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/rest.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/rest.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/rest.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/rest.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ApplicationCache

> Global switch to enable/disable REST application cache  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[CacheSettings]
ApplicationCache=enabled
*/ ?>
```

## ApplicationCacheDefault

> Default value if no specific value has been defined for your controller/action  
> If enabled, result of each service call will be cached  
> You can refine this with setting specific to your controller/action  
> The system will look for a \[<controllerClass>\_<action>\_CacheSettings\] block to check  
> if cache can be used, and if so, which TTL to use  
> If this block cannot be found, the system will search at the controller level,  
> and so look for a \[<controllerClass>\_CacheSettings\] block.  
> See example block below for more information  
> Basically this setting allow you to activate the cache to your controllers/actions individually  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[CacheSettings]
ApplicationCacheDefault=enabled
*/ ?>
```

## DefaultCacheTTL

> Set default TTL to 10min, in seconds  

Standard: `600`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[CacheSettings]
DefaultCacheTTL=600
*/ ?>
```

## RouteApcCache

> Example for action "viewContent", in "ezpRestContentController" controller class  
> \[ezpRestContentController\_viewContent\_CacheSettings\]  
> ApplicationCache=enabled  
> CacheTTL=3600  
> Below an example for every actions contained in "ezpRestContentController" controller class  
> \[ezpRestContentController\_CacheSettings\]  
> ApplicationCache=enabled  
> CacheTTL=1200  
> Switch to enable/disable Routes cache with APC  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[CacheSettings]
RouteApcCache=enabled
*/ ?>
```

## RouteApcCacheTTL

> TTL for Route APC cache, in seconds  

Standard: `3600`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[CacheSettings]
RouteApcCacheTTL=3600
*/ ?>
```

## Siehe auch

- [Alle Blöcke von rest.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/rest.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
