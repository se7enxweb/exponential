---
title: Check what a role allows
audience: government, admin, project-manager
category: public-sector
order: 310
summary: Step by step - read a role's page to see every policy as a sentence, who it reaches and with which limitations, spot dangerous badges and dead limitations, and test it with a test account.
keywords: role, policies, check role, role page, policy sentences, Full access, Can change roles, no handler denies, users affected, test account, access review
type: help
---
# Check what a role allows

This task is for administrators and for managers who must sign off a role. At the end you know exactly what a role
lets its holders do, and to whom it applies. The role page is described in
[Roles guide, section 2](../../../../guides/roles-and-policies.md#2-read-one-role).

## Contents
1. [Before you start](#before-you-start)
2. [Read the role page](#read-the-role-page)
3. [Warning signs](#warning-signs)
4. [Test it](#test-it)
5. [Related pages](#related-pages)

## Before you start

- An account with the `role` module (or an administrator who shows you the page).
- A test account you may use, not a real person's account.

## Read the role page

1. **Users > Roles and policies**, click the role.
2. **The figures**: policies, assignments, user groups, users assigned directly, and **users affected in all** (every
   user the role reaches, counting group members once). The line below says how many assignments are limited.
3. **The policies as sentences**, for example *May read content, in section Standard, of the classes Article and
   Folder*. Under each: the policy ID, its module and function, and the limitations as stored. Order the list by
   module to group it.
4. **The users and groups**, with their limitation (*Only in section ...*, *Only in the subtree ...*). Assignments
   whose user or group was removed are listed first.

Copy the sentences into your role record ([Roles, policies and limitations](../../operations/people-and-permissions/roles-policies-and-limitations.md#documenting-access-for-an-audit)).

## Warning signs

| You see | Means | Do |
|---|---|---|
| *Full access* (red) | a policy over every module | only for named administrators |
| *Can change roles* (amber) | a policy over the `role` module | only for administrators |
| a policy with every function of one module | every function of that module, including future ones | list the functions needed instead |
| *(no handler, denies)* after a limitation | no extension evaluates it; the policy gives nothing | activate the extension or remove the policy |
| *Not assigned* | gives nobody anything | remove it or record why it stays |
| an assignment without limitation where you expected one | the role applies everywhere | remove it and assign again with a limitation |

Remember: a user holds the union of all roles of all their groups. Checking one role does not tell what a person can
do; the person's row on **Users > Check user accounts** lists every role they hold.

## Test it

1. Put the test account into the group that holds the role (and nothing else).
2. Sign in as it in a private window.
3. Try one allowed action and one forbidden action per policy area. A forbidden view is refused and recorded as
   `access.permission.refused`.
4. Take the test account out of the group again.

## Related pages
- [Audit who has admin rights](audit-who-has-admin-rights.md)
- [Least-privilege principles](../../operations/people-and-permissions/least-privilege-principles.md)
- [Role policy order](../../../../features/6.0/role-policy-order.md)
- [Role and policy template operators](../../../../features/6.0/role-and-policy-template-operators.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
