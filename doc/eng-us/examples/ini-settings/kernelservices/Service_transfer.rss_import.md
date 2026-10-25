---
title: kernelservices.ini [Service_transfer.rss_import]
audience: developer, admin
category: examples
summary: "The settings of [Service_transfer.rss_import] in kernelservices.ini: Class, Factory, Arguments, Scope, Tags, Description, Name, Direction ..."
keywords: kernelservices.ini, Service_transfer.rss_import, INI, setting, Class, Factory, Arguments, Scope, Tags, Description, Name, Direction, Scopes, Formats, EntryURL, Permission, HelpPage, RoundTrip, Keywords, Weight
type: generated
generated: exp:help:examples
---
# kernelservices.ini [Service_transfer.rss_import]

The block `[Service_transfer.rss_import]` of `settings/kernelservices.ini`, with 16 setting(s).

Never edit `settings/kernelservices.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/kernelservices.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/kernelservices.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/kernelservices.ini.append.php` in an extension (read when the extension is active).

## Class

Default: `expKernelTransferTool`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_import]
Class=expKernelTransferTool
*/ ?>
```

## Factory

Default: `expKernelTransferTool::fromSettings`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_import]
Factory=expKernelTransferTool::fromSettings
*/ ?>
```

## Arguments

Default: a list of 1: `transfer.rss_import`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_import]
# an empty "Arguments[]" first replaces the shipped list; without it the items are added
Arguments[]
Arguments[]=transfer.rss_import
*/ ?>
```

## Scope

Default: `worker`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_import]
Scope=worker
*/ ?>
```

## Tags

Default: a list of 1: `transfer.tool`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_import]
# an empty "Tags[]" first replaces the shipped list; without it the items are added
Tags[]
Tags[]=transfer.tool
*/ ?>
```

## Description

Default: `Create content from an RSS or Atom feed of another website, again and again by the rssimport cronjob.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_import]
Description=Create content from an RSS or Atom feed of another website, again and again by the rssimport cronjob.
*/ ?>
```

## Name

Default: `RSS import`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_import]
Name=RSS import
*/ ?>
```

## Direction

Default: `import`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_import]
Direction=import
*/ ?>
```

## Scopes

Default: a list of 1: `subtree`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_import]
# an empty "Scopes[]" first replaces the shipped list; without it the items are added
Scopes[]
Scopes[]=subtree
*/ ?>
```

## Formats

Default: a list of 2: `RSS`, `Atom`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_import]
# an empty "Formats[]" first replaces the shipped list; without it the items are added
Formats[]
Formats[]=RSS
Formats[]=Atom
*/ ?>
```

## EntryURL

Default: `rss/list`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_import]
EntryURL=rss/list
*/ ?>
```

## Permission

Default: `rss/edit`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_import]
Permission=rss/edit
*/ ?>
```

## HelpPage

Default: `guides/rss-feeds.md#4-an-import-card-and-the-cronjob`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_import]
HelpPage=guides/rss-feeds.md#4-an-import-card-and-the-cronjob
*/ ?>
```

## RoundTrip

Default: a list of 1: `transfer.rss_export`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_import]
# an empty "RoundTrip[]" first replaces the shipped list; without it the items are added
RoundTrip[]
RoundTrip[]=transfer.rss_export
*/ ?>
```

## Keywords

Default: a list of 3: `feed`, `news from another website`, `Nachrichten einlesen`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_import]
# an empty "Keywords[]" first replaces the shipped list; without it the items are added
Keywords[]
Keywords[]=feed
Keywords[]=news from another website
Keywords[]=Nachrichten einlesen
*/ ?>
```

## Weight

Default: `60`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_import]
Weight=60
*/ ?>
```

## Related

- [All blocks of kernelservices.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/kernelservices.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
