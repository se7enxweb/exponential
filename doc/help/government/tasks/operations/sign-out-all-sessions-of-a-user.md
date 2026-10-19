---
title: Sign out all sessions of a user
audience: government, admin
category: public-sector
order: 320
summary: Step by step - end every browser session of one account (a lost laptop, a suspected compromise, a leaver), by disabling the account, changing its password, or removing its sessions on Setup > Sessions with the database session handler.
keywords: sessions, sign out, log out everywhere, Setup Sessions, ezpSessionHandlerDB, disable account, password change, EndOtherSessions, lost device, compromise
type: help
---
# Sign out all sessions of a user

This task is for administrators when one account must be signed out everywhere at once. Which way works depends on
the session handler ([Sessions](../../../../guides/sessions.md)).

## Contents
1. [Before you start](#before-you-start)
2. [Option 1: disable the account](#option-1-disable-the-account)
3. [Option 2: change the password](#option-2-change-the-password)
4. [Option 3: remove the sessions](#option-3-remove-the-sessions)
5. [Check it worked](#check-it-worked)
6. [Related pages](#related-pages)

## Before you start

- Decide whether the person may sign in again right away (lost laptop) or not (leaver, compromise).
- `user/hygiene` or the right to edit the account; an administrator account for **Setup > Sessions**.

## Option 1: disable the account

**Users > Check user accounts**, tick the account, **Disable**, **Confirm**: it can no longer sign in and its sessions
end ([Check user accounts](../../../../features/6.0/user-hygiene.md#the-actions)). Use it for leavers and suspected
compromise; **Enable** later if appropriate.

## Option 2: change the password

A password change signs out the account's other sessions on their next request, whatever session handler the site
uses ([Changing your password](../../../../features/6.0/modern-password-change.md#in-short)); with
`[PasswordSettings] EndOtherSessions=enabled` (the default). Set a new password on the account page or with
`php bin/php/resetuserpassword.php -u <login> -g --allow-root-user` ([Reset a password from the command line](../people-and-permissions/reset-a-password-from-the-command-line.md)).
Use it for a lost device: the person signs in again with the new password.

## Option 3: remove the sessions

Only with the database session handler (`site.ini [Session] Handler=ezpSessionHandlerDB`); with the default handler
**Setup > Sessions** shows who signed in recently but cannot remove sessions one by one.

1. **Setup > Sessions** (`/setup/session`), **Find a user**, **Update list**.
2. On the user's card, **Sessions**: one card per browser or device.
3. Tick them, **Remove selected**, confirm **Remove the sessions**.

Removing a session signs a browser out; it does not stop the person signing in again. Combine with option 1 or 2.

## Check it worked

- The account's sessions are gone from **Setup > Sessions** (database handler).
- The audit trail's `access` channel shows no new activity from the account, and a sign-out or disable event.

## Related pages
- [Suspected account compromise](../../operations/incidents/suspected-account-compromise.md)
- [Remove a leaver](../people-and-permissions/remove-a-leaver.md)
- [Sessions guide](../../../../guides/sessions.md)
- [Security hardening](../../../security-hardening.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
