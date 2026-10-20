---
title: Two-step sign-in for public staff
audience: government, admin, project-manager
category: public-sector
order: 110
summary: For public bodies that require multi-factor authentication - how Exponential's two-step sign-in works (authenticator app or e-mail code, extension sevenx_authentication_2fa), how to turn it on and require it, how to roll it out to staff, and how to handle a lost phone.
keywords: two-step sign-in, two-factor authentication, 2FA, MFA, TOTP, authenticator app, e-mail code, sevenx_authentication_2fa, Enforce2FA, DefaultMethod, user2fa, lost phone, rollout, public sector
type: help
---
# Two-step sign-in for public staff

This page is for administrators and project managers who must introduce multi-factor authentication for the staff
who edit a public site. Exponential's two-step sign-in is provided by the extension `sevenx_authentication_2fa`, which
`composer.json` requires and the installers activate; it is opt-in until you require it
([Two-step sign-in](../../../guides/two-factor-login.md)).

## Contents

1. [What staff experience](#what-staff-experience)
2. [Turning it on](#turning-it-on)
3. [Requiring it](#requiring-it)
4. [Rolling it out](#rolling-it-out)
5. [Lost phone and other support cases](#lost-phone-and-other-support-cases)
6. [What protects it](#what-protects-it)
7. [Related pages](#related-pages)

## What staff experience

1. Sign in with username and password as before.
2. A page asks for a 6-digit code: from an authenticator app, or from an e-mail.
3. Set up under *My account* > *Two-step sign-in* (`/user2fa/setup`): scan the QR code, enter one code to confirm.
4. Changing the method or turning it off asks for a current code.

## Turning it on

In `settings/override/site.ini.append.php`, with the extension active, put the login handler before `standard`:

```ini
[UserSettings]
LoginHandler[]
LoginHandler[]=sevenxUser2fa
LoginHandler[]=standard
```

Then:

1. add a class attribute of the datatype *7x 2FA Configuration* (for example identifier `two_factor`) to the user
   class;
2. give every role that signs in the policies `user2fa/setup` and `user2fa/verify`;
3. set an encryption key for the authenticator secrets in
   `settings/override/sevenxauthentication2fa.ini.append.php`:
   `[TOTPSettings] SecretKey=<output of: openssl rand -base64 48>` (losing it means everyone sets the app up again);
4. clear the caches and deploy: `./console exp:velocity deploy --allow-root-user` on Velocity.

Try it first on a test siteaccess, as the guide describes.

## Requiring it

| `[General] Enforce2FA` | `DefaultMethod` | Effect |
|---|---|---|
| `disabled` (default) | any | users opt in |
| `enabled` | `totp` | a user without a second step sets up the app right after the password |
| `enabled` | `email` | a user without a second step gets e-mail codes at once |

Set it in `settings/override/sevenxauthentication2fa.ini.append.php`, then `php bin/php/ezcache.php --clear-tag=ini`
and deploy. `Enforce2FA` applies to every account that signs in through the handler.

## Rolling it out

1. Enrol the administrators first, so nobody is caught without a phone.
2. Announce the date to editors with a one-page instruction and the support contact.
3. Offer opt-in for two weeks; then set `Enforce2FA=enabled`.
4. A role created later needs `user2fa/setup` and `user2fa/verify` for its users.
5. Check accounts on their account page: a hint shows when two-step sign-in is off
   ([Security and audit, 6](../../../guides/security-and-audit.md#6-check-an-account-on-its-account-page-2-minutes)).

## Lost phone and other support cases

- An administrator opens the user's object, sets the *Two-step sign-in* field to *E-mail one-time code* or *Disabled*,
  and publishes; the user signs in and sets the app up again.
- Wrong codes count as failed logins of the account; with `MaxNumberOfFailedLogin` set, they can lock it.
- Every step is written to `var/log/auth.log` (never a code or a secret), readable under Setup > Log files.

## What protects it

The password alone never signs in; the session gets a new id after the code; each code works once; e-mail codes are
stored hashed and rate limited; the QR code is drawn on the server and the key goes to no other service; redirects
pass the safe redirect rules ([Two-step sign-in: What protects it](../../../guides/two-factor-login.md#what-protects-it)).
Social login buttons, if enabled, involve third-party identity providers (see the privacy section of this help).

## Related pages

- [Two-step sign-in](../../../guides/two-factor-login.md)
- [Two-factor sign-in for hosts](../../hosting/security/two-factor-sign-in.md)
- [sevenx_authentication_2fa](../../../features/6.0/extensions/sevenx_authentication_2fa.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
