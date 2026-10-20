---
title: Reviewing stale accounts
audience: government, admin
category: public-sector
order: 60
summary: How to find and disable external accounts nobody uses any more with Users > Check user accounts (Never signed in, Not seen for N days, filtered to the external groups), export the list for the project managers, and act on it.
keywords: stale accounts, user hygiene, Check user accounts, user/hygiene, Not seen for N days, Never signed in, disable accounts, CSV export, access review, contractors
type: help
---
# Reviewing stale accounts

This page is for administrators who review external access, monthly or at the end of each project. **Users > Check
user accounts** (`/user/hygiene`) lists accounts with filters and acts on many at once
([Check user accounts](../../../../features/6.0/user-hygiene.md)).

## Contents

1. [Finding stale accounts](#finding-stale-accounts)
2. [Exporting for review](#exporting-for-review)
3. [Disabling](#disabling)
4. [A monthly routine](#a-monthly-routine)

## Finding stale accounts

1. Open **Users > Check user accounts** and limit the list to the external users' group.
2. Use the filters:
   - **Never signed in**: accounts created and never used;
   - **Not seen for N days** (180 by default; choose for example 60 for contractors);
   - **Disabled**, **Locked**: accounts already stopped or locked by failed sign-ins.
3. Each row shows the name, login, last visit with the days since, the number of sign-ins, the groups and the roles.

## Exporting for review

**Export the list as CSV** sends the whole filtered list as a UTF-8 file. Send each project manager the accounts of
their parties and ask which must stay. The file contains personal data; delete it after the review.

## Disabling

Tick the accounts and press **Disable**; a confirmation shows how many accounts change and how many are left out,
and why. A disabled account cannot sign in and its sessions end; content, groups and roles stay. **Enable** reverses
it. Nothing on this page removes an account.

## A monthly routine

- [ ] Filter external groups, **Not seen for 60 days**: disable unless the project manager confirms.
- [ ] Filter **Never signed in** older than two weeks: ask, then disable.
- [ ] Compare with the access register's end dates (Exponential has no expiry date of its own).
- [ ] Record the review.

## Related pages

- [Ending access](ending-access.md)
- [Check user accounts](../../../../features/6.0/user-hygiene.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
