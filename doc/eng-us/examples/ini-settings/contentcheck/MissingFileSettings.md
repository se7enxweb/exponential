---
title: contentcheck.ini [MissingFileSettings]
audience: developer, admin
category: examples
summary: "The settings of [MissingFileSettings] in contentcheck.ini: Handlers, Tables, AliasList, ChunkSize, ScanLimit, ListLimit, ListCacheTTL, MaxFixItems ..."
keywords: contentcheck.ini, MissingFileSettings, INI, setting, Handlers, Tables, AliasList, ChunkSize, ScanLimit, ListLimit, ListCacheTTL, MaxFixItems, FixBatchSize, MaxReplaceSize, CronjobReport
type: generated
generated: exp:help:examples
---
# contentcheck.ini [MissingFileSettings]

The block `[MissingFileSettings]` of `settings/contentcheck.ini`, with 11 setting(s).

Never edit `settings/contentcheck.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/contentcheck.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/contentcheck.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/contentcheck.ini.append.php` in an extension (read when the extension is active).

## Handlers

> Missing files and image aliases (the target "Missing files" of contentcheck/brokenrelations,  
> doc/eng-us/features/6.0/content-checks.md, "Missing files"). The class that says which files a field  
> of a datatype stands for: one implementing expContentCheckFileHandler. An extension adds its  
> file datatype here.  

Default: a hash of 3: `ezimage=expContentCheckFileImage`, `ezbinaryfile=expContentCheckFileTable`, `ezmedia=expContentCheckFileTable`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[MissingFileSettings]
# an empty "Handlers[]" first replaces the shipped list; without it the items are added
Handlers[]
Handlers[ezimage]=expContentCheckFileImage
Handlers[ezbinaryfile]=expContentCheckFileTable
Handlers[ezmedia]=expContentCheckFileTable
*/ ?>
```

## Tables

> The table of each datatype handled by expContentCheckFileTable (the columns of ezbinaryfile:  
> contentobject\_attribute\_id, version, filename, original\_filename, mime\_type)  

Default: a hash of 2: `ezbinaryfile=ezbinaryfile`, `ezmedia=ezmedia`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[MissingFileSettings]
# an empty "Tables[]" first replaces the shipped list; without it the items are added
Tables[]
Tables[ezbinaryfile]=ezbinaryfile
Tables[ezmedia]=ezmedia
*/ ?>
```

## AliasList

> The aliases of image.ini an image is expected to have when "Alias never made" is ticked; empty:  
> every alias of image.ini. Aliases are made when an image is first shown in them, so a  
> missing one is normal and only listed when asked for.  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[MissingFileSettings]
# an empty "AliasList[]" first replaces the shipped list; without it the items are added
AliasList[]
*/ ?>
```

## ChunkSize

> How many objects are checked with one set of queries (10 to 1000)  

Default: `200`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[MissingFileSettings]
ChunkSize=200
*/ ?>
```

## ScanLimit

> At most this many objects are checked for one page (ChunkSize to 100000)  

Default: `5000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[MissingFileSettings]
ScanLimit=5000
*/ ?>
```

## ListLimit

> The list of missing files has a total and pages: it walks every object below the node, at  
> most this many (100 to 1000000), and keeps the findings this many seconds for its pages  
> (0 to 3600; 0: walked again for every page). A resolution forgets them.  

Default: `50000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[MissingFileSettings]
ListLimit=50000
*/ ?>
```

## ListCacheTTL

Default: `60`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[MissingFileSettings]
ListCacheTTL=60
*/ ?>
```

## MaxFixItems

> At most this many field versions in one resolution (1 to 1000)  

Default: `200`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[MissingFileSettings]
MaxFixItems=200
*/ ?>
```

## FixBatchSize

> How many field versions one request resolves; the page then shows the progress and goes on  
> with the next batch (1 to 200)  

Default: `10`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[MissingFileSettings]
FixBatchSize=10
*/ ?>
```

## MaxReplaceSize

> The largest file "Re-upload or replace" takes, in bytes; 0: what PHP takes (upload\_max\_filesize)  

Default: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[MissingFileSettings]
MaxReplaceSize=0
*/ ?>
```

## CronjobReport

> enabled: the cronjob part contentcheck also counts the missing files and alias problems of  
> the whole tree and writes the count to its output (it never resolves anything)  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[MissingFileSettings]
CronjobReport=disabled
*/ ?>
```

## Related

- [All blocks of contentcheck.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/contentcheck.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
