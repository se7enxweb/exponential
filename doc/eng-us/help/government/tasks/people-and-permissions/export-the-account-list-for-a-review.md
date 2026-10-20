---
title: Export the account list for a review
audience: government, admin, project-manager
category: public-sector
order: 290
summary: Step by step - export every account, or a filtered set, as CSV from Users > Check user accounts for an access review, with what the file contains, who may include e-mail addresses, and how to handle it as personal data.
keywords: export accounts, CSV, account list, access review, Check user accounts, user hygiene, recertification, audit evidence, personal data
type: help
---
# Export the account list for a review

This task is for administrators preparing an access review or an audit. At the end you have a CSV of the accounts
with their state, last visit, groups and roles. The export is part of **Users > Check user accounts**
([Check user accounts, "The CSV"](../../../../features/6.0/user-hygiene.md#the-csv)).

## Contents
1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [What the file contains](#what-the-file-contains)
4. [Handling the file](#handling-the-file)
5. [Related pages](#related-pages)

## Before you start

- `user/hygiene`. E-mail addresses are included only for users who also have `role/assign`.
- Decide the scope: all accounts, staff only (a user group), or a filter (not seen for N days, locked ...).

## Steps

1. Open **Users > Check user accounts** (`/user/hygiene`).
2. Choose the filter and, if needed, **User group** and a search.
3. Press **Export the list as CSV**. The whole filtered list is exported, every page, up to 100,000 accounts.
4. Save the file to your review folder.

The export is recorded in the audit trail as `data.export.csv` with the number of rows, the filter and whether
e-mail addresses were included, never the values.

## What the file contains

| Column | Content |
|---|---|
| User ID | the account's id |
| Name, Login | as on the page |
| E-mail | only with `role/assign` |
| Status | the states, comma separated (Disabled, Locked, Never signed in ...) |
| Last visit | `YYYY-MM-DD HH:MM UTC`, empty when never |
| Sign-ins | number |
| Groups, Roles | the groups and the roles (own and inherited) |

UTF-8 with a byte order mark, comma separated; cells that look like formulas get a leading apostrophe so a
spreadsheet shows them as text.

## Handling the file

The file holds personal data (names, logins, possibly e-mail addresses). Keep it only as long as the review needs,
in a place with restricted access, and do not mail it around; leave the e-mail column out (export as a user without
`role/assign`) when the review does not need it. The privacy measures of the platform are described in the
[Privacy standard](../../../../standards/privacy.md).

## Related pages
- [Audit who has admin rights](audit-who-has-admin-rights.md)
- [Find accounts not used for months](find-accounts-not-used-for-months.md)
- [Admin access reviews](../../operations/people-and-permissions/admin-access-reviews.md)
- [User account hygiene](../../operations/people-and-permissions/user-account-hygiene.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
