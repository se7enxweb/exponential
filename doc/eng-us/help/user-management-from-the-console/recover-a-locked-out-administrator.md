---
title: "Recover a locked-out administrator"
audience: admin, hosting
category: administration
order: 13
summary: Get an administrator back into the administration from the server's shell - find out why the sign-in fails, unlock the account, enable it, reset a forgotten password with a generated one, or reset two-step sign-in.
keywords: locked out, administrator locked out, cannot sign in, forgot admin password, locked account, too many failed sign-ins, exp:user:unlock, exp:user:reset-password --generate, exp:user:enable, exp:user:2fa --reset, recover admin access, server shell
type: help
---
# Recover a locked-out administrator

When nobody can sign in to the administration, the console on the server still works. Find out why the sign-in
fails, then fix only that.

## What you'll have at the end

An administrator who can sign in again.

## Before you start

- Shell access to the server, in the installation root.
- The login of the administrator, here `admin`.

## Steps

1. Look at the account.

   ```bash
   ./console exp:user:show admin
   ```

   The State line says whether it is enabled, disabled or locked. The Two-step line shows the second step.
2. If the account is locked, unlock it.

   ```bash
   ./console exp:user:unlock admin --apply
   ```

3. If the account is disabled, enable it.

   ```bash
   ./console exp:user:enable admin --apply
   ```

4. If the password is forgotten, set a generated one.

   ```bash
   ./console exp:user:reset-password admin --generate --require-change --apply
   ```

   The password is printed once. The next sign-in asks for a new one.
5. If the phone with the authenticator app is lost, reset the second step.

   ```bash
   ./console exp:user:2fa admin --reset --apply
   ```

6. Sign in to the administration.

## Result

The administrator is back in. The audit records each step, with the user who acted.

**Note:** In demo mode (`EXP_DEMO_MODE=1`) an administrator's password is never changed.

## If it still fails

- Check that the password works without a browser:
  `./console exp:user:check-login admin --password-stdin`, then type the password and press Enter.
- No administrator is left at all: create one with `exp:user:create --group="Administrator users"` and check it
  with `exp:user:whoami --as=<login>`.
- [When a command fails](when-a-command-fails.md)

## Related pages

- [Sessions and sign-in security](sessions-and-sign-in-security.md)
- [Change the admin password](../change-the-admin-password.md)
