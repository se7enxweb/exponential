---
title: Ending access
audience: government, admin
category: public-sector
order: 70
summary: How to end a contractor's or partner's access in Exponential when a contract ends: disable accounts, or deactivate and move them (opt-in), password expiry with ezmbpaex as a safety net, what happens to their content, and why removing user accounts is final.
keywords: offboarding, end access, disable account, Deactivate and move, DeactivateAndMove, ezmbpaex, password expiry, remove user, contractors, contract end
type: help
---
# Ending access

This page is for administrators who close access at the end of a contract. Exponential has no expiry date for accounts;
access ends when an administrator acts, so tie this to your contract register.

## Contents

1. [Disable](#disable)
2. [Deactivate and move](#deactivate-and-move)
3. [Password expiry as a safety net](#password-expiry-as-a-safety-net)
4. [Their content](#their-content)
5. [Why not remove the account](#why-not-remove-the-account)

## Disable

**Users > Check user accounts**, tick the accounts, **Disable**, **Confirm**: they cannot sign in and their sessions
end ([Check user accounts](../../../../features/6.0/user-hygiene.md)). Do it on the contract's last day.

## Deactivate and move

With `site.ini [UserSettings] DeactivateAndMove=enabled` (off by default), **Deactivate and move** disables accounts and
brings their locations together in one hidden user group without roles, remembering where they were; **Reactivate and
move back** reverses it. Nothing is removed. This keeps the external groups clean of former contractors.

## Password expiry as a safety net

The `ezmbpaex` extension sets how long a password is valid (`mbpaex.ini [mbpaexSettings] DefaultPasswordLifeTime`, in
days, per group or user possible) and can warn by mail before expiry; after expiry the user must set a new password at
the next sign-in ([ezmbpaex](../../../../features/6.0/extensions/ezmbpaex.md)). It does not end access by itself (the
person can set a new password), but it limits how long a leaked password stays useful.

## Their content

Content contractors created stays, with their name as creator in the versions. If an object must change owner, the
`ezownerchange` extension does it for one object or a whole subtree ([Changing owners](../../../../guides/changing-owners.md)).

## Why not remove the account

A user object never goes to the trash in Exponential: removing it is final. Disabling or deactivating keeps the record
of who did what. Remove an account only when your retention rules require it, one account at a time, after the
retention of its records.

## Related pages

- [Reviewing stale accounts](reviewing-stale-accounts.md)
- [Contractor activity and the audit trail](contractor-activity-and-the-audit-trail.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
