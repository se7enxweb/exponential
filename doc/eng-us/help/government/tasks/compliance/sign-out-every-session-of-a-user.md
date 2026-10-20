---
title: Sign out every session of a user
audience: government, admin
category: public-sector
order: 40
summary: Step by step - after a suspected account takeover or a lost device, end every session of one user in Exponential by changing the password (EndOtherSessions), disabling the account, or removing sessions under Setup > Sessions with the database session handler.
keywords: sign out, end sessions, session hijacking, account takeover, lost laptop, EndOtherSessions, password stamp, Setup Sessions, ezpSessionHandlerDB, disable account, incident, public sector
type: help
---
# Sign out every session of a user

For administrators handling a suspected takeover, a stolen laptop or a leaver who is still signed in somewhere. A
session cookie is as good as the password; this task ends them ([Sessions and cookies](../../security/sessions-and-cookies.md)).

## Contents

1. [Before you start](#before-you-start)
2. [Option 1: change the password](#option-1-change-the-password)
3. [Option 2: disable the account](#option-2-disable-the-account)
4. [Option 3: remove sessions on the Sessions page](#option-3-remove-sessions-on-the-sessions-page)
5. [Check that it worked](#check-that-it-worked)
6. [Related pages](#related-pages)

## Before you start

- Rights to edit the user (and `user/hygiene` for Check user accounts).
- Note what you do and when: this may be an incident.

## Option 1: change the password

With `[PasswordSettings] EndOtherSessions=enabled` (the default), a password change signs out every other session of
that user at its next request, with every session handler; the session in which it was changed gets a new id. Reset the
password on the user's account page (**Password**) or on the command line:

```bash
./console exp:resetuserpassword --allow-root-user -u <login> -g -l 24
```

Give the new password to the person through a separate channel. Sessions started before 6.0.15 carry no stamp and are
checked only from their next sign-in.

## Option 2: disable the account

**Users > Check user accounts**, tick the account, **Disable**, **Confirm** (or the account's settings). A disabled
account cannot sign in and its sessions end at their next request. Recorded as `access.user.disable`.

## Option 3: remove sessions on the Sessions page

Only with the database session handler (`[Session] Handler=ezpSessionHandlerDB`): **Setup > Sessions**, find the user,
tick their sessions, **Remove selected**, confirm ([Sessions](../../../../guides/sessions.md)). With the default handler
the page cannot remove sessions one by one; use option 1 or 2. Removing sessions does not block the user from signing in
again.

## Check that it worked

- In the audit, the user's next request shows no activity from the old session; `/audit/console/(user)/<id>`.
- With the database handler, Setup > Sessions lists no sessions for the user except new ones.

## Related pages

- [Sessions and cookies](../../security/sessions-and-cookies.md)
- [Incident response](../../security/incident-response.md)
- [Change the administrator password](../../../change-the-admin-password.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
