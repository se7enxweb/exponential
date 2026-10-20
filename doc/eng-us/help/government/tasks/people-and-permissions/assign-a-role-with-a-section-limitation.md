---
title: Assign a role with a section limitation
audience: government, admin
category: public-sector
order: 100
summary: Step by step - give a user group an existing role only for the content of one section (for example an internal or restricted section), with Assign with limitation, and check the result.
keywords: assign role, section limitation, Assign with limitation, section, restricted section, user group, User_Section, role assignment
type: help
---
# Assign a role with a section limitation

This task is for administrators. At the end, a user group holds a role whose policies apply only to objects in one
section. The mechanism is described in [Sections, section 9.2](../../../../guides/sections.md#92-a-role-assigned-with-the-limitation-to-a-section).

## Contents
1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check it worked](#check-it-worked)
4. [When to use a section, when a subtree](#when-to-use-a-section-when-a-subtree)
5. [Related pages](#related-pages)

## Before you start

- The role exists and its policies make sense without a limitation of their own (for example "Department editor").
- The section exists (**Setup > Sections**) and holds the content ([Create a section for restricted content](create-a-section-for-restricted-content.md)).
- The user group exists.
- An account with the `role` module.

## Steps

1. Open **Users > Roles and policies** and click the role.
2. Below the list of users and groups, choose **Section** in the limitation select.
3. Press **Assign with limitation**.
4. Choose the section and continue.
5. Choose the user group (or users) in the content browser and press **Select**.
6. The role page lists the group with *Only in section ...*.

Every policy of the role now applies only to objects in that section (internally a `User_Section` limitation is
added).

## Check it worked

- The section's page (**Setup > Sections**, click the section) lists the assignment under "Users and user groups
  with role limitations associated with this section".
- Sign in as a member: content in the section can be edited, content elsewhere cannot (unless another role allows it).
- The section now counts as "Used by roles" and cannot be removed while the assignment exists.

## When to use a section, when a subtree

| Use a section limitation | Use a subtree limitation |
|---|---|
| the content is scattered in the tree but belongs together (internal documents, a members area) | the content sits under one folder (a department) |
| objects with several locations must be covered everywhere | only one place matters |

See [Sections and subtree limits](../../operations/people-and-permissions/sections-and-subtree-limits.md).

## Related pages
- [Assign a role with a subtree limitation](assign-a-role-with-a-subtree-limitation.md)
- [Remove a role assignment](remove-a-role-assignment.md)
- [Roles, policies and limitations](../../operations/people-and-permissions/roles-policies-and-limitations.md)
- [Sections guide](../../../../guides/sections.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
