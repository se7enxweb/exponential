---
title: exphelp.ini [ContextHelp]
audience: developer, admin
category: examples
summary: "The settings of [ContextHelp] in exphelp.ini: Providers, CheckPages, MenuTopics, MenuLinks, Map"
keywords: exphelp.ini, ContextHelp, INI, setting, Providers, CheckPages, MenuTopics, MenuLinks, Map
type: generated
generated: exp:help:examples
---
# exphelp.ini [ContextHelp]

The block `[ContextHelp]` of `settings/exphelp.ini`, with 5 setting(s).

Never edit `settings/exphelp.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/exphelp.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/exphelp.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/exphelp.ini.append.php` in an extension (read when the extension is active).

## Providers

> Context help: the "?" link in the header of the administration opens the page that answers "help for this page"  
> (expHelpContextRegistry; doc/eng-us/features/6.0/help-system.md, "Context help"). One entry per key:  
>   Map\[<module>/<view>\]=<page id>\[#<heading anchor>\]  
>   Map\[<module>\]=...                     every view of the module without an entry of its own  
>   Map\[<design>:<module>/<view>\]=...     only in that design (admin4l, editor, admin, adminui ...); wins over the  
>   Map\[<design>:<module>\]=...            same key without a design  
> A page id is the path below doc/eng-us/, the same in every language (ext/<extension>/<path> for an extension's doc directory, \[IndexSettings\]  
> ExtensionDocDirs\[\]); the anchor is the heading's anchor as the viewer makes it. The lookup: Providers\[\], then  
> design:module/view, module/view, design:module, module, then the help dashboard. An entry whose page the help does  
> not serve (not written yet, not git-tracked, outside doc/) is skipped, so the next key answers.  
> Extensions register the help of their own modules in their settings/exphelp.ini.append.php in the same way.  
> Classes implementing expHelpContextProviderInterface, asked before the map, in this order  
> Help topics maintained as content answer first for the keys of \[ContentSettings\] ContextHelpIds\[\]  

Default: a list of 1: `expHelpContentProvider`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContextHelp]
# an empty "Providers[]" first replaces the shipped list; without it the items are added
Providers[]
Providers[]=expHelpContentProvider
*/ ?>
```

## CheckPages

> enabled: an entry counts only when the help index serves its page; disabled: syntax checks only (faster)  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContextHelp]
CheckPages=enabled
*/ ?>
```

## MenuTopics

> The header's Help menu (the "?" button): how many topics related to the page it lists (the module's other  
> entries of this map, then \[FeaturedSettings\] PageList\[\]), and its quick links, key => page id\[#anchor\] or an  
> exphelp view. The kernel's keys are named in the menu's template; an extension's own key shows the page's title.  

Default: `6`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContextHelp]
MenuTopics=6
*/ ?>
```

## MenuLinks

Default: a hash of 5: `getting_started=help/getting-started.md`, `keyboard_shortcuts=help/keyboard-shortcuts.md`, `whats_new=changelogs/6.0/6.0.15.md`, `report=exphelp/report`, `git=install/03-getting-the-code.md#b-git-clone-and-composer-install`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContextHelp]
# an empty "MenuLinks[]" first replaces the shipped list; without it the items are added
MenuLinks[]
MenuLinks[getting_started]=help/getting-started.md
MenuLinks[keyboard_shortcuts]=help/keyboard-shortcuts.md
MenuLinks[whats_new]=changelogs/6.0/6.0.15.md
MenuLinks[report]=exphelp/report
MenuLinks[git]=install/03-getting-the-code.md#b-git-clone-and-composer-install
*/ ?>
```

## Map

> Content  
> Content model  
> Users and access  
> Setup  
> admin4l: the administration drawn by admin layouts  
> The editor siteaccess (SiteDesign=editor): the pages written for editors, before the administrators' guides  
> Modules of extensions that ship without help entries of their own (an extension's exphelp.ini.append.php wins over  
> these: it is read after this file)  
> The database manager of sevenx\_dse (module dse)  

Default: a hash of 134: `content=guides/content-model-and-editing.md`, `content/dashboard=help/admin-tour.md`, `content/view=help/editing-content.md`, `content/edit=help/editing-content.md`, `content/multiedit=guides/content-model-and-editing.md#6-edit-many-items-in-one-form-multi-node-edit`, `content/versionview=guides/content-history.md`, `content/history=guides/content-history.md`, `content/draft=guides/drafts-and-pending.md#1-my-drafts`, `content/pendinglist=guides/drafts-and-pending.md#2-my-pending-items`, `content/trash=guides/content-model-and-editing.md#7-the-trash`, `content/restore=guides/content-model-and-editing.md#7-the-trash`, `content/bookmark=guides/bookmarks.md` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContextHelp]
# an empty "Map[]" first replaces the shipped list; without it the items are added
Map[]
Map[content]=guides/content-model-and-editing.md
Map[content/dashboard]=help/admin-tour.md
Map[content/view]=help/editing-content.md
Map[content/edit]=help/editing-content.md
Map[content/multiedit]=guides/content-model-and-editing.md#6-edit-many-items-in-one-form-multi-node-edit
Map[content/versionview]=guides/content-history.md
Map[content/history]=guides/content-history.md
Map[content/draft]=guides/drafts-and-pending.md#1-my-drafts
Map[content/pendinglist]=guides/drafts-and-pending.md#2-my-pending-items
Map[content/trash]=guides/content-model-and-editing.md#7-the-trash
Map[content/restore]=guides/content-model-and-editing.md#7-the-trash
Map[content/bookmark]=guides/bookmarks.md
Map[content/search]=help/searching.md
Map[content/advancedsearch]=help/searching.md#the-advanced-search
Map[content/upload]=help/media-and-uploads.md
Map[content/translations]=guides/content-languages.md
Map[content/translation]=guides/content-languages.md
Map[content/urlalias]=guides/urls-and-aliases.md
Map[content/urltranslator]=guides/urls-and-aliases.md#3-global-url-aliases
Map[content/urlwildcards]=guides/urls-and-aliases.md#4-url-wildcards
# ... 114 more in the shipped file
*/ ?>
```

## Related

- [All blocks of exphelp.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/exphelp.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
