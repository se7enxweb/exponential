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
| Address | The address of the latest request (`eZSys::clientIP()`), shown as its network: `203.0.113.x` for IPv4, `2001:db8:85a3::/48` for IPv6. The address of the sign-in is shown too when it differs. What is stored follows `RecordIP` (see [Addresses and data protection](#addresses-and-data-protection)); the administrator's page shows the stored address |
| Location | The approximate place of the sign-in ("Berlin, Land Berlin, Germany"), only with a local GeoIP database (`GeoIPDatabase`, see [Location](#location-from-a-local-geoip-database)) |
| Signed in | The time of the sign-in and its method: password, password and two-step code (the second step of `user2fa/verify`), REST, SOAP or WebDAV (by the entry script), single sign-on (a login handler that signs in on another page), or "before sessions were recorded"; with the second step when one was used: an authenticator app, a code by e-mail, a recovery code, a passkey |
| Signed in at | The host the sign-in was made on (`www.example.com`) and the siteaccess |
| Last active | The latest request, written at most every `TouchInterval` seconds (60) or when the address changes |
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

## Addresses and data protection

`RecordIP` says how the addresses of a session are stored:

| Value | Stored |
|---|---|
| `audit` (default) | As the audit stores them: none with audit.ini `[UserAuditSettings] RecordIP=disabled` or `[AuditPrivacySettings] Field[actor.ip]=off`, the network with `truncate` (the default there) or `hash`, the full address with `full` |
| `full` | The full address |
| `truncated` | The network only: `203.0.113.x`, `2001:db8:85a3::/48` |
| `disabled` | No address; the page says "Not recorded" |

The place (below) is looked up from the full address in memory before it is shortened; the full address is then not
kept.

## Location from a local GeoIP database

With `GeoIPDatabase` set to a database file in the MaxMind DB format (`.mmdb`, for example GeoLite2 City or Country,
or DB-IP Lite), relative to the installation or absolute, and a reader installed (the PHP extension `maxminddb` or the
Composer package `maxmind-db/reader`, both provide `MaxMind\Db\Reader`), every sign-in keeps its country and an
approximate place. No other server is ever asked, and addresses of private and reserved networks are not looked up.
`GeoIPLanguage` picks the language of the names (English when a name is not in it). Without a database nothing is
looked up and nothing changes.

A sign-in from a country the account has not signed in from within `RetentionDays` is a **new location**: the audit
event `access.session.new_location` and the same e-mail as a new device (one mail when both are new). The first
sign-in with a country is not new.

## New sign-in e-mail

When a sign-in comes from a combination of browser, system and kind of device that the account has not used within
`RetentionDays`, or from a new location, the user gets **New sign-in to your account** (template
`design:user/new_signin_mail.tpl`, mail category `security`): the device, the time, the approximate place and
address, and the links to Your devices and to the security activity ("Secure your account"). The very first sign-in
of an account is not new: there is nothing to compare it with (`FirstSignInMail=enabled` changes that).

- On by default (`NewDeviceMail=enabled`). Each user turns it off or on with the box **E-mail me when my account
  signs in from a new device or browser** on Your devices; the choice is the user preference
  `expsession_signin_mail` (`0` off). The page **E-mail preferences** points there.
- `NewDeviceAdminMail=enabled` also sends a note to `AdminMailReceivers[]` (else `[MailSettings] AdminEmail`), category
  `admin`, template `design:user/new_signin_admin_mail.tpl`, with the link to the user's sessions page.
- The mail goes through the mail gate like every other mail; with `[MailSettings] Transport=file` it is written to
  `var/log/mail` and never sent.

## Security activity

**Security activity** (`user/security`) is the account's "recent security activity": a timeline of its security
events, newest first, with the sign-ins, failed attempts and account changes of the last 30 days on top. Every
signed-in user has it for the own account; `user/security/<user id>` shows another account to a viewer with the policy
`user/sessions`. It is linked from Your devices, the account page (the card **Security activity**, with the failed
sign-ins of the last 30 days), the user menu of the header and the "Current user" box. Read only.

| Shown | Events |
|---|---|
| Sign-ins and sessions | `access.session.login`, `.login.failed`, `.logout`, `.revoke`, `.new_device`, `.new_location`, `.reauth`, `.reauth.failed`, `access.passkey.use`, `.use.failed`, `access.user.second_factor.refused`, `access.user.two_factor.verify.failed` |
| Account changes | `access.user.lock`, `.unlock`, `.password.change`, `.password.change.failed`, `.password.reset`, `.password.reset.request`, `.email.change`, `.login.change`, `.two_factor.enroll`, `.two_factor.remove`, `.two_factor.recovery_used`, `.two_factor.recovery_issued`, `.app_password.create`, `.app_password.revoke`, `.enable`, `.disable`, `access.passkey.add`, `.remove` |
| Needs attention | The failures, refusals, locks, new devices and places, and the changes of password, e-mail, two-step sign-in and passkeys |

Nothing is recorded for the page: it reads the [user audit log](user-audit.md) (`expuser_audit`), which the audit's
sink `useraudit` fills from the same events (`expUserSecurityActivity`). Without that table, and on MongoDB, the page
says so. The events are kept for `audit.ini [UserAuditSettings] RetentionDays` (730). Each row shows the time, who
acted (the user, an administrator by login, or nobody signed in for a failed attempt), the approximate address, the
host and what the event names (method, browser, system, place, number of sessions, codes left); never a secret.

The two-step events `access.user.two_factor.*` are recorded by the two-step extension through
`expSignInSecondFactor::audit()`.

## Settings: site.ini [UserSessionSettings]

| Setting | Default | Meaning |
|---|---|---|
| `Tracking` | `enabled` | `disabled`: no rows are written, no session is checked, the page says so |
| `TouchInterval` | `60` | Seconds between two writes of a session's last use (or when the address changes) |
| `RecordIP` | `audit` | How addresses are stored: `audit`, `full`, `truncated`, `disabled` |
| `GeoIPDatabase` | (empty) | A local `.mmdb` database for the place of a sign-in; empty: none |
| `GeoIPLanguage` | `en` | The language of the place names |
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
| `access.session.new_location` | A sign-in from a new country (only with a GeoIP database): country, place, method |

## Retention: the cronjob usersessions.php

In the part `infrequent` and on its own (`php runcronjobs.php usersessions`):

- a session not used for `[Session] SessionTimeout` is ended as **expired** (the server has forgotten it too), unless
  it is kept by Remember me: such a session stays until its Remember me date (`[Session] RememberMeTimeout`, 360 days);
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

## The table expuser_session_info

One row per session, keyed by `session_id` (the row of `expuser_session`): `user_id`, `host`, `country` (ISO code),
`location`, `auth_detail` (`totp`, `email`, `recovery`, `passkey`, `oauth`); an index on `user_id`. A table of its
own, so an installation that already has `expuser_session` goes on tracking until it is created: the same update files
and the same script (`createusersessiontable.php`) create it. Its rows are removed with their sessions.

## Developers

- `expUserSessionTracker::setMethodHint( 'single_sign_on' )` before `eZUser::setCurrentlyLoggedInUser()` names the
  method of a sign-in a login handler makes; a second argument names the second step
  (`setMethodHint( 'two_factor', 'totp' )`). The hint counts only in the request that set it, so a persistent worker
  (Velocity) never hands it on.
- `expUserSessionTracker::exportForUser( $userID )` (every row with its details, without the token's hash) and
  `eraseForUser( $userID )` serve a data export and an erasure.
- `expUserGeoIP::lookup( $ip )`, `fromRecord()`; `expUserGeoIP::setLookup()` replaces the database in tests.
- `expUserSessionTracker::revoke( $userID, $sessionID, $actorID )`, `revokeOthers()`, `revokeAll()`,
  `endOtherRows( $userID, $reason, $actorID )`, `listForUser( $userID )`, `cleanup()`.
- `expUserAgent::parse()`, `label()`, `approximateIP()`: plain functions without state.
- Tests: `tests/tests/kernel/classes/user/expUserSessionTest.php`, `expUserSessionInfoTest.php`.
