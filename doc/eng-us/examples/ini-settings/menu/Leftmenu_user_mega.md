---
title: menu.ini [Leftmenu_user_mega]
audience: developer, admin
category: examples
summary: "The settings of [Leftmenu_user_mega] in menu.ini: Name, Links, LinkNames, PolicyList_accounts, PolicyList_sessions, Groups, GroupNames, GroupLinks_users ..."
keywords: menu.ini, Leftmenu_user_mega, INI, setting, Name, Links, LinkNames, PolicyList_accounts, PolicyList_sessions, Groups, GroupNames, GroupLinks_users, GroupSections, LinkIcons, LinkDescriptions, MegaMenuLinks
type: generated
generated: exp:help:examples
---
# menu.ini [Leftmenu_user_mega]

The block `[Leftmenu_user_mega]` of `settings/menu.ini`, with 12 setting(s).

> admin4's mega menu of the Users tab (\[TopAdminMenu\] MegaMenuTabs\[\]=users, \[Topmenu\_users\] MegaMenuTemplate,  
> drawn by parts/user/mega\_menu.tpl through parts/ini\_menu.tpl). Not a left menu: the Users tab's left menu is the  
> user tree and \[Leftmenu\_user\] (parts/user/menu.tpl), and a tree is no list of links. This section only gathers  
> the tab's functions in groups, and it reuses the menus that already define them instead of restating them:  
> GroupSections\[\] adds \[Leftmenu\_user\] (Roles and policies, Unactivated users) and \[Leftmenu\_my\] (the user's own  
> settings) with their addresses, policies, icons and descriptions; its own links are the two the Users tab has no  
> menu entry for, the user tree and the sessions. Named Leftmenu\_user\_\* so that it shares the translation context  
> of the tab's menu, design/admin/parts/user/menu. MegaMenuLinks\[\] chooses from all of them, a link of an added  
> section as <group>-<key>; at most 12 are shown, each in its group and only when the user may open it.  

Never edit `settings/menu.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/menu.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/menu.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/menu.ini.append.php` in an extension (read when the extension is active).

## Name

Default: `users`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user_mega]
Name=users
*/ ?>
```

## Links

> The user tree, the Users tab's own page (its PolicyList\[\] and URL\[default\]; content.ini \[NodeSettings\] UserRootNode:  
> a site with another user root node changes these two lines as it changes \[Topmenu\_users\])  

Default: a hash of 2: `accounts=content/view/full/5`, `sessions=setup/session`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user_mega]
# an empty "Links[]" first replaces the shipped list; without it the items are added
Links[]
Links[accounts]=content/view/full/5
Links[sessions]=setup/session
*/ ?>
```

## LinkNames

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user_mega]
# an empty "LinkNames[]" first replaces the shipped list; without it the items are added
LinkNames[]
*/ ?>
```

## PolicyList_accounts

Default: a list of 1: `5`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user_mega]
# an empty "PolicyList_accounts[]" first replaces the shipped list; without it the items are added
PolicyList_accounts[]
PolicyList_accounts[]=5
*/ ?>
```

## PolicyList_sessions

Default: a list of 1: `setup/administrate`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user_mega]
# an empty "PolicyList_sessions[]" first replaces the shipped list; without it the items are added
PolicyList_sessions[]
PolicyList_sessions[]=setup/administrate
*/ ?>
```

## Groups

Default: a list of 3: `users`, `access`, `account`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user_mega]
# an empty "Groups[]" first replaces the shipped list; without it the items are added
Groups[]
Groups[]=users
Groups[]=access
Groups[]=account
*/ ?>
```

## GroupNames

Default: a hash of 3: `users=Users`, `access=Access control`, `account=My account`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user_mega]
# an empty "GroupNames[]" first replaces the shipped list; without it the items are added
GroupNames[]
GroupNames[users]=Users
GroupNames[access]=Access control
GroupNames[account]=My account
*/ ?>
```

## GroupLinks_users

Default: a list of 2: `accounts`, `sessions`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user_mega]
# an empty "GroupLinks_users[]" first replaces the shipped list; without it the items are added
GroupLinks_users[]
GroupLinks_users[]=accounts
GroupLinks_users[]=sessions
*/ ?>
```

## GroupSections

Default: a hash of 2: `access=Leftmenu_user`, `account=Leftmenu_my`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user_mega]
# an empty "GroupSections[]" first replaces the shipped list; without it the items are added
GroupSections[]
GroupSections[access]=Leftmenu_user
GroupSections[account]=Leftmenu_my
*/ ?>
```

## LinkIcons

Default: a hash of 2: `accounts=people`, `sessions=person-check`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user_mega]
# an empty "LinkIcons[]" first replaces the shipped list; without it the items are added
LinkIcons[]
LinkIcons[accounts]=people
LinkIcons[sessions]=person-check
*/ ?>
```

## LinkDescriptions

Default: a hash of 2: `accounts=Users and user groups; create and edit accounts.`, `sessions=Who is signed in; end sessions.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user_mega]
# an empty "LinkDescriptions[]" first replaces the shipped list; without it the items are added
LinkDescriptions[]
LinkDescriptions[accounts]=Users and user groups; create and edit accounts.
LinkDescriptions[sessions]=Who is signed in; end sessions.
*/ ?>
```

## MegaMenuLinks

Default: a list of 9: `accounts`, `sessions`, `access-roles_and_policies`, `access-unactivated`, `access-user_hygiene`, `account-edit_profile`, `account-change_password`, `account-my_notifications`, `account-my_mail`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user_mega]
# an empty "MegaMenuLinks[]" first replaces the shipped list; without it the items are added
MegaMenuLinks[]
MegaMenuLinks[]=accounts
MegaMenuLinks[]=sessions
MegaMenuLinks[]=access-roles_and_policies
MegaMenuLinks[]=access-unactivated
MegaMenuLinks[]=access-user_hygiene
MegaMenuLinks[]=account-edit_profile
MegaMenuLinks[]=account-change_password
MegaMenuLinks[]=account-my_notifications
MegaMenuLinks[]=account-my_mail
*/ ?>
```

## Related

- [All blocks of menu.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/menu.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
