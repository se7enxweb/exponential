---
title: "Disable, enable and remove accounts"
audience: admin, developer, hosting
category: administration
order: 3
summary: exp:user:disable stops an account at once and ends its sessions, exp:user:enable starts it again, exp:user:delete removes it for good; protected accounts and the last administrator are refused, and how this relates to Deactivate and move in the administration.
keywords: exp:user:disable, exp:user:enable, exp:user:delete, disable user, enable user, remove user, delete account, deactivate, deactivate and move, end sessions, protected account, last administrator, offboarding, someone leaves, account taken over
type: help
---
# Disable, enable and remove accounts

Disable an account when someone leaves or when an account may have been taken over. Remove it only when nothing
refers to it any more.

## What you'll have at the end

An account that can no longer sign in, an account that works again, or an account that is gone.

## Disable an account

```bash
./console exp:user:disable jane
./console exp:user:disable jane --apply
```

`exp:user:disable` does what **Edit user settings** does in the administration. The account is disabled, and all its
sessions end, the tracked ones under **Your devices** too. Nothing is removed: content, groups and roles stay. The
audit records `access.user.disable`.

## Enable an account again

```bash
./console exp:user:enable jane --apply
```

`exp:user:enable` refuses an account that was never activated (it still has its activation key). Approve its
registration with `exp:user:approve` instead, so the registration finishes with its mails
([Approve registrations](approve-registrations.md)).

## Remove an account

1. Check what the account did and still owns.

   ```bash
   ./console exp:user:show jane
   ./console exp:user:audit jane
   ```

2. Run the command without `--apply`.

   ```bash
   ./console exp:user:delete jane
   ```

   The command names the account and says whether it is protected.
3. **Attention:** User accounts never go to the trash. The account, its settings and its sessions are gone for good.
   Run the command with `--apply`.

   ```bash
   ./console exp:user:delete jane --apply
   ```

The content the person created stays, with the name of the removed account.

## Protected accounts

`exp:user:disable` and `exp:user:delete` refuse these accounts, with exit code 1:

| Account | Why |
|---|---|
| the anonymous user (`[UserSettings] AnonymousUserID`) | every visitor is this user |
| the user of `[UserSettings] UserCreatorID` | the kernel creates accounts as this user |
| the administrator account (id 14, or the login `admin`) | the account of a new installation |
| the user who acts (`--as`) | you do not lock yourself out |
| the last enabled administrator | an account with every right must remain |

`exp:user:show` says whether an account is protected and why. In JSON the key `refusal` names the reason
(`anonymous`, `creator`, `admin`, `self`, `last_administrator`).

## Disable or "Deactivate and move"?

The administration has a second way to retire an account: **Deactivate and move**
([Check user accounts](../../features/6.0/user-hygiene.md#deactivate-and-move)). It is off by default
(`[UserSettings] DeactivateAndMove`). It disables the account and also moves all its locations into a hidden group
without roles, and remembers where they were.

| | `exp:user:disable` | Deactivate and move |
|---|---|---|
| Account can sign in | no | no |
| Sessions end | yes | yes |
| Groups and locations | stay as they are | moved into the group for deactivated accounts |
| Roles from groups | stay assigned | lost while in that group |
| Undo | `exp:user:enable` | **Reactivate and move back** in the administration |

The console only disables; it never moves an account. Use **Deactivate and move** in the administration when an
account must also lose the roles of its groups. An account moved there is enabled again only with **Reactivate and
move back**, so that it returns to its groups.

## Related pages

- [Offboard someone who leaves](offboard-someone-who-leaves.md): the whole procedure.
- [Bulk changes](import-export-and-bulk-changes.md#bulk-changes): disable many accounts at once.
- [Managing members](../managing-members.md): the same for members in the administration.
