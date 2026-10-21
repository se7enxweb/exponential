---
title: "Users, groups and roles in the example"
audience: admin, builder
category: books
order: 330
summary: Sections, user groups and role definitions for the club portal, done step by step, with the settings (default user group, policies that skip the check) and what to check today.
keywords: sections, user groups, roles, anonymous, visitors, members, management, DefaultUserPlacement, PolicyOmitList, Learning book
type: help
---
# Users, groups and roles in the example

This continues [planning an example site](planning-an-example-site.md). Permissions are easier to implement when sections
carry them.

## Sections

For the club portal the book creates four, assigns each to the top node of its branch (new children inherit it), and
then checks the result:

| Section | Holds | Who may what |
|---|---|---|
| Restricted | the management's area | management only |
| Forums | the discussion forums | everyone reads; members post |
| Members | member details | members read each other; not the public |
| Visitors | Home, Documents, Calendar, Forum | everyone, including anonymous |

See [Sections](../../guides/sections.md) for creating them and **Assign content**.

## User groups

There are four kinds of user, and each gets a group: **anonymous** (not logged in; the Anonymous user), **visitors**
(registered, not members), **members** (assigned by the management) and **club management**. A newly registered user
is put in the default group. That group is a setting, `site.ini [UserSettings] DefaultUserPlacement`, the node ID of the
group:

```ini
[UserSettings]
DefaultUserPlacement=12
```

The shipped value is a node ID of the default installation; change it in
`settings/override/` and set it to the node ID of your visitors group.

## Roles

Each group gets a role:

- **Anonymous:** `content/read` limited to the Visitors section, and the right to log in. The log-in right can also be
  given outside the role system: `site.ini [RoleSettings] PolicyOmitList[]=user/login` (the shipped setting also skips the check for
  `user/logout` and `user/register`).
- **Visitors:** the same as anonymous, plus bookmarks and notifications.
- **Members:** read of Members and Forums; create forum messages and topics; submit documents and images, which the
  management reviews before they are published ([workflows](workflows-and-triggers.md)).
- **Management:** read, create, edit and remove in all sections, and the restricted one.

[Roles and policies, step by step](roles-and-policies-step-by-step.md) explains the policy form. After setting up, log in
as a user of each group, and walk through what that user should and should not be able to do.

Next: the templates. [Template principles](template-principles.md) opens the next chapter.

## Source and license

Adapted from chapter 2, "Content Management with eZ publish", of *Learning eZ publish: Building Content Management Solutions* by Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding and Ben Pirt (additional material by Bruce Morrison), second edition, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x and Graham
Brookins. Licensed under the GNU Free Documentation License, version 1.3 or any later version, with no Invariant
Sections, no Front-Cover Texts and no Back-Cover Texts ([the license](../../LICENSE-GFDL-1.3.txt)).
Changes: rearranged and shortened; settings, labels, datatypes, tags and URLs checked against Exponential 6 and corrected; links to the current guides added.
