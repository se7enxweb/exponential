---
title: A user lost their phone - how do they sign in again?
audience: admin
category: manual
order: 290
summary: Edit the user's object, set the Two-step sign-in field to E-mail one-time code or Disabled and publish; the user signs in and sets up the authenticator app again.
keywords: two-step sign-in, 2FA, lost phone, reset 2FA, locked out, authenticator app, e-mail one-time code, user account
type: help
---
# A user lost their phone - how do they sign in again?

A user who set up an authenticator app cannot sign in without it. An administrator changes the user's second step so
that they can sign in and set it up again.

## Who can do this

Users who may edit user objects: `content/edit` on the user's group, usually the Administrator role. Check first that
the request really comes from the user, for example by phone.

## Steps

1. Click **Users** in the top menu and open the user's group, or search for the user by name or login.
2. Open the user and click **Edit**.
3. Find the field **Two-step sign-in** and choose **E-mail one-time code** (the code goes to the user's e-mail
   address) or **Disabled** (the password alone).
4. Click **Send for publishing**.
5. Tell the user to sign in and set up the app again on **My account** > **Two-step sign-in**.

## What can go wrong

- **The user cannot get the e-mail code.** Their e-mail address is wrong or old. Correct it in the same edit form, or
  choose **Disabled** for the first sign-in.
- **The user is locked out after too many wrong passwords.** Unlock the account too: see
  [How do I disable or unlock a user account?](how-do-i-disable-or-unlock-a-user-account.md)
- **You turned it off and forgot.** Accounts without a second step are listed in the security checks. Ask the user to
  turn it on again the same day.

## Related pages

- [How do I turn on two-step sign-in for my account?](how-do-i-turn-on-two-step-sign-in.md)
- [Two-step sign-in](../../guides/two-factor-login.md)
- [Change the administrator password: two-step sign-in and a lost phone](../change-the-admin-password.md#two-step-sign-in-and-a-lost-phone)
- [Admin accounts and access](../customers/healthy/admin-accounts-and-access.md)
- [User manual](README.md)

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
