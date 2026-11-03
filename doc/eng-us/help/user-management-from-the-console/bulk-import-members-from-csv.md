---
title: "Bulk-import members from CSV"
audience: admin, hosting
category: administration
order: 15
summary: Create many member accounts from a spreadsheet with exp:user:import - prepare the CSV without passwords, check every row in a dry run, import with the activation mail or as disabled accounts, and deal with skipped and refused rows.
keywords: import members, bulk import, CSV import, spreadsheet, exp:user:import, --send-activation, --disabled, dry run, would_create, skipped, refused, existing logins, groups, DefaultUserPlacement, migrate members, membership list
type: help
---
# Bulk-import members from CSV

Each row becomes an account, created exactly like `exp:user:create`. No password travels in the file.

## What you'll have at the end

The members of your list as accounts in the right groups, each with an activation mail or disabled until you set
a password.

## Before you start

- The list as a spreadsheet, with one row per person.
- The name or node id of the group the members belong to, here `Members`.

## Steps

1. Save the list as a CSV file in UTF-8, with this header row:

   ```text
   login,email,first_name,last_name,groups
   jdoe,jane.doe@example.com,Jane,Doe,Members
   rroe,richard.roe@example.com,Richard,Roe,
   ```

   `groups` may stay empty; then `--group` decides. Several groups are separated by `|`.
2. Remove any password column.
   A file with a password column is refused.
3. Copy the file to the server, for example to `var/tmp/members.csv`.
4. Run the import as a dry run.

   ```bash
   ./console exp:user:import var/tmp/members.csv --group=Members --send-activation
   ```

   Every row is checked. Each row shows `would_create`, `skipped` (the login exists) or `refused` with the reasons.
5. Fix the refused rows in the file, and run the dry run again.
6. Run the import with `--apply`.

   ```bash
   ./console exp:user:import var/tmp/members.csv --group=Members --send-activation --apply
   ```

   Each person gets the activation mail.
7. **Data protection:** The file holds names and e-mail addresses.
   Delete it from the server when the import is done.

## Result

`exp:user:list --group=Members --state=unactivated` lists the new accounts until the people activate them.

## Variants

- **No mail now**: use `--disabled` instead of `--send-activation`. When the members may start, enable the accounts
  with `exp:user:bulk enable`, then send each person the reset link with `exp:user:reset-password --send-link`.
- **JSON instead of CSV**: an array of objects with the keys `login`, `email`, `first_name`, `last_name`, `groups`,
  and `--type=json`.
- **A large list**: split it into files of a few hundred rows. A run that stops can be repeated: existing logins
  are skipped.

## Related pages

- [Import, export and bulk changes](import-export-and-bulk-changes.md#import-accounts)
- [Approve registrations](approve-registrations.md): reminders with `exp:user:resend-activation`.
