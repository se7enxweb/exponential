---
title: Set up two-step sign-in for staff
audience: government, admin
category: public-sector
order: 210
summary: Task for administrators: turn on two-step sign-in (authenticator app or e-mail code) with sevenx_authentication_2fa, require it for staff, protect the secrets, and test it.
keywords: two-step sign-in, 2FA, MFA, authenticator app, Enforce2FA, sevenxUser2fa, user2fa, staff, security
type: help
---
# Set up two-step sign-in for staff

This task is for administrators. Background: [Two-step sign-in](../../../../guides/two-factor-login.md) and
[Two-step sign-in for staff](../../building/intranets/two-step-sign-in-for-staff.md).

## Steps

1. Activate `sevenx_authentication_2fa` and put its handler first:

   ```ini
   # settings/override/site.ini.append.php
   [UserSettings]
   LoginHandler[]
   LoginHandler[]=sevenxUser2fa
   LoginHandler[]=standard
   ```

2. Add a field of the datatype *7x 2FA Configuration* to the user class.
3. Give every role that signs in the policies `user2fa/setup` and `user2fa/verify`.
4. Set `[TOTPSettings] SecretKey` in `settings/override/sevenxauthentication2fa.ini.append.php` (outside version
   control).
5. Require it: `[General] Enforce2FA=enabled`, `DefaultMethod=totp`.
6. Clear caches; with Velocity `./console exp:velocity deploy`.

Try it first on a test siteaccess, as the guide recommends.

## Check

Sign in with a test staff account: after the password, the setup of the app is required; after setup, a code is asked
at each sign-in. Five wrong codes end the sign-in.

## Problems

| Problem | Fix |
|---|---|
| A user lost the phone | open the user object, set *Two-step sign-in* to *E-mail one-time code* or *Disabled*, publish; verify identity first |
| Users of a class cannot keep a second step | the class lacks the 2FA field |

## Related pages

- [Sign-in options for staff](../../building/intranets/sign-in-options-for-staff.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
