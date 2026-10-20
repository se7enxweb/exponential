---
title: How do I give a group edit rights on one section only?
audience: admin
category: manual
order: 500
summary: Create a role with content/read, content/create and content/edit policies, then assign it to the user group with the limitation Section, so the rights apply only to the content of that section.
keywords: roles, policies, permissions, section, assign with limitation, user group, edit rights, content/edit, role/assign
type: help
---
# How do I give a group edit rights on one section only?

Rights come from roles: a role is a list of policies (what may be done), and assigning it to a user group with a
limitation decides where. Assigning with the limitation **Section** keeps the rights inside one section.

## Who can do this

Users whose role has `role/*` (create, edit and assign roles), usually administrators only.

## Steps

1. Open **Users** in the top menu and choose **Roles and policies**.
2. Click **New role**, give it a name (for example "News editors") and save.
3. Click **New policy** and add, one by one:
   - module **content**, function **read**;
   - module **content**, function **create**;
   - module **content**, function **edit**;
   - module **content**, function **versionread**, so the group can see drafts and versions.

   For each, choose **Grant full access** (the section limit comes from the assignment).
4. Click **Save**.
5. On the role's page, click **Assign with limitation**, choose **Section**, choose the section (for example
   "News") and click **Select**.
6. Choose the user group and confirm.
7. Sign in as a test member of the group and check that they can edit in the section and nowhere else.

## What can go wrong

- **The group still cannot edit.** They also need to read the place where they work and, for new content, the classes
  they create. Add `content/read` for the tree above, or limit `content/create` by **Class** in the policy.
- **The group can edit too much.** Another role of theirs gives wider rights. Rights add up: check every role assigned
  to the user and the groups above them.
- **Changes do not show at once.** Rights are cached per user. They apply at the next sign-in, or after the user cache
  is cleared.

## Related pages

- [Roles and policies](../../guides/roles-and-policies.md)
- [Sections](../../guides/sections.md)
- [Roles for your team](../customers/day-to-day/roles-for-your-team.md)
- [Choosing roles for new staff](../customers/day-to-day/choosing-roles-for-new-staff.md)
- [User manual](README.md)
