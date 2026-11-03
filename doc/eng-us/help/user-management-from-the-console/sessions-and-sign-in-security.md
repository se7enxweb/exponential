---
title: "Sessions and sign-in security"
audience: admin, developer, hosting
category: administration
order: 7
summary: exp:user:sessions lists and ends the signed-in sessions of an account, exp:user:unlock resets the failed sign-in counter of a locked account, and exp:user:2fa shows, resets or removes two-step sign-in and passkeys.
keywords: exp:user:sessions, exp:user:unlock, exp:user:2fa, sessions, your devices, sign out everywhere, end session, revoke session, locked account, failed sign-ins, MaxNumberOfFailedLogin, unlock, two-step sign-in, 2FA, authenticator app, lost phone, passkeys, remove passkeys, sevenx_authentication_2fa
type: help
---
# Sessions and sign-in security

These commands help when an account may have been taken over, when someone typed the password wrong too often, or
when someone lost the phone with the authenticator app.

## What you'll have at the end

An account signed out everywhere, unlocked, or with its second step reset.

## Sessions

```bash
./console exp:user:sessions jane
./console exp:user:sessions jane --end --apply
./console exp:user:sessions jane --session=8f3a21 --apply
```

Without options the command lists the account's active tracked sessions, as **Your devices** shows them: the id,
the sign-in method, the device, the browser, the approximate address and the last activity.

- `--end` ends all of them.
- `--session=<id>` ends one.

The audit records `access.session.revoke`. Sessions are tracked only where **Your devices** is switched on
([Your devices](../../features/6.0/user-sessions.md)).

**Note:** Ending sessions does not stop the person from signing in again. When an account may have been taken over,
also reset the password or disable the account.

## Unlock an account

```bash
./console exp:user:unlock jane
./console exp:user:unlock jane --apply
```

An account is locked after more wrong passwords than `[UserSettings] MaxNumberOfFailedLogin`. The command resets the
failed sign-in counter, as **Reset failed logins** does in the administration. The password stays as it is. The
audit records `access.user.unlock`.

`exp:user:list --state=locked` lists every locked account.

## Two-step sign-in and passkeys

```bash
./console exp:user:2fa jane
./console exp:user:2fa jane --reset --apply
./console exp:user:2fa jane --remove-passkeys --apply
```

Without options the command shows the state of two-step sign-in (the method, and whether it is active) and how many
passkeys the account has.

- `--reset` removes the second step, for someone who lost the phone. The failed sign-ins are reset too. Where two-step
  sign-in is enforced, the next sign-in asks for a new setup. It needs the extension `sevenx_authentication_2fa`.
- `--remove-passkeys` removes all passkeys of the account.

The audit records `access.user.two_factor.remove` and the `access.passkey.*` events.

**Attention:** Reset the second step only for the person it belongs to. Confirm who is asking on a channel you
already know, not on the channel the request came from.

## Related pages

- [Reset two-step sign-in after a lost phone](reset-two-step-sign-in-after-a-lost-phone.md)
- [Recover a locked-out administrator](recover-a-locked-out-administrator.md)
- [Your devices and sessions](../../guides/your-devices-and-sessions.md): what the person sees.
- [Your account and signing in](../accounts-and-sign-in.md#two-step-sign-in): two-step sign-in for the person.
