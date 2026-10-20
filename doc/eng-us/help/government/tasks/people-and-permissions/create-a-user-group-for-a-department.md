---
title: Create a user group for a department
audience: government, admin
category: public-sector
order: 170
summary: Step by step - create a user group for a department or team in the Users tree, give it its roles with limitations, and add members, so access is managed per group instead of per person.
keywords: user group, department, team, Users tree, create group, roles, members, access by group, organization structure
type: help
---
# Create a user group for a department

This task is for administrators. At the end, a department has a user group that carries its roles; adding or
removing a person is then all it takes to change their access. Background:
[Roles, policies and limitations](../../operations/people-and-permissions/roles-policies-and-limitations.md#assigning-roles).

## Contents
1. [Before you start](#before-you-start)
2. [Plan the structure](#plan-the-structure)
3. [Create the group](#create-the-group)
4. [Give it roles](#give-it-roles)
5. [Add members](#add-members)
6. [Check it worked](#check-it-worked)
7. [Related pages](#related-pages)

## Before you start

- An administrator account (creating user groups needs `content/create` of the user group class under Users).
- The department's name as it should appear to staff.

## Plan the structure

Roles assigned to a group also reach the members of the groups below it. A simple structure for a public body:

```
Users
  Staff                       (role: Staff sign-in)
    Web team                  (role: Web team)
    Departments
      Department of Example   (role: Department editor, subtree /services/example/)
      Office of Placeholder   (role: Department editor, subtree /services/placeholder/)
    Approvers - Example       (role: Approver, subtree /services/example/)
  Contractors
    Contract 2026-07          (role: Contractor - content, subtree of one folder)
```

Keep visitors' and members' accounts in other groups than staff.

## Create the group

1. Open **Users** in the administration and the parent group (for example "Departments").
2. Create a new item of the class "User group" there.
3. Name it after the department; publish.

## Give it roles

On each role page: **Assign** (or **Assign with limitation** with the department's subtree) and choose the new group
([Assign a role with a subtree limitation](assign-a-role-with-a-subtree-limitation.md)).

## Add members

- New staff: create their account inside the group ([Add a new staff member](add-a-new-staff-member.md)).
- Existing staff: add a location in the group (account's **Locations** tab, **Add locations**) or move the account
  ([Move a staff member to another department](move-a-staff-member-to-another-department.md)).

## Check it worked

- On each role page, the group is listed with its limitation.
- **Users > Check user accounts**, **User group** = the new group: the members are listed with the group's roles.

## Related pages
- [Create a department editor role for a subtree](create-a-department-editor-role-for-a-subtree.md)
- [Joiners, movers and leavers](../../operations/people-and-permissions/joiners-movers-leavers.md)
- [Roles and policies guide](../../../../guides/roles-and-policies.md)
- [Site administrators course](../../../trainers/site-administrators/README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
