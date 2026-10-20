---
title: Departments, roles and sections
audience: businesses, admin, project-manager
category: business-solutions
order: 20
summary: How an intranet maps departments onto Exponential - user groups for people, sections for areas of content, roles with limited policies, and subtree or section limitations so each department edits only its own pages.
keywords: departments, user groups, roles, policies, sections, subtree limitation, section limitation, assign with limitation, least privilege, intranet permissions, content/read, content/edit
type: help
---
# Departments, roles and sections

This page is for intranet owners and administrators who decide who may read and edit which part of the intranet. It
explains the three pieces Exponential uses (user groups, sections and roles), shows the pattern for departments that
each keep their own pages, and links to the guides with every click.

![Roles, one per department, limited by section](../../images/help/bu-dept-sections.webp)

## Contents

1. [Three pieces, three questions](#three-pieces-three-questions)
2. [The department pattern](#the-department-pattern)
3. [Section limitation or subtree limitation](#section-limitation-or-subtree-limitation)
4. [Other limitations worth knowing](#other-limitations-worth-knowing)
5. [Checking what a role really gives](#checking-what-a-role-really-gives)
6. [Mistakes to avoid](#mistakes-to-avoid)
7. [Checklist](#checklist)
8. [Related pages](#related-pages)

## Three pieces, three questions

| Piece | Answers | Where it lives |
|---|---|---|
| **User group** | Who are these people? (Sales, HR, all staff) | the Users tree, under **Users** in the administration |
| **Section** | Which area does this content belong to? (Sales pages, HR policies) | **Setup > Sections** (`/section/list`) |
| **Role** | What may the people of a group do, and where? | **Users > Roles and policies** (`role/list`) |

A role is a set of policies; a policy allows one module or function, possibly only in some sections, classes,
subtrees, languages or siteaccesses. A role does nothing until it is assigned to users or groups
([Roles and policies, In short](../../../guides/roles-and-policies.md#in-short)). Every object belongs to exactly one
section, and new content takes the section of its parent
([Sections, In short](../../../guides/sections.md#in-short)).

## The department pattern

The sections guide describes this exact case, "An intranet with departments"
([Sections, 13.2](../../../guides/sections.md#132-an-intranet-with-departments)). In business terms:

1. **One section per department area**, for example Sales and Marketing. Each department's folder is assigned to its
   section with **Assign content**, which gives the folder and everything below it that section.
2. **One user group per department**, with the staff of that department in it.
3. **One Staff role for everyone** with `content/read` limited to the staff sections, so every employee can read
   every department's pages.
4. **One shared Editor role**, assigned to each department's group **with a Section limitation** for that
   department's section ([Roles and policies, 5. Assign the role](../../../guides/roles-and-policies.md#5-assign-the-role)).
   A sales editor can then edit Sales pages and not Marketing pages.

The advantage for the business: there is one Editor role to maintain, not one per department. Adding a department
means one section, one group and one more assignment.

| Department | Section | Group | Editor role assigned with |
|---|---|---|---|
| Sales | Sales (`sales`) | Sales staff | Section( Sales ) |
| Marketing | Marketing (`marketing`) | Marketing staff | Section( Marketing ) |
| HR | HR (`hr`) | HR staff | Section( HR ) |
| Whole company | Standard or Intranet | All staff | read only, through the Staff role |

> **Note**
> A section is not a place in the tree. It has no URL and no root node; objects of one section can be anywhere
> ([Sections, 1](../../../guides/sections.md#1-what-a-section-is-and-what-it-is-not)). Keeping each department's
> content under one folder is a convention that makes the structure easy to understand.

## Section limitation or subtree limitation

A role can be assigned with a **Subtree** limitation instead of a Section limitation. Both work; they behave
differently when content moves.

| | Section limitation | Subtree limitation |
|---|---|---|
| Follows | the section stored on each object | the place in the tree |
| Content moved elsewhere | keeps its section, so the same editors keep access | leaves the subtree, so the editors lose access |
| Useful when | areas are defined by ownership | areas are defined by where pages sit |
| Visible in | the section's page lists the roles that use it | the role page lists the subtree |

For a document library shared by several departments, a section usually reads better. For a project area that is
archived by moving it, a subtree is natural.

## Other limitations worth knowing

The content module offers more limitations than section and subtree (`kernel/content/module.php`):

| Limitation | Use on an intranet |
|---|---|
| Class | editors of the news area may create News articles, not folders |
| Owner = Self | authors may edit only what they created |
| Language | translators may edit only their language |
| Node, Subtree | access to one page or one branch |
| `StateGroup_<identifier>` | access only to content in some states, for example Approved ([Object states, 8](../../../guides/object-states.md#8-states-in-roles-and-policies)) |
| `user/login` SiteAccess | staff may sign in on the intranet siteaccess (`kernel/user/module.php`) |

Extensions can add limitations of their own; a limitation that no handler evaluates denies access
([Content policy limitations of extensions](../../../features/6.0/content-limitation-handlers.md)).

## Checking what a role really gives

The role page writes each policy as a sentence, for example *May read content, in section Standard, of the classes
Article and Folder*, and counts every user the role reaches through its groups
([Roles and policies, 2](../../../guides/roles-and-policies.md#2-read-one-role)). Two badges deserve attention:

- **Full access** (red): a policy over every module.
- **Can change roles** (amber): a policy over the role module, which lets its users give themselves any access.

Neither belongs on a department editor role. Schedule a regular look at both badges
([Access reviews](../security/access-reviews.md)).

## Mistakes to avoid

- Giving `content/read` without a section limitation: staff could open user accounts and setup objects
  ([What visitors may see, Common mistakes](../../creators/website-creators/visitor-permissions.md#common-mistakes)).
- Creating one role per person instead of assigning a role to a group.
- Forgetting the new section in the Staff role: its pages then say "access denied" to everyone.
- Placing department editors in two groups whose roles add up to more than intended; policies of several roles add
  up.

## Checklist

- [ ] One section per area, each with an identifier.
- [ ] One user group per department.
- [ ] A Staff role with `content/read` for the staff sections, and `user/login` for the intranet siteaccess.
- [ ] One Editor role, assigned per department with a Section limitation.
- [ ] No department role shows Full access or Can change roles.
- [ ] Tested by signing in as a member of each department.

## Related pages

- [Sections](../../../guides/sections.md)
- [Roles, policies and unactivated users](../../../guides/roles-and-policies.md)
- [Sections and object states for site builders](../../creators/website-creators/sections-and-object-states.md)
- [Approval workflows](approval-workflows.md)
- [Customer and partner areas](../extranets/customer-and-partner-areas.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
