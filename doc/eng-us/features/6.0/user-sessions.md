# Your devices: signed-in sessions per user

Since 6.0.15 every sign-in is recorded as a session of its user. A user sees where the account is signed in right
now, on the page **Your devices** (`user/sessions`), and signs out one session or all other sessions; an
administrator with the policy `user/sessions` does the same for any user (`user/sessions/<user id>`). A sign-in from
a device the account has not used lately sends the user an e-mail. Retention runs as a cronjob, and every sign-out
from the page is recorded in the audit trail.

The guide for users and administrators is [Your devices and sessions](../../guides/your-devices-and-sessions.md).
The page about the session handler and the session table of the administration is
[Sessions](../../guides/sessions.md).

## What a session shows

| Shown | Where it comes from |
|---|---|
| Browser and version, system and version, kind of device (computer, phone, tablet, program or app, automated client) | The `User-Agent` header of the sign-in, read by `expUserAgent::parse()`; nothing is looked up elsewhere |
| Address | The address of the latest request (`eZSys::clientIP()`), shown as its network: `203.0.113.x` for IPv4, `2001:db8:85a3::/48` for IPv6. The address of the sign-in is shown too when it differs. The full address is stored, and the administrator's page shows it |
| Signed in | The time of the sign-in and its method: password, password and two-step code (the second step of `user2fa/verify`), REST, SOAP or WebDAV (by the entry script), single sign-on (a login handler that signs in on another page), or "before sessions were recorded" |
| Last active | The latest request, written at most every `TouchInterval` seconds or when the address changes |
| Stays signed in | "Remember me, until <date>" when the sign-in ticked Remember me (`[Session] RememberMeTimeout`, 360 days, ticked by default), else "until the browser is closed" |
| This device | The session the page is looked at with; it cannot be signed out from the list (sign out instead) |

Below the running sessions, **Recently ended** lists the sessions that ended within `RetentionDays`: signed out,
signed out from the page, by an administrator, by "all other sessions", replaced by a new sign-in in the same
browser, ended by a password change, or expired.

## How a session is ended, on every session handler

A session is ended on its next request, whatever handler stores the sessions (PHP's files, the default; the database
handler; Symfony):

1. At a sign-in (`eZUser::setCurrentlyLoggedInUser()`), a random token of 32 bytes goes into the session
   (`expUserSessionToken`) and only its SHA-256 into the table. The session id itself is never stored: it changes
   (a new id at the sign-in and at the second step of two-step sign-in) and would be a key to the session.
2. On every request of a signed-in user, `eZUser::currentUser()` looks the row up by the token's hash (one query by a
   unique index, once per request). When the row is ended or gone, the request is signed out at once: the session's
   data, its id and its cookie are removed (`eZUser::logoutCurrent()`), and the browser sees the sign-in page.
3. **Remember me is the session cookie itself.** It is kept for `RememberMeTimeout` and brings the session back after
   the browser was closed. A revoked session's cookie signs nobody in again, in the browser it was set in or anywhere
   it was copied to: the session it names has no live row.
4. With the database session handler, "sign out all other sessions" also deletes the other sessions' rows of
   `ezsession` at once (`deleteOtherSessionsOfUser()`), and an administrator's "sign out everywhere" deletes them all
   (`deleteByUserIDs()`).

A session that was signed in before the table existed has no token; on its next request it gets one and a row with
the method "before sessions were recorded". From then on it is listed and can be signed out like any other.

A password change ([PasswordSettings] `EndOtherSessions`) ends the other rows of the user too (reason
`password_change`), next to the password stamp that already signs those sessions out.

Not tracked: a REST, WebDAV or SOAP request that signs in for itself only, without a session (an app password, a
personal API key): it has no cookie that could be signed out. The two-step check of those channels is unchanged:
only the web sign-in form asks for the second step.

## New sign-in e-mail

When a sign-in comes from a combination of browser, system and kind of device that the account has not used within
`RetentionDays`, the user gets **New sign-in to your account** (template `design:user/new_signin_mail.tpl`, mail
category `security`): the device, the time and the approximate address, and the link to Your devices. The very first
sign-in of an account is not new: there is nothing to compare it with (`FirstSignInMail=enabled` changes that).

- On by default (`NewDeviceMail=enabled`). Each user turns it off or on with the box **E-mail me when my account
  signs in from a new device or browser** on Your devices; the choice is the user preference
  `expsession_signin_mail` (`0` off). The page **E-mail preferences** points there.
- `NewDeviceAdminMail=enabled` also sends a note to `AdminMailReceivers[]` (else `[MailSettings] AdminEmail`), category
  `admin`, template `design:user/new_signin_admin_mail.tpl`, with the link to the user's sessions page.
- The mail goes through the mail gate like every other mail; with `[MailSettings] Transport=file` it is written to
  `var/log/mail` and never sent.

## Settings: site.ini [UserSessionSettings]

| Setting | Default | Meaning |
|---|---|---|
| `Tracking` | `enabled` | `disabled`: no rows are written, no session is checked, the page says so |
| `TouchInterval` | `300` | Seconds between two writes of a session's last use (or when the address changes) |
| `RetentionDays` | `90` | Days an ended session is kept (Recently ended, the new-device check) |
| `NewDeviceMail` | `enabled` | The user's e-mail about a sign-in from a new device |
| `NewDeviceAdminMail` | `disabled` | The same note to the administrators |
| `AdminMailReceivers[]` | (empty) | Who gets that note; empty: `[MailSettings] AdminEmail` |
| `FirstSignInMail` | `disabled` | Whether the first sign-in of an account counts as a new device |

`[Session] RememberMeTimeout` (360 days) and `SessionTimeout` (360 days) are unchanged.

## Permissions

- **Your own devices**: every signed-in user (`user/login`). The anonymous user gets "access denied".
- **Another user's sessions**: the policy function **user/sessions** ("see where other users are signed in, and sign
  them out"). The account page of a user (`user/edit/<id>`) shows the card **Sessions** to a viewer who has it.
  Administrators with all rights have it.

Every change is a POST with a token of the page's own (an HMAC of the session's token), besides the form token of
ezformtoken; after it the page redirects to itself and shows the result once.

## Audit

| Event | When |
|---|---|
| `access.session.login` | A sign-in (as before), now with `after.session`: the row id |
| `access.session.logout` | A sign-out (as before), with `after.session` |
| `access.session.revoke` | A session signed out from the page: `after.count`, `after.sessions` (row ids), `after.scope` (one, others, all), `after.by` (user, admin), `after.actor_id`; reason `user_request` or `admin` |
| `access.session.new_device` | A sign-in from a new device (the e-mail goes out): browser, system, device, method |

## Retention: the cronjob usersessions.php

In the part `infrequent` and on its own (`php runcronjobs.php usersessions`):

- a session not used for `[Session] SessionTimeout` is ended as **expired** (the server has forgotten it too);
- an ended session older than `RetentionDays` is removed;
- the rows of users that no longer exist are removed.

## The table expuser_session

One row per sign-in: `id`, `user_id`, `token_hash` (unique), `created`, `last_active`, `first_ip`, `last_ip`,
`user_agent` (at most 512 characters), `browser`, `os`, `device_type`, `method`, `remember_until`, `siteaccess`,
`ended`, `ended_by`, `end_reason`; indexes on (`user_id`, `ended`) and (`ended`, `last_active`).

- New installations get it from `share/db_schema.dba` (MySQL/MariaDB, PostgreSQL, SQLite, Oracle, MongoDB).
- An upgrade from 6.0.0: `update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql` creates it when it is missing
  (MySQL, PostgreSQL, SQLite), or, on any engine, `php update/common/scripts/6.0/createusersessiontable.php`
  (`--dry-run` says whether it is missing).
- Without the table nothing is recorded and every page works as before; the page Your devices says what to run.

## Developers

- `expUserSessionTracker::setMethodHint( 'single_sign_on' )` before `eZUser::setCurrentlyLoggedInUser()` names the
  method of a sign-in a login handler makes.
- `expUserSessionTracker::revoke( $userID, $sessionID, $actorID )`, `revokeOthers()`, `revokeAll()`,
  `endOtherRows( $userID, $reason, $actorID )`, `listForUser( $userID )`, `cleanup()`.
- `expUserAgent::parse()`, `label()`, `approximateIP()`: plain functions without state.
- Tests: `tests/tests/kernel/classes/user/expUserSessionTest.php`.
