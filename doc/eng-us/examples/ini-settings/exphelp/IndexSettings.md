---
title: exphelp.ini [IndexSettings]
audience: developer, admin
category: examples
summary: "The settings of [IndexSettings] in exphelp.ini: Roots, ActiveExtensionDocs, ExtensionDocDirs, ExtensionTopFiles, AllowlistSources, DocumentSuffixes, PlainTextWithoutSuffix, AssetSuffixes ..."
keywords: exphelp.ini, IndexSettings, INI, setting, Roots, ActiveExtensionDocs, ExtensionDocDirs, ExtensionTopFiles, AllowlistSources, DocumentSuffixes, PlainTextWithoutSuffix, AssetSuffixes, ExcludePatterns, MaxFileSize, StatFiles, StatFilesInterval, GitHistory
type: generated
generated: exp:help:examples
---
# exphelp.ini [IndexSettings]

The block `[IndexSettings]` of `settings/exphelp.ini`, with 13 setting(s).

Never edit `settings/exphelp.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/exphelp.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/exphelp.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/exphelp.ini.append.php` in an extension (read when the extension is active).

## Roots

> The directories (relative to the installation root) whose git-tracked files the help shows. Nothing outside them  
> is ever read or served.  

Default: a list of 1: `doc`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[IndexSettings]
# an empty "Roots[]" first replaces the shipped list; without it the items are added
Roots[]
Roots[]=doc
*/ ?>
```

## ActiveExtensionDocs

> enabled: the documentation of every active extension is shown too, found when the help looks (an extension  
> switched on or off is added or left out at once): the files of extension/<name>/doc tracked by the extension's own  
> repository (or listed in its share/filelist.md5), and its README, CHANGELOG, LICENSE ... (ExtensionTopFiles\[\]).  
> Their pages have the id ext/<name>/<path below doc> (ext/<name>/README.md for a top file), in the folder and  
> category "Extensions" and on exphelp/extensions. disabled: only the extensions of ExtensionDocDirs\[\].  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[IndexSettings]
ActiveExtensionDocs=enabled
*/ ?>
```

## ExtensionDocDirs

> Extensions whose doc directory is shown whether active or not, as <extension name>. Empty: none.  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[IndexSettings]
# an empty "ExtensionDocDirs[]" first replaces the shipped list; without it the items are added
ExtensionDocDirs[]
*/ ?>
```

## ExtensionTopFiles

> The files at the top of an extension's directory that are shown besides its doc/ (shell patterns, without case);  
> nothing else outside doc/ is ever read. PHP and other source files are never shown: only DocumentSuffixes\[\] (and  
> text files without a suffix that do not start like a program, #! or <?) are pages.  

Default: a list of 6: `README*`, `CHANGELOG*`, `LICENSE*`, `INSTALL*`, `USAGE*`, `COPYING*`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[IndexSettings]
# an empty "ExtensionTopFiles[]" first replaces the shipped list; without it the items are added
ExtensionTopFiles[]
ExtensionTopFiles[]=README*
ExtensionTopFiles[]=CHANGELOG*
ExtensionTopFiles[]=LICENSE*
ExtensionTopFiles[]=INSTALL*
ExtensionTopFiles[]=USAGE*
ExtensionTopFiles[]=COPYING*
*/ ?>
```

## AllowlistSources

> Where the list of git-tracked files comes from, first one that answers: the .git index (read in PHP, no program  
> is started), \`git ls-files\`, and share/filelist.md5 for an installation without .git  

Default: a list of 3: `gitindex`, `gitcommand`, `manifest`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[IndexSettings]
# an empty "AllowlistSources[]" first replaces the shipped list; without it the items are added
AllowlistSources[]
AllowlistSources[]=gitindex
AllowlistSources[]=gitcommand
AllowlistSources[]=manifest
*/ ?>
```

## DocumentSuffixes

> The suffixes of pages  

Default: a list of 4: `md`, `markdown`, `txt`, `rst`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[IndexSettings]
# an empty "DocumentSuffixes[]" first replaces the shipped list; without it the items are added
DocumentSuffixes[]
DocumentSuffixes[]=md
DocumentSuffixes[]=markdown
DocumentSuffixes[]=txt
DocumentSuffixes[]=rst
*/ ?>
```

## PlainTextWithoutSuffix

> enabled: files without a suffix (the old changelogs) are pages too, as plain text  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[IndexSettings]
PlainTextWithoutSuffix=enabled
*/ ?>
```

## AssetSuffixes

> The suffixes of images the asset view serves  

Default: a list of 5: `png`, `jpg`, `jpeg`, `gif`, `webp`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[IndexSettings]
# an empty "AssetSuffixes[]" first replaces the shipped list; without it the items are added
AssetSuffixes[]
AssetSuffixes[]=png
AssetSuffixes[]=jpg
AssetSuffixes[]=jpeg
AssetSuffixes[]=gif
AssetSuffixes[]=webp
*/ ?>
```

## ExcludePatterns

> Never served, even when tracked (shell patterns on the path relative to the installation root; a pattern without  
> "/" also matches the file name): generated documentation, HTML, SVG (it can carry scripts) and PHP, and  
> doc/README.md (the list of the language directories for readers of the repository; the help chooses the language)  

Default: a list of 6: `doc/eng-us/doxygen/*`, `doc/README.md`, `*.html`, `*.htm`, `*.svg`, `*.php`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[IndexSettings]
# an empty "ExcludePatterns[]" first replaces the shipped list; without it the items are added
ExcludePatterns[]
ExcludePatterns[]=doc/eng-us/doxygen/*
ExcludePatterns[]=doc/README.md
ExcludePatterns[]=*.html
ExcludePatterns[]=*.htm
ExcludePatterns[]=*.svg
ExcludePatterns[]=*.php
*/ ?>
```

## MaxFileSize

> Bytes; larger files are left out  

Default: `2097152`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[IndexSettings]
MaxFileSize=2097152
*/ ?>
```

## StatFiles

> enabled: every request compares the size and time of each listed file with the cached index, so an edited page  
> shows at once. disabled: only the git index, the manifest and the settings are compared (faster on big doc trees;  
> run "./console exp:help index" after changing a page without a commit)  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[IndexSettings]
StatFiles=enabled
*/ ?>
```

## StatFilesInterval

> With StatFiles enabled: seconds for which that comparison, once made, is reused by every request and process (it is  
> kept in the help cache directory). Thousands of listed files make it the most expensive part of a help request,  
> about 25 ms behind PHP-FPM and 0.5 s under Velocity. An edited page then shows within this many seconds; a page  
> added or removed changes the git index and shows at once, and "./console exp:help index" always looks again.  
> 0: compare on every request  

Default: `5`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[IndexSettings]
StatFilesInterval=5
*/ ?>
```

## GitHistory

> enabled: when git can be run, the index reads the history of the roots once per build, for each page's  
> "last changed" date and the dashboard's recent changes. disabled: the file times are used, no recent changes  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[IndexSettings]
GitHistory=enabled
*/ ?>
```

## Related

- [All blocks of exphelp.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/exphelp.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
