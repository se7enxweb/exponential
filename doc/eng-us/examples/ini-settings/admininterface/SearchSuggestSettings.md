---
title: admininterface.ini [SearchSuggestSettings]
audience: developer, admin
category: examples
summary: "The settings of [SearchSuggestSettings] in admininterface.ini: Enabled, MinLength, Groups, Limits, PagesPerDocument, PageHitClasses, Wildcard, AdminPagesCacheTTL ..."
keywords: admininterface.ini, SearchSuggestSettings, INI, setting, Enabled, MinLength, Groups, Limits, PagesPerDocument, PageHitClasses, Wildcard, AdminPagesCacheTTL, AdminMenuSkipParts
type: generated
generated: exp:help:examples
---
# admininterface.ini [SearchSuggestSettings]

The block `[SearchSuggestSettings]` of `settings/admininterface.ini`, with 9 setting(s).

Never edit `settings/admininterface.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/admininterface.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/admininterface.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/admininterface.ini.append.php` in an extension (read when the extension is active).

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

Default: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[SearchSuggestSettings]
Enabled=true
*/ ?>
```

## MinLength

> The fewest characters typed before suggestions are asked for.  

Default: `3`.

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

Default: a list of 5: `content`, `pages`, `users`, `classes`, `admin`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[SearchSuggestSettings]
# an empty "Groups[]" first replaces the shipped list; without it the items are added
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

Default: a hash of 5: `content=6`, `pages=3`, `users=3`, `classes=3`, `admin=4`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[SearchSuggestSettings]
# an empty "Limits[]" first replaces the shipped list; without it the items are added
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

Default: `2`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[SearchSuggestSettings]
PagesPerDocument=2
*/ ?>
```

## PageHitClasses

> The classes whose objects are documents with pages (pages group): the file class of the binary files.  

Default: a list of 1: `file`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[SearchSuggestSettings]
# an empty "PageHitClasses[]" first replaces the shipped list; without it the items are added
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

Default: `auto`.

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

Default: `300`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[SearchSuggestSettings]
AdminPagesCacheTTL=300
*/ ?>
```

## AdminMenuSkipParts

> Navigation parts whose left menu holds no list of pages (a tree): only their tab is suggested.  

Default: a list of 2: `ezcontentnavigationpart`, `ezmedianavigationpart`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[SearchSuggestSettings]
# an empty "AdminMenuSkipParts[]" first replaces the shipped list; without it the items are added
AdminMenuSkipParts[]
AdminMenuSkipParts[]=ezcontentnavigationpart
AdminMenuSkipParts[]=ezmedianavigationpart
*/ ?>
```

## Related

- [All blocks of admininterface.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/admininterface.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
