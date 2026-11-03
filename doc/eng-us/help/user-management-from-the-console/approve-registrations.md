---
title: "Approve registrations"
audience: admin, developer, hosting
category: administration
order: 5
summary: exp:user:pending lists the registrations that wait for the e-mail address, an administrator or an approval item; exp:user:approve approves or rejects one, and exp:user:resend-activation sends the activation mail again with a new key.
keywords: exp:user:pending, exp:user:approve, exp:user:resend-activation, pending registration, waiting registration, unactivated users, approve account, reject account, activation mail, resend activation, registration approval, approval item, workflow, spam accounts
type: help
---
# Approve registrations

A **pending registration** is an account whose registration is not finished. It cannot sign in until it is.

## What you'll have at the end

Every waiting registration approved, rejected or reminded.

## See what waits

```bash
./console exp:user:pending
./console exp:user:pending --search=example.org --limit=100
```

The State column says what each registration waits for:

| State | Waits for |
|---|---|
| `email` | the person to confirm the e-mail address (the account has an activation key) |
| `administrator` | an administrator to activate the account |
| `workflow` | an approver to accept the approval item of the account's publish |

The list is the newest first. `exp:user:pending` is the same as `exp:user:list --state=pending`.

## Approve or reject one

```bash
./console exp:user:approve member1
./console exp:user:approve member1 --apply
./console exp:user:approve spam-account --reject --apply
```

Approving activates the account as **Users > Unactivated users** does: the registration finishes, the account is
published and the welcome mail is sent. For `workflow`, the approval item is accepted and its workflow continues at
once.

`--reject` denies the approval item and removes the account for good, as the reject link of the registration mail
does. An account that is already active is never removed by `--reject`.

The audit records `access.registration.approve` or `access.registration.reject`.

## Send the activation mail again

```bash
./console exp:user:resend-activation member1
./console exp:user:resend-activation member1 --apply
```

The command sends the activation mail again, with a new activation key, to an account that never activated. The old
link stops working. Use it when the first mail was lost or its link expired.

To activate the account without the person, use `exp:user:approve` instead.

## Related pages

- [Approve a backlog of registrations](approve-a-backlog-of-registrations.md): many at once.
- [Approval of new accounts](../../bc/6.0/user-account-approval.md): how an installation holds new accounts back.
- [Approving new editor accounts](../../guides/approving-new-editor-accounts.md): the same in the administration.
