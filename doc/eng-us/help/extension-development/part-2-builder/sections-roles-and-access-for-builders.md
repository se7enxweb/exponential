---
title: Sections, roles and access for builders
audience: developer, designer, app-creator, admin
category: extension-development
order: 180
summary: Who may see and do what - sections, object states, roles, policies, limitations and assignments - explained for builders, with a members-only area as a worked plan and the template operators that ask about access.
keywords: sections, roles, policies, limitations, assignments, access, anonymous, members area, object states, content/read, can_edit, has_access_to, permissions
type: help
---
# Sections, roles and access for builders

Part 2, chapter 18. Access in Exponential is configured, not programmed: **roles** hold **policies**, policies can be
**limited** (by section, class, subtree, state, language, siteaccess), and roles are **assigned** to users and groups.
A builder can create a members-only area, an editors' workflow or a private intranet with these tools alone. The
reference pages are [Roles and policies](../../../guides/roles-and-policies.md) and [Sections](../../../guides/sections.md).

## The pieces

| Piece | What it is |
|---|---|
| Section | a label every object carries (Standard, Users, Media, Setup, Design in a new installation); used to limit policies and to pick designs |
| Object state | a second, independent label from a state group (for example locked or not locked) |
| Policy | permission to use a module, or one function of it (`content/read`, `content/edit`, `user/login`) |
| Limitation | narrows a policy: only Section Standard, only class Article, only below one node, only in one state |
| Role | a named set of policies |
| Assignment | a role given to a user or a user group, optionally limited to a section or a subtree |

A user has every policy of every role assigned to them or to one of their groups. Policies only add; there is no
"deny".

## Why visitors do not see everything

The default Anonymous role has `content/read` limited to **Section Standard**. Everything in other sections (user
accounts, media folders, setup objects) is invisible to visitors who are not signed in. That single limitation is the
model for every private area you build.

## Worked plan: a members-only area

1. **Create a section** `members` (Setup > Sections): name "Members", identifier `members`.
2. **Assign the section** to the subtree of the members' area (on the section page, assign to a node; the objects
   below get the section).
3. **Create a role** "Members read" with one policy: module `content`, function `read`, limitation Section =
   Members.
4. **Assign the role** to the user group "Members".
5. Check: signed out, the area's pages answer "access denied"; signed in as a member, they show.
6. Optional: give the area its own look with overrides matching `Match[section_identifier]=members`.

No code was written, and the area is secure, because fetches and views apply the same policies: a list of the area's
articles is empty for anonymous visitors.

## Asking about access in templates

| Template | Answers |
|---|---|
| `$node.can_edit`, `$node.can_create`, `$node.can_remove`, `$node.can_read` | may the current user do this to this node |
| `fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'edit' ) )` | may the current user use this function anywhere |
| `fetch( 'user', 'current_user' )` | the current user, with `.is_logged_in` |

```text
{if $node.can_edit}
    <a href={concat( '/content/edit/', $node.contentobject_id )|ezurl}>Edit</a>
{/if}
```

Hiding a link is a courtesy; the policy is what protects. Never rely on a template condition alone for security.
Role and policy operators added in 6.0 are described in
[Role and policy template operators](../../../features/6.0/role-and-policy-template-operators.md).

## Checking a user's access

```bash
./console exp:accesscheck --help
```

The command answers what a user may do with a node or object and which limitation refuses it, which is the quickest
way to understand an "access denied".

## Rules

- Give roles to **groups**, not to single users.
- Keep a policy on every module (`*`) for administrators only; the role list marks such roles in red.
- A policy on the `role` module lets its users give themselves anything: treat it as administrator access.
- New extension modules declare their own policy functions; grant them deliberately
  ([Policy functions and access checks](../part-3-extension-developer/policy-functions-and-access.md)).

## Related pages

- Previous: [Content classes for builders](content-classes-for-builders.md)
- Next: [Workflows for builders](workflows-for-builders.md)
- [Roles and policies](../../../guides/roles-and-policies.md), [Sections](../../../guides/sections.md), [Object states](../../../guides/object-states.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
