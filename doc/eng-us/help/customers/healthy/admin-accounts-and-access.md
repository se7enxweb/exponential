---
title: Administrator accounts and access
audience: customer, admin, businesses
category: customer-success
order: 10
summary: The owner's rules for administrator accounts on an Exponential site - one account per person, few administrators, two-step sign-in, retiring the shared admin account, a tested way back in, and what to do when staff or providers change.
keywords: administrator, accounts, access, roles, policies, two-step sign-in, admin account, break-glass, offboarding, provider access
type: help
---
# Administrator accounts and access

An administrator can change everything on the site. This page sets out the rules an owner should insist on for
those accounts, and what to do when people or providers change. Roles for editors and other staff are a separate
topic: see [Roles and policies](../../../guides/roles-and-policies.md).

## The rules

| Rule | Why | Source |
|---|---|---|
| One account per person | So the audit trail says who did what | [Change the administrator password, account hygiene](../../change-the-admin-password.md) |
| Few administrators | Granting the Administrator role raises an audit alert; every extra one is a risk | [Security and audit](../../../guides/security-and-audit.md) |
| Never widen the Administrator role for a colleague | "make a new role" instead | [Security and audit](../../../guides/security-and-audit.md) |
| Two-step sign-in for administrators | The password alone is then useless to anyone else | [Two-step sign-in](../../../guides/two-factor-login.md) |
| Retire `admin` as a sign-in | "its login name is the first one an attacker tries" | [Change the administrator password](../../change-the-admin-password.md) |
| A tested way back in | So a lost password is not an outage | Same page, "break-glass procedure" |

## Retiring the shared admin account

Every installation has an account called `admin`. The account hygiene guide gives two supported ways to stop it
being used: disable it, after the setting `[UserSettings] UserCreatorID` points to another enabled administrator
account, or keep it with a long random password nobody keeps
([Change the administrator password](../../change-the-admin-password.md)). Your administrator or partner chooses
the way; your part is to ask that it is done and to receive confirmation.

## The break-glass procedure

"Keep a break-glass procedure, written down and tested", and "keep the procedure (not a password) where the people on
call can find it, and test it once a year on a copy of the site"
([Change the administrator password](../../change-the-admin-password.md)). Ask who can carry it out (usually the
host, with shell access to the server), and add that person to your
[escalation contacts](../partners-and-hosts/escalation-contacts-template.md).

## Provider accounts

Your host and partner may hold administrator accounts on your site. Agree:

- [ ] Each person at the provider has a named account, not a shared one.
- [ ] Their accounts use two-step sign-in.
- [ ] You receive the list of their accounts, and are told when it changes.
- [ ] When the contract ends, their accounts are disabled; the offboarding checklist for hosts says to "tell the
      customer which accounts were yours" ([Customer offboarding](../../hosting/checklists/customer-offboarding.md)).
- [ ] Hosting accounts, the domain and the administrator access belong to you, or are handed over on request
      ([Contracts and SLA basics](../../businesses/choosing/contracts-and-slas-basics.md)).

## When staff join or leave

| Event | Do |
|---|---|
| Someone joins | A personal account in the right group, with a role that fits the job ([Roles and policies](../../../guides/roles-and-policies.md)) |
| Someone changes job | Change the group or role; do not add rights on top of old ones |
| Someone leaves | Disable the account on the last day; check their open drafts |
| An administrator leaves | Also review who else holds the Administrator role, and whether the break-glass procedure named them |

## The quarterly review

**Users > Check user accounts** lists accounts that never signed in, have not been seen for months, are disabled or
locked, or share a login or e-mail address ([Check user accounts](../../../features/6.0/user-hygiene.md)). Ask your
administrator to go through it each quarter, together with the assignments of the Administrator role, and to send
you the changes made. A yearly, deeper version with a record to keep is in
[Access reviews](../../businesses/security/access-reviews.md).

> **Tip**
> To see what changed in access, the audit trail records role assignments, and alerts on a new administrator role
> assignment ([The audit trail](../../../features/6.0/audit-trail.md)).

## Related pages

- [Security basics](security-basics.md)
- [User accounts and passwords](../../businesses/security/user-accounts-and-passwords.md)
- [Accounts and sign-in](../../accounts-and-sign-in.md)
- [The default administrator password](../../default-administrator-password.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
