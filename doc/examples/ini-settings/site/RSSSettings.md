---
title: site.ini [RSSSettings]
audience: developer, admin
category: examples
summary: "The settings of [RSSSettings] in site.ini: ActiveExtensions, NumberOfObjectsList, NumberOfObjectsDefault, CacheTime, AvailableVersionList, OPMLMaxOutlines, DefaultVersion, DefaultFeedItemClasses"
keywords: site.ini, RSSSettings, INI, setting, ActiveExtensions, NumberOfObjectsList, NumberOfObjectsDefault, CacheTime, AvailableVersionList, OPMLMaxOutlines, DefaultVersion, DefaultFeedItemClasses
type: generated
generated: exp:help:examples
---
# site.ini [RSSSettings]

The block `[RSSSettings]` of `settings/site.ini`, with 8 setting(s).

> Settings for RSS  

Never edit `settings/site.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/site.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in an extension (read when the extension is active).

## ActiveExtensions

> RSS import handler Extension settings  
> Must be placed in <extension directory>/<active extension>/rss/ezrssimporthandler.php  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RSSSettings]
# an empty "ActiveExtensions[]" first replaces the shipped list; without it the items are added
ActiveExtensions[]
*/ ?>
```

## NumberOfObjectsList

> List of numbers of objects being included in the RSS feed.  
> This list is being shown in the RSS export edit admin interface.  

Default: a list of 11: `5`, `10`, `20`, `30`, `40`, `50`, `100`, `250`, `350`, `500`, `1000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RSSSettings]
# an empty "NumberOfObjectsList[]" first replaces the shipped list; without it the items are added
NumberOfObjectsList[]
NumberOfObjectsList[]=5
NumberOfObjectsList[]=10
NumberOfObjectsList[]=20
NumberOfObjectsList[]=30
NumberOfObjectsList[]=40
NumberOfObjectsList[]=50
NumberOfObjectsList[]=100
NumberOfObjectsList[]=250
NumberOfObjectsList[]=350
NumberOfObjectsList[]=500
NumberOfObjectsList[]=1000
*/ ?>
```

## NumberOfObjectsDefault

> Selected by default in the RSS export edit GUI.  

Default: `20`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RSSSettings]
NumberOfObjectsDefault=20
*/ ?>
```

## CacheTime

> Cache Time in Seconds  
>
> The timespan a RSS feed is served from the cache in seconds.  
> After the end of the timespan, the RSS feed is regenerated and  
> again served from the cache.  
>
> This means that changes to the content being fed show up  
> after the maximum cache time at the latest.  
>
> "CacheTime = 0" turns off cacheing.  

Default: `1200`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RSSSettings]
CacheTime=1200
*/ ?>
```

## AvailableVersionList

> The list of available RSS versions.  
> OPML is a list of feeds rather than a list of articles: an export set to it  
> has no content source, and lists the other exports on this installation, or  
> content nodes, instead. See kernel/classes/ezrssexportopmlitem.php.  
> An Apple Podcasts feed: RSS 2.0 with the iTunes namespace, which Apple reads  
> for everything RSS has no room for - the show's author, owner, artwork,  
> category and rating. The episodes come from content sources like any other  
> feed, and each one needs its enclosure mapped to a media or file attribute.  
> See eZRSSExport::generateITunes().  

Default: a list of 5: `1.0`, `2.0`, `ATOM`, `OPML`, `ITUNES`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RSSSettings]
# an empty "AvailableVersionList[]" first replaces the shipped list; without it the items are added
AvailableVersionList[]
AvailableVersionList[]=1.0
AvailableVersionList[]=2.0
AvailableVersionList[]=ATOM
AvailableVersionList[]=OPML
AvailableVersionList[]=ITUNES
*/ ?>
```

## OPMLMaxOutlines

> The most outlines one OPML document will carry. A document large enough to  
> exhaust memory is served from a public address, so it would take the site  
> down rather than just itself. Bounded at 50000 whatever this says.  

Default: `5000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RSSSettings]
OPMLMaxOutlines=5000
*/ ?>
```

## DefaultVersion

> Selected by default in the RSS export edit GUI.  

Default: `2.0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RSSSettings]
DefaultVersion=2.0
*/ ?>
```

## DefaultFeedItemClasses

> Default source to child mapping  
> DefaultFeedItemClasses\[<source\_class>\]=<child\_class>\[;<child\_class\_2>\]  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RSSSettings]
# an empty "DefaultFeedItemClasses[]" first replaces the shipped list; without it the items are added
DefaultFeedItemClasses[]
*/ ?>
```

## Related

- [All blocks of site.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/site.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
