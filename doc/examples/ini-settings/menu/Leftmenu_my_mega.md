---
title: menu.ini [Leftmenu_my_mega]
audience: developer, admin
category: examples
summary: "The settings of [Leftmenu_my_mega] in menu.ini: Name, Links, LinkNames, Groups, GroupNames, GroupSections, MegaMenuLinks"
keywords: menu.ini, Leftmenu_my_mega, INI, setting, Name, Links, LinkNames, Groups, GroupNames, GroupSections, MegaMenuLinks
type: generated
generated: exp:help:examples
---
# menu.ini [Leftmenu_my_mega]

The block `[Leftmenu_my_mega]` of `settings/menu.ini`, with 7 setting(s).

> admin4's mega menu of the Dashboard tab (\[TopAdminMenu\] MegaMenuTabs\[\]=dashboard, \[Topmenu\_dashboard\]  
> MegaMenuTemplate, drawn by parts/my/mega\_menu.tpl through parts/ini\_menu.tpl). Not a left menu: the Dashboard tab's  
> left menu is \[Leftmenu\_my\], one list. This section only sorts the tab's functions into groups and reuses the menus  
> that define them instead of restating them: each group adds \[Leftmenu\_my\] (GroupSections\[\]), with the links the  
> extensions append there (Exponential Intelligence: expai, expai\_vector, expai\_chain, expai\_log, expai\_help; the  
> Help center, which a site adds to the choice as overview-help\_center once its link is there), the group "ei" adds \[Leftmenu\_design\] too (Missing alt text, expai\_alttext), the group "activity"  
> adds \[Leftmenu\_audit\] (the audit console and the AI events). Their addresses, policies, icons and descriptions are  
> those of their menus, so a link of an extension that is not active, or one the user may not open, is not shown.  
> Named Leftmenu\_my\_\* so that it shares the translation context of the tab's menu, design/admin/parts/my/menu.  
> MegaMenuLinks\[\] chooses from them as <group>-<key>, in this order (the groups follow the order of their first link:  
> the audit before Exponential Intelligence, Change password and Edit profile last, as in the left menu); at most 19  
> are shown. My shopping basket and the preferred currency stay in the left menu.  

Never edit `settings/menu.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/menu.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/menu.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/menu.ini.append.php` in an extension (read when the extension is active).

## Name

Default: `dashboard`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_my_mega]
Name=dashboard
*/ ?>
```

## Links

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_my_mega]
# an empty "Links[]" first replaces the shipped list; without it the items are added
Links[]
*/ ?>
```

## LinkNames

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_my_mega]
# an empty "LinkNames[]" first replaces the shipped list; without it the items are added
LinkNames[]
*/ ?>
```

## Groups

Default: a list of 5: `overview`, `work`, `activity`, `ei`, `account`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_my_mega]
# an empty "Groups[]" first replaces the shipped list; without it the items are added
Groups[]
Groups[]=overview
Groups[]=work
Groups[]=activity
Groups[]=ei
Groups[]=account
*/ ?>
```

## GroupNames

Default: a hash of 5: `overview=Overview`, `work=Your work`, `activity=Activity and audit`, `ei=Exponential Intelligence`, `account=My account`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_my_mega]
# an empty "GroupNames[]" first replaces the shipped list; without it the items are added
GroupNames[]
GroupNames[overview]=Overview
GroupNames[work]=Your work
GroupNames[activity]=Activity and audit
GroupNames[ei]=Exponential Intelligence
GroupNames[account]=My account
*/ ?>
```

## GroupSections

Default: a hash of 5: `overview=Leftmenu_my`, `work=Leftmenu_my`, `activity=Leftmenu_audit`, `ei=Leftmenu_my;Leftmenu_design`, `account=Leftmenu_my`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_my_mega]
# an empty "GroupSections[]" first replaces the shipped list; without it the items are added
GroupSections[]
GroupSections[overview]=Leftmenu_my
GroupSections[work]=Leftmenu_my
GroupSections[activity]=Leftmenu_audit
GroupSections[ei]=Leftmenu_my;Leftmenu_design
GroupSections[account]=Leftmenu_my
*/ ?>
```

## MegaMenuLinks

Default: a list of 18: `overview-dashboard`, `overview-help_center`, `work-my_drafts`, `work-my_pending`, `work-collaboration`, `work-my_bookmarks`, `activity-console`, `activity-expai_events`, `ei-expai`, `ei-expai_vector`, `ei-expai_chain`, `ei-expai_alttext` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_my_mega]
# an empty "MegaMenuLinks[]" first replaces the shipped list; without it the items are added
MegaMenuLinks[]
MegaMenuLinks[]=overview-dashboard
MegaMenuLinks[]=overview-help_center
MegaMenuLinks[]=work-my_drafts
MegaMenuLinks[]=work-my_pending
MegaMenuLinks[]=work-collaboration
MegaMenuLinks[]=work-my_bookmarks
MegaMenuLinks[]=activity-console
MegaMenuLinks[]=activity-expai_events
MegaMenuLinks[]=ei-expai
MegaMenuLinks[]=ei-expai_vector
MegaMenuLinks[]=ei-expai_chain
MegaMenuLinks[]=ei-expai_alttext
MegaMenuLinks[]=ei-expai_log
MegaMenuLinks[]=ei-expai_help
MegaMenuLinks[]=account-my_notifications
MegaMenuLinks[]=account-my_mail
MegaMenuLinks[]=account-change_password
MegaMenuLinks[]=account-edit_profile
*/ ?>
```

## Related

- [All blocks of menu.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/menu.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
