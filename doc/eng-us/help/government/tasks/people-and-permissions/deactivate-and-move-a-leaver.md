---
title: Deactivate and move a leaver
audience: government, admin
category: public-sector
order: 60
summary: Step by step - switch on Deactivate and move, create the hidden group for deactivated accounts, and retire a leaver's account reversibly so its content, versions and audit history stay intact.
keywords: deactivate and move, leaver, retire account, deactivated users, hidden group, reactivate and move back, DeactivateAndMove, ezcreatedeactivatedusergroup, offboarding
type: help
---
# Deactivate and move a leaver

This task is for administrators. At the end, a leaver's account is disabled and all its locations sit in one hidden
group without roles; where it was is remembered, so **Reactivate and move back** can restore it. Nothing is removed.
The feature is described in full on [Deactivate and move](../../../../features/6.0/user-hygiene.md#deactivate-and-move).

## Contents
1. [Before you start](#before-you-start)
2. [Switch the feature on](#switch-the-feature-on)
3. [Create the group beforehand](#create-the-group-beforehand)
4. [Deactivate and move](#deactivate-and-move)
5. [Check it worked](#check-it-worked)
6. [Reactivate](#reactivate)
7. [Related pages](#related-pages)

## Before you start

- The function `user/deactivate` (only Administrator in a clean installation; a role with `user/*` also gets it).
- `content/edit` on the account, and the content rights to move it (see the feature page, "Who may use it").
- Shell access for the settings, unless your settings are managed by deployment (then through version control).

## Switch the feature on

It is off by default (`site.ini [UserSettings] DeactivateAndMove=disabled`). In
`settings/override/site.ini.append.php` (or the administration siteaccess's `site.ini.append.php`):

```ini
[UserSettings]
DeactivateAndMove=enabled
```

Then clear the INI cache:

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
```

## Create the group beforehand

The first **Deactivate and move** creates the group "Deactivated users" below the Users node if the user doing it may
create user groups there. To create it in advance, as part of a change:

```bash
php bin/php/ezcreatedeactivatedusergroup.php -s <admin siteaccess>            # dry run: what it would do
php bin/php/ezcreatedeactivatedusergroup.php -s <admin siteaccess> --commit   # creates it
```

Options `--name="Former staff"` and `--section-id=N` give it another name or section. The script prints the node id
and the settings lines; it never writes an INI file. To use a group of your own, set
`DeactivatedUserGroupNodeID=<node id>`.

## Deactivate and move

1. Open the leaver's account page (its node in the Users tree) and press **Deactivate and move**, or tick it on
   **Users > Check user accounts** and use **Deactivate and move the ticked accounts**.
2. The confirmation page (`user/deactivate`) lists the account with its current groups and where it goes.
3. Press **Confirm**. In one transaction the account is disabled (its sessions end), its other locations are removed
   and its main location is moved into the group.

The audit trail records `access.user.deactivate_move` with the locations before and after.

## Check it worked

- **Check user accounts**: the account is **Disabled** and carries the badge *deactivated* in its groups.
- The account does not appear in the site's lists of users (the group is hidden).
- No role is assigned to the group: **Assign** on a role page leaves the group out and says so.

## Reactivate

If the person returns: on the account page, **Reactivate and move back**, then **Confirm**. The account is enabled and
moved back to its remembered main parent (or to `[UserSettings] DefaultUserPlacement` if that no longer exists).
Recorded as `access.user.reactivate_move`.

## Related pages
- [Remove a leaver](remove-a-leaver.md)
- [Joiners, movers and leavers](../../operations/people-and-permissions/joiners-movers-leavers.md)
- [User account hygiene](../../operations/people-and-permissions/user-account-hygiene.md)
- [Roles and policies guide, section 7](../../../../guides/roles-and-policies.md#7-clean-up-unactivated-users)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
