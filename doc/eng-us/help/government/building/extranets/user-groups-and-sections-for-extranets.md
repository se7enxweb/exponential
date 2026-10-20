---
title: User groups and sections for extranets
audience: government, admin
category: public-sector
order: 30
summary: How to structure an extranet in Exponential so each contractor or partner sees only its own content: one user group and one section (or subtree) per party, a role assigned with a limitation, and tests from each side.
keywords: user groups, sections, extranet structure, role assignment limitation, subtree, contractors, partners, separation, content/read
type: help
---
# User groups and sections for extranets

This page is for administrators. Separation between external parties rests on user groups, sections and role
assignments ([Sections](../../../../guides/sections.md), [Roles and policies](../../../../guides/roles-and-policies.md)).

## Contents

1. [The structure](#the-structure)
2. [One role, assigned with a limitation](#one-role-assigned-with-a-limitation)
3. [Shared material for all parties](#shared-material-for-all-parties)
4. [Tests](#tests)

## The structure

```
Users
  External
    Contractor A (user group)
    Contractor B (user group)
Content
  Extranet (folder)
    Project X - Contractor A (section: Extranet A)
    Project Y - Contractor B (section: Extranet B)
    For all partners (section: Extranet shared)
```

Create the sections in **Setup > Sections** and use **Assign content** on each folder.

## One role, assigned with a limitation

Create one role "Extranet reader" with `content/read` and `user/login` (for the extranet siteaccess). Assign it to each
group with a limitation to that party's section or subtree: Contractor A's group gets it limited to section
"Extranet A" (and "Extranet shared"). One role, many limited assignments, is easier to review than a role per party.

If a party may upload, add `content/create` for the File class in its own subtree, in a separate role with the same
limitation.

## Shared material for all parties

A section read by every external group holds general information (rules, templates, contacts).

## Tests

Use a test account in each group: Contractor A's account opens Project X and not Project Y; signed out, neither opens.
Refused views are recorded as `access.permission.refused` in the audit trail.

## Related pages

- [Giving a contractor access](giving-a-contractor-access.md)
- [Sections and roles for intranet content](../intranets/sections-and-roles-for-intranet-content.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
