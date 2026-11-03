---
title: "Onboard a new editor"
audience: admin, hosting
category: administration
order: 10
summary: Create the account of a new editor from the console in the Editors group with a one-time password, require a new password at the first sign-in, check the roles, and hand over the sign-in safely.
keywords: onboarding, new editor, new employee, create editor account, exp:user:create, Editors group, generated password, --require-change, exp:user:require-password-change, exp:user:roles, hand over password, first sign-in
type: help
---
# Onboard a new editor

Create the account of a new editor with a password nobody typed, and make the editor choose a new one at the first
sign-in.

## What you'll have at the end

An editor account in the Editors group, with the Editor role, that asks for a new password at its first sign-in.

## Before you start

- Shell access to the server, in the installation root.
- The editor's e-mail address and name. The examples use `jane@example.com` and Jane Doe.

## Steps

1. Check that the login is free.

   ```bash
   ./console exp:user:list --search=jane
   ```

2. Run the create command as a dry run.

   ```bash
   ./console exp:user:create --login=jane --email=jane@example.com --first-name=Jane --last-name=Doe \
       --group=Editors --generate-password
   ```

   The command lists what it would create. A failed check is named.
3. Run the same command with `--apply`.
   The account is published. The generated password is printed once.
4. Require a new password at the first sign-in.

   ```bash
   ./console exp:user:require-password-change jane --apply
   ```

5. Check the groups and roles.

   ```bash
   ./console exp:user:roles list jane
   ```

   The Editor role is listed, via the Editors group.
6. Hand over the login and the password on two different channels, for example the login by e-mail and the password
   by phone.

If the command says that the publish waits, the installation approves new accounts. Approve it with
`./console exp:user:approve jane --apply`.

## Result

The editor signs in with the password, chooses a new one, and starts working.

## Variants

- **The editor chooses the password**: use `--send-activation` instead of `--generate-password`. The editor gets
  the activation mail and then uses **Forgot password**. Skip steps 4 and 6.
- **Editors for one part of the site**: create a group and limit the role to a subtree
  ([Groups and roles](groups-and-roles.md)).

## Related pages

- [Setting up accounts for editors and administrators](../setting-up-editor-and-admin-accounts.md): the same in the
  administration.
- [Offboard someone who leaves](offboard-someone-who-leaves.md)
