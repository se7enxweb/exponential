---
title: Account settings reference
audience: government, admin
category: public-sector
order: 90
summary: Every setting named in the citizen accounts guides in one table - registration, sign-in, sessions, passwords, lockout, two-step sign-in, deactivation and password expiry - with the file, the block, the shipped value and a public-sector recommendation.
keywords: settings reference, site.ini, UserSettings, PasswordSettings, Session, sevenxauthentication2fa.ini, mbpaex.ini, MinPasswordLength, MaxNumberOfFailedLogin, Enforce2FA, DeactivateAndMove, VerifyUserType
type: reference
---
# Account settings reference

This page is for administrators who configure accounts on a public-sector site. It collects the settings of the
citizen accounts guides in one place. Values marked "shipped" are those of the shipped settings or of a new
installation as documented in the linked pages; recommendations are starting points (example assumptions) to adapt.
Put changes in `settings/override/` or a siteaccess's `settings/siteaccess/<name>/`, never in the shipped files, and
clear the INI cache afterwards: `php bin/php/ezcache.php --clear-tag=ini`.

## Contents

1. [Registration](#registration)
2. [Sign-in and sessions](#sign-in-and-sessions)
3. [Passwords](#passwords)
4. [Two-step sign-in](#two-step-sign-in)
5. [Deactivation and expiry](#deactivation-and-expiry)

## Registration

| File | Block | Key | Shipped | Recommendation | Read |
|---|---|---|---|---|---|
| `site.ini` | `UserSettings` | `DefaultUserPlacement` | `12` | the node of your public accounts group | [Registration](registration.md) |
| `site.ini` | `UserSettings` | `VerifyUserType` | `email` | keep | [Registration](registration.md) |
| `site.ini` | `UserSettings` | `RegistrationFeedback` | `email` | keep; set `RegistrationEmail` to a shared mailbox | [Roles and policies, 7](../../../../guides/roles-and-policies.md#7-clean-up-unactivated-users) |
| `site.ini` | `UserSettings` | `RegistrationAdminLinks` | `enabled` | keep | the same |
| `site.ini` | `UserSettings` | `RegistrationActionTTL` | `604800` | shorter if approvals are quick | the same |
| `site.ini` | `UserSettings` | `PasswordInRegistrationEmail` | `disabled` | keep | [Member registration](../../../creators/website-creators/member-registration-and-areas.md) |

## Sign-in and sessions

| File | Block | Key | Shipped | Recommendation | Read |
|---|---|---|---|---|---|
| `site.ini` | `Session` | `SessionTimeout` | `259200` | shorter for shared computers | [Sessions](../../../../guides/sessions.md) |
| `site.ini` | `Session` | `ActivityTimeout` | `3600` | keep | the same |
| `site.ini` | `Session` | `Handler` | empty | `ezpSessionHandlerDB` to list and remove sessions | the same |
| `site.ini` | `Session` | `CookieSecure` | not set (`auto` behavior, per Security standards) | keep; serve everything over HTTPS | [Security standards](../../../../standards/security.md) |
| `site.ini` | `UserSettings` | `MaxNumberOfFailedLogin` | `0` (off) | a value with `TrustedIPList[]`, or rely on 2FA and alerts | [Security hardening](../../../security-hardening.md#brute-force-and-credential-stuffing-on-the-sign-in) |
| `site.ini` | `UserSettings` | `TrustedIPList[]` | empty | your office networks | the same |

## Passwords

| File | Block | Key | Shipped | Recommendation | Read |
|---|---|---|---|---|---|
| `site.ini` | `UserSettings` | `MinPasswordLength` | `10` | 12 for the public, 15 for staff | [Password rules](password-rules.md) |
| `site.ini` | `UserSettings` | `WeakPasswordCheck` | `enabled` | keep | the same |
| `site.ini` | `PasswordSettings` | `ForbidLogin` | off | `enabled` | the same |
| `site.ini` | `PasswordSettings` | `ForbidCurrentPassword` | off | `enabled` for staff | the same |
| `site.ini` | `PasswordSettings` | `MinCharacterClasses` | `0` | as your policy says | the same |
| `site.ini` | `PasswordSettings` | `EndOtherSessions` | `enabled` | keep | [Changing your password](../../../../features/6.0/modern-password-change.md) |

## Two-step sign-in

| File | Block | Key | Shipped | Recommendation | Read |
|---|---|---|---|---|---|
| `site.ini` | `UserSettings` | `LoginHandler[]` | `sevenxUser2fa`, `standard` (set by the installer) | keep | [Two-step sign-in](two-factor-sign-in.md) |
| `sevenxauthentication2fa.ini` | `General` | `Enforce2FA` | `disabled` | `enabled` for the administration siteaccess | the same |
| `sevenxauthentication2fa.ini` | `General` | `DefaultMethod` | `totp` | keep | the same |
| `sevenxauthentication2fa.ini` | `TOTPSettings` | `SecretKey` | made by the installer | keep secret, back it up | [Two-step sign-in guide](../../../../guides/two-factor-login.md) |

## Deactivation and expiry

| File | Block | Key | Shipped | Recommendation | Read |
|---|---|---|---|---|---|
| `site.ini` | `UserSettings` | `DeactivateAndMove` | `disabled` | `enabled` | [Deactivation and removal](deactivation-and-removal.md) |
| `site.ini` | `UserSettings` | `DeactivatedUserGroupNodeID` | empty | keep empty unless you have a group | the same |
| `mbpaex.ini` | `mbpaexSettings` | `DefaultPasswordLifeTime` | `3` | decide before activating ezmbpaex; `0` disables expiry | [ezmbpaex](../../../../features/6.0/extensions/ezmbpaex.md) |
| `mbpaex.ini` | `mbpaexSettings` | `PasswordValidationRegexp` | empty | only if a rule cannot be expressed otherwise | the same |

Check the value in force on your installation with **Setup > Ini settings** or `./console exp:ini`
([exp:ini command](../../../../features/6.0/exp-ini-command.md)).

## Related pages

- [Citizen accounts](README.md)
- [Settings page](../../../../guides/settings-page.md)
- [13. Security hardening](../../../../install/13-security-hardening.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
