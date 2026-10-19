---
title: User accounts and passwords
audience: businesses, admin, editor
category: business-solutions
order: 50
summary: The account and password policy a business should set on Exponential - password rules, common-password refusal, two-step sign-in, what happens when a password changes, reset links, lockouts and keeping the account list tidy - with the settings and pages for each.
keywords: user accounts, passwords, password policy, MinPasswordLength, WeakPasswordCheck, two-step sign-in, 2FA, two-factor, Enforce2FA, password change, reset, lockout, account hygiene, check user accounts, administrator
type: help
---
# User accounts and passwords

This page is for owners and administrators who decide how staff, editors, members and customers sign in. Stolen and
guessed passwords are the most common way into any site; a few decisions close most of that door. Each section says
what Exponential does already and what you should decide.

> **Note**
> Basics, not legal advice or a full security assessment. If contracts or regulations set rules for sign-in in
> your sector, those rules come first.

## Contents

1. [Policy decisions at a glance](#policy-decisions-at-a-glance)
2. [Password rules](#password-rules)
3. [Two-step sign-in](#two-step-sign-in)
4. [When a password changes or is forgotten](#when-a-password-changes-or-is-forgotten)
5. [Administrator accounts](#administrator-accounts)
6. [Keeping the account list tidy](#keeping-the-account-list-tidy)
7. [A policy text to adapt](#a-policy-text-to-adapt)

## Policy decisions at a glance

| Decision | Default | Recommendation to consider | Source |
|---|---|---|---|
| Minimum password length | 10 on a new installation | longer, for example 15, as the hardening guide suggests | [Changing your password](../../../features/6.0/modern-password-change.md) |
| Refuse common passwords | on (`WeakPasswordCheck=enabled`) | keep it on | same |
| Other rules (digits, symbols, not the login) | off | turn on `ForbidLogin` and `ForbidCurrentPassword` | same |
| Two-step sign-in | users opt in | require it for administrators and editors | [Two-step sign-in](../../../guides/two-factor-login.md) |
| Lock after failed attempts | off (`MaxNumberOfFailedLogin` 0) | optional; a lockout also lets anyone lock a known account | [Security hardening](../../security-hardening.md#brute-force-and-credential-stuffing-on-the-sign-in) |
| Reset link lifetime | works once, for one hour | keep | same |

## Password rules

- The length rule is `site.ini [UserSettings] MinPasswordLength`: 10 on a new installation.
- The optional rules in `site.ini [PasswordSettings]` (lowercase, uppercase, digit, symbol, character classes, not
  the login, not the current password) are all off by default, so an upgrade never makes rules stricter by itself.
- `WeakPasswordCheck` is on since 6.0.15: new passwords that are well known or common, or simple variants of one,
  are refused. Nobody is refused at sign-in.
- The same rules apply to every form that sets a password: registration, account editing, the change page and the
  command line.

Source: [Changing your password](../../../features/6.0/modern-password-change.md#the-rules). Passwords are stored as
bcrypt hashes ([Security hardening](../../security-hardening.md#brute-force-and-credential-stuffing-on-the-sign-in)).

## Two-step sign-in

After the password, a six-digit code from an authenticator app or an e-mail: the password alone is then useless to
anyone else. It is provided by `sevenx_authentication_2fa`, which `composer.json` requires and the installers
activate ([Two-step sign-in](../../../guides/two-factor-login.md)).

| You decide | Setting |
|---|---|
| Everyone chooses for themselves | `[General] Enforce2FA=disabled` (the default) |
| Required, with an authenticator app | `Enforce2FA=enabled`, `DefaultMethod=totp` |

What the business needs to know:

- Turning it on takes about 15 minutes; each user needs about 2 minutes to set it up (same source).
- Changing or turning it off asks for a current code, so someone at an unattended computer cannot weaken an account.
- A lost phone is handled by an administrator, who sets the user's field to e-mail codes or disabled; the user then
  sets the app up again.
- Keep the encryption key (`SecretKey`) safe and out of version control: losing it means everyone sets up the app
  again.

The intranet view is in [Two-step sign-in for intranets](../intranets/two-factor-and-sign-in.md).

## When a password changes or is forgotten

From [Changing your password](../../../features/6.0/modern-password-change.md):

- The change page lists the requirements and reports each problem next to its field.
- After a change the user stays signed in with a new session id; other sessions on other devices are signed out on
  their next request. This also happens when an administrator changes it.
- An e-mail "Your password was changed" goes to the account's address. It never contains the password and nobody can
  switch it off.
- Every change and every refused change is recorded in the audit trail, never the password itself.
- "Forgot your password?" answers the same for every address, so it does not reveal which accounts exist
  ([Security hardening](../../security-hardening.md#brute-force-and-credential-stuffing-on-the-sign-in)).

An administrator who has lost access can set a new password from the command line
([Reset a user password](../../../features/6.0/reset-user-password.md)).

## Administrator accounts

- No installer of Exponential 6 leaves the old default password in place
  ([The default administrator password](../../default-administrator-password.md)).
- Give every administrator a personal account and retire the shared `admin` sign-in, so the audit trail shows who
  did what ([Change the admin password](../../change-the-admin-password.md#account-hygiene)).
- Keep the number of administrators small; give others roles with only what they need
  ([Access reviews](access-reviews.md)).
- Consider a separate host name or network limit for the administration
  ([Restricting the administration](../../hosting/security/restricting-the-administration.md)).

## Keeping the account list tidy

**Users > Check user accounts** (`user/hygiene`) lists accounts that never signed in, have not been seen for a
number of days (180 by default), are disabled, locked, orphaned, or share a login or e-mail address. Selected
accounts can be disabled, enabled or unlocked in one step, and the list exported as CSV. Nothing on the page removes
an account; an optional **Deactivate and move** setting moves disabled accounts into one hidden group without roles
([Check user accounts](../../../features/6.0/user-hygiene.md)).

Do this at least once a year, and whenever someone leaves: [Access reviews](access-reviews.md) has the steps.

## A policy text to adapt

```text
ACCOUNT AND PASSWORD POLICY - [Organization name]   Version [x], [date]

1. Every person has their own account. Shared accounts are not allowed.
2. Passwords have at least [15] characters. The site refuses common passwords.
3. Two-step sign-in is required for [administrators and editors / everyone].
4. Administrator rights are held by at most [number] named people: [names or roles].
5. When someone leaves, [role] disables their account on their last working day.
6. Accounts not used for [180] days are reviewed and disabled if not needed.
7. A lost phone for two-step sign-in is reported to [contact]; identity is checked by [method].
8. Suspected misuse is reported to [contact] at once.
Approved by: [name, role]   Next review: [date]
```

## Related pages

- [Accounts and sign-in](../../accounts-and-sign-in.md)
- [Two-step sign-in for hosts](../../hosting/security/two-factor-sign-in.md)
- [Security and audit guide](../../../guides/security-and-audit.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
