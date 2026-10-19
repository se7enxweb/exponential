---
title: site.ini [ContentSettings]
audience: developer, admin
category: examples
summary: "The settings of [ContentSettings] in site.ini: SurplusNode, CacheDir, ViewCaching, CachedViewModes, ViewCacheTweaks, CachedViewPreferences, ComplexDisplayViewModes, CacheThreshold ..."
keywords: site.ini, ContentSettings, INI, setting, SurplusNode, CacheDir, ViewCaching, CachedViewModes, ViewCacheTweaks, CachedViewPreferences, ComplexDisplayViewModes, CacheThreshold, EditDirtyObjectAction, PreViewCache, PreCacheSiteaccessArray, PreviewCacheUsers, StaticCache, StaticCacheHandler, CacheGenerationTimeout, ContentClassEditHandler, ContentObjectNameLimit, ContentObjectNameLimitSequence, DatatypeBlackListForExternal, RedirectAfterPublish
type: generated
generated: exp:help:examples
---
# site.ini [ContentSettings]

The block `[ContentSettings]` of `settings/site.ini`, with 20 setting(s).

> CompileIncludeNodeTree\[\]=final  
> CompileIncludeNodeTree\[\]=transformed  
> CompileIncludeNodeTree\[\]=static  
> CompileIncludeNodeTree\[\]=combined  
> CompileIncludeNodeTree\[\]=original  

Never edit `settings/site.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/site.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in an extension (read when the extension is active).

## SurplusNode

> Node where all new related objects are default placed  
> OBSOLETE: See content.ini for more advanced settings  

Default: `3`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
SurplusNode=3
*/ ?>
```

## CacheDir

> Where to place view cache files, it's a subdir of the global cache dir.  

Default: `content`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
CacheDir=content
*/ ?>
```

## ViewCaching

> Whether to use view caching or not  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
ViewCaching=enabled
*/ ?>
```

## CachedViewModes

> A list of viewmodes which will be cached  

Default: `full;sitemap;pdf`.

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

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
# an empty "ViewCacheTweaks[]" first replaces the shipped list; without it the items are added
ViewCacheTweaks[]
*/ ?>
```

## CachedViewPreferences

> A semicolon separated list of the user preferences (possibly with the default value after the '=' sign)  
> on which the viewmode, determined by an array key, dependes.  
> Ex.: CachedViewPreferences\[full\]=show\_more\_info=0;show\_right\_menu  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
# an empty "CachedViewPreferences[]" first replaces the shipped list; without it the items are added
CachedViewPreferences[]
*/ ?>
```

## ComplexDisplayViewModes

> A list of viewmodes which displays more than just it's immediate children  
> These viewmodes are too complex to cleanup file by file and needs to  
> be expired globally.  

Default: `sitemap`.

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

Default: `250`.

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

Default: `showversions`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
EditDirtyObjectAction=showversions
*/ ?>
```

## PreViewCache

> Set to 'enabled' to make Exponential pre-cache the view cache files on publishing  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
PreViewCache=disabled
*/ ?>
```

## PreCacheSiteaccessArray

> List of siteaccesses that will have preview cache generated on publish.  

Default: a list of 2: `admin`, `base`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
# an empty "PreCacheSiteaccessArray[]" first replaces the shipped list; without it the items are added
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

Default: a list of 1: `anonymous`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
# an empty "PreviewCacheUsers[]" first replaces the shipped list; without it the items are added
PreviewCacheUsers[]
PreviewCacheUsers[]=anonymous
*/ ?>
```

## StaticCache

> Set to 'enabled' to make Exponential store all published objects as static  
> HTML. This requires also settings in staticcache.ini and special URL rewrite  
> rules.  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
StaticCache=disabled
*/ ?>
```

## StaticCacheHandler

> Sets a Cache Handler  

Default: `eZStaticCache`.

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

Default: `60`.

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

Default: `eZContentClassEditHandler`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
ContentClassEditHandler=eZContentClassEditHandler
*/ ?>
```

## ContentObjectNameLimit

> Length limit on contentObjectName, must be between 1 and 255.  

Default: `150`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
ContentObjectNameLimit=150
*/ ?>
```

## ContentObjectNameLimitSequence

> End sequence for object name when reaching length limit.  

Default: `...`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
ContentObjectNameLimitSequence=...
*/ ?>
```

## DatatypeBlackListForExternal

> List of datatypes that should not expose their content through external calls (Ajax/REST)  

Default: a list of 1: `ezuser`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
# an empty "DatatypeBlackListForExternal[]" first replaces the shipped list; without it the items are added
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

Default: `parentNode`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSettings]
RedirectAfterPublish=parentNode
*/ ?>
```

## Related

- [All blocks of site.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/site.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
