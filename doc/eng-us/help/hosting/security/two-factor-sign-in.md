---
title: Two-step sign-in
audience: hosting, admin
category: hosting
order: 80
summary: Two-step sign-in for the Exponential sites a host runs: what the sevenx_authentication_2fa extension does, how to switch it on per site or siteaccess, how to require it for administrators, the encryption key for authenticator secrets, lost phones, auth.log, and how a host can offer it.
keywords: two-factor, two-step sign-in, 2FA, TOTP, authenticator app, e-mail code, sevenx_authentication_2fa, sevenxUser2fa, LoginHandler, Enforce2FA, DefaultMethod, SecretKey, user2fa/setup, user2fa/verify, auth.log, lost phone, social login
type: help
---
# Two-step sign-in

This page is for system administrators and support staff at a host. With two-step sign-in, a user types a code from
an authenticator app or from an e-mail after the password, so a stolen or guessed password alone is useless. It is the
strongest single defense against credential stuffing on the sign-in page. The reference is
[Two-step sign-in](../../../guides/two-factor-login.md); the extension is `sevenx_authentication_2fa`, which
`composer.json` requires and the installers activate.

## Contents

1. [What users see](#what-users-see)
2. [Switching it on](#switching-it-on)
3. [Requiring it](#requiring-it)
4. [The secrets key](#the-secrets-key)
5. [Lost phones and support](#lost-phones-and-support)
6. [What protects it](#what-protects-it)
7. [Offering it to customers](#offering-it-to-customers)
8. [Checklist](#checklist)

## What users see

1. Sign in with username and password as always.
2. A page asks for the 6-digit code: from the authenticator app, or from the e-mail (also in its subject). Each code
   works once; after five wrong codes the sign-in starts again with the password.
3. Set up under *My account* > *Two-step sign-in* (`/user2fa/setup`): choose *Authenticator app*, scan the QR code,
   enter the code. Nothing changes until that code is entered.
4. Switching to e-mail codes or off asks for a current code first.

## Switching it on

Per installation, in `settings/override/site.ini.append.php` (or per siteaccess for a trial):

```ini
[ExtensionSettings]
ActiveExtensions[]=sevenx_authentication_2fa

[UserSettings]
LoginHandler[]
LoginHandler[]=sevenxUser2fa
LoginHandler[]=standard
```

Then:

1. Add a class attribute of the datatype *7x 2FA Configuration* to the user class (identifier for example
   `two_factor`). Users of a class without it cannot keep a second step.
2. Give every role that signs in the policies `user2fa/setup` and `user2fa/verify`. Nothing is needed for anonymous
   visitors.
3. Clear the caches and, under Velocity, deploy: `./console exp:velocity deploy --allow-root-user`.

> **Tip**
> Try it on one siteaccess first: activate the extension as an access extension of a test siteaccess and put the
> `LoginHandler` lines in that siteaccess's `site.ini.append.php`. Remember that `settings/override` outranks a
> siteaccess.

## Requiring it

| `[General] Enforce2FA` | `DefaultMethod` | Effect |
|---|---|---|
| `disabled` (default) | any | users opt in on the setup page |
| `enabled` | `totp` | a user without a second step sets up the app right after the password |
| `enabled` | `email` | a user without a second step gets e-mail codes at once; the account's address must be real |

```ini
# settings/override/sevenxauthentication2fa.ini.append.php
[General]
Enforce2FA=enabled
DefaultMethod=totp
```

Clear the INI cache (`php bin/php/ezcache.php --clear-tag=ini`) and deploy. Enrol the administrators first, so nobody
is caught without a phone. A role created later needs `user2fa/setup` and `user2fa/verify` too. Requiring it at least
for administrators is the recommendation of [Security hardening](../../security-hardening.md#brute-force-and-credential-stuffing-on-the-sign-in).

## The secrets key

Recommended: a key that encrypts the authenticator secrets in the database.

```ini
# settings/override/sevenxauthentication2fa.ini.append.php
[TOTPSettings]
SecretKey=<output of: openssl rand -base64 48>
```

Keep it out of version control and back it up with the rest of `settings/override/` as a secret. Losing it means
every user sets the app up again. Generate one key per site; never reuse a key across customers.

## Lost phones and support

An administrator of the site opens the user's object in the administration, sets the *Two-step sign-in* field to
*E-mail one-time code* or *Disabled*, and publishes; the user signs in and sets the app up again
([Two-step sign-in and a lost phone](../../change-the-admin-password.md#two-step-sign-in-and-a-lost-phone)).

For a host's support desk this means:

- verify the caller's identity by the customer's own procedure before anyone resets a second step; a reset request
  is a classic social-engineering route;
- prefer that the customer's own administrator does the reset; the host's staff do it only on a documented request;
- check `var/log/auth.log` and the audit trail afterwards.

## What protects it

From [What protects it](../../../guides/two-factor-login.md#what-protects-it):

- The password alone never signs in; the user is signed in, with a new session id, only after the right code.
- Wrong codes count as failed logins of the account (`[UserSettings] MaxNumberOfFailedLogin` locks it).
- E-mail codes are stored hashed; a new one can be asked for once a minute, three times per sign-in.
- The QR code is drawn on the server; pages with a key or a pending sign-in are not cached.
- Every step is written to `var/log/auth.log`, never a code or a secret.

## Offering it to customers

Options a host can choose:

- switched on as opt-in on every new site (login handler, class attribute and policies set by your provisioning);
- required for administrator roles as part of a managed or security-focused plan;
- a documented reset procedure as part of the support tier ([Support tiers](../business/support-tiers.md)).

## Checklist

- [ ] Extension active; `LoginHandler` order `sevenxUser2fa`, then `standard`.
- [ ] The user class has the *7x 2FA Configuration* attribute.
- [ ] `user2fa/setup` and `user2fa/verify` on every role that signs in.
- [ ] `[TOTPSettings] SecretKey` set, unique per site, backed up as a secret.
- [ ] `Enforce2FA=enabled` at least where administrators sign in, after enrolling them.
- [ ] Reset procedure for lost phones written down, with identity checks.

## Related pages

- [Security for hosts: overview](README.md)
- [Two-step sign-in (guide)](../../../guides/two-factor-login.md)
- [Security and audit: lock it down](../../../guides/security-and-audit.md)
- [Restricting the administration](restricting-the-administration.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
