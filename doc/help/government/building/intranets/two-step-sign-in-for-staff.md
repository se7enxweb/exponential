---
title: Two-step sign-in for staff
audience: government, admin
category: public-sector
order: 70
summary: How to require a second step at sign-in for staff with the sevenx_authentication_2fa extension (authenticator app or e-mail code): turning it on, enforcing it, protecting the secrets, lost phones, and combining it with other login handlers.
keywords: two-step sign-in, two-factor, 2FA, MFA, TOTP, authenticator app, e-mail code, Enforce2FA, sevenx_authentication_2fa, user2fa, staff security
type: help
---
# Two-step sign-in for staff

This page is for administrators. After the password, a code from an authenticator app or one sent by e-mail makes the
password alone useless to anyone else. It is provided by `sevenx_authentication_2fa`, which `composer.json` requires
([Two-step sign-in](../../../../guides/two-factor-login.md)).

## Contents

1. [Turning it on](#turning-it-on)
2. [Requiring it](#requiring-it)
3. [Protecting the secrets](#protecting-the-secrets)
4. [Lost phones](#lost-phones)
5. [With other login handlers](#with-other-login-handlers)

## Turning it on

1. Activate the extension and put its login handler before `standard`:

   ```ini
   [UserSettings]
   LoginHandler[]
   LoginHandler[]=sevenxUser2fa
   LoginHandler[]=standard
   ```

2. Add a field of the datatype *7x 2FA Configuration* to the user class.
3. Give every role that signs in the policies `user2fa/setup` and `user2fa/verify`.
4. Clear the caches; with Velocity, `./console exp:velocity deploy`.

Users set it up under *My account* > *Two-step sign-in* (`/user2fa/setup`).

## Requiring it

`[General] Enforce2FA=enabled` with `DefaultMethod=totp` makes a user without a second step set up the app right after
the password; with `email`, they get e-mail codes at once (the account's address must be real). For staff with
administrative rights, require it.

## Protecting the secrets

Set `[TOTPSettings] SecretKey` in `settings/override/sevenxauthentication2fa.ini.append.php` to encrypt the authenticator
secrets in the database; keep it out of version control. Losing it means everyone sets the app up again.

## Lost phones

An administrator opens the user's object, sets the *Two-step sign-in* field to *E-mail one-time code* or *Disabled*,
and publishes; the user signs in and sets the app up again. Verify the person's identity first, by your own procedure.

## With other login handlers

The documented setup chains the 2FA handler with `standard`. Whether it applies to accounts created by the LDAP
handler is not documented; test the combination before relying on it (see "LDAP sign-in").

## Related pages

- [Two-step sign-in](../../../../guides/two-factor-login.md)
- [Security and audit](../../../../guides/security-and-audit.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
