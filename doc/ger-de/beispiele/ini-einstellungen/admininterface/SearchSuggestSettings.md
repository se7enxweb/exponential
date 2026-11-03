---
title: admininterface.ini [SearchSuggestSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [SearchSuggestSettings] in admininterface.ini: Enabled, MinLength, Groups, Limits, PagesPerDocument, PageHitClasses, Wildcard, AdminPagesCacheTTL ..."
keywords: Einstellung, admininterface.ini, SearchSuggestSettings, INI, setting, Enabled, MinLength, Groups, Limits, PagesPerDocument, PageHitClasses, Wildcard, AdminPagesCacheTTL, AdminMenuSkipParts
type: generated
generated: exp:help:examples
---
# admininterface.ini [SearchSuggestSettings]

Der Block `[SearchSuggestSettings]` von `settings/admininterface.ini`, mit 9 Einstellung(en).

Bearbeiten Sie `settings/admininterface.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/admininterface.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/admininterface.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/admininterface.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Enabled

> Suggestions under the header search field of the administration (design admin and admin4) while the user types:  
> content, PDF pages, users, content classes and the administration's own pages, in groups, each only as far as  
> the user may see it, with "View all results" leading to the full search (content/search). The script  
> (javascript/page/search\_suggest.js) asks content/suggest/(text)/<text> for them (expSearchSuggest).  
> Guide: doc/eng-us/help/searching.md ("Suggestions while typing").  
>
> The view content/suggest needs the policy content/read (as content/search): a user without it gets no  
> suggestions at all, not even the administration's pages, and the field is a plain search field for them.  
> The groups pages and limits below also bound the load: one request per typed pause (250 ms), plus at most  
> Limits\[pages\] requests for the pages of documents.  
>
> true or false. false: the field is a plain search field, content/suggest answers 404.  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[SearchSuggestSettings]
Enabled=true
*/ ?>
```

## MinLength

> The fewest characters typed before suggestions are asked for.  

Standard: `3`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[SearchSuggestSettings]
MinLength=3
*/ ?>
```

## Groups

> The groups, in the order they are shown. Leave one out to switch it off:  
>   content  the content search (eZ Find when it is the search engine, else the kernel's search engine), with the  
>            user's read permissions applied by the search itself  
>   pages    the PDF pages holding the words: only with eZ Find and the extension ezfindpdfdeepsearch active;  
>            without them a document is suggested as a whole, in the content group  
>   users    user accounts by login, e-mail address or name, only those the user may read  
>   classes  content classes by name or identifier, only for a user who may open the class module  
>   admin    the top tabs and the links of their left menus, as the design draws them for the user ("Setup › Cache")  

Standard: eine Liste mit 5: `content`, `pages`, `users`, `classes`, `admin`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[SearchSuggestSettings]
# ein leeres "Groups[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Groups[]
Groups[]=content
Groups[]=pages
Groups[]=users
Groups[]=classes
Groups[]=admin
*/ ?>
```

## Limits

> The most suggestions per group (0 to 20; 0 switches the group off).  

Standard: ein Hash mit 5: `content=6`, `pages=3`, `users=3`, `classes=3`, `admin=4`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[SearchSuggestSettings]
# ein leeres "Limits[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Limits[]
Limits[content]=6
Limits[pages]=3
Limits[users]=3
Limits[classes]=3
Limits[admin]=4
*/ ?>
```

## PagesPerDocument

> The pages of one document, at most (pages group).  

Standard: `2`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[SearchSuggestSettings]
PagesPerDocument=2
*/ ?>
```

## PageHitClasses

> The classes whose objects are documents with pages (pages group): the file class of the binary files.  

Standard: eine Liste mit 1: `file`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[SearchSuggestSettings]
# ein leeres "PageHitClasses[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PageHitClasses[]
PageHitClasses[]=file
*/ ?>
```

## Wildcard

> A "\*" after the last word typed, so that a word is found while it is typed ("Oste" finds "Ostern"):  
>   auto      only when site.ini \[SearchSettings\] EnableWildcard=true (both search engines honour it only then),  
>             and only for a word of MinCharacterWildcard characters or more  
>   enabled   always (for a word of MinCharacterWildcard characters or more)  
>   disabled  never: only whole words are found  

Standard: `auto`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[SearchSuggestSettings]
Wildcard=auto
*/ ?>
```

## AdminPagesCacheTTL

> The administration's pages of a user are drawn from the menus once and kept for this many seconds, per user  
> and rights (cache item searchsuggest, cleared with the template caches). 0: drawn on every request.  

Standard: `300`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[SearchSuggestSettings]
AdminPagesCacheTTL=300
*/ ?>
```

## AdminMenuSkipParts

> Navigation parts whose left menu holds no list of pages (a tree): only their tab is suggested.  

Standard: eine Liste mit 2: `ezcontentnavigationpart`, `ezmedianavigationpart`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[SearchSuggestSettings]
# ein leeres "AdminMenuSkipParts[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AdminMenuSkipParts[]
AdminMenuSkipParts[]=ezcontentnavigationpart
AdminMenuSkipParts[]=ezmedianavigationpart
*/ ?>
```

## Siehe auch

- [Alle Blöcke von admininterface.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/admininterface.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
