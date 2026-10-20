---
title: site.ini [ContentSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ContentSettings] in site.ini: SurplusNode, CacheDir, ViewCaching, CachedViewModes, ViewCacheTweaks, CachedViewPreferences, ComplexDisplayViewModes, CacheThreshold ..."
keywords: Einstellung, site.ini, ContentSettings, INI, setting, SurplusNode, CacheDir, ViewCaching, CachedViewModes, ViewCacheTweaks, CachedViewPreferences, ComplexDisplayViewModes, CacheThreshold, EditDirtyObjectAction, PreViewCache, PreCacheSiteaccessArray, PreviewCacheUsers, StaticCache, StaticCacheHandler, CacheGenerationTimeout, ContentClassEditHandler, ContentObjectNameLimit, ContentObjectNameLimitSequence, DatatypeBlackListForExternal, RedirectAfterPublish
type: generated
generated: exp:help:examples
---
# site.ini [ContentSettings]

Der Block `[ContentSettings]` von `settings/site.ini`, mit 20 Einstellung(en).

> CompileIncludeNodeTree\[\]=final  
> CompileIncludeNodeTree\[\]=transformed  
> CompileIncludeNodeTree\[\]=static  
> CompileIncludeNodeTree\[\]=combined  
> CompileIncludeNodeTree\[\]=original  

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## SurplusNode

> Node where all new related objects are default placed  
> OBSOLETE: See content.ini for more advanced settings  

Standard: `3`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
SurplusNode=3
*/ ?>
```

## CacheDir

> Where to place view cache files, it's a subdir of the global cache dir.  

Standard: `content`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
CacheDir=content
*/ ?>
```

## ViewCaching

> Whether to use view caching or not  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
ViewCaching=enabled
*/ ?>
```

## CachedViewModes

> A list of viewmodes which will be cached  

Standard: `full;sitemap;pdf`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
CachedViewModes=full;sitemap;pdf
*/ ?>
```

## ViewCacheTweaks

> View cache tweaks for cache strategy on specific node id's  
> ViewCacheTweaks\[<node\_id>\]=<setting>\[;<setting2>\]  
> currently supported settings:  
> disabled: Same as setting cache\_ttl=0 in template, just a bit more efficient by knowing about it in advance  
> pr\_user: cache page pr user, more efficient then disabling view cache and using cache-blocks  
> protocol: builds different cache based on the request protocol (http vs https)  
> ignore\_discountlist do not include users shop discountlist in cache hash  
> ignore\_userroles do not include users roles in cache hash  
> ignore\_userlimitedlist do not include users limted policy assignement list in cache hash  
> ignore\_viewparameters do not include view parameters in cache hash  
> ignore\_userpreferences do not include users preferencest in cache hash  
> ignore\_siteaccess\_name do not include siteaccess name in cache path, use default siteaccess instead  
> ignore\_siteaccess\_type do not include siteaccess type in cache hash  
> Note: you can also set 'global' as <node\_id> to set tweaks globally for all tweaks but 'disabled' as you have other settings for that  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
# ein leeres "ViewCacheTweaks[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ViewCacheTweaks[]
*/ ?>
```

## CachedViewPreferences

> A semicolon separated list of the user preferences (possibly with the default value after the '=' sign)  
> on which the viewmode, determined by an array key, dependes.  
> Ex.: CachedViewPreferences\[full\]=show\_more\_info=0;show\_right\_menu  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
# ein leeres "CachedViewPreferences[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
CachedViewPreferences[]
*/ ?>
```

## ComplexDisplayViewModes

> A list of viewmodes which displays more than just it's immediate children  
> These viewmodes are too complex to cleanup file by file and needs to  
> be expired globally.  

Standard: `sitemap`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
ComplexDisplayViewModes=sitemap
*/ ?>
```

## CacheThreshold

> The threshold for file cleanup, if it is exceeded a global expiry is used instead  
> The value is the number of affected nodes  

Standard: `250`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
CacheThreshold=250
*/ ?>
```

## EditDirtyObjectAction

> What to do when editing an object that has draft versions newer than the current version.  
> Use 'showversions' to select which version to edit, or 'usecurrent' to always edit the current version.  

Standard: `showversions`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
EditDirtyObjectAction=showversions
*/ ?>
```

## PreViewCache

> Set to 'enabled' to make Exponential pre-cache the view cache files on publishing  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
PreViewCache=disabled
*/ ?>
```

## PreCacheSiteaccessArray

> List of siteaccesses that will have preview cache generated on publish.  

Standard: eine Liste mit 2: `admin`, `base`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
# ein leeres "PreCacheSiteaccessArray[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PreCacheSiteaccessArray[]
PreCacheSiteaccessArray[]=admin
PreCacheSiteaccessArray[]=base
*/ ?>
```

## PreviewCacheUsers

> A list of users to pre-generate the content cache for  
> The list should contain the User ID or a special identifier, possible identifiers are:  
> - 'anonymous' for the anonymous user.  
> - 'current' for the currently logged in user  
> The default is to pregenerate for anonymous users  

Standard: eine Liste mit 1: `anonymous`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
# ein leeres "PreviewCacheUsers[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PreviewCacheUsers[]
PreviewCacheUsers[]=anonymous
*/ ?>
```

## StaticCache

> Set to 'enabled' to make Exponential store all published objects as static  
> HTML. This requires also settings in staticcache.ini and special URL rewrite  
> rules.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
StaticCache=disabled
*/ ?>
```

## StaticCacheHandler

> Sets a Cache Handler  

Standard: `eZStaticCache`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
StaticCacheHandler=eZStaticCache
*/ ?>
```

## CacheGenerationTimeout

> maximum cache generation time. If a file stays in generation mode for more than  
> this value in seconds, it is considered timed out (process died, for instance),  
> and generation is taken over by the requesting process  

Standard: `60`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
CacheGenerationTimeout=60
*/ ?>
```

## ContentClassEditHandler

> Handler responsible of storing changes made on a content class when objects exist  
> and require an update. This might be a huge task that can lead to time out,  
> you may consider using the 'eZContentClassEditDeferredHandler' provided with the  
> eZ Script Monitor extension.  

Standard: `eZContentClassEditHandler`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
ContentClassEditHandler=eZContentClassEditHandler
*/ ?>
```

## ContentObjectNameLimit

> Length limit on contentObjectName, must be between 1 and 255.  

Standard: `150`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
ContentObjectNameLimit=150
*/ ?>
```

## ContentObjectNameLimitSequence

> End sequence for object name when reaching length limit.  

Standard: `...`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
ContentObjectNameLimitSequence=...
*/ ?>
```

## DatatypeBlackListForExternal

> List of datatypes that should not expose their content through external calls (Ajax/REST)  

Standard: eine Liste mit 1: `ezuser`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
# ein leeres "DatatypeBlackListForExternal[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
DatatypeBlackListForExternal[]
DatatypeBlackListForExternal[]=ezuser
*/ ?>
```

## RedirectAfterPublish

> Use this setting to change the redirection after publishing a node.  
> It can contain the following values:  
> - parentNode - redirect to the parent of the edited object's main node (default value)  
> - node       - redirect to the edited object's main node  
> - rootNode   - redirect to siteaccess root node (ie. content.ini/NodeSettings/rootNode )  
> - uri        - redirect to a specific URI (see RedirectAfterPublishUri settings below)  

Standard: `parentNode`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
RedirectAfterPublish=parentNode
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
