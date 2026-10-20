---
title: Copy a role for a new department
audience: government, admin
category: public-sector
order: 180
summary: Step by step - decide whether a new department needs a copy of a role or only a new limited assignment of the same role, and make the copy safely when its policies really differ.
keywords: copy role, new department, Copy, Make the copy, role variant, role assignment, reuse role, duplicate role
type: help
---
# Copy a role for a new department

This task is for administrators. Most new departments do **not** need a new role: the same "Department editor" role,
assigned with a subtree limitation, is enough. Copy a role only when the new department's policies differ (other
classes, other functions). The **Copy** action is described in the [Roles guide, section 6](../../../../guides/roles-and-policies.md#6-copy-and-remove-roles).

## Contents
1. [Before you start](#before-you-start)
2. [First ask: copy or assign?](#first-ask-copy-or-assign)
3. [Make the copy](#make-the-copy)
4. [Adapt and assign it](#adapt-and-assign-it)
5. [Check it worked](#check-it-worked)
6. [Related pages](#related-pages)

## Before you start

- An account with the `role` module. **Copy** is off for a role with a policy beyond your own access.
- The new department's group and folder exist.

## First ask: copy or assign?

| Situation | Do |
|---|---|
| Same work, different folder | no copy: **Assign with limitation**, Subtree, of the existing role ([Assign a role with a subtree limitation](assign-a-role-with-a-subtree-limitation.md)) |
| Same work, a section of its own | no copy: **Assign with limitation**, Section |
| Different content classes or functions | copy, then change the copy |

Every copy is one more role to review; fewer roles are easier to audit.

## Make the copy

1. **Users > Roles and policies**; on the role's card press **Copy**.
2. A page says what the copy holds. Press **Make the copy**. A role "Copy of <name>" is created with the same
   policies and no assignments, and opens in the editor. Opening the copy address alone makes nothing.

## Adapt and assign it

1. Rename it, for example "Department editor - <department> (records)".
2. Change the policies that differ (click **Limitations** next to a policy to change them).
3. **Save**.
4. **Assign with limitation** to the department's group and folder.

## Check it worked

- The role list shows the new role with its policies count and assignments.
- Sign in as a member and try one action the copy adds and one it removes.
- Record why the copy exists, so a later review does not merge it back by mistake.

## Related pages
- [Create a department editor role for a subtree](create-a-department-editor-role-for-a-subtree.md)
- [Check what a role allows](check-what-a-role-allows.md)
- [Role design: department editors](../../operations/people-and-permissions/role-design-department-editors.md)
- [Role policy order](../../../../features/6.0/role-policy-order.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
