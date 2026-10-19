---
title: Unlock a locked account
audience: government, admin
category: public-sector
order: 210
summary: Step by step - find out why an account is locked, check the failed sign-ins in the audit trail, and unlock it on Check user accounts or the account settings, without unlocking an attacker.
keywords: locked account, unlock, failed logins, failed sign-ins, MaxNumberOfFailedLogin, Reset failed logins, Unlock, brute force, Check user accounts
type: help
---
# Unlock a locked account

This task is for service-desk staff and administrators. An account is locked when it has more failed sign-ins than
`site.ini [UserSettings] MaxNumberOfFailedLogin` allows. Lockout is off by default (the value is `0`), so this only
happens where it was switched on ([Switch on account lockout](switch-on-account-lockout.md)).

## Contents
1. [Before you start](#before-you-start)
2. [Look before you unlock](#look-before-you-unlock)
3. [Unlock](#unlock)
4. [Check it worked](#check-it-worked)
5. [Related pages](#related-pages)

## Before you start

- `user/hygiene` for the bulk page, or the right to edit the account's settings; `audit/read` on `access` to look
  at the failed sign-ins.
- Verify the person asking is the account's owner.

## Look before you unlock

1. **Users > Check user accounts**, filter **Locked**: the account is listed.
2. Look at the failed sign-ins: in the **Audit** tab, **Console**, channel `access`, search for the account; or

   ```bash
   ./console exp:audit search --name='access.*' --query=<login> --limit=20 --allow-root-user
   ```

3. Many failures from addresses the person does not recognize mean an attack, not a forgotten password: change the
   password before unlocking, and see [Suspected account compromise](../../operations/incidents/suspected-account-compromise.md).
   Audit alerts `brute_force_user` (10 failures for one account in 15 minutes) may already have fired.

Wrong two-step codes also count as failed logins of the account.

## Unlock

Either:

- **Users > Check user accounts**: tick the account, **Unlock**, **Confirm**. Recorded as `access.user.unlock`.
- Or on the account page (`/user/edit/<id>`), **Account settings**, reset failed sign-ins.

If the person also forgot the password, follow [Reset a staff password](reset-a-staff-password.md).

## Check it worked

- The account is no longer under **Locked**.
- The person signs in.

## Related pages
- [Switch on account lockout](switch-on-account-lockout.md)
- [User account hygiene](../../operations/people-and-permissions/user-account-hygiene.md)
- [Audit alerts](../../operations/monitoring/audit-alerts.md)
- [Check user accounts](../../../../features/6.0/user-hygiene.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
