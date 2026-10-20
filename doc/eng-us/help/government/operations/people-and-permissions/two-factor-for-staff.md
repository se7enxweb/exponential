---
title: Two-step sign-in for staff
audience: government, admin, project-manager
category: public-sector
order: 130
summary: How a public body turns on two-step sign-in (authenticator app or e-mail code) for its staff with the sevenx_authentication_2fa extension, how to roll it out without locking anyone out, and what to do when someone loses their phone.
keywords: two-factor, 2FA, two-step sign-in, MFA, multi-factor authentication, TOTP, authenticator app, e-mail code, Enforce2FA, user2fa, lost phone, staff accounts, security policy
type: help
---
# Two-step sign-in for staff

This page is for administrators and security officers who must require more than a password for staff accounts, as
many public-sector security policies do. Two-step sign-in is provided by the extension `sevenx_authentication_2fa`;
the guide [Two-step sign-in](../../../../guides/two-factor-login.md) has every setting. This page covers the roll-out
in an organization.

## Contents
1. [What staff get](#what-staff-get)
2. [What has to be in place](#what-has-to-be-in-place)
3. [Rolling it out](#rolling-it-out)
4. [Lost phones and new devices](#lost-phones-and-new-devices)
5. [Limits to know](#limits-to-know)
6. [Related pages](#related-pages)

## What staff get

After the password, a 6-digit code from an authenticator app (any app for time-based one-time codes) or from an
e-mail. Each code works once; after five wrong codes the sign-in starts again with the password. Users set it up
under **My account > Two-step sign-in** (`/user2fa/setup`). Changing or switching it off asks for a current code, so
an unattended computer cannot weaken the account ([Two-step sign-in, "What a user sees"](../../../../guides/two-factor-login.md#what-a-user-sees)).

## What has to be in place

| Item | Where | Note |
|---|---|---|
| The extension active | `[ExtensionSettings] ActiveExtensions[]` | `composer.json` requires it and the installers activate it |
| The login handler before `standard` | `site.ini [UserSettings] LoginHandler[]` | `sevenxUser2fa`, then `standard` |
| A field on the user class | class attribute of datatype *7x 2FA Configuration* | without it, users cannot keep a second step |
| Policies | `user2fa/setup` and `user2fa/verify` on every role that signs in | a role created later needs them too |
| An encryption key for the secrets | `[TOTPSettings] SecretKey` in `settings/override/sevenxauthentication2fa.ini.append.php` | keep it out of version control and in your backup of secrets; losing it means everyone sets up again |

Settings and policies are in the guide's section [Turning it on](../../../../guides/two-factor-login.md#turning-it-on).

## Rolling it out

1. **Try it on a test copy** with test accounts; never enroll a real administrator account while trying.
2. **Opt-in first** (`[General] Enforce2FA=disabled`, the default): ask administrators and the web team to enroll.
   The account page of each user shows a hint when two-step sign-in is off
   ([Security and audit, step 6](../../../../guides/security-and-audit.md#6-check-an-account-on-its-account-page-2-minutes)).
3. **Communicate** to staff: which app is accepted, what to do on a new phone, who to call.
4. **Require it**: `Enforce2FA=enabled` with `DefaultMethod=totp` (an authenticator right after the password) or
   `email` (codes by e-mail at once; the account's e-mail address must be real). Then clear the INI cache
   (`php bin/php/ezcache.php --clear-tag=ini`) and, on Velocity, deploy (`./console exp:velocity deploy`).
5. **Check**: sign in as a test user; a wrong code is refused and recorded; `var/log/auth.log` has each step,
   never a code.

Procedure: [Turn on two-step sign-in for staff](../../tasks/people-and-permissions/turn-on-two-step-sign-in-for-staff.md).

## Lost phones and new devices

An administrator opens the user's object, sets the *Two-step sign-in* field to *E-mail one-time code* or *Disabled*
and publishes; the user signs in and sets up the app again
([Help a user who lost their phone](../../tasks/people-and-permissions/help-a-user-who-lost-their-phone.md)). Verify
the person's identity through your normal service-desk process first: resetting a second factor on request of an
unverified caller defeats it.

## Limits to know

- **Enforcement is not per role.** `Enforce2FA` is one switch in the extension's settings, read for the sign-ins of
  the installation where it is set; Exponential has no "require two-step for this role only" setting. Organizations
  that want it for staff only often run staff sign-in on the administration or editor siteaccess and keep visitor
  accounts off it.
- **E-mail codes** are only as strong as the mailbox; prefer the authenticator app for administrators.
- **Wrong codes count as failed logins** of the account, so with lockout switched on (`MaxNumberOfFailedLogin`)
  repeated wrong codes lock the account ([What protects it](../../../../guides/two-factor-login.md#what-protects-it)).
- Social login buttons, if enabled, still require the user's own second step.

## Related pages
- [User account hygiene](user-account-hygiene.md)
- [Least-privilege principles](least-privilege-principles.md)
- [Two-factor sign-in (hosting)](../../../hosting/security/two-factor-sign-in.md)
- [Two-factor and social login extension](../../../../features/6.0/extensions/sevenx_authentication_2fa.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
