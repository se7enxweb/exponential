---
title: Joiners, movers and leavers
audience: government, admin, project-manager
category: public-sector
order: 110
summary: The account life cycle of public-sector staff in Exponential - creating accounts in the right group, changing department, and retiring accounts on the last day without losing content or the audit record - with the screens and commands for each step.
keywords: joiners movers leavers, JML, onboarding, offboarding, new staff, staff changes, department change, leaver, disable account, Deactivate and move, user groups, account life cycle, HR process
type: help
---
# Joiners, movers and leavers

This page is for administrators and HR or IT service teams who handle staff accounts for a public website. Most public
bodies run a joiners, movers and leavers process; this page maps each step to what Exponential offers. The facts come
from [Check user accounts](../../../../features/6.0/user-hygiene.md), [Roles, policies and unactivated users](../../../../guides/roles-and-policies.md)
and [Reset a user password](../../../../features/6.0/reset-user-password.md).

## Contents
1. [The principle: groups carry access](#the-principle-groups-carry-access)
2. [Joiners](#joiners)
3. [Movers](#movers)
4. [Leavers](#leavers)
5. [Records to keep](#records-to-keep)
6. [Related pages](#related-pages)

## The principle: groups carry access

Roles are assigned to user groups, so an account's access follows the groups it is placed in. A joiner is placed in
groups, a mover changes groups, a leaver leaves all of them. Nobody edits roles for one person. Exponential has no
connection to an HR system of its own; the request usually comes as a ticket, and the account is changed by an
administrator (or by an LDAP or single sign-on handler if your installation uses one; check with your developers).

## Joiners

1. Receive the request with the department, the job and the approver of the request.
2. In the **Users** tree, open the department's user group and create a new user there; the user's main location is
   in that group, so the group's roles apply at once
   ([Add a new staff member](../../tasks/people-and-permissions/add-a-new-staff-member.md)).
3. Give a first password through a secure channel, or let the person use **Forgot your password?**; the minimum length is
   `site.ini [UserSettings] MinPasswordLength` (10 by default, [Security standard](../../../../standards/security.md)).
4. Ask the person to set up two-step sign-in if your policy requires it
   ([Two-factor for staff](two-factor-for-staff.md)).
5. Check: the user's account page (`/user/edit/<id>`) lists the groups and roles.

## Movers

A department change is a move of the account between groups:

1. Add a location in the new department's group, then make it the main location, then remove the old location.
   Or move the account's node directly.
2. Check the account page: the roles of the old group are gone, those of the new group are there.
3. Approver duties: if the person approved content in the old department, remove them from that approvers' group
   and from any **Users who approve content** field of an Approve event.

See [Move a staff member to another department](../../tasks/people-and-permissions/move-a-staff-member-to-another-department.md).
Location changes are in the audit trail: removing a location by default, adding one and changing the main location
once `content.node.add` and `content.node.main` are enabled
([Security and audit, step 3](../../../../guides/security-and-audit.md#3-read-the-audit-trail-5-minutes)).

## Leavers

On the last working day:

1. **Disable** the account on **Users > Check user accounts** (or **Account settings** on its account page). It can
   no longer sign in and its sessions end; content, groups and roles stay. Recorded as `access.user.disable`.
2. Within your agreed period, retire it:
   - **Deactivate and move** (switch on with `[UserSettings] DeactivateAndMove=enabled`): disabled, all locations
     brought into one hidden group without roles, where it was is remembered; reversible
     ([Deactivate and move a leaver](../../tasks/people-and-permissions/deactivate-and-move-a-leaver.md)).
   - Or leave it disabled in place.
3. Hand over: drafts of the leaver appear on **My drafts** only for them, so ask them to publish or remove drafts
   before leaving; pending versions show on **Setup > Workflow processes**.
4. Remove direct role assignments and approver entries naming the person.

Removing the account is possible but final: "A user object never goes to the trash in Exponential: removing it is
final" ([Check user accounts](../../../../features/6.0/user-hygiene.md#in-short)). Most public bodies keep the
account disabled, because the content it owns and the audit trail refer to it.

## Records to keep

| Event | What to keep | Where it comes from |
|---|---|---|
| Joiner | request, approver, groups | your ticket; the account page |
| Mover | old and new group | your ticket; audit channel `content` (locations) |
| Leaver | date disabled, retired | audit events `access.user.disable`, `access.user.deactivate_move` |
| Periodic review | list of accounts | **Export the list as CSV** on Check user accounts |

## Related pages
- [Remove a leaver](../../tasks/people-and-permissions/remove-a-leaver.md)
- [User account hygiene](user-account-hygiene.md)
- [Admin access reviews](admin-access-reviews.md)
- [Role design: contractors](role-design-contractors.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
