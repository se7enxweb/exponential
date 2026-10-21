---
title: Sections, roles and policies
audience: cms-developer
category: cms-development
order: 19
summary: For CMS developers, how access works in Exponential. It covers roles made of policies, limitations by class, section, subtree, owner, language and state, role assignment, sections and object states as the levers, the Anonymous role, checking access in templates and from the shell, and the usual designs.
keywords: roles, policies, permissions, access control, limitations, section, subtree, class, owner, language, object state, content/read, content/create, content/edit, Anonymous role, role assignment, can_edit, has_policy, accesscheck, members area
type: help
---
# Sections, roles and policies

This page is for developers who decide who may read and change what. Exponential's access control is part of the
content model: rules are written against classes, sections, subtrees and states, so a good model makes the rules
short, and the rules are checked by every fetch, view and API call without your code doing anything.

## Contents

1. [Roles, policies and limitations](#roles-policies-and-limitations)
2. [The content policies you use most](#the-content-policies-you-use-most)
3. [Limitations](#limitations)
4. [Assigning roles](#assigning-roles)
5. [Sections: the main lever](#sections-the-main-lever)
6. [Object states: stages with their own rules](#object-states-stages-with-their-own-rules)
7. [Access in templates](#access-in-templates)
8. [Checking and debugging access](#checking-and-debugging-access)
9. [Designs that work](#designs-that-work)

## Roles, policies and limitations

| Piece | Is | Example |
|---|---|---|
| Policy | permission for one module and one function (or all) | `content/read`, `content/create`, `user/login` |
| Limitation | a condition on a policy | Class = Article; Section = Members |
| Role | a named set of policies | *Editor*, *Member*, *Anonymous* |
| Assignment | a role given to a user or a user group, optionally limited to a subtree or a section | *Editor* to the group *News team*, on the subtree *News* |

Permissions only add up: there is no "deny" policy. A user may do something when any of their roles has a policy
that allows it. Roles are managed under **Users** > **Roles and policies**
([Roles, policies and unactivated users](../../guides/roles-and-policies.md)).

## The content policies you use most

The functions of the `content` module (`kernel/content/module.php`) include:

| Function | Allows |
|---|---|
| `read` | seeing content: views, fetches, search results, the REST interface |
| `create` | creating objects of a class below a parent |
| `edit` | creating new versions of existing objects |
| `publish` | publishing a draft |
| `remove`, `move`, `hide`, `manage_locations` | changing the tree |
| `translate` | working on translations |
| `versionread`, `versionremove`, `diff` | reading and removing old versions, comparing them |
| `view_embed` | showing an object embedded in other content without full read access |
| `pdf`, `bookmark`, `dashboard`, `restore`, `cleantrash`, `jobs` | the named features |

Other modules have their own functions: `user/login` (signing in to a siteaccess), `user/selfedit`, `setup/*`,
`apikey/create` and so on. Your own modules declare theirs
([Policy functions and access](../extension-development/part-3-extension-developer/policy-functions-and-access.md)).

## Limitations

The limitations of content functions are `Class`, `Section`, `Owner`, `Group`, `Node`, `Subtree`, `Language`, the
parent-side `ParentClass`, `ParentOwner`, `ParentGroup`, `Status` for versions, and one limitation per object state
group. Within a policy all limitations must match; across policies any one may allow.

| Need | Policy |
|---|---|
| Editors create articles in News only | `content/create` with Class = Article and Subtree = News (or Node = the News folder) |
| Authors change only their own items | `content/edit` with Owner = self |
| A translator works only in German | `content/edit` and `content/translate` with Language = German |
| Reviewers edit only items in review | `content/edit` with the state limitation of your review group = In review |

## Assigning roles

Assign roles to **user groups**, not to single users, so a new team member gets the right access by being placed in
the group. An assignment can itself be limited to a subtree or a section, so one *Editor* role serves several
departments, each assigned to its own subtree. Users are content in groups:
[Users and registration](users-and-registration.md).

## Sections: the main lever

A section is one label per object. A new installation has five (Standard, Users, Media, Setup, Design), and the
**Anonymous** role reads only Standard: that single limitation keeps user accounts and other sections from visitors
([Sections, section 2](../../guides/sections.md#2-the-sections-of-a-new-installation)). For a members area, create a
section *Members*, assign the subtree to it, and give a *Member* role `content/read` limited to that section. New
content takes the section of its parent. Sections also select templates (`Match[section]` in override rules) and
navigation parts in the administration ([Sections](../../guides/sections.md)).

## Object states: stages with their own rules

Object states are labels of your own in groups (for example *Review*: draft, in review, approved). Policies can be
limited by state, and states change by hand, from workflows or from code. Use them for editorial stages that need
different rights; use sections for areas of the site ([Object states](../../guides/object-states.md)).

## Access in templates

Fetches and views already leave out what the user may not read. For buttons and links, ask the node:

```smarty
{if $node.can_edit}
    <a href={concat( 'content/edit/', $node.contentobject_id )|ezurl}>{'Edit'|i18n( 'extension/exp_mysite' )}</a>
{/if}
```

Nodes answer `can_read`, `can_create`, `can_edit`, `can_hide`, `can_remove`, `can_move` and more
([Node attributes reference](../template-language/node-attributes-reference.md)). The operators `has_role`,
`has_policy` and their variants ask about the user instead
([Role and policy template operators](../../features/6.0/role-and-policy-template-operators.md)). Never use them to
hide content that the user may read anyway: that is design, not security.

## Checking and debugging access

```bash
./console exp:accesscheck --help
```

answers what a user may do with a node or object and which limitation refuses it
([Sections, roles and access for builders](../extension-development/part-2-builder/sections-roles-and-access-for-builders.md#checking-a-users-access)).
Test with a dedicated test account (address at `example.invalid`), never by trying things as an administrator.

## Designs that work

- **Few roles, many assignments.** One *Editor* role assigned per subtree beats one role per department.
- **Sections for audiences, states for stages, classes for kinds of content.** Do not encode one in another.
- **Least privilege.** Start from nothing and add; an import user gets exactly the create and edit rights it needs.
- **Keep the Anonymous role small.** `content/read` on Standard, `user/login` on the public siteaccess, and only
  what the site's forms need.

## Related pages

- [Sections](../../guides/sections.md): every page of the section administration
- [Security checklist](security-checklist.md)
- [eZUser and access](../extension-development/part-8-reference/ezuser-and-access.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
