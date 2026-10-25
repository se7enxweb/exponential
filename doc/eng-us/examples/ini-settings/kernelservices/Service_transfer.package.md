---
title: kernelservices.ini [Service_transfer.package]
audience: developer, admin
category: examples
summary: "The settings of [Service_transfer.package] in kernelservices.ini: Class, Factory, Arguments, Scope, Tags, Description, Name, Direction ..."
keywords: kernelservices.ini, Service_transfer.package, INI, setting, Class, Factory, Arguments, Scope, Tags, Description, Name, Direction, Scopes, Formats, EntryURL, Permission, HelpPage, RoundTrip, Keywords, Actions, Weight
type: generated
generated: exp:help:examples
---
# kernelservices.ini [Service_transfer.package]

The block `[Service_transfer.package]` of `settings/kernelservices.ini`, with 17 setting(s).

Never edit `settings/kernelservices.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/kernelservices.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/kernelservices.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/kernelservices.ini.append.php` in an extension (read when the extension is active).

## Class

Default: `expKernelTransferTool`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.package]
Class=expKernelTransferTool
*/ ?>
```

## Factory

Default: `expKernelTransferTool::fromSettings`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.package]
Factory=expKernelTransferTool::fromSettings
*/ ?>
```

## Arguments

Default: a list of 1: `transfer.package`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.package]
# an empty "Arguments[]" first replaces the shipped list; without it the items are added
Arguments[]
Arguments[]=transfer.package
*/ ?>
```

## Scope

Default: `worker`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.package]
Scope=worker
*/ ?>
```

## Tags

Default: a list of 1: `transfer.tool`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.package]
# an empty "Tags[]" first replaces the shipped list; without it the items are added
Tags[]
Tags[]=transfer.tool
*/ ?>
```

## Description

Default: `Move content with its classes, or classes, extensions and designs, to another installation as a package file.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.package]
Description=Move content with its classes, or classes, extensions and designs, to another installation as a package file.
*/ ?>
```

## Name

Default: `Packages`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.package]
Name=Packages
*/ ?>
```

## Direction

Default: `both`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.package]
Direction=both
*/ ?>
```

## Scopes

Default: a list of 4: `site`, `subtree`, `object`, `class`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.package]
# an empty "Scopes[]" first replaces the shipped list; without it the items are added
Scopes[]
Scopes[]=site
Scopes[]=subtree
Scopes[]=object
Scopes[]=class
*/ ?>
```

## Formats

Default: a list of 1: `ezpkg`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.package]
# an empty "Formats[]" first replaces the shipped list; without it the items are added
Formats[]
Formats[]=ezpkg
*/ ?>
```

## EntryURL

Default: `package/list`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.package]
EntryURL=package/list
*/ ?>
```

## Permission

Default: `package/list|package/read`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.package]
Permission=package/list|package/read
*/ ?>
```

## HelpPage

Default: `guides/packages.md`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.package]
HelpPage=guides/packages.md
*/ ?>
```

## RoundTrip

Default: a list of 2: `transfer.package`, `transfer.xrowextract`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.package]
# an empty "RoundTrip[]" first replaces the shipped list; without it the items are added
RoundTrip[]
RoundTrip[]=transfer.package
RoundTrip[]=transfer.xrowextract
*/ ?>
```

## Keywords

Default: a list of 6: `copy to another site`, `move content between installations`, `class definitions`, `install a package`, `Paket`, `auf eine andere Website kopieren`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.package]
# an empty "Keywords[]" first replaces the shipped list; without it the items are added
Keywords[]
Keywords[]=copy to another site
Keywords[]=move content between installations
Keywords[]=class definitions
Keywords[]=install a package
Keywords[]=Paket
Keywords[]=auf eine andere Website kopieren
*/ ?>
```

## Actions

Default: a list of 2: `class|export|Export classes as a package…|package/create/(creator)/ezcontentclass|package/create`, `setup|import|Upload and install a package…|package/upload|package/import`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.package]
# an empty "Actions[]" first replaces the shipped list; without it the items are added
Actions[]
Actions[]=class|export|Export classes as a package…|package/create/(creator)/ezcontentclass|package/create
Actions[]=setup|import|Upload and install a package…|package/upload|package/import
*/ ?>
```

## Weight

Default: `10`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.package]
Weight=10
*/ ?>
```

## Related

- [All blocks of kernelservices.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/kernelservices.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
