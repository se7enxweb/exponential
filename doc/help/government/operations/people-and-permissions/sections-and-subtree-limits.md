---
title: Sections and subtree limits
audience: government, admin
category: public-sector
order: 30
summary: When to limit staff access by section and when by subtree of the content tree, how each behaves when content moves, and how a public body with many departments, restricted areas and shared pages combines the two.
keywords: sections, subtree, subtree limitation, section limitation, departments, restricted content, intranet, Anonymous role, Assign with limitation, section/assign, NewSection, content tree
type: help
---
# Sections and subtree limits

This page is for administrators who decide how staff access follows the structure of a public website: one subtree
per department, a section for restricted content, shared pages that several teams edit. It summarizes the two
mechanisms as [Sections](../../../../guides/sections.md) and
[Roles, policies and unactivated users](../../../../guides/roles-and-policies.md) describe them, and gives a decision
guide.

## Contents
1. [Two ways to draw a boundary](#two-ways-to-draw-a-boundary)
2. [What happens when content moves](#what-happens-when-content-moves)
3. [Which to use](#which-to-use)
4. [A typical public-sector layout](#a-typical-public-sector-layout)
5. [Pitfalls](#pitfalls)
6. [Related pages](#related-pages)

## Two ways to draw a boundary

| | Section | Subtree |
|---|---|---|
| What it is | A named group of objects; every object is in exactly one section | A location in the content tree and everything below it |
| Where it is managed | **Setup > Sections** (`/section/list`) | the content tree itself |
| Used in policies | the `Section` limitation of most `content` functions | the `Subtree` limitation of most `content` functions |
| Used in assignments | **Assign with limitation: Section** | **Assign with limitation: Subtree** |
| Follows an object with two locations | yes, one section for the object | only the location inside the subtree |
| Used elsewhere | visitor access (the Anonymous role reads Section Standard only), template overrides by section, the admin menu tab | URLs and navigation |

The default installation has the sections Standard (1), Users (2), Media (3), Setup (4) and Design (5), and the
Anonymous role may read Standard only ([Sections, section 2](../../../../guides/sections.md#2-the-sections-of-a-new-installation)).
That one rule is why content put into a new section is invisible to visitors until a policy allows it.

## What happens when content moves

- **New content takes the section of its parent.**
- **Moving a subtree** gives the new parent's section to every object below that still had the old parent's section;
  objects that had a section of their own keep it.
- **Assign content** on a section puts an item and everything below it into that section; large subtrees can run as
  a content job ([Sections, section 8](../../../../guides/sections.md#8-which-section-content-is-in-creating-moving-and-assigning)).
- A **subtree limitation** follows the tree: an object moved out of the subtree is out of the policy at once.

## Which to use

| You need | Use |
|---|---|
| Each department edits its own pages, which sit together in the tree | a subtree limitation per department (one role, assigned per department with **Assign with limitation: Subtree**) |
| Content that must stay away from visitors wherever it sits (internal drafts, restricted documents) | a section the Anonymous role does not read |
| The same editor role for several areas | one role, several limited assignments |
| A team that edits one content type everywhere (for example news items) | a `Class` limitation, combined with a subtree or section |
| A public area and a members area under one folder | sections, because one folder can hold objects of several sections |

When unsure, use subtrees for "who edits where" and sections for "who may see what". That keeps visitor access and
staff access in different places, which is easier to explain to an auditor.

## A typical public-sector layout

An example layout for "the Department of Example" site (placeholders, adapt to your own structure):

| Tree | Section | Who edits | How |
|---|---|---|---|
| `/` home, about, contact | Standard | web team | role "Web team" without limitation on content |
| `/services/<department>/` | Standard | department editors | role "Department editor", assigned per department group with a Subtree limitation |
| `/news/` | Standard | communications | role "Department editor", Subtree `/news/` |
| `/internal/` | a new section "Internal" | web team | visitors do not read it, because Anonymous reads Standard only |
| Media library | Media | all editors (upload) | `content/create` limited to the media subtree and image and file classes |

The role designs on [Department editors](role-design-department-editors.md) and
[Web team](role-design-web-team.md) build exactly these policies.

## Pitfalls

- **A new section is invisible to visitors.** If you create a section for a new public area, give the Anonymous role
  `content/read` for it too ([Let visitors read a new section](../../tasks/people-and-permissions/let-visitors-read-a-new-section.md)).
- **Removing a section** is possible only when no object, policy or role assignment uses it; there is no undo, and a
  new section with the same name gets a new ID ([Sections, section 7](../../../../guides/sections.md#7-removing-a-section)).
- **Section identifiers** are used by templates; renaming one can break a design
  ([Sections, section 6](../../../../guides/sections.md#6-editing-a-section-and-what-a-change-affects)).
- **Who may put content into a section** is itself a right: `section/assign` with `NewSection`. Without that
  limitation, every section may be assigned ([Sections, section 9.3](../../../../guides/sections.md#93-who-may-assign-sections-sectionassign)).
- **Secondary locations** of an object outside a department's subtree are not covered by that department's subtree
  limitation.

## Related pages
- [Roles, policies and limitations](roles-policies-and-limitations.md)
- [Create a section for restricted content](../../tasks/people-and-permissions/create-a-section-for-restricted-content.md)
- [Assign a role with a subtree limitation](../../tasks/people-and-permissions/assign-a-role-with-a-subtree-limitation.md)
- [Assign a role with a section limitation](../../tasks/people-and-permissions/assign-a-role-with-a-section-limitation.md)
- [Sections guide](../../../../guides/sections.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
