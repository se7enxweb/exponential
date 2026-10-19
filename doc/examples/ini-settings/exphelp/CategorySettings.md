---
title: exphelp.ini [CategorySettings]
audience: developer, admin
category: examples
summary: "The settings of [CategorySettings] in exphelp.ini: Categories, CategoryNames, CategoryIcons, PathRules, TypeDefaults"
keywords: exphelp.ini, CategorySettings, INI, setting, Categories, CategoryNames, CategoryIcons, PathRules, TypeDefaults
type: generated
generated: exp:help:examples
---
# exphelp.ini [CategorySettings]

The block `[CategorySettings]` of `settings/exphelp.ini`, with 5 setting(s).

Never edit `settings/exphelp.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/exphelp.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/exphelp.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/exphelp.ini.append.php` in an extension (read when the extension is active).

## Categories

> The sections of the help, in dashboard order. A page's category: its front matter (category: editing), else the  
> first PathRules\[\] that matches, else TypeDefaults\[<type>\]  

Default: a list of 35: `getting-started`, `editing`, `media`, `publishing`, `search`, `languages`, `account`, `layouts`, `shortcuts`, `support`, `leadership`, `business` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[CategorySettings]
# an empty "Categories[]" first replaces the shipped list; without it the items are added
Categories[]
Categories[]=getting-started
Categories[]=editing
Categories[]=media
Categories[]=publishing
Categories[]=search
Categories[]=languages
Categories[]=account
Categories[]=layouts
Categories[]=shortcuts
Categories[]=support
Categories[]=leadership
Categories[]=business
Categories[]=partnership
Categories[]=advocacy
Categories[]=theme-design
Categories[]=templates
Categories[]=extension-development
Categories[]=theme-extensions
Categories[]=theme-distribution
Categories[]=theme-sales
# ... 15 more in the shipped file
*/ ?>
```

## CategoryNames

Default: a hash of 35: `getting-started=Getting started`, `editing=Editing content`, `media=Images and media`, `publishing=Publishing`, `search=Search`, `languages=Languages`, `account=Your account`, `layouts=Layouts`, `shortcuts=Keyboard shortcuts`, `support=Help and support`, `leadership=Project leadership`, `business=Sales and marketing` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[CategorySettings]
# an empty "CategoryNames[]" first replaces the shipped list; without it the items are added
CategoryNames[]
CategoryNames[getting-started]=Getting started
CategoryNames[editing]=Editing content
CategoryNames[media]=Images and media
CategoryNames[publishing]=Publishing
CategoryNames[search]=Search
CategoryNames[languages]=Languages
CategoryNames[account]=Your account
CategoryNames[layouts]=Layouts
CategoryNames[shortcuts]=Keyboard shortcuts
CategoryNames[support]=Help and support
CategoryNames[leadership]=Project leadership
CategoryNames[business]=Sales and marketing
CategoryNames[partnership]=Partnership
CategoryNames[advocacy]=Advocacy and community
CategoryNames[theme-design]=Theme design
CategoryNames[templates]=Templates
CategoryNames[extension-development]=Extension development
CategoryNames[theme-extensions]=Theme extensions
CategoryNames[theme-distribution]=Publishing and distribution
CategoryNames[theme-sales]=Selling themes and services
# ... 15 more in the shipped file
*/ ?>
```

## CategoryIcons

> Icon names; each must have an .a4-bi-<name> class in stylesheets/components.css  

Default: a hash of 35: `getting-started=signpost-split`, `editing=pencil-square`, `media=images`, `publishing=journal-check`, `search=search`, `languages=translate`, `account=person-lock`, `layouts=layout-sidebar-inset`, `shortcuts=input-cursor-text`, `support=info-circle`, `leadership=diagram-3`, `business=graph-up-arrow` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[CategorySettings]
# an empty "CategoryIcons[]" first replaces the shipped list; without it the items are added
CategoryIcons[]
CategoryIcons[getting-started]=signpost-split
CategoryIcons[editing]=pencil-square
CategoryIcons[media]=images
CategoryIcons[publishing]=journal-check
CategoryIcons[search]=search
CategoryIcons[languages]=translate
CategoryIcons[account]=person-lock
CategoryIcons[layouts]=layout-sidebar-inset
CategoryIcons[shortcuts]=input-cursor-text
CategoryIcons[support]=info-circle
CategoryIcons[leadership]=diagram-3
CategoryIcons[business]=graph-up-arrow
CategoryIcons[partnership]=building
CategoryIcons[advocacy]=people
CategoryIcons[theme-design]=palette
CategoryIcons[templates]=file-earmark-code
CategoryIcons[extension-development]=puzzle
CategoryIcons[theme-extensions]=puzzle
CategoryIcons[theme-distribution]=box-seam
CategoryIcons[theme-sales]=tag
# ... 15 more in the shipped file
*/ ?>
```

## PathRules

> <pattern>;<category>, first match wins  

Default: a list of 72: `doc/help/leadership/*;leadership`, `doc/help/business/*;business`, `doc/help/partners/*;partnership`, `doc/help/advocates/*;advocacy`, `doc/help/designers/templates/*;templates`, `doc/help/designers/*;theme-design`, `doc/help/hosting/*;hosting`, `doc/help/creators/app-creators/*;app-creation`, `doc/help/creators/website-creators/*;website-creation`, `doc/help/creators/*;website-creation`, `doc/help/supporters/*;supporting`, `doc/help/sponsors/*;sponsorship` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[CategorySettings]
# an empty "PathRules[]" first replaces the shipped list; without it the items are added
PathRules[]
PathRules[]=doc/help/leadership/*;leadership
PathRules[]=doc/help/business/*;business
PathRules[]=doc/help/partners/*;partnership
PathRules[]=doc/help/advocates/*;advocacy
PathRules[]=doc/help/designers/templates/*;templates
PathRules[]=doc/help/designers/*;theme-design
PathRules[]=doc/help/hosting/*;hosting
PathRules[]=doc/help/creators/app-creators/*;app-creation
PathRules[]=doc/help/creators/website-creators/*;website-creation
PathRules[]=doc/help/creators/*;website-creation
PathRules[]=doc/help/supporters/*;supporting
PathRules[]=doc/help/sponsors/*;sponsorship
PathRules[]=doc/help/extension-development/*;extension-development
PathRules[]=doc/LICENSE-GFDL-1.3.txt;reference
PathRules[]=doc/glossary.md;reference
PathRules[]=doc/languages.md;languages
PathRules[]=doc/guides/README.md;getting-started
PathRules[]=doc/guides/getting-started.md;getting-started
PathRules[]=doc/guides/content-model-and-editing.md;editing
PathRules[]=doc/guides/bookmarks.md;editing
# ... 52 more in the shipped file
*/ ?>
```

## TypeDefaults

> <type> => <category>, when nothing else says  

Default: a hash of 12: `help=getting-started`, `guide=administration`, `feature=features`, `bc=install-upgrade`, `install=install-upgrade`, `changelog=release-notes`, `specification=development`, `history=history`, `example=examples`, `generated=examples`, `reference=reference`, `catalogue=standards`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[CategorySettings]
# an empty "TypeDefaults[]" first replaces the shipped list; without it the items are added
TypeDefaults[]
TypeDefaults[help]=getting-started
TypeDefaults[guide]=administration
TypeDefaults[feature]=features
TypeDefaults[bc]=install-upgrade
TypeDefaults[install]=install-upgrade
TypeDefaults[changelog]=release-notes
TypeDefaults[specification]=development
TypeDefaults[history]=history
TypeDefaults[example]=examples
TypeDefaults[generated]=examples
TypeDefaults[reference]=reference
TypeDefaults[catalogue]=standards
*/ ?>
```

## Related

- [All blocks of exphelp.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/exphelp.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
