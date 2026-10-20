---
title: Turn on two-step sign-in for staff
audience: government, admin
category: public-sector
order: 230
summary: Step by step - check the 2FA extension and login handler, add the field to the user class, give every staff role user2fa/setup and user2fa/verify, encrypt the secrets, enroll administrators first, then require it with Enforce2FA.
keywords: two-step sign-in, two-factor, 2FA, MFA, TOTP, authenticator, Enforce2FA, user2fa/setup, user2fa/verify, sevenx_authentication_2fa, roll-out
type: help
---
# Turn on two-step sign-in for staff

This task is for administrators. At the end, staff sign in with a password and a code from an authenticator app.
Every setting is in [Two-step sign-in](../../../../guides/two-factor-login.md); the roll-out plan is on
[Two-step sign-in for staff](../../operations/people-and-permissions/two-factor-for-staff.md).

## Contents
1. [Before you start](#before-you-start)
2. [Check the extension and the handler](#check-the-extension-and-the-handler)
3. [The field and the policies](#the-field-and-the-policies)
4. [Encrypt the secrets](#encrypt-the-secrets)
5. [Enroll, then require](#enroll-then-require)
6. [Check it worked](#check-it-worked)
7. [Related pages](#related-pages)

## Before you start

- Shell access and an administrator account; try it on a test copy first, never with a real administrator account.
- A communication to staff ready: which app, what to do with a new phone, who to call.

## Check the extension and the handler

```bash
./console exp:ini get 'site.ini/ExtensionSettings/ActiveExtensions' --allow-root-user
./console exp:ini get 'site.ini/UserSettings/LoginHandler' --allow-root-user
```

`sevenx_authentication_2fa` must be active (`composer.json` requires it and the installers activate it), and
`LoginHandler` must list `sevenxUser2fa` before `standard`. If not, add to `settings/override/site.ini.append.php`:

```ini
[UserSettings]
LoginHandler[]
LoginHandler[]=sevenxUser2fa
LoginHandler[]=standard
```

## The field and the policies

1. The user class needs an attribute of the datatype *7x 2FA Configuration* (identifier for example `two_factor`).
   Without it, users cannot keep a second step.
2. Give every role whose users sign in the policies `user2fa/setup` and `user2fa/verify`
   (**Users > Roles and policies**, each role, **Edit**, **New policy**, module `user2fa`). Administrator has them
   through full access. Note it in your role record: a role made later needs them too.

## Encrypt the secrets

In `settings/override/sevenxauthentication2fa.ini.append.php` (keep it out of version control and in your secrets
backup):

```ini
[TOTPSettings]
SecretKey=<output of: openssl rand -base64 48>
```

Losing the key means everyone sets the app up again.

## Enroll, then require

1. Administrators and the web team enroll first: **My account > Two-step sign-in** (`/user2fa/setup`), choose the
   authenticator app, scan the code, enter the six digits.
2. When ready, require it:

   ```ini
   # settings/override/sevenxauthentication2fa.ini.append.php
   [General]
   Enforce2FA=enabled
   DefaultMethod=totp
   ```

3. Clear the INI cache and deploy:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini --allow-root-user
   ./console exp:velocity deploy --allow-root-user
   ```

## Check it worked

- A test user without a second step is sent to set up the app right after the password.
- A wrong code is refused; `var/log/auth.log` records the step (never the code).
- Account pages no longer show the hint that two-step sign-in is off for enrolled staff.

## Related pages
- [Help a user who lost their phone](help-a-user-who-lost-their-phone.md)
- [Two-step sign-in for staff](../../operations/people-and-permissions/two-factor-for-staff.md)
- [Two-factor sign-in (hosting)](../../../hosting/security/two-factor-sign-in.md)
- [Security and audit, step 5](../../../../guides/security-and-audit.md#5-two-factor-sign-in-optional-10-minutes)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
