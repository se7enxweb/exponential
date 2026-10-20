---
title: site.ini [SearchSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [SearchSettings] in site.ini: SearchEngine, SearchViewHandling, LogSearchStats, MaximumSearchLimit, MaximumSearchTextWords, MaximumSearchTextLength, AllowEmptySearch, EnableWildcard ..."
keywords: Einstellung, site.ini, SearchSettings, INI, setting, SearchEngine, SearchViewHandling, LogSearchStats, MaximumSearchLimit, MaximumSearchTextWords, MaximumSearchTextLength, AllowEmptySearch, EnableWildcard, MinCharacterWildcard, StopWordThresholdValue, StopWordThresholdPercent, DelayedIndexing, DelayedIndexingClassList, ExtensionDirectories
type: generated
generated: exp:help:examples
---
# site.ini [SearchSettings]

Der Block `[SearchSettings]` von `settings/site.ini`, mit 14 Einstellung(en).

> Settings for the search engine framework in Exponential  

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## SearchEngine

Standard: `eZSearchEngine`.

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

Standard: `default`.

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

Standard: `disabled`.

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

Standard: `30`.

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

Standard: `16`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SearchSettings]
MaximumSearchTextWords=16
*/ ?>
```

## MaximumSearchTextLength

Standard: `1024`.

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

Standard: `disabled`.

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

Standard: `false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SearchSettings]
EnableWildcard=false
*/ ?>
```

## MinCharacterWildcard

> Set minimum number of characters in wildcard search  

Standard: `3`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SearchSettings]
MinCharacterWildcard=3
*/ ?>
```

## StopWordThresholdValue

> Only use stop words if total objects is greater than StopWordThresholdValue  

Standard: `100`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SearchSettings]
StopWordThresholdValue=100
*/ ?>
```

## StopWordThresholdPercent

> Do not search words which are present in more than StopWordThresholdPercent % of total objects  

Standard: `60`.

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

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SearchSettings]
DelayedIndexing=disabled
*/ ?>
```

## DelayedIndexingClassList

> List of class identifiers to use delayed indexing with.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SearchSettings]
# ein leeres "DelayedIndexingClassList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
DelayedIndexingClassList[]
*/ ?>
```

## ExtensionDirectories

> Search extension path list. Add extension name where the search plug-in is  
> located to this list.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SearchSettings]
# ein leeres "ExtensionDirectories[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ExtensionDirectories[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
