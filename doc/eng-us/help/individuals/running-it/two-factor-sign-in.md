---
title: Two-factor sign-in
audience: individual, customer, website-creator
category: website-creation
order: 1643
summary: How one person protects the administrator account and the accounts of relatives or club volunteers with two-step sign-in from sevenx_authentication_2fa - authenticator app or code by mail, setting it up, making it optional or required, a lost phone, and the settings involved.
keywords: two-factor, two-step sign-in, 2FA, TOTP, authenticator app, e-mail code, sevenx_authentication_2fa, Enforce2FA, user2fa, lost phone, account security, administrator, individual
type: help
---
# Two-factor sign-in

This page is for individuals who want a stolen password to be useless. With two-step sign-in, after the password the
site asks for a 6-digit code from an authenticator app on your phone, or one sent by mail. It ships with Exponential
as `sevenx_authentication_2fa` (the root `composer.json` requires it; the installers activate it), and the full guide
is [Two-step sign-in](../../../guides/two-factor-login.md).

## Contents

1. [Why one person needs it most](#why-one-person-needs-it-most)
2. [Setting it up for yourself](#setting-it-up-for-yourself)
3. [App or mail](#app-or-mail)
4. [If it is not offered yet](#if-it-is-not-offered-yet)
5. [Optional or required for others](#optional-or-required-for-others)
6. [A lost phone](#a-lost-phone)
7. [Checklist](#checklist)

## Why one person needs it most

When you run a site alone, your administrator account is the whole site: content, users, settings. Nobody else will
notice a stranger signing in. Two-step sign-in is the single strongest protection for that account. Turn it on before
launch.

## Setting it up for yourself

1. Sign in, open **My account** > **Two-step sign-in** (`/user2fa/setup`).
2. Choose **Authenticator app** and scan the QR code with an app such as any RFC 6238 authenticator (the extension's
   `README.md` names Google Authenticator, Authy and Microsoft Authenticator), or type the key shown.
3. Enter the code the app shows. Until you do, nothing changes.
4. Sign out and in again to see it work.

The key is not shown again. If your app offers an encrypted backup of its codes, use it.

![Setting up two-step sign-in](../../images/two-step-setup.webp)

## App or mail

| Method | Good | Less good |
|---|---|---|
| Authenticator app | Works without mail; codes change every 30 seconds | Needs a phone and a little care when the phone is replaced |
| Code by mail | Nothing to install; good for less technical relatives | Only as safe as the mailbox; depends on working mail |

Use the app for your administrator account. Offer mail codes to readers who struggle with apps.

## If it is not offered yet

On an older or custom installation an administrator may need to switch it on once
([Two-step sign-in, Turning it on](../../../guides/two-factor-login.md#turning-it-on)):

1. The extension and its login handler (`sevenxUser2fa` before `standard`) in `settings/override/site.ini.append.php`.
2. A field of the datatype *7x 2FA Configuration* on the user class.
3. The policies `user2fa/setup` and `user2fa/verify` for every role that signs in (Member, Editor, Administrator).
4. Recommended: a `SecretKey` in `settings/override/sevenxauthentication2fa.ini.append.php` that encrypts the stored
   secrets; keep it safe, losing it means everyone sets up the app again.

On your own site, you are that administrator.

## Optional or required for others

| `[General] Enforce2FA` | `DefaultMethod` | Effect |
|---|---|---|
| `disabled` (default) | any | Each person opts in |
| `enabled` | `totp` | A person without a second step sets up the app right after the password |
| `enabled` | `email` | A person without a second step gets mail codes straight away |

A sensible split for a family or club site: required (app) for administrators and editors, optional for readers.
Enroll yourself first, so you are never locked out by your own switch
([Accounts, passwords and two-step sign-in](../../customers/day-to-day/accounts-passwords-and-two-factor.md)).

## A lost phone

For someone else: open their user object in the administration, set the *Two-step sign-in* field to *E-mail one-time
code* or *Disabled*, and publish; they sign in and set up the app again.

For yourself, as the only administrator: prevent the problem.

- Keep a second administrator account (a deputy's, or your own emergency account with a long password kept offline)
  ([Sharing the work with volunteers](../club-sites/sharing-the-work-with-volunteers.md#the-deputy-administrator)).
- Or set your own method to mail codes temporarily before changing phones.

## Checklist

- [ ] Your administrator account uses the app.
- [ ] A second administrator account exists and is protected.
- [ ] Editors and volunteers use two-step sign-in.
- [ ] Readers can opt in.
- [ ] `SecretKey` set and backed up somewhere safe.
- [ ] You know how to reset someone's second step.

## Related pages

- [Running it alone](README.md)
- [Two-step sign-in (guide)](../../../guides/two-factor-login.md)
- [sevenx_authentication_2fa](../../../features/6.0/extensions/sevenx_authentication_2fa.md)
- [Accounts and sign-in](../../accounts-and-sign-in.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
