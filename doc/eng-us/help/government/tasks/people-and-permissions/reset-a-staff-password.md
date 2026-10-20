---
title: Reset a staff password
audience: government, admin
category: public-sector
order: 80
summary: Step by step - help a colleague who cannot sign in, either through Forgot password or by setting a new password on their account page, after verifying who is asking, with what the system does for you afterwards.
keywords: password reset, forgot password, cannot sign in, new password, account page, service desk, identity check, other sessions, password changed mail
type: help
---
# Reset a staff password

This task is for service-desk staff and administrators. At the end, the colleague signs in with a new password and
their other sessions are signed out. For a reset on the shell (lost administrator password), see
[Reset a password from the command line](reset-a-password-from-the-command-line.md).

## Contents
1. [Before you start](#before-you-start)
2. [Option 1: Forgot password](#option-1-forgot-password)
3. [Option 2: set it on the account page](#option-2-set-it-on-the-account-page)
4. [What happens afterwards](#what-happens-afterwards)
5. [Pitfalls](#pitfalls)
6. [Related pages](#related-pages)

## Before you start

- **Verify who is asking** with your service-desk procedure. A reset for an unverified caller is an account takeover.
- Check whether the account is **Locked** or **Disabled** (**Users > Check user accounts**); a new password does
  not help a disabled account ([Unlock a locked account](unlock-a-locked-account.md)).

## Option 1: Forgot password

The safest way, because the password never passes through you:

1. Ask the colleague to open the sign-in page and use **Forgot your password?**.
2. They receive a mail with a link at the account's e-mail address and choose a new password.

This needs a working mail transport ([Mail problems](../../operations/incidents/mail-problems.md)).

## Option 2: set it on the account page

1. Open the colleague's account page, `/user/edit/<id>` (from the Users tree).
2. Use the **Password** card (shown when you may change it) and set a new password; **Generate a strong password**
   offers one.
3. Give the password through a separate channel and ask the colleague to change it at once under **My account** >
   **Password** (`/user/password`).

## What happens afterwards

- The password must meet `MinPasswordLength` and is checked against common passwords; any rules of
  `site.ini [PasswordSettings]` apply ([Changing your password](../../../../features/6.0/modern-password-change.md)).
- The account's other sessions are signed out on their next request.
- A mail "Your password was changed" goes to the account's address; nobody can switch it off.
- The change is recorded in the audit trail; the password never is.

## Pitfalls

- Two-step sign-in: a new password does not reset the second step. A lost phone is a separate task
  ([Help a user who lost their phone](help-a-user-who-lost-their-phone.md)).
- Never send login and password in the same message.
- If many staff ask at once, suspect a problem (mail, sign-in handler, an attack) before resetting each.

## Related pages
- [Reset a password from the command line](reset-a-password-from-the-command-line.md)
- [Two-step sign-in for staff](../../operations/people-and-permissions/two-factor-for-staff.md)
- [Change the administrator password (help)](../../../change-the-admin-password.md)
- [Accounts and sign-in (help)](../../../accounts-and-sign-in.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
