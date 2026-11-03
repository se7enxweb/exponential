---
title: "Offboard someone who leaves"
audience: admin, hosting
category: administration
order: 11
summary: Close the access of someone who leaves the same day from the console - disable the account so every session ends, check the sessions, two-step sign-in and roles, record what happened, and remove the account later only when nothing refers to it.
keywords: offboarding, someone leaves, employee leaves, close access, disable account, end sessions, exp:user:disable, exp:user:sessions, exp:user:roles unassign, exp:user:audit, remove account later, account taken over
type: help
---
# Offboard someone who leaves

Disable the account the same day. Disabling keeps the name in the history of everything the person did.

## What you'll have at the end

An account that cannot sign in, with no session left, and a record of its last actions.

## Steps

1. Look at the account.

   ```bash
   ./console exp:user:show jane
   ```

   The output lists the groups, roles, active sessions and whether the account is protected.
2. Disable the account.

   ```bash
   ./console exp:user:disable jane --apply
   ```

   The account cannot sign in. All its sessions end.
3. Check that no session is left.

   ```bash
   ./console exp:user:sessions jane
   ```

4. Remove the passkeys of the account.

   ```bash
   ./console exp:user:2fa jane --remove-passkeys --apply
   ```

5. Remove roles that were assigned to the account itself.

   ```bash
   ./console exp:user:roles list jane
   ./console exp:user:roles unassign Administrator jane --apply
   ```

   Roles that come from a group stay with the group.
6. Keep a copy of the account's recent history, if your policy asks for one.

   ```bash
   ./console exp:user:audit jane --from=2026-01-01 --format=json > var/tmp/jane-audit.json
   ```

   **Data protection:** the file holds personal data. Store it where only the people who need it can read it.

## Result

The person has no access. The content the person created stays, with the name.

## Later

Remove the account only when nothing refers to it any more, for example after the retention period of your
organization ([Disable, enable and remove accounts](disable-enable-and-remove-accounts.md#remove-an-account)).

When the account must also lose the roles of its groups, use **Deactivate and move** in the administration
([Disable or "Deactivate and move"?](disable-enable-and-remove-accounts.md#disable-or-deactivate-and-move)).

## Related pages

- [Onboard a new editor](onboard-a-new-editor.md)
- [Setting up accounts for editors and administrators](../setting-up-editor-and-admin-accounts.md#when-someone-leaves)
