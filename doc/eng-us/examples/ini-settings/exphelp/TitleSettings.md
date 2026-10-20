---
title: exphelp.ini [TitleSettings]
audience: developer, admin
category: examples
summary: "The settings of [TitleSettings] in exphelp.ini: Titles, Summaries, TitleRules"
keywords: exphelp.ini, TitleSettings, INI, setting, Titles, Summaries, TitleRules
type: generated
generated: exp:help:examples
---
# exphelp.ini [TitleSettings]

The block `[TitleSettings]` of `settings/exphelp.ini`, with 3 setting(s).

Never edit `settings/exphelp.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/exphelp.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/exphelp.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/exphelp.ini.append.php` in an extension (read when the extension is active).

## Titles

> A clearer title and summary for pages whose own title is unclear: a raw file name, "README", a bare version, the  
> first line of a source code comment, or the same title as another page. A page's front matter title or summary  
> always wins; this is for files that have none (plain text, the inherited documentation) and must keep their name  
> because other files refer to them. Keys are paths relative to the installation root.  
> Titles\[<path>\]=<title>  

Default: a hash of 46: `doc/eng-us/standards/header.txt=Source file header template (inherited, 4.1)`, `doc/eng-us/bc/5.90/README.md=Upgrade notes for the 5.90 series (2017.08 and later)`, `doc/eng-us/features/5.90/README.md=Features of the 5.90 series (2017.08 and later)`, `doc/eng-us/bc/6.0/love/CHANGELOG.md=LoveStack features: changelog`, `doc/eng-us/bc/6.0/love/FEATURES.md=LoveStack features: the feature list`, `doc/eng-us/bc/6.0/love/GETTING_STARTED.md=LoveStack features: getting started`, `doc/eng-us/bc/6.0/love/TESTING.md=LoveStack features: testing`, `doc/eng-us/bc/6.0/content-jobs.md=Content jobs: upgrade notes`, `doc/eng-us/bc/6.0/debug-bar.md=The Exp Debug bar: upgrade notes`, `doc/eng-us/bc/4.4/changes-4.4.0.txt=Changes to BC and behavior in version 4.4.0`, `doc/changelogs/2.9/CHANGELOG-2.9-1=Changes in 2.9-1 (first release)`, `doc/changelogs/2.9/CHANGELOG-2.9-2=Changes from 2.9-1 to 2.9-2` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[TitleSettings]
# an empty "Titles[]" first replaces the shipped list; without it the items are added
Titles[]
Titles[doc/eng-us/standards/header.txt]=Source file header template (inherited, 4.1)
Titles[doc/eng-us/bc/5.90/README.md]=Upgrade notes for the 5.90 series (2017.08 and later)
Titles[doc/eng-us/features/5.90/README.md]=Features of the 5.90 series (2017.08 and later)
Titles[doc/eng-us/bc/6.0/love/CHANGELOG.md]=LoveStack features: changelog
Titles[doc/eng-us/bc/6.0/love/FEATURES.md]=LoveStack features: the feature list
Titles[doc/eng-us/bc/6.0/love/GETTING_STARTED.md]=LoveStack features: getting started
Titles[doc/eng-us/bc/6.0/love/TESTING.md]=LoveStack features: testing
Titles[doc/eng-us/bc/6.0/content-jobs.md]=Content jobs: upgrade notes
Titles[doc/eng-us/bc/6.0/debug-bar.md]=The Exp Debug bar: upgrade notes
Titles[doc/eng-us/bc/4.4/changes-4.4.0.txt]=Changes to BC and behavior in version 4.4.0
Titles[doc/changelogs/2.9/CHANGELOG-2.9-1]=Changes in 2.9-1 (first release)
Titles[doc/changelogs/2.9/CHANGELOG-2.9-2]=Changes from 2.9-1 to 2.9-2
Titles[doc/changelogs/2.9/CHANGELOG-2.9-3]=Changes from 2.9-2 to 2.9-3
Titles[doc/changelogs/2.9/CHANGELOG-2.9-4]=Changes from 2.9-3 to 2.9-4
Titles[doc/changelogs/2.9/CHANGELOG-2.9-5]=Changes from 2.9-4 to 2.9-5
Titles[doc/changelogs/2.9/CHANGELOG-2.9-6]=Changes from 2.9-5 to 2.9-6
Titles[doc/changelogs/3.3/unstable/CHANGELOG-desc-3.3.0-2]=Release 3.3.0-2 summary (empty in the original)
Titles[doc/changelogs/3.3/unstable/RELEASE-3.3.0-1]=Release notes for 3.3.0-1
Titles[doc/changelogs/4.6/CHANGELOG-4.5.0-to-4.6.0.txt]=Changes from 4.5.0 to 4.6.0
Titles[doc/changelogs/4.7/CHANGELOG-4.6.0-to-4.7.0.txt]=Changes from 4.6.0 to 4.7.0
# ... 26 more in the shipped file
*/ ?>
```

## Summaries

> Summaries\[<path>\]=<one sentence for the cards and the search results>  

Default: a hash of 3: `doc/eng-us/standards/header.txt=The copyright and license header that every PHP file of the inherited 4.x code base started with, kept as the historical template; new files use the docblock header described in the coding standards.`, `doc/eng-us/specifications/trunk/personal_trashcan/functional_requirements.txt=Why restoring folders from the shared trash is hard, and the requirements for a trash can per user.`, `doc/changelogs/3.3/unstable/CHANGELOG-desc-3.3.0-2=This release summary file is empty in the original distribution.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[TitleSettings]
# an empty "Summaries[]" first replaces the shipped list; without it the items are added
Summaries[]
Summaries[doc/eng-us/standards/header.txt]=The copyright and license header that every PHP file of the inherited 4.x code base started with, kept as the historical template; new files use the docblock header described in the coding standards.
Summaries[doc/eng-us/specifications/trunk/personal_trashcan/functional_requirements.txt]=Why restoring folders from the shared trash is hard, and the requirements for a trash can per user.
Summaries[doc/changelogs/3.3/unstable/CHANGELOG-desc-3.3.0-2]=This release summary file is empty in the original distribution.
*/ ?>
```

## TitleRules

> <pattern>;<template>, first match wins, for pages without a Titles\[\] entry. Placeholders: {title} (the page's own  
> title), {name} (its file name, humanized), {fileversion} (the version at the end of the file name) and {dirversion}  
> (the nearest version directory)  

Default: a list of 2: `doc/changelogs/*/CHANGELOG-desc-*;Release {fileversion} summary: {title}`, `doc/eng-us/features/*/event.*;{title} ({dirversion})`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[TitleSettings]
# an empty "TitleRules[]" first replaces the shipped list; without it the items are added
TitleRules[]
TitleRules[]=doc/changelogs/*/CHANGELOG-desc-*;Release {fileversion} summary: {title}
TitleRules[]=doc/eng-us/features/*/event.*;{title} ({dirversion})
*/ ?>
```

## Related

- [All blocks of exphelp.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/exphelp.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
