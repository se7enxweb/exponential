---
title: site.ini [RoleSettings]
audience: developer, admin
category: examples
summary: "The settings of [RoleSettings] in site.ini: EnableCaching, PoliciesPerPage, AccessArrayPrefetch, PreventPrivilegeEscalation, AssignmentsPerPage, RolesPerPageList, PolicyPreviewPerRole, PermissionSQLOptimization ..."
keywords: site.ini, RoleSettings, INI, setting, EnableCaching, PoliciesPerPage, AccessArrayPrefetch, PreventPrivilegeEscalation, AssignmentsPerPage, RolesPerPageList, PolicyPreviewPerRole, PermissionSQLOptimization, PolicyOmitList, PolicyOmitListExclude, ShowAccessDeniedReason, MaxParentDepthLimitation, LimitationHandlers
type: generated
generated: exp:help:examples
---
# site.ini [RoleSettings]

The block `[RoleSettings]` of `settings/site.ini`, with 13 setting(s).

Never edit `settings/site.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/site.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in an extension (read when the extension is active).

## EnableCaching

Default: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RoleSettings]
EnableCaching=true
*/ ?>
```

## PoliciesPerPage

> How many policies a screen shows at once.  
>
> A role's policy list is paged. $role.policies is every policy the role has,  
> which the permission system needs and a screen does not: on an installation  
> whose roles carry policies in the millions, loading them all to draw a  
> screenful exhausts memory before the first row is written.  

Default: `25`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RoleSettings]
PoliciesPerPage=25
*/ ?>
```

## AccessArrayPrefetch

> How the access array of a user is built when the role cache is rebuilt.  
> enabled loads the policies, limitations and values of all the user's roles in  
> three queries and computes the limitations of a policy once for all the  
> assignments of its role (a member of 120 teamrooms has every policy of the  
> member role 120 times). disabled asks the database once for every role, policy  
> and limitation, as before. The access array is the same either way, in the same  
> order.  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RoleSettings]
AccessArrayPrefetch=enabled
*/ ?>
```

## PreventPrivilegeEscalation

> Whether a user who may edit roles can give more than they have themselves.  
> enabled (the default) refuses, in the role editor, the policy wizard, the  
> policy editor, role/copy and role/assign, any policy or assignment that is  
> broader than the editor's own access: a policy must be covered by one of the  
> editor's policies (module, function and every limitation of that policy, a  
> subtree inside one of its subtrees, sections, classes, nodes, languages and  
> other values among its values), and a role may only be assigned when the  
> editor covers every policy of it, narrowed by the assignment's limitation.  
> Users with access to every function of every module may grant anything.  
> disabled gives back the old behaviour: role editors may grant anything.  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RoleSettings]
PreventPrivilegeEscalation=enabled
*/ ?>
```

## AssignmentsPerPage

> How many of the users and user groups a role is assigned to role/view shows  
> at once, sorted by name and paged on (assignment\_offset). A role given to  
> thousands of users took long to draw. 0 lists all of them on one page.  

Default: `50`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RoleSettings]
AssignmentsPerPage=50
*/ ?>
```

## RolesPerPageList

> The page sizes the role list offers in its items per page selector.  
>
> The stored preference is the position in this list, counting from one, so a  
> site that changes these keeps everyone's choice rather than resetting it -  
> and the old 1, 2 and 3 still mean the first, second and third entry.  

Default: a list of 3: `10`, `25`, `50`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RoleSettings]
# an empty "RolesPerPageList[]" first replaces the shipped list; without it the items are added
RolesPerPageList[]
RolesPerPageList[]=10
RolesPerPageList[]=25
RolesPerPageList[]=50
*/ ?>
```

## PolicyPreviewPerRole

> How many of a role's policies the summary window on a user or user group  
> shows before it stops and links to the role. That window lists every role the  
> user has, so it is the product of two lists rather than one.  

Default: `10`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RoleSettings]
PolicyPreviewPerRole=10
*/ ?>
```

## PermissionSQLOptimization

> How the permission condition of a content fetch is built from the read policies  
> of the user. enabled merges the policies that differ only in the subtree their  
> role is assigned for (a member of 120 teamrooms has every read policy of the  
> member role 120 times) and leaves out those that cannot give access to a node  
> the fetch returns: subtrees outside the fetched one, classes outside its class  
> filter. The fetch returns the same nodes. disabled ORs every policy as before;  
> SQLite refuses that condition from 1000 policies on.  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RoleSettings]
PermissionSQLOptimization=enabled
*/ ?>
```

## PolicyOmitList

> A list of modules to omit policy checking on,  
> You should add 'role' to the list if you loose  
> access to the role module  
> You can also specify views by adding a / and the viewname  
> This is a fake omission: we need a manual policy check, therefore implemented directly in oauth/authorize.php  
> Order receipts decide for themselves: the customer who placed the order, or  
> shop/administrate (eZShopReceipt::canView); everyone else gets the login form.  
> E-mail preferences: settings and export check the login themselves, manage,  
> unsubscribe and confirm work with a signed link, request sends one.  
> API access: apikey/list checks for itself (signed in to see and revoke one's own keys,  
> apikey/create to make one).  
> admin4's mega menu: user/megamenu checks for itself (a signed-in user who may log in to the  
> siteaccess, user/login with its SiteAccess limitation, and the tab open to that user), so every  
> refusal is a bare 403 for its script instead of a page.  
> The reports of the Content Security Policy come from browsers, signed in or not; csp/report protects itself  
> (content types, size, rate limits, masked log lines).  

Default: a list of 23: `user/login`, `user/logout`, `user/register`, `user/activate`, `user/success`, `user/forgotpassword`, `layout`, `manual`, `ezinfo`, `paypal/notify_url`, `switchlanguage`, `oauth/authorize` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RoleSettings]
# an empty "PolicyOmitList[]" first replaces the shipped list; without it the items are added
PolicyOmitList[]
PolicyOmitList[]=user/login
PolicyOmitList[]=user/logout
PolicyOmitList[]=user/register
PolicyOmitList[]=user/activate
PolicyOmitList[]=user/success
PolicyOmitList[]=user/forgotpassword
PolicyOmitList[]=layout
PolicyOmitList[]=manual
PolicyOmitList[]=ezinfo
PolicyOmitList[]=paypal/notify_url
PolicyOmitList[]=switchlanguage
PolicyOmitList[]=oauth/authorize
PolicyOmitList[]=shop/orderreceipt
PolicyOmitList[]=shop/orderreciept
PolicyOmitList[]=mailpreferences/settings
PolicyOmitList[]=mailpreferences/manage
PolicyOmitList[]=mailpreferences/unsubscribe
PolicyOmitList[]=mailpreferences/confirm
PolicyOmitList[]=mailpreferences/request
PolicyOmitList[]=mailpreferences/export
# ... 3 more in the shipped file
*/ ?>
```

## PolicyOmitListExclude

> Entries taken out of PolicyOmitList\[\] after every settings file is merged, so a site can  
> close one entry without resetting the list (a reset would also drop the entries the active  
> extensions add). New installations write PolicyOmitListExclude\[\]=user/register in  
> settings/override/site.ini.append.php: registering then needs the user/register policy  
> (give it to the Anonymous role for open registration) and the login pages show the  
> "register" link only to a user who has it. An upgraded site keeps user/register open;  
> to close it, add the line above to settings/override/site.ini.append.php.  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RoleSettings]
# an empty "PolicyOmitListExclude[]" first replaces the shipped list; without it the items are added
PolicyOmitListExclude[]
*/ ?>
```

## ShowAccessDeniedReason

> Should we try to show reason for access denied in view  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RoleSettings]
ShowAccessDeniedReason=disabled
*/ ?>
```

## MaxParentDepthLimitation

> Specifies max level for the 'ParentDepth' limitation.  

Default: `20`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RoleSettings]
MaxParentDepthLimitation=20
*/ ?>
```

## LimitationHandlers

> Handlers of content policy limitations the kernel does not know itself, one  
> class per limitation name; the class implements ezpContentLimitationHandler.  
> An extension that adds a limitation to a content function (event filter  
> module/functionlist) registers its handler here. A limitation without a  
> handler denies: in checkAccess() and in the SQL of list and tree fetches.  
> LimitationHandlers\[MyLimitation\]=myLimitationHandler  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RoleSettings]
# an empty "LimitationHandlers[]" first replaces the shipped list; without it the items are added
LimitationHandlers[]
*/ ?>
```

## Related

- [All blocks of site.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/site.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
