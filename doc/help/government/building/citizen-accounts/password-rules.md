---
title: Password rules
audience: government, admin
category: public-sector
order: 50
summary: The password rules Exponential applies and can apply - minimum length, the weak password check, character class rules, not containing the login, password expiry with ezmbpaex, forgot-password links - and how to choose rules for public and staff accounts.
keywords: password rules, password policy, MinPasswordLength, WeakPasswordCheck, PasswordSettings, RequireDigit, MinCharacterClasses, ForbidLogin, ForbidCurrentPassword, ezmbpaex, password expiry, forgot password, ForgotPasswordKeyLifetime
type: help
---
# Password rules

This page is for administrators who set the password policy of a public-sector site. It lists every rule Exponential
has, where it is set, and gives a starting point for public and staff accounts. Sources:
[Changing your password](../../../../features/6.0/modern-password-change.md),
[ezmbpaex](../../../../features/6.0/extensions/ezmbpaex.md) and
[Security hardening](../../../security-hardening.md#brute-force-and-credential-stuffing-on-the-sign-in).

## Contents

1. [The rules](#the-rules)
2. [Password expiry](#password-expiry)
3. [Forgotten passwords](#forgotten-passwords)
4. [Choosing a policy](#choosing-a-policy)
5. [Where the rules apply](#where-the-rules-apply)

## The rules

| Setting | Default | Rule |
|---|---|---|
| `site.ini [UserSettings] MinPasswordLength` | 10 on a new installation (3 when missing) | minimum length, counted in bytes |
| `site.ini [UserSettings] WeakPasswordCheck` | enabled (since 6.0.15) | refuse well-known and common passwords and simple variants |
| `[PasswordSettings] RequireLowercase`, `RequireUppercase`, `RequireDigit`, `RequireSymbol` | off | each requires one character of that kind |
| `[PasswordSettings] MinCharacterClasses` | 0 (off) | mix at least N of the four kinds |
| `[PasswordSettings] ForbidLogin` | off | must not contain the login name |
| `[PasswordSettings] ForbidCurrentPassword` | off | must differ from the current password |
| `[PasswordSettings] EndOtherSessions` | enabled | other sessions signed out after a change |

The password change page lists the requirements beside the field, ticks them off while the user types (with
JavaScript), and sends a "Your password was changed" mail that cannot be switched off. Set rules in
`settings/override/site.ini.append.php`, then clear the INI cache.

## Password expiry

The `ezmbpaex` extension adds expiry and a regular expression rule:

| Setting (`mbpaex.ini [mbpaexSettings]`) | Default | Meaning |
|---|---|---|
| `DefaultPasswordLifeTime` | 3 (days) | days a password is valid; `0` never expires |
| `ExpirationNotification` | 172800 (two days) | when the warning mail is sent before expiry |
| `PasswordValidationRegexp` | empty | a pattern new passwords must match |
| `ForgotPasswordHashLifeTime` | 86400 | seconds a forgot-password link of the extension works |

It needs two cronjob parts (`ezmbpaex_send_expiry_notifications`, `ezmbpaex_updatechildren`). Note the shipped
lifetime of three days: set your own before activating it. Current guidance from security bodies generally prefers
long passwords, a check against known weak passwords and a second step over forced periodic changes; decide with your
security officer.

## Forgotten passwords

| Property | Exponential |
|---|---|
| Same answer for every address | yes: the page never reveals whether an account exists |
| Link lifetime | one use, one hour (`ForgotPasswordKeyLifetime`) |
| Key strength | random keys from the system's secure source |
| Without access to the mailbox | an administrator resets the password (in the user's account, or `bin/php/resetuserpassword.php` for administrators, [Reset a user password](../../../../features/6.0/reset-user-password.md)) after checking identity |

## Choosing a policy

A starting point to adapt (example assumption):

| Accounts | Length | Other rules | Second step |
|---|---|---|---|
| Public accounts | `MinPasswordLength=12` | `WeakPasswordCheck=enabled`, `ForbidLogin=enabled` | offered |
| Staff | `MinPasswordLength=15` (the hardening guide's suggestion) | `WeakPasswordCheck`, `ForbidLogin`, `ForbidCurrentPassword` | enforced |

The settings apply per installation or per siteaccess; registrations on the public siteaccess and staff accounts
edited in the administration can therefore follow different rules where your setup separates them.

## Where the rules apply

Every form that sets a password: registration, the password change page (`user/password`), editing a user in
content/edit or the administration, and `bin/php/resetuserpassword.php`. Existing passwords are not checked again at
sign-in; nobody is locked out by a new rule.

## Related pages

- [Citizen accounts](README.md)
- [Two-step sign-in](two-factor-sign-in.md)
- [13.6 Passwords and sign-in](../../../../install/13-security-hardening.md#136-passwords-and-sign-in)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
