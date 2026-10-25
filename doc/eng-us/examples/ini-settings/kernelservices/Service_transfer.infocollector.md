---
title: kernelservices.ini [Service_transfer.infocollector]
audience: developer, admin
category: examples
summary: "The settings of [Service_transfer.infocollector] in kernelservices.ini: Class, Factory, Arguments, Scope, Tags, Description, Name, Direction ..."
keywords: kernelservices.ini, Service_transfer.infocollector, INI, setting, Class, Factory, Arguments, Scope, Tags, Description, Name, Direction, Scopes, Formats, EntryURL, Permission, HelpPage, Keywords, Weight
type: generated
generated: exp:help:examples
---
# kernelservices.ini [Service_transfer.infocollector]

The block `[Service_transfer.infocollector]` of `settings/kernelservices.ini`, with 15 setting(s).

Never edit `settings/kernelservices.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/kernelservices.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/kernelservices.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/kernelservices.ini.append.php` in an extension (read when the extension is active).

## Class

Default: `expKernelTransferTool`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.infocollector]
Class=expKernelTransferTool
*/ ?>
```

## Factory

Default: `expKernelTransferTool::fromSettings`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.infocollector]
Factory=expKernelTransferTool::fromSettings
*/ ?>
```

## Arguments

Default: a list of 1: `transfer.infocollector`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.infocollector]
# an empty "Arguments[]" first replaces the shipped list; without it the items are added
Arguments[]
Arguments[]=transfer.infocollector
*/ ?>
```

## Scope

Default: `worker`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.infocollector]
Scope=worker
*/ ?>
```

## Tags

Default: a list of 1: `transfer.tool`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.infocollector]
# an empty "Tags[]" first replaces the shipped list; without it the items are added
Tags[]
Tags[]=transfer.tool
*/ ?>
```

## Description

Default: `Download what one form collected as a CSV file, from the list of collected information.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.infocollector]
Description=Download what one form collected as a CSV file, from the list of collected information.
*/ ?>
```

## Name

Default: `Collected information`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.infocollector]
Name=Collected information
*/ ?>
```

## Direction

Default: `export`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.infocollector]
Direction=export
*/ ?>
```

## Scopes

Default: a list of 1: `data`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.infocollector]
# an empty "Scopes[]" first replaces the shipped list; without it the items are added
Scopes[]
Scopes[]=data
*/ ?>
```

## Formats

Default: a list of 1: `CSV`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.infocollector]
# an empty "Formats[]" first replaces the shipped list; without it the items are added
Formats[]
Formats[]=CSV
*/ ?>
```

## EntryURL

Default: `infocollector/overview`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.infocollector]
EntryURL=infocollector/overview
*/ ?>
```

## Permission

Default: `infocollector/read`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.infocollector]
Permission=infocollector/read
*/ ?>
```

## HelpPage

Default: `guides/collected-information.md`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.infocollector]
HelpPage=guides/collected-information.md
*/ ?>
```

## Keywords

Default: a list of 5: `form submissions`, `contact form`, `survey answers`, `Formular`, `Einsendungen`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.infocollector]
# an empty "Keywords[]" first replaces the shipped list; without it the items are added
Keywords[]
Keywords[]=form submissions
Keywords[]=contact form
Keywords[]=survey answers
Keywords[]=Formular
Keywords[]=Einsendungen
*/ ?>
```

## Weight

Default: `70`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.infocollector]
Weight=70
*/ ?>
```

## Related

- [All blocks of kernelservices.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/kernelservices.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
