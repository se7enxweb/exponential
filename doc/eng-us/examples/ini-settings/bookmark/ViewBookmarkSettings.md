---
title: bookmark.ini [ViewBookmarkSettings]
audience: developer, admin
category: examples
summary: "The settings of [ViewBookmarkSettings] in bookmark.ini: NonBookmarkableViews, QueryParameters, DefaultIcon, ModuleIcons"
keywords: bookmark.ini, ViewBookmarkSettings, INI, setting, NonBookmarkableViews, QueryParameters, DefaultIcon, ModuleIcons
type: generated
generated: exp:help:examples
---
# bookmark.ini [ViewBookmarkSettings]

The block `[ViewBookmarkSettings]` of `settings/bookmark.ini`, with 4 setting(s).

Never edit `settings/bookmark.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/bookmark.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/bookmark.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/bookmark.ini.append.php` in an extension (read when the extension is active).

## NonBookmarkableViews

> Module views that can never be bookmarked, as module/view, or module/\* for every view of a module.  
> These are the views that only act on a form that was posted (a bookmark of them would open an empty or  
> failing page, or repeat an action), the views of one moment (log in, log out, a confirmation, a link that  
> is used once) and the editing views (a draft is not a page to come back to).  
> A module definition can say the same of a view with 'bookmarkable' => false in its $ViewList entry.  

Default: a list of 40: `content/action`, `content/edit`, `content/removeobject`, `content/removeassignment`, `content/removeeditversion`, `content/copy`, `content/copysubtree`, `content/collectedinfo`, `content/browse`, `content/upload`, `content/download`, `content/restore` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/bookmark.ini.append.php
[ViewBookmarkSettings]
# an empty "NonBookmarkableViews[]" first replaces the shipped list; without it the items are added
NonBookmarkableViews[]
NonBookmarkableViews[]=content/action
NonBookmarkableViews[]=content/edit
NonBookmarkableViews[]=content/removeobject
NonBookmarkableViews[]=content/removeassignment
NonBookmarkableViews[]=content/removeeditversion
NonBookmarkableViews[]=content/copy
NonBookmarkableViews[]=content/copysubtree
NonBookmarkableViews[]=content/collectedinfo
NonBookmarkableViews[]=content/browse
NonBookmarkableViews[]=content/upload
NonBookmarkableViews[]=content/download
NonBookmarkableViews[]=content/restore
NonBookmarkableViews[]=content/pdf
NonBookmarkableViews[]=content/keyword
NonBookmarkableViews[]=user/login
NonBookmarkableViews[]=user/logout
NonBookmarkableViews[]=user/activate
NonBookmarkableViews[]=user/forgotpassword
NonBookmarkableViews[]=user/register
NonBookmarkableViews[]=user/success
# ... 20 more in the shipped file
*/ ?>
```

## QueryParameters

> The query string keys a view keeps in its bookmark, as module/view=key;key. Every other key and the query  
> string of every other view are dropped: a module view takes its parameters in its address  
> (/(offset)/20, /(folder)/top), and a query string usually carries one moment's state.  
> A key keeps a single value of at most 255 characters; the keys are stored sorted, so the same search is  
> one bookmark however its form ordered them.  

Default: a hash of 1: `content/search=SearchText;SectionID;SubTreeArray`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/bookmark.ini.append.php
[ViewBookmarkSettings]
# an empty "QueryParameters[]" first replaces the shipped list; without it the items are added
QueryParameters[]
QueryParameters[content/search]=SearchText;SectionID;SubTreeArray
*/ ?>
```

## DefaultIcon

> The icon of a system page bookmark by its module: an icon name of the administration designs  
> (a4-bi-<name>). A module that is not listed gets DefaultIcon.  

Default: `gear`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/bookmark.ini.append.php
[ViewBookmarkSettings]
DefaultIcon=gear
*/ ?>
```

## ModuleIcons

Default: a hash of 25: `setup=tools`, `content=files`, `user=person`, `role=shield-lock`, `section=collection`, `class=diagram-3`, `state=toggles`, `workflow=diagram-2`, `trigger=lightning`, `url=link-45deg`, `shop=piggy-bank`, `package=box-seam` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/bookmark.ini.append.php
[ViewBookmarkSettings]
# an empty "ModuleIcons[]" first replaces the shipped list; without it the items are added
ModuleIcons[]
ModuleIcons[setup]=tools
ModuleIcons[content]=files
ModuleIcons[user]=person
ModuleIcons[role]=shield-lock
ModuleIcons[section]=collection
ModuleIcons[class]=diagram-3
ModuleIcons[state]=toggles
ModuleIcons[workflow]=diagram-2
ModuleIcons[trigger]=lightning
ModuleIcons[url]=link-45deg
ModuleIcons[shop]=piggy-bank
ModuleIcons[package]=box-seam
ModuleIcons[settings]=sliders
ModuleIcons[notification]=bell
ModuleIcons[rss]=rss
ModuleIcons[audit]=journal-check
ModuleIcons[exphelp]=question-circle
ModuleIcons[git_manager]=git
ModuleIcons[visual]=palette
ModuleIcons[collaboration]=chat-left-dots
# ... 5 more in the shipped file
*/ ?>
```

## Related

- [All blocks of bookmark.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/bookmark.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
