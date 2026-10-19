---
title: Give contractors extranet access
audience: government, admin
category: public-sector
order: 190
summary: Task for administrators: give one contractor's staff access to their own project area only, with a user group, a section, a limited role assignment and personal accounts, and record the end date.
keywords: extranet access, contractor, user group, section, role assignment limitation, external accounts, end date
type: help
---
# Give contractors extranet access

This task is for administrators. Background: [User groups and sections for extranets](../../building/extranets/user-groups-and-sections-for-extranets.md)
and [Giving a contractor access](../../building/extranets/giving-a-contractor-access.md).

## Before you start

A written request naming the contractor, the people, the project area and the end date.

## Steps

1. **Users**: under an "External" group, create a user group for the contractor.
2. **Setup > Sections**: create a section for the project area; **Assign content** to its folder.
3. **Users > Roles and policies**: assign the role "Extranet reader" (`content/read`, `user/login`) to the contractor's
   group, limited to the project's section.
4. Create one account per person in the group (**Create new**, user class); let each set a password with **Forgot
   password**.
5. Record people and end date in your access register: Exponential has no account expiry date.

## Check

A test account in the group opens the project area and nothing else; signed out, the area does not open.

## Problems

| Problem | Fix |
|---|---|
| The contractor sees other projects | the role was assigned without the section limitation, or another role gives more |
| A person cannot download files | the files are outside the project's section |

## Related pages

- [Review and disable stale contractor accounts](review-and-disable-stale-contractor-accounts.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
