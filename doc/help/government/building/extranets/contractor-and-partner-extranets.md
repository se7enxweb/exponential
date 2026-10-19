---
title: Contractor and partner extranets
audience: government, project-manager, admin
category: public-sector
order: 20
summary: How a public body gives contractors, partner organizations or other agencies access to selected content with Exponential: separate user groups and sections, an extranet siteaccess or area, protected files, and the gaps (no account expiry date, no external identity federation) with what to do instead.
keywords: extranet, contractors, partners, external users, user groups, sections, protected area, account expiry, offboarding, collaboration
type: help
---
# Contractor and partner extranets

This page is for managers and administrators who share information with people outside the organization: contractors
on a project, partner organizations, other agencies. In Exponential an extranet is a protected area or siteaccess, a
user group per external party, and sections that only those groups may read.

## Contents

1. [The building blocks](#the-building-blocks)
2. [Area or siteaccess](#area-or-siteaccess)
3. [What is missing and what to do](#what-is-missing-and-what-to-do)

## The building blocks

| Need | Exponential | Page |
|---|---|---|
| Separate parties | one user group per contractor or partner | "User groups and sections for extranets" |
| Content only they see | a section per party, read only by its group | same |
| Accounts | created by an administrator, never by self-registration | "Giving a contractor access" |
| Documents | files in the protected sections, downloaded through `content/download` | "Sharing files with contractors" |
| End of access | disable, or deactivate and move | "Ending access" |
| Who did what | the audit trail | "Contractor activity and the audit trail" |

## Area or siteaccess

| Option | When |
|---|---|
| A protected area on the public site (members-only folder) | a few documents for one partner |
| A separate extranet siteaccess with `RequireUserLogin=true` | many parties, its own design and address |

Both are set up like intranets (see the [intranets section](../intranets/README.md)).

## What is missing and what to do

- **No account expiry date.** Exponential has no field that ends an account on a date. Record the end date in your
  contract register, and disable accounts on time; **Users > Check user accounts** finds accounts not seen for N days
  (see "Reviewing stale accounts").
- **No federation with partners' identity systems** beyond the login handlers available (see "Sign-in options for
  staff" in the intranets section).
- **No per-document access log by default**: downloads are recorded in the audit trail only when reads are switched on.

## Related pages

- [Member registration and members-only areas](../../../creators/website-creators/member-registration-and-areas.md)
- [Check user accounts](../../../../features/6.0/user-hygiene.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
