---
title: site.ini [RSSSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [RSSSettings] in site.ini: ActiveExtensions, NumberOfObjectsList, NumberOfObjectsDefault, CacheTime, AvailableVersionList, OPMLMaxOutlines, DefaultVersion, DefaultFeedItemClasses"
keywords: Einstellung, site.ini, RSSSettings, INI, setting, ActiveExtensions, NumberOfObjectsList, NumberOfObjectsDefault, CacheTime, AvailableVersionList, OPMLMaxOutlines, DefaultVersion, DefaultFeedItemClasses
type: generated
generated: exp:help:examples
---
# site.ini [RSSSettings]

Der Block `[RSSSettings]` von `settings/site.ini`, mit 8 Einstellung(en).

> Settings for RSS  

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ActiveExtensions

> RSS import handler Extension settings  
> Must be placed in <extension directory>/<active extension>/rss/ezrssimporthandler.php  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RSSSettings]
# ein leeres "ActiveExtensions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ActiveExtensions[]
*/ ?>
```

## NumberOfObjectsList

> List of numbers of objects being included in the RSS feed.  
> This list is being shown in the RSS export edit admin interface.  

Standard: eine Liste mit 11: `5`, `10`, `20`, `30`, `40`, `50`, `100`, `250`, `350`, `500`, `1000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RSSSettings]
# ein leeres "NumberOfObjectsList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
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

Standard: `20`.

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

Standard: `1200`.

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

Standard: eine Liste mit 5: `1.0`, `2.0`, `ATOM`, `OPML`, `ITUNES`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RSSSettings]
# ein leeres "AvailableVersionList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
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

Standard: `5000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RSSSettings]
OPMLMaxOutlines=5000
*/ ?>
```

## DefaultVersion

> Selected by default in the RSS export edit GUI.  

Standard: `2.0`.

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

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RSSSettings]
# ein leeres "DefaultFeedItemClasses[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
DefaultFeedItemClasses[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
