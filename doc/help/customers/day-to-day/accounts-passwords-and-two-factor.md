---
title: Accounts, passwords and two-step sign-in
audience: customer, admin, editor
category: customer-success
order: 8
summary: The owner's rules for the team's accounts on an Exponential site - personal accounts, the shipped password rules, two-step sign-in as an option or a requirement, lost phones and locked accounts - and what to ask the administrator to switch on.
keywords: accounts, passwords, two-step sign-in, two-factor, 2FA, authenticator, e-mail code, password rules, locked account, shared login, site owner
type: help
---
# Accounts, passwords and two-step sign-in

This page is for the owner or manager who sets the account rules for the team. The clicks for each person are on
[Your account and signing in](../../accounts-and-sign-in.md); the setup for the administrator is
[Two-factor login](../../../guides/two-factor-login.md). Here you find what to decide and what to check.

## Contents

1. [Four rules for the team](#four-rules-for-the-team)
2. [Passwords: what the software already does](#passwords-what-the-software-already-does)
3. [Two-step sign-in: optional or required](#two-step-sign-in-optional-or-required)
4. [When something goes wrong](#when-something-goes-wrong)
5. [What to check every quarter](#what-to-check-every-quarter)

## Four rules for the team

1. **One personal account per person.** No shared logins; the account review page finds them with the filter
   **Shared login** ([Check user accounts](../../../features/6.0/user-hygiene.md)).
2. **A real e-mail address on every account.** Password reset, the "Your password was changed" mail and e-mail codes
   go there.
3. **Two-step sign-in for everyone who can publish**
   ([Handing the site over to editors](../../creators/website-creators/handing-over-to-editors.md)), and always for
   administrators.
4. **Accounts end on the last working day** ([Offboarding staff](offboarding-staff.md)).

## Passwords: what the software already does

| Behavior | Source |
|---|---|
| A new password must be at least 10 characters (`[UserSettings] MinPasswordLength=10`), and a weak-password check is on (`WeakPasswordCheck=enabled`) | `settings/site.ini` |
| The change page lists the requirements next to the field, can generate a strong password, and never writes a typed password back into the page | [Changing your password](../../../features/6.0/modern-password-change.md) |
| After a change the person's other sessions are signed out, and a "Your password was changed" mail goes to the account's address; nobody can switch that mail off | same |
| The same rules apply to registration, editing an account and the console reset | same |
| Every change and refused change is in the audit trail; the password never is | same |

`[UserSettings] MaxNumberOfFailedLogin` locks an account after that many failed sign-ins; the shipped value is `0`,
which means no lock (`settings/site.ini`). Ask your administrator whether your site sets a limit.

## Two-step sign-in: optional or required

Two-step sign-in comes with the extension `sevenx_authentication_2fa`: after the password a 6-digit code from an
authenticator app, or a code sent by e-mail ([Two-factor login](../../../guides/two-factor-login.md)).

| Setting (`[General] Enforce2FA`) | What your team experiences |
|---|---|
| `disabled` (default) | each person turns it on under *My account* > *Two-step sign-in* |
| `enabled` with `DefaultMethod=totp` | a person without a second step sets up the app right after the password |
| `enabled` with `DefaultMethod=email` | a person without a second step gets e-mail codes straight away |

The demonstration server runs it as opt-in since 6 October 2026 (same source). If you decide to require it, ask the
administrator to **enroll the administrators first**, so nobody is caught without a phone at hand.

> **Note**
> A role made later needs the policies `user2fa/setup` and `user2fa/verify` before its users can set it up
> ([Two-factor login](../../../guides/two-factor-login.md)). Tell your administrator when you ask for a new role.

## When something goes wrong

| Situation | What to do |
|---|---|
| Someone forgot their password | they use "Forgot your password?" on the sign-in page ([Your account and signing in](../../accounts-and-sign-in.md)) |
| Someone lost their phone | an administrator sets the user's *Two-step sign-in* field to *E-mail one-time code* or *Disabled* and publishes; the person sets the app up again ([Two-factor login](../../../guides/two-factor-login.md)) |
| An account is locked after failed sign-ins | the account manager uses **Unlock** on Check user accounts ([Check user accounts](../../../features/6.0/user-hygiene.md)) |
| Nobody can sign in as administrator any more | your host or partner resets it from the command line ([Reset a user password](../../../features/6.0/reset-user-password.md)) |
| You suspect someone else used an account | disable it, then ask the administrator to look at the audit trail ([Security and audit](../../../guides/security-and-audit.md)) |

## What to check every quarter

- [ ] Check user accounts, filter **Shared login** and **Shared e-mail address**: none, or each explained.
- [ ] Filter **Locked**: each one understood.
- [ ] Every administrator has two-step sign-in on.
- [ ] The default administrator password of the installation was changed long ago
      ([Change the admin password](../../change-the-admin-password.md)).

## Related pages

- [Your account and signing in](../../accounts-and-sign-in.md)
- [User accounts and passwords](../../businesses/security/user-accounts-and-passwords.md)
- [Two-factor and sign-in for intranets](../../businesses/intranets/two-factor-and-sign-in.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
