---
title: Switch on account lockout
audience: government, admin, hosting
category: public-sector
order: 220
summary: Step by step - lock accounts after a number of failed sign-ins with MaxNumberOfFailedLogin, exempt trusted office networks, decide what users are told, and plan how locked staff get unlocked.
keywords: account lockout, failed logins, MaxNumberOfFailedLogin, TrustedIPList, ShowMessageIfExceeded, brute force, password guessing, security policy, sign-in protection
type: help
---
# Switch on account lockout

This task is for administrators whose security policy requires accounts to be locked after repeated failed
sign-ins. Lockout is **off** by default: `settings/site.ini [UserSettings] MaxNumberOfFailedLogin=0`. The security
standard lists this as a known gap: brute force is detected by the audit alerts, not throttled by the kernel
([Security standard, "Known gaps"](../../../../standards/security.md#known-gaps)).

## Contents
1. [Before you start](#before-you-start)
2. [The settings](#the-settings)
3. [Steps](#steps)
4. [Check it worked](#check-it-worked)
5. [Pitfalls](#pitfalls)
6. [Related pages](#related-pages)

## Before you start

- Shell access, or your deployment process if settings are managed by deployment.
- A service-desk procedure to unlock staff ([Unlock a locked account](unlock-a-locked-account.md)).
- The networks of your offices, if they should be exempt.

## The settings

All in `site.ini [UserSettings]`:

| Setting | Shipped | Meaning |
|---|---|---|
| `MaxNumberOfFailedLogin` | `0` | number of failed attempts after which sign-in is denied; `0` switches it off |
| `TrustedIPList[]` | empty | addresses or networks that are always allowed to sign in after failed attempts |
| `ShowMessageIfExceeded` | `false` | `true`: the user is told the limit was exceeded |

## Steps

1. Add to `settings/override/site.ini.append.php`, with your values:

   ```ini
   [UserSettings]
   MaxNumberOfFailedLogin=5
   TrustedIPList[]
   TrustedIPList[]=192.0.2.0/24
   ShowMessageIfExceeded=false
   ```

2. Clear the INI cache and, on Velocity, deploy:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini --allow-root-user
   ./console exp:velocity deploy --dry-run --allow-root-user
   ```

3. Read the effective value:

   ```bash
   ./console exp:ini get 'site.ini/UserSettings/MaxNumberOfFailedLogin' --allow-root-user
   ```

## Check it worked

- With a test account, fail the sign-in more times than the limit from an address outside `TrustedIPList`: the
  account appears under **Locked** on **Users > Check user accounts**.
- **Unlock** it there.

## Pitfalls

- A lockout lets anyone who knows a login lock that person out. A trusted network list and a quick unlock procedure
  limit the harm; rate limiting in front of the site is another measure
  ([Security hardening](../../../security-hardening.md#a-shield-in-front-request-shield)).
- Wrong two-step codes count as failed logins too.
- `ShowMessageIfExceeded=true` tells an attacker the account exists and is locked; most public bodies leave it off.

## Related pages
- [Unlock a locked account](unlock-a-locked-account.md)
- [User account hygiene](../../operations/people-and-permissions/user-account-hygiene.md)
- [Restricting the administration (hosting)](../../../hosting/security/restricting-the-administration.md)
- [Security hardening](../../../security-hardening.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
