---
title: Find accounts not used for months
audience: government, admin
category: public-sector
order: 200
summary: Step by step - list staff accounts nobody has used for a chosen number of days, or never used, with Users > Check user accounts, confirm with the departments, and disable them.
keywords: dormant accounts, inactive accounts, unused accounts, not seen, never signed in, last visit, Check user accounts, user hygiene, disable, access review
type: help
---
# Find accounts not used for months

This task is for administrators doing account hygiene or answering an audit finding about dormant accounts. At the
end, unused accounts are listed, checked and disabled. The page is **Users > Check user accounts** (`/user/hygiene`),
described on [Check user accounts](../../../../features/6.0/user-hygiene.md).

## Contents
1. [Before you start](#before-you-start)
2. [List the accounts](#list-the-accounts)
3. [Confirm and disable](#confirm-and-disable)
4. [Check it worked](#check-it-worked)
5. [Related pages](#related-pages)

## Before you start

- The function `user/hygiene` (and `content/edit` on the accounts to change them). E-mail addresses on the page
  need `role/assign`.
- Your policy's period, for example 90 or 180 days.
- The page needs an SQL database; on MongoDB it lists nothing.

## List the accounts

1. Open **Users > Check user accounts**.
2. Choose the filter **Not seen for N days** and set N (1 to 36500; 180 by default). Each filter shows how many
   accounts it holds.
3. Optional: choose **User group** = Staff (or a department) to leave visitors' accounts out.
4. Order by **Last visit** to see the longest unused first.
5. Repeat with **Never signed in**: accounts created but never used. The anonymous user is not counted.
6. **Export the list as CSV** to send to department heads.

The last visit is the time of the last sign-in or of the first request after a longer pause
(`site.ini [Session] ActivityTimeout`).

## Confirm and disable

1. Send each department its list and a date by which to answer.
2. Back on the page, tick the accounts nobody claimed and press **Disable**.
3. The confirmation says how many will change and which are left out and why (the anonymous user, the user creator,
   the administrator account, `admin` and you are never changed). Press **Confirm**.
4. Each account is recorded as `access.user.disable`.

## Check it worked

- Filter **Disabled**: the accounts are listed.
- Keep the CSV and the department answers with your review record.

## Related pages
- [User account hygiene](../../operations/people-and-permissions/user-account-hygiene.md)
- [Export the account list for a review](export-the-account-list-for-a-review.md)
- [Deactivate and move a leaver](deactivate-and-move-a-leaver.md)
- [Admin access reviews](../../operations/people-and-permissions/admin-access-reviews.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
