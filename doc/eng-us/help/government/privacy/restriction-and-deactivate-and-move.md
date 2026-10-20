---
title: Restriction and Deactivate and move
audience: government, admin
category: public-sector
order: 180
summary: For public bodies that must restrict processing or retire accounts without deleting them - Exponential's Disable, Deactivate and move (opt-in, reversible, hidden group without roles) and Check user accounts, with what each changes and what stays.
keywords: restriction of processing, GDPR article 18, disable account, Deactivate and move, DeactivateAndMove, Reactivate and move back, hidden group, user/deactivate, user/hygiene, leavers, reversible, retire accounts, public sector
type: help
---
# Restriction and Deactivate and move

This page is for administrators. Sometimes data must stay but its use must stop: a contested account, a pending
investigation, a leaver whose content and history must be kept. Exponential offers disabling and, opt-in, **Deactivate
and move** ([Check user accounts](../../../features/6.0/user-hygiene.md), [Privacy standards](../../../standards/privacy.md#gdpr)).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Three tools](#three-tools)
2. [Switching Deactivate and move on](#switching-deactivate-and-move-on)
3. [What it does](#what-it-does)
4. [Who may use it](#who-may-use-it)
5. [Related pages](#related-pages)

## Three tools

| Tool | Effect | Reversible |
|---|---|---|
| **Disable** (Users > Check user accounts, or the account settings) | the account cannot sign in, its sessions end; content, groups and roles stay | yes, **Enable** |
| **Deactivate and move** (opt-in) | disabled and all locations brought into one hidden group without roles; where it was is remembered | yes, **Reactivate and move back** |
| **Remove** | the user object is gone for good (no trash) | no |

## Switching Deactivate and move on

```ini
# settings/override/site.ini.append.php, or the admin siteaccess's site.ini.append.php
[UserSettings]
DeactivateAndMove=enabled
# optional: a group of your own instead of the one found by its remote id
DeactivatedUserGroupNodeID=
```

Off by default; with it off nothing is shown. The group ("Deactivated users", remote id
`exp_deactivated_users_group`) is created on first use below the Users node, or beforehand with
`php bin/php/ezcreatedeactivatedusergroup.php -s <admin siteaccess> --commit`.

## What it does

For each account, in one transaction: remembers its locations; disables it (sessions end); removes the other locations;
moves the main location into the hidden group. Content, versions, roles assigned to the account itself and relations
stay. The group has no roles, and assigning a role to it is refused. Hidden, so the accounts do not show on the site or
in its user lists. Recorded as `access.user.deactivate_move`; reversal as `access.user.reactivate_move`. The remembered
locations are kept as node ids only in `ezsite_data`.

## Who may use it

The function `user/deactivate` (only Administrator in a clean installation; also any role with `user/*`, so check those
first), plus `user/hygiene` for the bulk actions, and the content rights on each account. The anonymous user, the user
creator, the administrator account and your own account are never touched.

## Related pages

- [Check user accounts: Deactivate and move](../../../features/6.0/user-hygiene.md#deactivate-and-move)
- [The right to erasure](right-to-erasure.md)
- [Roles, policies and unactivated users](../../../guides/roles-and-policies.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
