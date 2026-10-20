---
title: Account hygiene
audience: government, admin
category: public-sector
order: 60
summary: How to keep the accounts of an Exponential site tidy with Users > Check user accounts - never signed in, not seen for N days, disabled, locked, orphaned, shared logins and addresses - with bulk disable, enable and unlock, a CSV export and a monthly routine.
keywords: account hygiene, Check user accounts, user/hygiene, never signed in, not seen, disabled, locked, orphaned, shared e-mail, disable, enable, unlock, export CSV, inactive accounts
type: help
---
# Account hygiene

This page is for administrators who look after the accounts of a public-sector site. Unused accounts are a risk (an
old password reused elsewhere) and personal data without a purpose. **Users > Check user accounts** (`user/hygiene`)
finds them and acts on many at once. Source: [Check user accounts](../../../../features/6.0/user-hygiene.md).

## Contents

1. [The page](#the-page)
2. [The filters](#the-filters)
3. [The actions](#the-actions)
4. [A monthly routine](#a-monthly-routine)
5. [Who may use the page](#who-may-use-the-page)

## The page

Open **Users > Check user accounts**, also a tile of the Setup overview under *Users and messages*. It lists every
account, or those below a user group, with name, login, e-mail address (only with the right to see addresses), states,
last visit with the days since, number of sign-ins, groups and roles. Search, page sizes of 25, 50 and 100, and
ordering by name, login, address, last visit or sign-ins narrow the list. **Export the list as CSV** sends the whole
filtered list.

## The filters

| Filter | Shows |
|---|---|
| All accounts | every account whose object is not in the trash |
| Never signed in | accounts that never signed in (not the anonymous user) |
| Not seen for N days | signed in once, last visit more than N days ago (180 by default) |
| Disabled | disabled accounts; with an activation key marked *Not activated* |
| Locked | over `MaxNumberOfFailedLogin` failed sign-ins (none while the setting is 0) |
| Orphaned | an account row whose user object is missing, never published or without a location |
| Shared login | logins that another account has too (any case) |
| Shared e-mail address | addresses used by more than one account (only with `role/assign`) |

## The actions

| Action | Effect |
|---|---|
| **Disable** | the ticked accounts can no longer sign in; reversible |
| **Enable** | the reverse |
| **Unlock** | resets failed sign-ins of locked accounts |
| **Deactivate and move** (opt-in) | disables and moves accounts into one hidden group without roles, remembering where they were ([Deactivation and removal](deactivation-and-removal.md)) |

Each button shows a confirmation with how many accounts change and how many are left out, and why. Nothing on this
page removes an account: removing a user object is final and is done one account at a time with the node's own
**Remove**.

## A monthly routine

- [ ] Filter **Never signed in**, older than your registration period: remove those registrations or disable them.
- [ ] Filter **Not seen for N days** with your retention period (for example 365 for public accounts, an example
  assumption): disable, warn by mail if your policy says so, then remove after a further period.
- [ ] Filter **Locked**: unlock legitimate users after checking; investigate patterns.
- [ ] Filter **Shared e-mail address**: merge or clean duplicates.
- [ ] For staff groups: compare with the list of people who left.

Unactivated registrations are on their own page, **Users > Unactivated users** ([Registration](registration.md#unactivated-registrations)).

## Who may use the page

The page needs the user management rights described in [Check user accounts](../../../../features/6.0/user-hygiene.md);
addresses are shown only with `role/assign`. Keep this to a small group: the list is a directory of everyone who has an
account.

## Related pages

- [Citizen accounts](README.md)
- [Deactivation and removal](deactivation-and-removal.md)
- [Roles and policies](../../../../guides/roles-and-policies.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
