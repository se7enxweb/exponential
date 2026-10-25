---
title: site.ini [ExtensionSettings]
audience: developer, admin
category: examples
summary: "The settings of [ExtensionSettings] in site.ini: ExtensionDirectory, AdditionalExtensionDirectories, ExtensionOrdering, ActiveExtensions, RequiredExtensions, ActiveAccessExtensions"
keywords: site.ini, ExtensionSettings, INI, setting, ExtensionDirectory, AdditionalExtensionDirectories, ExtensionOrdering, ActiveExtensions, RequiredExtensions, ActiveAccessExtensions
type: generated
generated: exp:help:examples
---
# site.ini [ExtensionSettings]

The block `[ExtensionSettings]` of `settings/site.ini`, with 6 setting(s).

Never edit `settings/site.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/site.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in an extension (read when the extension is active).

## ExtensionDirectory

Default: `extension`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ExtensionSettings]
ExtensionDirectory=extension
*/ ?>
```

## AdditionalExtensionDirectories

> Further places extensions may live, searched after ExtensionDirectory above.  
> A second root separates what this project wrote from what it took from  
> elsewhere, so who owns a package is readable off the directory layout rather  
> than off a list somebody has to maintain.  
>
> An extension of the same name in a later root shadows the one before it. That  
> is how a package is forked without being edited - copy it across and change  
> it there - and also how one gets shadowed by accident.  
>
> The directory has to exist, and the autoload generator scans every configured  
> root, so after adding one:  
>
>     php bin/php/ezpgenerateautoloads.php  
>
> Nothing else: moving a package between roots is a copy, with no manifest to  
> edit and no composer dump-autoload to run.  
>
> Code should never build a path out of "extension/" and a name. Use  
> eZExtension::extensionPath() and eZExtension::expandedPathList(), which  
> resolve across every root. eZExtension::filterExtensionRootDirectories() is  
> the last word, for a root that has to be worked out at runtime.  
>
> See doc/eng-us/bc/6.0/AdditionalExtensionDirectories.md  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ExtensionSettings]
# an empty "AdditionalExtensionDirectories[]" first replaces the shipped list; without it the items are added
AdditionalExtensionDirectories[]
*/ ?>
```

## ExtensionOrdering

> AdditionalExtensionDirectories\[\]=extension\_src  
> Use extension reordering to avoid issues because of ActiveExtensions and  
> ActiveAccessExtensions not being correctly defined  
> Known issues:  
>   - Breaks clustering settings in siteaccess and extensions support  
>     Only known workaround is to disable this setting and order manually  
>   - Still loads an extension even if dependant extension is not loaded  
>   - Does not fix order issues across the two extensions settings  
>     ( ActiveExtensions and ActiveAccessExtensions )  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ExtensionSettings]
ExtensionOrdering=enabled
*/ ?>
```

## ActiveExtensions

> A list of active extensions, add new ones to activate them  
> The extension itself will then have it's settings directory read (if any)  
> and any extra configurability is done automatically.  

Default: a list of 1: `xrowmetadata`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ExtensionSettings]
# an empty "ActiveExtensions[]" first replaces the shipped list; without it the items are added
ActiveExtensions[]
ActiveExtensions[]=xrowmetadata
*/ ?>
```

## RequiredExtensions

> Extensions the kernel's own designs need. Each one that is installed is active  
> in every siteaccess, ahead of ActiveExtensions\[\], whatever an override sets  
> there; one that is not installed is left out without an error. The admin  
> designs request the script packs of Exponential UI (expui, which the kernel  
> package requires: exp::core::shared, exp::io, exp::datatable ...) for the  
> sub-items table, relation search, uploads and date fields; without it ezjscore  
> answers "Not a valid ezjscServer function" for each of them. A project that  
> resets ActiveExtensions\[\] (a Platform Legacy project's override) keeps it.  

Default: a list of 1: `expui`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ExtensionSettings]
# an empty "RequiredExtensions[]" first replaces the shipped list; without it the items are added
RequiredExtensions[]
RequiredExtensions[]=expui
*/ ?>
```

## ActiveAccessExtensions

> Same as ActiveExtensions but will be loaded after the siteaccess is chosen.  
> This allows for activating different extensions on different siteaccesses  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ExtensionSettings]
# an empty "ActiveAccessExtensions[]" first replaces the shipped list; without it the items are added
ActiveAccessExtensions[]
*/ ?>
```

## Related

- [All blocks of site.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/site.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
