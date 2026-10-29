---
title: menu.ini [Leftmenu_user]
audience: developer, admin
category: examples
summary: "The settings of [Leftmenu_user] in menu.ini: Name, Links, LinkNames, PolicyList_roles_and_policies, PolicyList_unactivated, PolicyList_user_hygiene, PolicyList_user_audit, PolicyList_session_report ..."
keywords: menu.ini, Leftmenu_user, INI, setting, Name, Links, LinkNames, PolicyList_roles_and_policies, PolicyList_unactivated, PolicyList_user_hygiene, PolicyList_user_audit, PolicyList_session_report, LinkIcons, LinkDescriptions
type: generated
generated: exp:help:examples
---
# menu.ini [Leftmenu_user]

The block `[Leftmenu_user]` of `settings/menu.ini`, with 10 setting(s).

Never edit `settings/menu.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/menu.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/menu.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/menu.ini.append.php` in an extension (read when the extension is active).

## Name

Default: `access_controll`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user]
Name=access_controll
*/ ?>
```

## Links

> Check user accounts (doc/eng-us/features/6.0/user-hygiene.md), also a tile of the Setup overview under the same key  
> The user audit log (doc/eng-us/features/6.0/user-audit.md): who changed, signed in or was refused, per account  
> The sign-in report (doc/eng-us/features/6.0/user-sessions.md): who is signed in, sign-ins and failures per day,  
> signing a user out everywhere; works with every session handler  

Default: a hash of 5: `roles_and_policies=role/list`, `unactivated=user/unactivated`, `user_hygiene=user/hygiene`, `user_audit=user/audit`, `session_report=user/sessionreport`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user]
# an empty "Links[]" first replaces the shipped list; without it the items are added
Links[]
Links[roles_and_policies]=role/list
Links[unactivated]=user/unactivated
Links[user_hygiene]=user/hygiene
Links[user_audit]=user/audit
Links[session_report]=user/sessionreport
*/ ?>
```

## LinkNames

Default: a hash of 3: `user_hygiene=Check user accounts`, `user_audit=User audit log`, `session_report=Sign-in report`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user]
# an empty "LinkNames[]" first replaces the shipped list; without it the items are added
LinkNames[]
LinkNames[user_hygiene]=Check user accounts
LinkNames[user_audit]=User audit log
LinkNames[session_report]=Sign-in report
*/ ?>
```

## PolicyList_roles_and_policies

Default: a list of 1: `role/*`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user]
# an empty "PolicyList_roles_and_policies[]" first replaces the shipped list; without it the items are added
PolicyList_roles_and_policies[]
PolicyList_roles_and_policies[]=role/*
*/ ?>
```

## PolicyList_unactivated

Default: a list of 1: `user/activation`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user]
# an empty "PolicyList_unactivated[]" first replaces the shipped list; without it the items are added
PolicyList_unactivated[]
PolicyList_unactivated[]=user/activation
*/ ?>
```

## PolicyList_user_hygiene

Default: a list of 1: `user/hygiene`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user]
# an empty "PolicyList_user_hygiene[]" first replaces the shipped list; without it the items are added
PolicyList_user_hygiene[]
PolicyList_user_hygiene[]=user/hygiene
*/ ?>
```

## PolicyList_user_audit

Default: a list of 1: `user/audit`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user]
# an empty "PolicyList_user_audit[]" first replaces the shipped list; without it the items are added
PolicyList_user_audit[]
PolicyList_user_audit[]=user/audit
*/ ?>
```

## PolicyList_session_report

Default: a list of 1: `user/sessions_manage`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user]
# an empty "PolicyList_session_report[]" first replaces the shipped list; without it the items are added
PolicyList_session_report[]
PolicyList_session_report[]=user/sessions_manage
*/ ?>
```

## LinkIcons

> Icons and descriptions of these links in admin4's mega menu of the Users tab (\[Leftmenu\_user\_mega\] adds this menu  
> to its group "access"); descriptions are translated in design/admin/parts/user/menu  

Default: a hash of 5: `roles_and_policies=shield-lock`, `unactivated=person-slash`, `user_hygiene=person-badge`, `user_audit=journal-text`, `session_report=bar-chart-line`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user]
# an empty "LinkIcons[]" first replaces the shipped list; without it the items are added
LinkIcons[]
LinkIcons[roles_and_policies]=shield-lock
LinkIcons[unactivated]=person-slash
LinkIcons[user_hygiene]=person-badge
LinkIcons[user_audit]=journal-text
LinkIcons[session_report]=bar-chart-line
*/ ?>
```

## LinkDescriptions

Default: a hash of 5: `roles_and_policies=Who may do what, and where.`, `unactivated=Accounts not yet activated; activate or remove them.`, `user_hygiene=Accounts nobody uses or that need a look; disable, unlock or export them.`, `user_audit=Who changed, signed in or was refused, per account; filter and export.`, `session_report=Who is signed in, sign-ins and failures per day; sign a user out everywhere.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_user]
# an empty "LinkDescriptions[]" first replaces the shipped list; without it the items are added
LinkDescriptions[]
LinkDescriptions[roles_and_policies]=Who may do what, and where.
LinkDescriptions[unactivated]=Accounts not yet activated; activate or remove them.
LinkDescriptions[user_hygiene]=Accounts nobody uses or that need a look; disable, unlock or export them.
LinkDescriptions[user_audit]=Who changed, signed in or was refused, per account; filter and export.
LinkDescriptions[session_report]=Who is signed in, sign-ins and failures per day; sign a user out everywhere.
*/ ?>
```

## Related

- [All blocks of menu.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/menu.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
