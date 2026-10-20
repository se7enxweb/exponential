---
title: Password policy
audience: government, admin, project-manager
category: public-sector
order: 180
summary: For public bodies that must set a password policy - Exponential's password settings (minimum length, weak password check, composition rules, reset link lifetime, change notification), the shipped defaults, and a length-based policy in line with current NIST guidance and ASVS.
keywords: password policy, MinPasswordLength, WeakPasswordCheck, composition rules, PasswordSettings, ForbidLogin, ForbidCurrentPassword, EndOtherSessions, ChangeNotificationMail, ForgotPasswordKeyLifetime, NIST SP 800-63B, ASVS, bcrypt, public sector
type: help
---
# Password policy

This page is for administrators and security officers who write or implement the password policy of a public body for
its website accounts. The settings are in `settings/site.ini [UserSettings]` and `[PasswordSettings]`; every form
that sets a password applies them through `expPasswordPolicy`
([13.6 Passwords and sign-in](../../../install/13-security-hardening.md#136-passwords-and-sign-in)).

## Contents

1. [The settings and their defaults](#the-settings-and-their-defaults)
2. [A recommended policy](#a-recommended-policy)
3. [If your policy prescribes composition rules](#if-your-policy-prescribes-composition-rules)
4. [What changes for users](#what-changes-for-users)
5. [Related pages](#related-pages)

## The settings and their defaults

| Setting | Default | Meaning |
|---|---|---|
| `[UserSettings] MinPasswordLength` | `10` | minimum, in **bytes** |
| `GeneratePasswordLength` | `16` | length of generated passwords |
| `WeakPasswordCheck` | `enabled` | refuse well-known and common passwords |
| `WeakPasswordFile`, `WeakPasswordList[]` | empty | your own forbidden words |
| `ForgotPasswordKeyLifetime` | `3600` | seconds a "Forgot your password?" link works |
| `HashType` | `php_default` | bcrypt today; never plaintext or md5 types |
| `[PasswordSettings] RequireLowercase`, `RequireUppercase`, `RequireDigit`, `RequireSymbol` | `disabled` | composition rules |
| `MinCharacterClasses` | `0` | at least this many of the four kinds |
| `ForbidLogin` | `disabled` | must not contain the login name |
| `ForbidCurrentPassword` | `disabled` | must differ from the current one |
| `EndOtherSessions` | `enabled` | sign other sessions out after a change |
| `ChangeNotificationMail` | `enabled` | mail "your password was changed" |
| `StrengthMeter`, `GenerateButton` | `enabled` | help users reach length |

## A recommended policy

The installation guide recommends, in line with NIST SP 800-63B-4 (which asks for at least 15 characters where a
password is the only factor and advises against composition rules):

```ini
# settings/override/site.ini.append.php
[UserSettings]
MinPasswordLength=15

[PasswordSettings]
ForbidLogin=enabled
ForbidCurrentPassword=enabled
```

Add your organization's own forbidden words (its name, the site's name, the city) with `WeakPasswordList[]` or
`WeakPasswordFile`. ASVS 4.0.3 level 1 asks for at least 12; 15 meets both. Combine with two-step sign-in for staff.

## If your policy prescribes composition rules

Some organizational policies still prescribe them. Exponential supports them:

```ini
[PasswordSettings]
MinCharacterClasses=3
```

or the individual `Require*` switches. The installation guide notes that such rules tend to produce predictable
passwords; record the reason if you use them.

## What changes for users

- Raising the minimum locks nobody out: existing passwords keep working; the rule applies when a password is next set.
- `MinPasswordLength` counts bytes: in scripts with multi-byte characters fewer characters reach it.
- After a change, other sessions of the user are signed out and the user gets a notification mail.
- Accounts with older hash types are re-hashed at their next sign-in (`UpdateHash=true`).

## Related pages

- [13.6 Passwords and sign-in](../../../install/13-security-hardening.md#136-passwords-and-sign-in)
- [Changing your password](../../../features/6.0/modern-password-change.md)
- [Change the administrator password](../../change-the-admin-password.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
