---
title: "Roles and policies, step by step"
audience: admin, builder, developer
category: books
order: 240
summary: How the role system decides who may do what: a role is a set of policies (module, function, limitation), assigned to users and groups, with the functions of the content module and what changed since the book.
keywords: role, policy, module, function, limitation, assignment, permission, access, anonymous, content read, content create, Learning book
type: help
---
# Roles and policies, step by step

The permission system is built on **roles**. That differs from a file system, where permissions sit on single files and
folders. The role system can still reach that fine a grain, down to one node or one node and its children, and goes
further: it also decides which **functions of the modules** a user may use. A normal user may be allowed to add comments
to articles but not to submit articles.

## The parts

- A **role** is a named set of policies.
- A **policy** names a **module**, a **function** of it, and optional **limitations**. With no limitation the policy
  applies to everything the function does. The administrator's policy is "all modules, all functions".
- A role is **assigned** to a user or a user group, for the whole site or limited to a section or a subtree.
- A user's rights are the policies of all roles of the user and of the groups above the user. When any policy allows an
  action, it is allowed.

## The content module's functions

The module a builder uses most is `content`. The functions and what they can be limited by (the book listed seven;
today there are more):

| Function | Lets a user | Limited by |
|---|---|---|
| `read` | see content | class, section, owner, node, subtree, state |
| `create` | create content under a place | class, section, parent class, parent depth, node, subtree, language |
| `edit` | change content | class, section, owner, node, subtree, state, language |
| `publish` | publish a draft | class, section, owner, node, subtree, state |
| `remove` | remove content | class, section, owner, node, subtree, state |
| `translate` | add translations | class, section, owner, node, subtree |
| `versionread`, `versionremove` | see or remove old versions | class, section, owner, status, node, subtree |
| `manage_locations`, `hide` | add and remove places; hide and show | class, section, node, subtree |
| `bookmark`, `pendinglist`, `urltranslator`, `cleantrash`, `restore` | use bookmarks; see pending items; manage URL aliases; empty and restore trash | none |

"Owner" refines *self* against *any*. The status for `versionread` is draft, published, pending, rejected or archived.
The limitation lists are read from `kernel/content/module.php`; the administration shows exactly the limitations the
installation offers when you add a policy.

Other modules have their own functions, for example `class` (the class editor), `notification` (use, administrate),
`package`, `role`, `setup`, `shop`, `user` (login, register, password), `workflow`, `trigger`, `state`, `section`,
`url`, `search`, `ezinfo`, `layout` (change the page layout, such as a print layout), `rss`. A kernel module's access can
also be switched off outside the role system with settings, as the book said.

## Do it

The steps are in [Roles, policies and unactivated users](../../guides/roles-and-policies.md): read the role list, create
a role with limited policies, order the policies, assign the role. Rules of thumb that the book also gives:

1. Start from what each group must do, not from what each person is.
2. Give the **Anonymous** role as little as possible (read of public content, and perhaps register).
3. Prefer a section or subtree limit to a class list when a group works in one area.
4. After changing a role, log in as a member of the group (a test account) and try the work.

See also [Sections, roles and access for builders](../extension-development/part-2-builder/sections-roles-and-access-for-builders.md)
and, in the example site, [users, groups and roles](users-groups-and-roles-example.md).

Next: [creating a content class](creating-a-content-class.md).

## Source and license

Adapted from chapter 2, "Content Management with eZ publish", of *Learning eZ publish: Building Content Management Solutions* by Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding and Ben Pirt (additional material by Bruce Morrison), second edition, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x and Graham
Brookins. Licensed under the GNU Free Documentation License, version 1.3 or any later version, with no Invariant
Sections, no Front-Cover Texts and no Back-Cover Texts ([the license](../../LICENSE-GFDL-1.3.txt)).
Changes: rearranged and shortened; settings, labels, datatypes, tags and URLs checked against Exponential 6 and corrected; links to the current guides added.
