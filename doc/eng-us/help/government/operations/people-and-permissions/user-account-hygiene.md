---
title: User account hygiene
audience: government, admin
category: public-sector
order: 120
summary: Keeping staff and visitor accounts tidy with Users > Check user accounts - accounts never used, not seen for months, disabled, locked, orphaned, sharing a login or address - and the bulk actions, lockout settings and logins check behind it.
keywords: user hygiene, Check user accounts, dormant accounts, inactive accounts, locked accounts, orphaned accounts, shared e-mail, duplicate login, account lockout, MaxNumberOfFailedLogin, unactivated users, exp:user-login-check
type: help
---
# User account hygiene

This page is for administrators who keep the accounts of a public website in order, usually as part of a security
policy or an audit finding about dormant accounts. The main tool is **Users > Check user accounts** (`/user/hygiene`),
described in full on [Check user accounts](../../../../features/6.0/user-hygiene.md).

## Contents
1. [What to look for](#what-to-look-for)
2. [The bulk actions](#the-bulk-actions)
3. [Account lockout](#account-lockout)
4. [One account per login](#one-account-per-login)
5. [Unactivated registrations](#unactivated-registrations)
6. [A monthly routine](#a-monthly-routine)
7. [Related pages](#related-pages)

## What to look for

The page lists every account, or those below one user group, with a filter:

| Filter | Finds | Typical action |
|---|---|---|
| Never signed in | accounts created but never used | ask the owner; disable |
| Not seen for N days | signed in once, idle since (180 days by default) | disable; ask the department |
| Disabled | accounts that cannot sign in | check they are leavers; retire them |
| Locked | more failed sign-ins than allowed | unlock after checking with the person |
| Orphaned | an account row whose user object is missing, unpublished or without location | investigate before anything else |
| Shared login | logins that more than one account has | resolve, then add the unique index |
| Shared e-mail address | one address on several accounts (needs `role/assign`) | check for shared team accounts |

Each row shows the last visit, the number of sign-ins, groups and roles, own and inherited. The page needs
`user/hygiene`; give it only to account administrators, because it shows every account's name, login, state, groups
and roles ([Who may use it](../../../../features/6.0/user-hygiene.md#who-may-use-it)). It needs an SQL database; on
MongoDB it lists nothing.

## The bulk actions

**Disable**, **Enable** and **Unlock** act on ticked accounts after a confirmation that says how many will change
and why others are left out. The anonymous user, the user creator, the administrator account of a clean
installation, the account `admin` and you yourself are never changed. Each change is an audit event
(`access.user.disable`, `access.user.enable`, `access.user.unlock`). Nothing here removes an account. With
`DeactivateAndMove=enabled`, **Deactivate and move** retires accounts reversibly.

**Export the list as CSV** writes the filtered list (UTF-8 with byte order mark); the export is recorded as
`data.export.csv` with the row count, never the values. Cells that look like formulas are prefixed so a spreadsheet
shows them as text.

## Account lockout

Lockout is **off** by default: `settings/site.ini [UserSettings] MaxNumberOfFailedLogin=0`
([Security standard](../../../../standards/security.md)). Brute force is reported by the audit alerts
`brute_force` and `brute_force_user` regardless. To lock accounts after repeated failures, set a number in
`settings/override/site.ini.append.php`; consider `TrustedIPList[]` for office networks, as the standard notes
([Switch on account lockout](../../tasks/people-and-permissions/switch-on-account-lockout.md)). Locked accounts then
show under the **Locked** filter.

## One account per login

Two accounts with the same login (or logins differing only in case) cannot both sign in reliably. Check and fix:

```bash
./console exp:user-login-check --allow-root-user
./console exp:user-login-check --fix-index --allow-root-user
```

The first lists duplicates (logins shortened unless `--show-logins`); the second adds a unique index once there are
none ([Security and audit, step 7](../../../../guides/security-and-audit.md#7-one-account-per-login-2-minutes)).

## Unactivated registrations

If visitors can register, **Users > Unactivated users** lists registrations never activated; a registration older
than 30 days is marked **Old**, and **Remove all unactivated users** cleans them up, recorded as
`access.user.remove` ([Roles guide, section 7](../../../../guides/roles-and-policies.md#7-clean-up-unactivated-users)).
On a new installation open registration needs the `user/register` policy; an upgraded site keeps it open until
closed ([Close open registration](../../tasks/people-and-permissions/close-open-registration.md)).

## A monthly routine

1. **Check user accounts**, filter **Not seen for 180 days** (or your policy's period); send the list to the
   departments; disable what nobody claims.
2. Filter **Locked** and **Orphaned**; act on each.
3. Filter **Shared e-mail address**; replace shared accounts by personal ones.
4. Run `./console exp:user-login-check --allow-root-user`.
5. Keep the CSV export with your review record ([Admin access reviews](admin-access-reviews.md)).

## Related pages
- [Find accounts not used for months](../../tasks/people-and-permissions/find-accounts-not-used-for-months.md)
- [Unlock a locked account](../../tasks/people-and-permissions/unlock-a-locked-account.md)
- [Export the account list for a review](../../tasks/people-and-permissions/export-the-account-list-for-a-review.md)
- [Joiners, movers and leavers](joiners-movers-leavers.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
