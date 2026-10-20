---
title: Editors and roles per department
audience: government, admin
category: public-sector
order: 80
summary: How to let each department's editors work only on their own site in a shared Exponential installation: roles limited by subtree or section, one or several administration siteaccesses, shared media, and review of access.
keywords: roles per department, subtree limitation, section limitation, editors, departments, administration siteaccess, permissions, multisite, separation, access review
type: help
---
# Editors and roles per department

This page is for administrators. In a shared installation, one administration can serve every site; roles limited by
subtree keep each team in its site's folder
([Setting up several sites, Editors and the administration](../../../creators/website-creators/multisite-setup.md#editors-and-the-administration),
[Roles and policies](../../../../guides/roles-and-policies.md)).

## Contents

1. [A role per department](#a-role-per-department)
2. [Subtree or section](#subtree-or-section)
3. [Shared media](#shared-media)
4. [Several administrations](#several-administrations)
5. [Reviewing access](#reviewing-access)

## A role per department

| Policy | Limitation |
|---|---|
| `content/read` | Subtree: the department's folder, the media folder, shared folders to read |
| `content/create`, `content/edit` | Subtree: the department's folder |
| `content/versionread` | Subtree: the department's folder |
| `user/login` | the administration siteaccess |

Assign the role to the department's editor group; or use one role and limit the assignment by subtree when assigning
it to each group.

## Subtree or section

| Limit by | Good for |
|---|---|
| Subtree | a department's folder; follows the tree |
| Section | content spread over the tree that belongs together; also controls what visitors see |

## Shared media

Give each department a subfolder in the media library with create rights, and read rights on the whole library, so
images can be reused but not changed by others.

## Several administrations

When teams should not see each other's sites at all, several administration siteaccesses are possible, each with its
own settings; access is still decided by roles, so limit them as well.

## Reviewing access

- Twice a year, list role assignments per group (**Users > Roles and policies**).
- Use **Users > Check user accounts** to find accounts not used for a long time ([Check user accounts](../../../../features/6.0/user-hygiene.md)).
- Role assignments are recorded in the audit trail's `access` channel ([Audit trail](../../../../features/6.0/audit-trail.md)).

## Related pages

- [Roles and policies](../../../../guides/roles-and-policies.md)
- [Sections](../../../../guides/sections.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
