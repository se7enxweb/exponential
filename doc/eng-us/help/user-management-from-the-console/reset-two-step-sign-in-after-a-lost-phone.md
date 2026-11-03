---
title: "Reset two-step sign-in after a lost phone"
audience: admin, hosting
category: administration
order: 14
summary: Help someone who lost the phone with the authenticator app - confirm who is asking, reset the second step and the failed sign-ins with exp:user:2fa --reset, end the old sessions, and let the person set up the app again.
keywords: lost phone, new phone, authenticator app, two-step sign-in, 2FA reset, TOTP, exp:user:2fa --reset, remove passkeys, end sessions, cannot sign in, second step, sevenx_authentication_2fa
type: help
---
# Reset two-step sign-in after a lost phone

Without the phone, the person cannot finish the sign-in. Reset the second step so the person can sign in with the
password and set up the app again.

## What you'll have at the end

A person who signs in again and has two-step sign-in on the new phone.

## Before you start

- The extension `sevenx_authentication_2fa` is active.
- You have confirmed who is asking, on a channel you already know: a phone number from your records, or in person.

## Steps

1. Look at the second step of the account.

   ```bash
   ./console exp:user:2fa jane
   ```

   The output shows the method, whether it is active, and the number of passkeys.
2. Run the reset as a dry run.

   ```bash
   ./console exp:user:2fa jane --reset
   ```

3. Reset the second step.

   ```bash
   ./console exp:user:2fa jane --reset --apply
   ```

   The second step is removed. The failed sign-ins are reset too.
4. If the lost phone held a passkey, remove the passkeys.

   ```bash
   ./console exp:user:2fa jane --remove-passkeys --apply
   ```

5. End the sessions the lost phone may still have.

   ```bash
   ./console exp:user:sessions jane --end --apply
   ```

6. Tell the person to sign in and set up the app again.

## Result

The person signs in with the password. Where two-step sign-in is enforced, the sign-in asks for a new setup at
once. Otherwise the person turns it on again under **My account**.

## Related pages

- [Sessions and sign-in security](sessions-and-sign-in-security.md#two-step-sign-in-and-passkeys)
- [Your account and signing in](../accounts-and-sign-in.md#lost-your-phone): what the person reads.
