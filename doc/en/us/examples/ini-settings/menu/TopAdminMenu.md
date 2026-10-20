---
title: menu.ini [TopAdminMenu]
audience: developer, admin
category: examples
summary: "The settings of [TopAdminMenu] in menu.ini: HiddenTabs, SetupTabs, MegaMenuTabs, Tabs"
keywords: menu.ini, TopAdminMenu, INI, setting, HiddenTabs, SetupTabs, MegaMenuTabs, Tabs
type: generated
generated: exp:help:examples
---
# menu.ini [TopAdminMenu]

The block `[TopAdminMenu]` of `settings/menu.ini`, with 4 setting(s).

Never edit `settings/menu.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/menu.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/menu.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/menu.ini.append.php` in an extension (read when the extension is active).

## HiddenTabs

> Tabs the top menu leaves out, whatever Tabs\[\] says: a siteaccess cannot take a tab  
> out of Tabs\[\] that an extension appends (extension settings are read after the  
> siteaccess's). The editor siteaccess hides the administration's tabs with it.  
>
> Fewer tabs, nothing lost (admin4, doc/en/us/features/6.0/admin4-design.md "Moving a tab into Setup"):  
> design and audit have a group of the Setup menu and overview (\[Leftmenu\_setup\] GroupTabs\[\]),  
> shown only while their tab is not in the top menu; their pages then mark the Setup tab and  
> draw the Setup menu. Without the dashboard tab the logo leads to the dashboard. In a  
> siteaccess or override menu.ini.append.php:  
>   HiddenTabs\[\]  
>   HiddenTabs\[\]=dashboard  
>   HiddenTabs\[\]=design  
>   HiddenTabs\[\]=audit  
> or restate the whole list (Tabs\[\] with no value, then the tabs to keep); an extension's tab  
> can only be left out with HiddenTabs\[\].  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[TopAdminMenu]
# an empty "HiddenTabs[]" first replaces the shipped list; without it the items are added
HiddenTabs[]
*/ ?>
```

## SetupTabs

> Tabs that move into the Setup menu for a user who may open the Setup overview (setup/menu):  
> such a user finds the tab's group there (\[Leftmenu\_setup\] GroupTabs\[\]) and has no tab; a user  
> who may not open the overview (a role that holds only the tab's own policy) keeps the tab.  
> Empty by default, e.g. SetupTabs\[\]=audit in a siteaccess or override menu.ini.append.php.  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[TopAdminMenu]
# an empty "SetupTabs[]" first replaces the shipped list; without it the items are added
SetupTabs[]
*/ ?>
```

## MegaMenuTabs

> admin4 (prototype): tabs that get a mega menu, a panel under the header with the groups and  
> links of the tab's menu, opened by a small button beside the tab (the tab stays a link).  
> A tab takes part when its \[Topmenu\_<tab>\] names a MegaMenuTemplate: setup (the Setup menu,  
> \[Leftmenu\_setup\]), users (\[Leftmenu\_user\_mega\]) and dashboard (\[Leftmenu\_my\_mega\]) do; a name without one is  
> ignored (named in the debug report). The  
> panel holds the menu's links with their names, icons (LinkIcons\[\]), descriptions  
> (LinkDescriptions\[\]) and access checks, a short choice of them (MegaMenuLinks\[\] of the menu's  
> section), loaded from user/megamenu/<tab> when it is first opened. A tab name is lower case  
> letters, digits and \_; a tab with a mega menu should be access checked (PolicyList\[\], or its URL under  
> CheckViewAccess). Shown from 576px, where  
> the main menu is one line (not in the phone menu). Empty by default: no button, no script. To switch it on, in a  
> siteaccess or override menu.ini.append.php:  
>   MegaMenuTabs\[\]=setup  
>   MegaMenuTabs\[\]=users  
>   MegaMenuTabs\[\]=dashboard  
> (doc/en/us/features/6.0/admin4-design.md, "Mega menu")  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[TopAdminMenu]
# an empty "MegaMenuTabs[]" first replaces the shipped list; without it the items are added
MegaMenuTabs[]
*/ ?>
```

## Tabs

> This list contains menuitems of the top menu in admin interface  
> Hidden by default as of 4.3  
> Tabs\[\]=shop  
> Unhidden by default as of 6.0  
> Hidden by default as of 4.3  
> Tabs\[\]=design  
> Unhidden by default as of 6.0.6  
> The audit console (doc/en/us/bc/6.0/audit.md): shown only with the policy audit/read  

Default: a list of 8: `dashboard`, `content`, `media`, `users`, `shop`, `design`, `setup`, `audit`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[TopAdminMenu]
# an empty "Tabs[]" first replaces the shipped list; without it the items are added
Tabs[]
Tabs[]=dashboard
Tabs[]=content
Tabs[]=media
Tabs[]=users
Tabs[]=shop
Tabs[]=design
Tabs[]=setup
Tabs[]=audit
*/ ?>
```

## Related

- [All blocks of menu.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/menu.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
