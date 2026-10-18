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

Default: a list of 18: `getting-started`, `editing`, `media`, `publishing`, `search`, `languages`, `account`, `layouts`, `shortcuts`, `support`, `administration`, `features` ....

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
Categories[]=administration
Categories[]=features
Categories[]=development
Categories[]=examples
Categories[]=install-upgrade
Categories[]=release-notes
Categories[]=history
Categories[]=reference
*/ ?>
```

## CategoryNames

Default: a hash of 18: `getting-started=Getting started`, `editing=Editing content`, `media=Images and media`, `publishing=Publishing`, `search=Search`, `languages=Languages`, `account=Your account`, `layouts=Layouts`, `shortcuts=Keyboard shortcuts`, `support=Help and support`, `administration=Administration`, `features=Features` ....

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
CategoryNames[administration]=Administration
CategoryNames[features]=Features
CategoryNames[development]=Development
CategoryNames[examples]=Examples
CategoryNames[install-upgrade]=Installation and upgrades
CategoryNames[release-notes]=Release notes
CategoryNames[history]=History
CategoryNames[reference]=Reference
*/ ?>
```

## CategoryIcons

> Icon names; each must have an .a4-bi-<name> class in stylesheets/components.css  

Default: a hash of 18: `getting-started=signpost-split`, `editing=pencil-square`, `media=images`, `publishing=journal-check`, `search=search`, `languages=translate`, `account=person-lock`, `layouts=layout-sidebar-inset`, `shortcuts=input-cursor-text`, `support=info-circle`, `administration=gear`, `features=stars` ....

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
CategoryIcons[administration]=gear
CategoryIcons[features]=stars
CategoryIcons[development]=code-square
CategoryIcons[examples]=file-earmark-code
CategoryIcons[install-upgrade]=box-seam
CategoryIcons[release-notes]=journal-text
CategoryIcons[history]=clock-history
CategoryIcons[reference]=collection
*/ ?>
```

## PathRules

> <pattern>;<category>, first match wins  

Default: a list of 58: `doc/LICENSE-GFDL-1.3.txt;reference`, `doc/glossary.md;reference`, `doc/languages.md;languages`, `doc/guides/README.md;getting-started`, `doc/guides/getting-started.md;getting-started`, `doc/guides/content-model-and-editing.md;editing`, `doc/guides/bookmarks.md;editing`, `doc/guides/collected-information.md;editing`, `doc/guides/urls-and-aliases.md;editing`, `doc/guides/content-history.md;publishing`, `doc/guides/drafts-and-pending.md;publishing`, `doc/guides/pdf-exports.md;publishing` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[CategorySettings]
# an empty "PathRules[]" first replaces the shipped list; without it the items are added
PathRules[]
PathRules[]=doc/LICENSE-GFDL-1.3.txt;reference
PathRules[]=doc/glossary.md;reference
PathRules[]=doc/languages.md;languages
PathRules[]=doc/guides/README.md;getting-started
PathRules[]=doc/guides/getting-started.md;getting-started
PathRules[]=doc/guides/content-model-and-editing.md;editing
PathRules[]=doc/guides/bookmarks.md;editing
PathRules[]=doc/guides/collected-information.md;editing
PathRules[]=doc/guides/urls-and-aliases.md;editing
PathRules[]=doc/guides/content-history.md;publishing
PathRules[]=doc/guides/drafts-and-pending.md;publishing
PathRules[]=doc/guides/pdf-exports.md;publishing
PathRules[]=doc/guides/rss-feeds.md;publishing
PathRules[]=doc/guides/content-languages.md;languages
PathRules[]=doc/guides/test-language-lorem-ipsum.md;languages
PathRules[]=doc/guides/ui-switch-design.md;administration
PathRules[]=doc/guides/ui-switch-settings.md;administration
PathRules[]=doc/guides/two-factor-login.md;account
PathRules[]=doc/guides/api-keys.md;account
PathRules[]=doc/guides/mail-preferences-administrator.md;account
# ... 38 more in the shipped file
*/ ?>
```

## TypeDefaults

> <type> => <category>, when nothing else says  

Default: a hash of 11: `help=getting-started`, `guide=administration`, `feature=features`, `bc=install-upgrade`, `install=install-upgrade`, `changelog=release-notes`, `specification=development`, `history=history`, `example=examples`, `generated=examples`, `reference=reference`.

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
*/ ?>
```

## Related

- [All blocks of exphelp.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/exphelp.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
