---
title: site.ini [SearchSettings]
audience: developer, admin
category: examples
summary: "The settings of [SearchSettings] in site.ini: SearchEngine, SearchViewHandling, LogSearchStats, MaximumSearchLimit, MaximumSearchTextWords, MaximumSearchTextLength, AllowEmptySearch, EnableWildcard ..."
keywords: site.ini, SearchSettings, INI, setting, SearchEngine, SearchViewHandling, LogSearchStats, MaximumSearchLimit, MaximumSearchTextWords, MaximumSearchTextLength, AllowEmptySearch, EnableWildcard, MinCharacterWildcard, StopWordThresholdValue, StopWordThresholdPercent, DelayedIndexing, DelayedIndexingClassList, ExtensionDirectories
type: generated
generated: exp:help:examples
---
# site.ini [SearchSettings]

The block `[SearchSettings]` of `settings/site.ini`, with 14 setting(s).

> Settings for the search engine framework in Exponential  

Never edit `settings/site.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/site.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in an extension (read when the extension is active).

## SearchEngine

Default: `eZSearchEngine`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SearchSettings]
SearchEngine=eZSearchEngine
*/ ?>
```

## SearchViewHandling

> How the search and advanced search page works,  
> by default the search view does the search but it's  
> possible to do the search in pure template code.  
> Possible values are:  
> default - The view code does the search and passes result to template  
> template - The template does the search and passes the result back to the view code  

Default: `default`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SearchSettings]
SearchViewHandling=default
*/ ?>
```

## LogSearchStats

> Whether to log search stats or not  
> The search statistics store every search phrase a visitor typed, which can  
> hold personal data. Disabled by default; enable it in an override when the  
> statistics in the admin interface are needed.  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SearchSettings]
LogSearchStats=disabled
*/ ?>
```

## MaximumSearchLimit

> The upper value for search limits fetched from HTTP Post variables  
> This is needed to avoid users on a site to have ridiciously huge limits  

Default: `30`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SearchSettings]
MaximumSearchLimit=30
*/ ?>
```

## MaximumSearchTextWords

> The search views (content/search and content/advancedsearch) search for at most this many words  
> and characters of a search text and cut a longer one; every word is a clause of the search engine's  
> query, and with too many of them the search fails instead of giving results (eZ Find: too many clauses)  

Default: `16`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SearchSettings]
MaximumSearchTextWords=16
*/ ?>
```

## MaximumSearchTextLength

Default: `1024`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SearchSettings]
MaximumSearchTextLength=1024
*/ ?>
```

## AllowEmptySearch

> Controls whether empty searches are allowed, by default it is disabled  
> since those kind of searches will take a lot of resources.  
> If empty searching is enabled you must make sure your template  
> submits enough limitations to the search, if not expect to really  
> slow down your site  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SearchSettings]
AllowEmptySearch=disabled
*/ ?>
```

## EnableWildcard

> Enable or disable wildcard support for search. Note that enabling wildcard search  
> might heavily influence the performance of the system.  

Default: `false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SearchSettings]
EnableWildcard=false
*/ ?>
```

## MinCharacterWildcard

> Set minimum number of characters in wildcard search  

Default: `3`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SearchSettings]
MinCharacterWildcard=3
*/ ?>
```

## StopWordThresholdValue

> Only use stop words if total objects is greater than StopWordThresholdValue  

Default: `100`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SearchSettings]
StopWordThresholdValue=100
*/ ?>
```

## StopWordThresholdPercent

> Do not search words which are present in more than StopWordThresholdPercent % of total objects  

Default: `60`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SearchSettings]
StopWordThresholdPercent=60
*/ ?>
```

## DelayedIndexing

> Controls whether objects are indexed when they are published, or delayed  
> and processed later by a cronjob, can be set class based via 'classbased'  
> In this case DelayedIndexingClassList should be set with class identifiers  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SearchSettings]
DelayedIndexing=disabled
*/ ?>
```

## DelayedIndexingClassList

> List of class identifiers to use delayed indexing with.  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SearchSettings]
# an empty "DelayedIndexingClassList[]" first replaces the shipped list; without it the items are added
DelayedIndexingClassList[]
*/ ?>
```

## ExtensionDirectories

> Search extension path list. Add extension name where the search plug-in is  
> located to this list.  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SearchSettings]
# an empty "ExtensionDirectories[]" first replaces the shipped list; without it the items are added
ExtensionDirectories[]
*/ ?>
```

## Related

- [All blocks of site.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/site.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
