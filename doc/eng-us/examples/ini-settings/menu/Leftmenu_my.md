---
title: menu.ini [Leftmenu_my]
audience: developer, admin
category: examples
summary: "The settings of [Leftmenu_my] in menu.ini: Name, Links, LinkNames, PolicyList_dashboard, PolicyList_collaboration, PolicyList_my_pending, PolicyList_my_drafts, PolicyList_my_bookmarks ..."
keywords: menu.ini, Leftmenu_my, INI, setting, Name, Links, LinkNames, PolicyList_dashboard, PolicyList_collaboration, PolicyList_my_pending, PolicyList_my_drafts, PolicyList_my_bookmarks, PolicyList_my_notifications, PolicyList_help_center, PolicyList_audit, PolicyList_change_password, LinkIcons, LinkDescriptions, TrailLinks
type: generated
generated: exp:help:examples
---
# menu.ini [Leftmenu_my]

The block `[Leftmenu_my]` of `settings/menu.ini`, with 15 setting(s).

> Name=Media Menu  
> Links\[\]  
> LinkNames\[\]  

Never edit `settings/menu.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/menu.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/menu.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/menu.ini.append.php` in an extension (read when the extension is active).

## Name

Default: `dashboard`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_my]
Name=dashboard
*/ ?>
```

## Links

> The user's own work, right after Collaboration  
> The e-mail preferences: which optional e-mail the user gets, consent history, data download  
> The Help center (exphelp/dashboard), the start page of the help: guides, search, how to report an error  
> The audit console, only with audit/read (doc/eng-us/bc/6.0/audit.md, "Placement"); the last kernel link, so the links  
> extensions append follow it  

Default: a hash of 11: `dashboard=content/dashboard`, `collaboration=collaboration/view/summary`, `my_pending=content/pendinglist`, `my_drafts=content/draft`, `my_bookmarks=content/bookmark`, `my_mail=mailpreferences/settings`, `my_notifications=notification/settings`, `help_center=exphelp/dashboard`, `audit=audit/console`, `change_password=user/password`, `edit_profile=user/edit/(action)/edit`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_my]
# an empty "Links[]" first replaces the shipped list; without it the items are added
Links[]
Links[dashboard]=content/dashboard
Links[collaboration]=collaboration/view/summary
Links[my_pending]=content/pendinglist
Links[my_drafts]=content/draft
Links[my_bookmarks]=content/bookmark
Links[my_mail]=mailpreferences/settings
Links[my_notifications]=notification/settings
Links[help_center]=exphelp/dashboard
Links[audit]=audit/console
Links[change_password]=user/password
Links[edit_profile]=user/edit/(action)/edit
*/ ?>
```

## LinkNames

Default: a hash of 1: `audit=Audit trail`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_my]
# an empty "LinkNames[]" first replaces the shipped list; without it the items are added
LinkNames[]
LinkNames[audit]=Audit trail
*/ ?>
```

## PolicyList_dashboard

Default: a list of 1: `content/dashboard`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_my]
# an empty "PolicyList_dashboard[]" first replaces the shipped list; without it the items are added
PolicyList_dashboard[]
PolicyList_dashboard[]=content/dashboard
*/ ?>
```

## PolicyList_collaboration

Default: a list of 1: `collaboration/*`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_my]
# an empty "PolicyList_collaboration[]" first replaces the shipped list; without it the items are added
PolicyList_collaboration[]
PolicyList_collaboration[]=collaboration/*
*/ ?>
```

## PolicyList_my_pending

Default: a list of 1: `content/pendinglist`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_my]
# an empty "PolicyList_my_pending[]" first replaces the shipped list; without it the items are added
PolicyList_my_pending[]
PolicyList_my_pending[]=content/pendinglist
*/ ?>
```

## PolicyList_my_drafts

Default: a list of 1: `content/edit`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_my]
# an empty "PolicyList_my_drafts[]" first replaces the shipped list; without it the items are added
PolicyList_my_drafts[]
PolicyList_my_drafts[]=content/edit
*/ ?>
```

## PolicyList_my_bookmarks

Default: a list of 1: `content/bookmark`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_my]
# an empty "PolicyList_my_bookmarks[]" first replaces the shipped list; without it the items are added
PolicyList_my_bookmarks[]
PolicyList_my_bookmarks[]=content/bookmark
*/ ?>
```

## PolicyList_my_notifications

Default: a list of 1: `notification/use`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_my]
# an empty "PolicyList_my_notifications[]" first replaces the shipped list; without it the items are added
PolicyList_my_notifications[]
PolicyList_my_notifications[]=notification/use
*/ ?>
```

## PolicyList_help_center

Default: a list of 1: `exphelp/read`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_my]
# an empty "PolicyList_help_center[]" first replaces the shipped list; without it the items are added
PolicyList_help_center[]
PolicyList_help_center[]=exphelp/read
*/ ?>
```

## PolicyList_audit

Default: a list of 1: `audit/read`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_my]
# an empty "PolicyList_audit[]" first replaces the shipped list; without it the items are added
PolicyList_audit[]
PolicyList_audit[]=audit/read
*/ ?>
```

## PolicyList_change_password

Default: a list of 1: `user/password`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_my]
# an empty "PolicyList_change_password[]" first replaces the shipped list; without it the items are added
PolicyList_change_password[]
PolicyList_change_password[]=user/password
*/ ?>
```

## LinkIcons

> No PolicyList: the profile is edited through content/edit, open with user/selfedit or with  
> content/edit on the user's own object; CheckViewAccess asks the object itself (canEdit).  
> Icons and descriptions of some of these links, for admin4's mega menu of the Users tab (\[Leftmenu\_user\_mega\] adds  
> this menu to its group "account"); descriptions are translated in design/admin/parts/my/menu  
> Icons and descriptions of the other links, for admin4's mega menu of the Dashboard tab (\[Leftmenu\_my\_mega\] adds  
> this menu to its groups), including the links the extension expai appends here; translated in  
> design/admin/parts/my/menu  

Default: a hash of 15: `my_notifications=bell`, `my_mail=envelope`, `change_password=key`, `edit_profile=person-circle`, `help_center=question-circle`, `dashboard=speedometer2`, `collaboration=inbox`, `my_pending=hourglass-split`, `my_drafts=pencil-square`, `my_bookmarks=bookmark`, `expai=stars`, `expai_vector=magic` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_my]
# an empty "LinkIcons[]" first replaces the shipped list; without it the items are added
LinkIcons[]
LinkIcons[my_notifications]=bell
LinkIcons[my_mail]=envelope
LinkIcons[change_password]=key
LinkIcons[edit_profile]=person-circle
LinkIcons[help_center]=question-circle
LinkIcons[dashboard]=speedometer2
LinkIcons[collaboration]=inbox
LinkIcons[my_pending]=hourglass-split
LinkIcons[my_drafts]=pencil-square
LinkIcons[my_bookmarks]=bookmark
LinkIcons[expai]=stars
LinkIcons[expai_vector]=magic
LinkIcons[expai_chain]=link-45deg
LinkIcons[expai_log]=journal-text
LinkIcons[expai_help]=info-circle
*/ ?>
```

## LinkDescriptions

Default: a hash of 15: `my_notifications=The content you follow and how you are told.`, `my_mail=Which optional e-mail you get; your consents.`, `change_password=Set a new password for your account.`, `edit_profile=Your name, e-mail address and other details.`, `help_center=Guides, search and how to report an error.`, `dashboard=Your start page: your work, your tasks and what changed.`, `collaboration=Items that wait for your approval, and yours that wait for others.`, `my_pending=Your items that wait to be published.`, `my_drafts=The drafts you started and have not published yet.`, `my_bookmarks=The content you marked to find it again.`, `expai=The start page of Exponential Intelligence: its tools and their use.`, `expai_vector=Threads that change content with AI, under your control.` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_my]
# an empty "LinkDescriptions[]" first replaces the shipped list; without it the items are added
LinkDescriptions[]
LinkDescriptions[my_notifications]=The content you follow and how you are told.
LinkDescriptions[my_mail]=Which optional e-mail you get; your consents.
LinkDescriptions[change_password]=Set a new password for your account.
LinkDescriptions[edit_profile]=Your name, e-mail address and other details.
LinkDescriptions[help_center]=Guides, search and how to report an error.
LinkDescriptions[dashboard]=Your start page: your work, your tasks and what changed.
LinkDescriptions[collaboration]=Items that wait for your approval, and yours that wait for others.
LinkDescriptions[my_pending]=Your items that wait to be published.
LinkDescriptions[my_drafts]=The drafts you started and have not published yet.
LinkDescriptions[my_bookmarks]=The content you marked to find it again.
LinkDescriptions[expai]=The start page of Exponential Intelligence: its tools and their use.
LinkDescriptions[expai_vector]=Threads that change content with AI, under your control.
LinkDescriptions[expai_chain]=Content written in steps: the runs and their checkpoints.
LinkDescriptions[expai_log]=The AI requests, their tokens and their cost.
LinkDescriptions[expai_help]=How the AI features work and how to use them.
*/ ?>
```

## TrailLinks

> Change password and Edit profile close the menu, after the links extensions append (parts/ini\_menu.tpl)  

Default: a list of 2: `change_password`, `edit_profile`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_my]
# an empty "TrailLinks[]" first replaces the shipped list; without it the items are added
TrailLinks[]
TrailLinks[]=change_password
TrailLinks[]=edit_profile
*/ ?>
```

## Related

- [All blocks of menu.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/menu.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
