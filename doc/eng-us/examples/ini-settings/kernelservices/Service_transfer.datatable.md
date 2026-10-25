---
title: kernelservices.ini [Service_transfer.datatable]
audience: developer, admin
category: examples
summary: "The settings of [Service_transfer.datatable] in kernelservices.ini: Class, Factory, Arguments, Scope, Tags, Description, Name, Direction ..."
keywords: kernelservices.ini, Service_transfer.datatable, INI, setting, Class, Factory, Arguments, Scope, Tags, Description, Name, Direction, Scopes, Formats, EntryURL, Permission, Modules, HelpPage, RoundTrip, Keywords, Actions, Weight
type: generated
generated: exp:help:examples
---
# kernelservices.ini [Service_transfer.datatable]

The block `[Service_transfer.datatable]` of `settings/kernelservices.ini`, with 18 setting(s).

Never edit `settings/kernelservices.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/kernelservices.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/kernelservices.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/kernelservices.ini.append.php` in an extension (read when the extension is active).

## Class

Default: `expKernelTransferTool`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.datatable]
Class=expKernelTransferTool
*/ ?>
```

## Factory

Default: `expKernelTransferTool::fromSettings`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.datatable]
Factory=expKernelTransferTool::fromSettings
*/ ?>
```

## Arguments

Default: a list of 1: `transfer.datatable`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.datatable]
# an empty "Arguments[]" first replaces the shipped list; without it the items are added
Arguments[]
Arguments[]=transfer.datatable
*/ ?>
```

## Scope

Default: `worker`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.datatable]
Scope=worker
*/ ?>
```

## Tags

Default: a list of 1: `transfer.tool`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.datatable]
# an empty "Tags[]" first replaces the shipped list; without it the items are added
Tags[]
Tags[]=transfer.tool
*/ ?>
```

## Description

Default: `The tables of the persistent classes and the named database connections: describe, create and upgrade tables on any connection, browse their rows and see what points at content.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.datatable]
Description=The tables of the persistent classes and the named database connections: describe, create and upgrade tables on any connection, browse their rows and see what points at content.
*/ ?>
```

## Name

Default: `Data tables`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.datatable]
Name=Data tables
*/ ?>
```

## Direction

Default: `both`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.datatable]
Direction=both
*/ ?>
```

## Scopes

Default: a list of 3: `data`, `class`, `subtree`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.datatable]
# an empty "Scopes[]" first replaces the shipped list; without it the items are added
Scopes[]
Scopes[]=data
Scopes[]=class
Scopes[]=subtree
*/ ?>
```

## Formats

Default: a list of 2: `SQL`, `.dba`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.datatable]
# an empty "Formats[]" first replaces the shipped list; without it the items are added
Formats[]
Formats[]=SQL
Formats[]=.dba
*/ ?>
```

## EntryURL

Default: `datatable/dashboard`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.datatable]
EntryURL=datatable/dashboard
*/ ?>
```

## Permission

Default: `datatable/read`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.datatable]
Permission=datatable/read
*/ ?>
```

## Modules

Default: a list of 1: `secrets`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.datatable]
# an empty "Modules[]" first replaces the shipped list; without it the items are added
Modules[]
Modules[]=secrets
*/ ?>
```

## HelpPage

Default: `features/6.0/data-tables.md`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.datatable]
HelpPage=features/6.0/data-tables.md
*/ ?>
```

## RoundTrip

Default: a list of 1: `transfer.sqliimport`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.datatable]
# an empty "RoundTrip[]" first replaces the shipped list; without it the items are added
RoundTrip[]
RoundTrip[]=transfer.sqliimport
*/ ?>
```

## Keywords

Default: a list of 13: `custom table`, `module table`, `persistent object`, `database connection`, `external database`, `SQLite`, `MySQL`, `PostgreSQL`, `MongoDB`, `Oracle`, `SQL Server`, `eigene Tabelle` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.datatable]
# an empty "Keywords[]" first replaces the shipped list; without it the items are added
Keywords[]
Keywords[]=custom table
Keywords[]=module table
Keywords[]=persistent object
Keywords[]=database connection
Keywords[]=external database
Keywords[]=SQLite
Keywords[]=MySQL
Keywords[]=PostgreSQL
Keywords[]=MongoDB
Keywords[]=Oracle
Keywords[]=SQL Server
Keywords[]=eigene Tabelle
Keywords[]=Datenbankverbindung
*/ ?>
```

## Actions

Default: a list of 2: `setup|import|Create or upgrade a table on a database connection…|datatable/schema|datatable/manage`, `setup|export|Browse the tables of the persistent classes…|datatable/dashboard`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.datatable]
# an empty "Actions[]" first replaces the shipped list; without it the items are added
Actions[]
Actions[]=setup|import|Create or upgrade a table on a database connection…|datatable/schema|datatable/manage
Actions[]=setup|export|Browse the tables of the persistent classes…|datatable/dashboard
*/ ?>
```

## Weight

Default: `75`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.datatable]
Weight=75
*/ ?>
```

## Related

- [All blocks of kernelservices.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/kernelservices.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
