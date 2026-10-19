---
title: Roles and least privilege
audience: government, admin, project-manager
category: public-sector
order: 210
summary: For public bodies that must apply least privilege to website staff - how Exponential's roles, policies and limitations work, which policies are security-sensitive, a sample role design for a public body, leavers, and periodic access reviews with Check user accounts.
keywords: roles, policies, least privilege, limitations, section, subtree, Administrator role, audit/read, setup/setup, user/hygiene, access review, leavers, Deactivate and move, one account per person, public sector
type: help
---
# Roles and least privilege

This page is for administrators and project managers who decide who may do what on a public site. In Exponential
access is decided by **roles** made of **policies** (module, function, limitations), assigned to users or groups;
nobody gets anything no role gives ([Roles, policies and unactivated users](../../../guides/roles-and-policies.md),
[Security and audit, 2](../../../guides/security-and-audit.md#2-give-people-only-the-access-they-need-10-minutes)).

## Contents

1. [Rules of thumb](#rules-of-thumb)
2. [Security-sensitive policies](#security-sensitive-policies)
3. [A sample role design](#a-sample-role-design)
4. [Leavers and movers](#leavers-and-movers)
5. [Access reviews](#access-reviews)
6. [Related pages](#related-pages)

## Rules of thumb

- One personal account per person; retire the shared `admin` login ([Account hygiene](../../change-the-admin-password.md#account-hygiene)).
- Never edit the Administrator role to give a colleague more; make a new role.
- Keep few administrators: granting a role with `*/*`, `setup/*`, `role/*` or `audit/manage` raises the alert
  `admin_role_granted`.
- Limit by **section** or **subtree** so a department edits only its own content ([Sections](../../../guides/sections.md)).
- An edit policy also opens the object's versions; limit it as you limit the read policy.

## Security-sensitive policies

From [13.5 The administration siteaccess](../../../install/13-security-hardening.md#135-the-administration-siteaccess) and
the feature pages:

| Policy | Allows |
|---|---|
| `setup/setup` | settings views and writes |
| `setup/managecache` | cache clearing |
| `setup/logs`, `setup/managelogs`, `setup/unmasklogs` | reading, rotating, unmasking logs |
| `audit/read`, `audit/manage` | the audit trail; archives and verification |
| `role/*` | creating and assigning roles |
| `user/hygiene`, `user/deactivate` | account review, disabling, Deactivate and move |
| `mailpreferences/administrate`, `mailpreferences/export` | people's mail preferences and consent log |
| `infocollector/read` | form submissions |
| `contentfinder/search`, `contentfinder/delete` | finding content by id or path and deleting it |
| `package/*` | uploading and installing packages |

## A sample role design

| Role | Policies (limited) | Assigned to |
|---|---|---|
| Department editor | `content/read`, `content/create`, `content/edit` limited to the department's section or subtree | each department's editors group |
| Publisher | as editor, plus approval in the workflow | senior editors |
| Web administrator | content everywhere, `setup/managecache`, `user/hygiene` | web team |
| Auditor | `audit/read` limited by `Channel` | internal audit, security officer |
| System administrator | Administrator | two or three named people |

## Leavers and movers

- Disable the account on the day someone leaves (Users > Check user accounts, **Disable**); its sessions end.
- With `[UserSettings] DeactivateAndMove=enabled` (opt-in), **Deactivate and move** disables the account and moves it
  into a hidden group without roles, reversibly, keeping its content and history
  ([Deactivate and move](../../../features/6.0/user-hygiene.md#deactivate-and-move)). Removing a user object is final:
  users never go to the trash.
- Movers: change group membership, not the roles of the group.

## Access reviews

Twice a year, or as your policy says:

1. **Users > Check user accounts**: filters **Never signed in**, **Not seen for N days** (180 by default), **Shared
   login**, **Shared e-mail address**; **Export the list as CSV** for the review record.
2. Roles: open each role (Users > Roles and policies, `/role/list`) and check its assignments.
3. Audit: `/audit/console/(name)/access.role.*` lists role changes since the last review.
4. File the CSV and the decisions.

## Related pages

- [Roles, policies and unactivated users](../../../guides/roles-and-policies.md)
- [Check user accounts](../../../features/6.0/user-hygiene.md)
- [Roles and policies course](../../trainers/site-administrators/module-03-roles-and-policies/README.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
