---
title: "Approve a backlog of registrations"
audience: admin, hosting
category: administration
order: 12
summary: Work through many waiting registrations from the console - list them, reject the obvious spam in bulk with a confirmed count, approve the real ones, and send the activation mail again to those who never confirmed.
keywords: backlog, many registrations, pending registrations, approve in bulk, reject in bulk, spam registrations, exp:user:pending, exp:user:bulk approve, exp:user:bulk reject, exp:user:resend-activation, --confirm, unactivated users
type: help
---
# Approve a backlog of registrations

Reject the spam first, then approve the rest. Each bulk change needs the count you saw in its dry run.

## What you'll have at the end

No registration left waiting without a decision.

## Steps

1. List the registrations that wait.

   ```bash
   ./console exp:user:pending --limit=500
   ```

   The State column says what each one waits for: `email`, `administrator` or `workflow`.
2. Find the spam with a search, for example by the domain of the addresses.

   ```bash
   ./console exp:user:bulk reject --state=pending --search=spam.example.net
   ```

   The dry run lists the accounts and their count, for example 37.
3. **Attention:** Rejecting removes the accounts for good.
   Reject them with the count of the dry run.

   ```bash
   ./console exp:user:bulk reject --state=pending --search=spam.example.net --confirm=37 --apply
   ```

4. Look at the rest.

   ```bash
   ./console exp:user:pending --limit=500
   ```

5. Approve the remaining registrations.

   ```bash
   ./console exp:user:bulk approve --state=pending
   ./console exp:user:bulk approve --state=pending --confirm=112 --apply
   ```

   Each account is approved as `exp:user:approve` would approve it: published, with its welcome mail.

If a person should confirm the address personally, send the activation mail again instead of approving:
`./console exp:user:resend-activation <user> --apply`.

## Result

`exp:user:pending` lists nothing, or only the registrations you want to leave to the people themselves.

## Related pages

- [Approve registrations](approve-registrations.md)
- [Import, export and bulk changes](import-export-and-bulk-changes.md#bulk-changes)
