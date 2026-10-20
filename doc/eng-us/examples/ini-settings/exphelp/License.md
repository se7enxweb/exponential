---
title: exphelp.ini [License]
audience: developer, admin
category: examples
summary: "The settings of [License] in exphelp.ini: Enabled, Name, ShortName, Version, VersionDate, OrLater, URL, TextURL ..."
keywords: exphelp.ini, License, INI, setting, Enabled, Name, ShortName, Version, VersionDate, OrLater, URL, TextURL, TextPage, NoticePage, ExplainerPage, CopyrightYears, CopyrightHolder, OtherLicensePathRules, OtherCopyright, OtherLicenseName, OtherLicenseURL
type: generated
generated: exp:help:examples
---
# exphelp.ini [License]

The block `[License]` of `settings/exphelp.ini`, with 17 setting(s).

Never edit `settings/exphelp.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/exphelp.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/exphelp.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/exphelp.ini.append.php` in an extension (read when the extension is active).

## Enabled

> The license of the documentation the help shows (doc/eng-us/README-LICENSE.md, doc/eng-us/help/free-documentation.md).  
> Every help page, the dashboard, the print view and the PDF carry its notice; the name stays untranslated.  

Default: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[License]
Enabled=true
*/ ?>
```

## Name

Default: `GNU Free Documentation License`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[License]
Name=GNU Free Documentation License
*/ ?>
```

## ShortName

Default: `GFDL`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[License]
ShortName=GFDL
*/ ?>
```

## Version

Default: `1.3`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[License]
Version=1.3
*/ ?>
```

## VersionDate

Default: `3 November 2008`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[License]
VersionDate=3 November 2008
*/ ?>
```

## OrLater

> "or any later version published by the Free Software Foundation"  

Default: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[License]
OrLater=true
*/ ?>
```

## URL

Default: `https://www.gnu.org/licenses/fdl-1.3.html`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[License]
URL=https://www.gnu.org/licenses/fdl-1.3.html
*/ ?>
```

## TextURL

Default: `https://www.gnu.org/licenses/fdl-1.3.txt`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[License]
TextURL=https://www.gnu.org/licenses/fdl-1.3.txt
*/ ?>
```

## TextPage

> Help page ids (paths below doc/eng-us/): the unmodified license text, the copyright and permission notice,  
> and the plain explanation for readers  

Default: `LICENSE-GFDL-1.3.txt`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[License]
TextPage=LICENSE-GFDL-1.3.txt
*/ ?>
```

## NoticePage

Default: `README-LICENSE.md`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[License]
NoticePage=README-LICENSE.md
*/ ?>
```

## ExplainerPage

Default: `help/free-documentation.md`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[License]
ExplainerPage=help/free-documentation.md
*/ ?>
```

## CopyrightYears

Default: `1998 - 2026`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[License]
CopyrightYears=1998 - 2026
*/ ?>
```

## CopyrightHolder

Default: `7x & Exponential Foundation and the Exponential contributors`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[License]
CopyrightHolder=7x & Exponential Foundation and the Exponential contributors
*/ ?>
```

## OtherLicensePathRules

> Pages the project does not hold the copyright of and does not relicense: the documentation inherited  
> from the original software (eZ Systems AS, 1999 - 2014). They keep the terms of the repository's  
> LICENSE and show the notice below instead. <pattern> on the doc/ path, "\*" also matches "/".  

Default: a list of 9: `doc/eng-us/features/[2-5].*`, `doc/eng-us/bc/[2-5].*`, `doc/eng-us/specifications/[2-5].*`, `doc/eng-us/specifications/trunk/*`, `doc/changelogs/[2-5].*`, `doc/changelogs/README.md`, `doc/eng-us/shop/*`, `doc/eng-us/release-overview`, `doc/eng-us/standards/header.txt`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[License]
# an empty "OtherLicensePathRules[]" first replaces the shipped list; without it the items are added
OtherLicensePathRules[]
OtherLicensePathRules[]=doc/eng-us/features/[2-5].*
OtherLicensePathRules[]=doc/eng-us/bc/[2-5].*
OtherLicensePathRules[]=doc/eng-us/specifications/[2-5].*
OtherLicensePathRules[]=doc/eng-us/specifications/trunk/*
OtherLicensePathRules[]=doc/changelogs/[2-5].*
OtherLicensePathRules[]=doc/changelogs/README.md
OtherLicensePathRules[]=doc/eng-us/shop/*
OtherLicensePathRules[]=doc/eng-us/release-overview
OtherLicensePathRules[]=doc/eng-us/standards/header.txt
*/ ?>
```

## OtherCopyright

Default: `Copyright (C) 1999 - 2014 eZ Systems AS and contributors`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[License]
OtherCopyright=Copyright (C) 1999 - 2014 eZ Systems AS and contributors
*/ ?>
```

## OtherLicenseName

Default: `GNU General Public License, version 2`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[License]
OtherLicenseName=GNU General Public License, version 2
*/ ?>
```

## OtherLicenseURL

Default: `https://www.gnu.org/licenses/old-licenses/gpl-2.0.html`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[License]
OtherLicenseURL=https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/ ?>
```

## Related

- [All blocks of exphelp.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/exphelp.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
