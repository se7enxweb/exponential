---
title: Roles, policies and limitations
audience: government, admin, project-manager
category: public-sector
order: 20
summary: How Exponential decides who may do what - roles made of policies, policies limited by section, subtree, class, owner, language, siteaccess or state, and roles assigned to groups - explained for public-sector web teams that must document and justify every access right.
keywords: roles, policies, limitations, permissions, access control, least privilege, user groups, role assignment, section limitation, subtree limitation, PreventPrivilegeEscalation, access rights, authorization
type: help
---
# Roles, policies and limitations

This page is for the administrator who sets up staff access and for the project manager who must explain it to an
auditor or an information security officer. It describes the permission model of Exponential as the guide
[Roles, policies and unactivated users](../../../../guides/roles-and-policies.md) and the module definitions in
`kernel/<module>/module.php` describe it, and adds the documentation habits public bodies usually need.

## Contents
1. [The model in four sentences](#the-model-in-four-sentences)
2. [Policies: module, function, limitations](#policies-module-function-limitations)
3. [Limitations you will use](#limitations-you-will-use)
4. [Assigning roles](#assigning-roles)
5. [Safety rails built in](#safety-rails-built-in)
6. [Documenting access for an audit](#documenting-access-for-an-audit)
7. [Related pages](#related-pages)

## The model in four sentences

- A **role** is a set of **policies**; a role does nothing until it is **assigned** to users or user groups.
- A **policy** lets its holders use one module, or one function of a module, possibly only within **limitations**.
- A user has every policy of every role assigned to them or to any group above them: policies only ever **add**
  access. There is no "deny" policy.
- Nobody gets anything that no role gives them, so the starting point of every design is "no access".

All role pages are under **Users > Roles and policies** (`/role/list`). Every change in the role editor is a draft
until you press **Save** ([the guide, section 3](../../../../guides/roles-and-policies.md#3-create-a-role-with-limited-policies)).

## Policies: module, function, limitations

A policy names a module (`content`, `user`, `setup`, `section`, `state`, `audit` ...) and either every function of it
or one function. The functions that matter most for staff, as defined in the kernel:

| Module | Functions (selection) |
|---|---|
| `content` | `read`, `create`, `edit`, `publish`, `remove`, `hide`, `move`, `manage_locations`, `translate`, `versionread`, `versionremove`, `diff`, `pdf`, `dashboard`, `pendinglist`, `restore`, `cleantrash`, `jobs`, `publish_without_notification` |
| `user` | `login`, `password`, `preferences`, `selfedit`, `register`, `activation`, `hygiene`, `deactivate` |
| `setup` | `administrate`, `logs`, `managelogs`, `unmasklogs`, `managecache`, `managecronjobs`, `system_info`, `setup`, `preload`, `install` |
| `section` | `view`, `edit`, `assign` |
| `state` | `administrate`, `assign` |
| `audit` | `read`, `manage` |

The `role` and `workflow` modules have no separate functions: a policy over them gives the whole module. A policy over
the `role` module lets its holder change roles, which the role list marks *Can change roles* in amber; a policy over
every module is *Full access*, marked in red.

One fact matters for approvals: in the edit form, whether a user may publish a draft is decided by `content/edit`
([Publish without notification](../../../../features/6.0/publish-without-notification.md)). Approval before
publishing is therefore enforced by a workflow, not by withholding a publish right; see
[Two-person rule for publishing](../editorial-workflows/two-person-rule-for-publishing.md).

## Limitations you will use

| Limitation | Offered by | Means |
|---|---|---|
| Section | most `content` functions, `section/assign`, `state/assign` | only objects in these sections |
| Subtree | most `content` functions | only objects at or below these locations |
| Node | most `content` functions | only these exact locations |
| Class | most `content` functions | only objects of these content classes |
| Owner / Group | `read`, `edit`, `remove` ... | only objects the user (or the user's group) owns |
| Language | `create`, `edit`, `publish`, `translate`, `hide` | only these translations |
| SiteAccess | `user/login` | may sign in only on these siteaccesses |
| `StateGroup_<group>` | `read`, `edit`, `publish`, `manage_locations`, `remove`, `state/assign` | only objects in these object states |
| NewState, NewSection | `state/assign`, `section/assign` | which states or sections the user may set |

Several limitations of one policy must all be met; several values of one limitation are alternatives
([guide, section 3](../../../../guides/roles-and-policies.md#3-create-a-role-with-limited-policies)). Sections are
explained on [Sections and subtree limits](sections-and-subtree-limits.md), states on
[Object states in permissions](object-states-in-permissions.md).

## Assigning roles

On a role's page, **Assign** gives the role to users or groups; **Assign with limitation** gives it only within one
subtree or one section. Prefer groups: a new member gets the role at once, and a leaver loses it when moved out of the
group. One role can be assigned several times with different limitations, for example the same "Department editor"
role to each department's group, limited to that department's subtree
([guide, section 5](../../../../guides/roles-and-policies.md#5-assign-the-role)).

## Safety rails built in

- **No escalation.** With `site.ini [RoleSettings] PreventPrivilegeEscalation=enabled` (the default), a user who may
  edit roles can grant only what their own roles cover; a broader policy is refused with *The policy was not added*
  or *The role was not saved* ([guide, "Who may do this"](../../../../guides/roles-and-policies.md#who-may-do-this)).
- **Everything is recorded.** Role assignments and permission changes are events of the `access` channel of the
  [audit trail](../../../../features/6.0/audit-trail.md), and granting an administrator role raises the alert rule
  `admin_role_granted` (`settings/audit.ini`).
- **Links follow permissions.** The administration hides links a user could not open
  ([admin links follow permissions](../../../../features/6.0/admin-links-follow-permissions.md)).

## Documenting access for an audit

Public bodies are usually asked to show who may do what and why. A practical record per role:

| Field | Example |
|---|---|
| Role name and purpose | "Department editor: edits and publishes the pages of one department" |
| Owner of the role | the web team lead (a function, not a person's name) |
| Policies | copied from the role page, which writes each policy as a sentence |
| Assigned to | user groups, with limitations |
| Review date | when the next [admin access review](admin-access-reviews.md) looks at it |

The role page shows the policies as sentences (*May read content, in section Standard, of the classes Article and
Folder*) and counts the users the role reaches, which makes it a good source for this record.

## Related pages
- [Least-privilege principles](least-privilege-principles.md)
- [Separation of duties](separation-of-duties.md)
- [Check what a role allows](../../tasks/people-and-permissions/check-what-a-role-allows.md)
- [Roles, policies and unactivated users](../../../../guides/roles-and-policies.md)
- [Role policy order](../../../../features/6.0/role-policy-order.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
