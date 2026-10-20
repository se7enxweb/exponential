---
title: Review and disable stale contractor accounts
audience: government, admin
category: public-sector
order: 200
summary: Task for administrators: once a month, find external accounts that were never used or not used for a long time in Users > Check user accounts, export the list for project managers, and disable what is no longer needed.
keywords: stale accounts, Check user accounts, user/hygiene, Not seen for N days, Never signed in, disable, export CSV, monthly review
type: help
---
# Review and disable stale contractor accounts

This task is for administrators. Background: [Reviewing stale accounts](../../building/extranets/reviewing-stale-accounts.md).

## Steps

1. **Users > Check user accounts** (`/user/hygiene`); limit the list to the "External" user group.
2. Choose **Not seen for N days** with, for example, 60 days.
3. **Export the list as CSV** and send each project manager the accounts of their contractors; ask which must stay.
4. Back on the page, tick the accounts to stop and press **Disable**; read the confirmation (how many change, how many
   are left out and why), then **Confirm**.
5. Repeat with **Never signed in** for accounts older than two weeks.
6. Compare with the end dates in your access register and disable accounts whose contract ended.
7. Delete the exported CSV file after the review.

## Check

The **Disabled** filter now shows the accounts; they cannot sign in and their sessions ended.

## Problems

| Problem | Fix |
|---|---|
| An account is left out | the confirmation says why (for example it is already disabled) |
| You need the account removed | removing a user is final; do it one account at a time on the user's node, after the retention period |

## Related pages

- [Check user accounts](../../../../features/6.0/user-hygiene.md)
- [Ending access](../../building/extranets/ending-access.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
