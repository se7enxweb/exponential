---
title: Account lockout
audience: government, admin
category: public-sector
order: 190
summary: For administrators of public sites - Exponential's account lockout after failed sign-ins is off by default; how to enable it, how to keep your own network out of it, the trade-off with deliberate lockouts, and how to unlock accounts.
keywords: account lockout, failed logins, MaxNumberOfFailedLogin, TrustedIPList, ShowMessageIfExceeded, brute force, credential stuffing, unlock, reset failed sign-ins, Check user accounts, two-step sign-in, public sector
type: help
---
# Account lockout

This page is for administrators. Exponential can lock an account after a number of failed sign-ins, but **the lockout is
off by default** (`settings/site.ini [UserSettings] MaxNumberOfFailedLogin=0`). Brute force is detected by the audit's
alert rules either way; the kernel does not throttle sign-ins by itself
([Security standards: Known gaps](../../../standards/security.md#known-gaps)).

## Contents

1. [The trade-off](#the-trade-off)
2. [Enabling it](#enabling-it)
3. [Unlocking accounts](#unlocking-accounts)
4. [Alternatives and complements](#alternatives-and-complements)
5. [Related pages](#related-pages)

## The trade-off

| With lockout | Without lockout |
|---|---|
| password guessing against one account stops after N tries | guessing can continue, slowed only by the web server or request-shield |
| anyone who knows a login can lock that account on purpose | no deliberate lockouts |
| locked staff need an administrator | no support calls |

Public bodies often publish staff names, which makes logins guessable; weigh both sides and record the decision.

## Enabling it

```ini
# settings/override/site.ini.append.php
[UserSettings]
MaxNumberOfFailedLogin=10
TrustedIPList[]
TrustedIPList[]=203.0.113.0/24
ShowMessageIfExceeded=false
```

- `MaxNumberOfFailedLogin`: failed attempts before the account is locked until an administrator unlocks it.
- `TrustedIPList[]`: addresses never locked out, for example your office network, so an attack from outside cannot
  lock staff out while they are in the office.
- `ShowMessageIfExceeded`: whether the user is told that the account is locked.

Behind a proxy, configure `TrustedProxies[]` first, or every visitor has the proxy's address
([13.10](../../../install/13-security-hardening.md#1310-behind-a-proxy-trusted-headers)). Wrong two-step sign-in codes
count as failed logins too. Clear the INI cache and reload PHP after the change.

## Unlocking accounts

- **One account**: open its account page (`/user/edit/<id>`); the badge says **Locked**; **Account settings** resets the
  failed sign-ins ([Security and audit, 6](../../../guides/security-and-audit.md#6-check-an-account-on-its-account-page-2-minutes)).
- **Several**: **Users > Check user accounts** (`/user/hygiene`), filter **Locked**, tick, **Unlock**, **Confirm**. Each
  unlock is recorded as `access.user.unlock` ([Check user accounts](../../../features/6.0/user-hygiene.md)).
- Before unlocking, look at the failed sign-ins in the Audit console:
  `/audit/console/(name)/access.session.login.failed`. Many failures from unknown networks mean an attack: change the
  password as well.

## Alternatives and complements

- **Two-step sign-in** makes a guessed password useless on its own (see "Two-step sign-in for public staff").
- **request-shield** slows sign-in floods and puts a browser check before the sign-in form.
- **Alert rules** `brute_force` (20 failures from one network in 5 minutes) and `brute_force_user` (10 for one account
  in 15 minutes) report attacks to the alert recipients.
- **Restricting the administration** to your network removes most guessing from the internet.

## Related pages

- [13.6 Passwords and sign-in: Failed sign-ins](../../../install/13-security-hardening.md#failed-sign-ins)
- [Check user accounts](../../../features/6.0/user-hygiene.md)
- [Audit alerts and their recipients](audit-alerts-and-recipients.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
