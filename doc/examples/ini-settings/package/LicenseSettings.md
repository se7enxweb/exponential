---
title: package.ini [LicenseSettings]
audience: developer, admin
category: examples
summary: "The settings of [LicenseSettings] in package.ini: DefaultLicense, GroupList, LicenseList, AliasList"
keywords: package.ini, LicenseSettings, INI, setting, DefaultLicense, GroupList, LicenseList, AliasList
type: generated
generated: exp:help:examples
---
# package.ini [LicenseSettings]

The block `[LicenseSettings]` of `settings/package.ini`, with 4 setting(s).

Never edit `settings/package.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/package.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/package.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/package.ini.append.php` in an extension (read when the extension is active).

## DefaultLicense

> The licenses a package can be given in the package creation wizards  
> (Setup > Packages > Create new package, every creator's "Package information"  
> step). The license is a strict choice from this list: the step shows a  
> drop-down and refuses any value that is not listed here.  
>
> The value stored in a package's package.xml (<licence>) is the license's  
> identifier. Use the SPDX identifier where one exists (https://spdx.org/licenses/),  
> otherwise an SPDX style "LicenseRef-<name>" identifier.  
>
> The identifier chosen when the step opens. Must be one of LicenseList.  

Default: `GPL-2.0-or-later`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[LicenseSettings]
DefaultLicense=GPL-2.0-or-later
*/ ?>
```

## GroupList

> The drop-down's groups (<optgroup>), in the order shown. Each group has a  
> \[LicenseGroup\_<group>\] block with Name=, the group's heading. A license whose  
> Group= is not listed here is shown under "Other licenses" at the end.  

Default: a list of 9: `software`, `documentation`, `cc-4.0`, `cc-3.0`, `cc-2.5`, `cc-2.0`, `cc-1.0`, `public-domain`, `proprietary`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[LicenseSettings]
# an empty "GroupList[]" first replaces the shipped list; without it the items are added
GroupList[]
GroupList[]=software
GroupList[]=documentation
GroupList[]=cc-4.0
GroupList[]=cc-3.0
GroupList[]=cc-2.5
GroupList[]=cc-2.0
GroupList[]=cc-1.0
GroupList[]=public-domain
GroupList[]=proprietary
*/ ?>
```

## LicenseList

> The licenses that can be chosen, in the order shown within their group. Each  
> one has a \[License\_<identifier>\] block:  
>   Name=         the license's official name (shown as it is, not translated)  
>   URL=          the official page of the license  
>   Group=        the group it is listed under (see GroupList)  
>   Description=  optional, a short text shown below the drop-down  
>
> To add a license, in settings/override/package.ini.append.php:  
>   \[LicenseSettings\]  
>   LicenseList\[\]=Apache-2.0  
>   \[License\_Apache-2.0\]  
>   Name=Apache License 2.0  
>   URL=https://www.apache.org/licenses/LICENSE-2.0  
>   Group=software  
>
> To replace the whole list, start with an empty LicenseList\[\] line in the  
> override and list only the licenses you want (and set DefaultLicense to one  
> of them).  

Default: a list of 46: `GPL-2.0-or-later`, `GPL-2.0-only`, `GPL-3.0-or-later`, `GPL-3.0-only`, `LGPL-2.1-or-later`, `LGPL-2.1-only`, `LGPL-3.0-or-later`, `LGPL-3.0-only`, `AGPL-3.0-or-later`, `AGPL-3.0-only`, `MIT`, `GFDL-1.3-or-later` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[LicenseSettings]
# an empty "LicenseList[]" first replaces the shipped list; without it the items are added
LicenseList[]
LicenseList[]=GPL-2.0-or-later
LicenseList[]=GPL-2.0-only
LicenseList[]=GPL-3.0-or-later
LicenseList[]=GPL-3.0-only
LicenseList[]=LGPL-2.1-or-later
LicenseList[]=LGPL-2.1-only
LicenseList[]=LGPL-3.0-or-later
LicenseList[]=LGPL-3.0-only
LicenseList[]=AGPL-3.0-or-later
LicenseList[]=AGPL-3.0-only
LicenseList[]=MIT
LicenseList[]=GFDL-1.3-or-later
LicenseList[]=GFDL-1.3-only
LicenseList[]=CC-BY-4.0
LicenseList[]=CC-BY-SA-4.0
LicenseList[]=CC-BY-ND-4.0
LicenseList[]=CC-BY-NC-4.0
LicenseList[]=CC-BY-NC-SA-4.0
LicenseList[]=CC-BY-NC-ND-4.0
LicenseList[]=CC-BY-3.0
# ... 26 more in the shipped file
*/ ?>
```

## AliasList

> Other names a stored license is known by, mapped to a LicenseList identifier.  
> Packages made before the license became a choice store "GPL", which always  
> meant the GNU GPL version 2 or (at your option) any later version, the license  
> of Exponential itself. An alias is shown as the license it stands for, and a  
> posted alias is stored as that license's identifier.  

Default: a hash of 1: `GPL=GPL-2.0-or-later`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[LicenseSettings]
# an empty "AliasList[]" first replaces the shipped list; without it the items are added
AliasList[]
AliasList[GPL]=GPL-2.0-or-later
*/ ?>
```

## Related

- [All blocks of package.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/package.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
