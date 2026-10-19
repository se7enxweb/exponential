---
title: Assign a role with a subtree limitation
audience: government, admin
category: public-sector
order: 110
summary: Step by step - give a user group an existing role only below one folder of the content tree, with Assign with limitation and Subtree, and check that the boundary holds.
keywords: assign role, subtree limitation, Assign with limitation, subtree, folder, department, user group, role assignment, boundary
type: help
---
# Assign a role with a subtree limitation

This task is for administrators. At the end, a user group holds a role that applies only at and below one location of
the content tree, the usual way to give a department its own area
([Roles guide, section 5](../../../../guides/roles-and-policies.md#5-assign-the-role)).

## Contents
1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check it worked](#check-it-worked)
4. [Pitfalls](#pitfalls)
5. [Related pages](#related-pages)

## Before you start

- The role exists ([Create a department editor role for a subtree](create-a-department-editor-role-for-a-subtree.md)).
- The folder and the user group exist.
- An account with the `role` module; with escalation prevention on, the role must be within your own access.

## Steps

1. **Users > Roles and policies**, click the role.
2. Below the assignments, choose **Subtree** in the limitation select.
3. Press **Assign with limitation**.
4. In the content browser, navigate to the folder, choose it with its radio button and press **Select**.
5. Choose the user group and press **Select**.
6. The role page lists the group with *Only in the subtree ...* and the line under the figures counts limited
   assignments.

## Check it worked

1. Sign in as a member in a private browser window.
2. In the folder and below it: **Edit** is offered (if the role has edit).
3. Outside it: not offered; a typed edit address is refused and recorded as `access.permission.refused`.

## Pitfalls

- **Assign** without limitation gives the role across the whole site; if you pressed it by mistake, tick the new
  assignment and **Remove selected** at once ([Remove a role assignment](remove-a-role-assignment.md)).
- A subtree limitation follows the tree: content moved out leaves the group's reach immediately.
- Policies with their own subtree limitation combine with the assignment's; both must be met.
- The `user/login` policy should not depend on a subtree assignment; keep it in a staff sign-in role assigned
  without limitation.

## Related pages
- [Assign a role with a section limitation](assign-a-role-with-a-section-limitation.md)
- [Let a contractor edit only one folder](let-a-contractor-edit-only-one-folder.md)
- [Sections and subtree limits](../../operations/people-and-permissions/sections-and-subtree-limits.md)
- [Role assignment paging](../../../../features/6.0/role-assignment-paging.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
