---
title: kernelservices.ini [Service_transfer.rss_export]
audience: developer, admin
category: examples
summary: "The settings of [Service_transfer.rss_export] in kernelservices.ini: Class, Factory, Arguments, Scope, Tags, Description, Name, Direction ..."
keywords: kernelservices.ini, Service_transfer.rss_export, INI, setting, Class, Factory, Arguments, Scope, Tags, Description, Name, Direction, Scopes, Formats, EntryURL, Permission, HelpPage, RoundTrip, Keywords, Weight
type: generated
generated: exp:help:examples
---
# kernelservices.ini [Service_transfer.rss_export]

The block `[Service_transfer.rss_export]` of `settings/kernelservices.ini`, with 16 setting(s).

Never edit `settings/kernelservices.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/kernelservices.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/kernelservices.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/kernelservices.ini.append.php` in an extension (read when the extension is active).

## Class

Default: `expKernelTransferTool`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_export]
Class=expKernelTransferTool
*/ ?>
```

## Factory

Default: `expKernelTransferTool::fromSettings`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_export]
Factory=expKernelTransferTool::fromSettings
*/ ?>
```

## Arguments

Default: a list of 1: `transfer.rss_export`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_export]
# an empty "Arguments[]" first replaces the shipped list; without it the items are added
Arguments[]
Arguments[]=transfer.rss_export
*/ ?>
```

## Scope

Default: `worker`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_export]
Scope=worker
*/ ?>
```

## Tags

Default: a list of 1: `transfer.tool`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_export]
# an empty "Tags[]" first replaces the shipped list; without it the items are added
Tags[]
Tags[]=transfer.tool
*/ ?>
```

## Description

Default: `Publish the newest items of a folder as an RSS or Atom feed, or a list of feeds as OPML.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_export]
Description=Publish the newest items of a folder as an RSS or Atom feed, or a list of feeds as OPML.
*/ ?>
```

## Name

Default: `RSS export`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_export]
Name=RSS export
*/ ?>
```

## Direction

Default: `export`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_export]
Direction=export
*/ ?>
```

## Scopes

Default: a list of 1: `subtree`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_export]
# an empty "Scopes[]" first replaces the shipped list; without it the items are added
Scopes[]
Scopes[]=subtree
*/ ?>
```

## Formats

Default: a list of 3: `RSS`, `Atom`, `OPML`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_export]
# an empty "Formats[]" first replaces the shipped list; without it the items are added
Formats[]
Formats[]=RSS
Formats[]=Atom
Formats[]=OPML
*/ ?>
```

## EntryURL

Default: `rss/list`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_export]
EntryURL=rss/list
*/ ?>
```

## Permission

Default: `rss/edit`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_export]
Permission=rss/edit
*/ ?>
```

## HelpPage

Default: `guides/rss-feeds.md#3-an-export-card`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_export]
HelpPage=guides/rss-feeds.md#3-an-export-card
*/ ?>
```

## RoundTrip

Default: a list of 1: `transfer.rss_import`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_export]
# an empty "RoundTrip[]" first replaces the shipped list; without it the items are added
RoundTrip[]
RoundTrip[]=transfer.rss_import
*/ ?>
```

## Keywords

Default: a list of 4: `feed`, `newsletter feed`, `subscribe`, `abonnieren`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_export]
# an empty "Keywords[]" first replaces the shipped list; without it the items are added
Keywords[]
Keywords[]=feed
Keywords[]=newsletter feed
Keywords[]=subscribe
Keywords[]=abonnieren
*/ ?>
```

## Weight

Default: `61`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_export]
Weight=61
*/ ?>
```

## Related

- [All blocks of kernelservices.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/kernelservices.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
