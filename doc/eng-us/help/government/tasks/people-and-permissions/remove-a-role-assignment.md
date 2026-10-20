---
title: Remove a role assignment
audience: government, admin
category: public-sector
order: 190
summary: Step by step - take a role away from a user or a group on the role page with Remove selected, clean up assignments whose user or group no longer exists, and check the access is gone.
keywords: remove role, role assignment, Remove selected, revoke access, take away rights, removed user, orphaned assignment, access review
type: help
---
# Remove a role assignment

This task is for administrators. At the end, a user or group no longer holds a role. The users and groups are not
removed; they only lose the role ([Roles guide, section 5](../../../../guides/roles-and-policies.md#5-assign-the-role)).

## Contents
1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Assignments of removed users or groups](#assignments-of-removed-users-or-groups)
4. [Check it worked](#check-it-worked)
5. [Related pages](#related-pages)

## Before you start

- An account with the `role` module.
- Know whether the person got the role directly or through a group: the account's row on **Users > Check user
  accounts** lists its own and inherited roles. To take away a role a group gives, move the person out of the group
  instead of removing the group's assignment.

## Steps

1. **Users > Roles and policies**, open the role.
2. In "Users and groups using the <role> role", find the assignment; with many assignments use the name filter.
3. Tick the assignment (one user or group, with its limitation).
4. Open the confirmation and press **Remove the ticked assignments**.
5. The users and groups lose the role at once.

Every action on the role pages needs the page's form token; a request without it is refused with 403.

## Assignments of removed users or groups

An assignment whose user or group was removed is listed first on the role page and can be removed the same way.
Remove them during access reviews; they are noise in every audit.

## Check it worked

- The role page no longer lists the assignment; **users affected in all** went down.
- The account's page (`/user/edit/<id>`) no longer lists the role.
- The change is recorded in the audit trail's `access` channel.

Change record: role, user or group, limitation, date, reason.

## Related pages
- [Audit who has admin rights](audit-who-has-admin-rights.md)
- [Move a staff member to another department](move-a-staff-member-to-another-department.md)
- [Admin access reviews](../../operations/people-and-permissions/admin-access-reviews.md)
- [Role assignment paging](../../../../features/6.0/role-assignment-paging.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
