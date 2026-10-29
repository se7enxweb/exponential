# User audit log

The user audit log answers one question for any account of the installation: **who did what to it, when, and from
where** — and who tried and was refused. It is a table in the site's database, `expuser_audit`, with one row per
security event about a user account or a user group, and a page in the administration, **Users > User audit log**
(`user/audit`), that filters it and exports it as CSV.

It is the base the account pages build on: a later "sessions" view of one account reads the same table.

## What is recorded

Every event the audit records about an account is copied into the table, whatever the interface that caused it:

| Event | When |
| --- | --- |
| `access.user.create` | an account was made (user/register, the content editor, member management, an import) |
| `access.user.change` | fields of an account's object changed (user/edit, content/edit of a user object), with the fields before and after |
| `access.user.email.change`, `access.user.login.change` | the e-mail address or the login changed |
| `access.user.password.change`, `.password.reset`, `.password.reset.request` | a password was changed or reset (never the password itself) |
| `access.user.enable`, `.disable`, `.lock`, `.unlock`, `.activate` | the account's state changed |
| `access.user.move`, `.remove` | the account moved to another group, or was removed |
| `access.user.approval.*`, `access.registration.*` | a registration waited for approval, was approved, denied or refused |
| `access.role.assign`, `access.role.unassign` | a role was assigned to the account or to a user group, or taken away |
| `access.session.login`, `.login.failed`, `.logout`, `.reauth*`, `.revoke` | sign-ins, failed sign-ins, "Verify it's you", sessions ended |
| `access.passkey.*` | passkeys added, renamed, removed, used |
| `access.permission.refused` | a view was refused while it was about an account (an editor tried to edit an administrator) |
| `*.failed`, `*.refused` | every refused or failed attempt of the events above, with its reason |

Member management ([member-management.md](../../bc/6.0/member-management.md)) is covered by the same events: an
editor creating, changing, disabling, moving or removing a member, and every refusal when the editor reaches for an
administrator or another editor.

Each row holds:

- **who**: the user id and login of the user who acted (`system` for a command or a cronjob);
- **when**: the time to the millisecond;
- **where**: the siteaccess, the host the request was sent to, the module and view, and the address of the request
  as the audit's privacy rules leave it (by default shortened to its /24 or /48 network, see below);
- **whom**: the account or the user group the event was about, by id and login;
- **what**: the event name, the result (done, refused, failed) and the reason;
- **before and after**: the fields that changed, as the audit recorded them.

Passwords, password hashes, tokens and other secrets are **never** stored: the audit does not record them, and every
member whose name looks like one is dropped once more before a row is written. E-mail addresses are stored as the
audit's privacy rules say (`audit.ini [AuditPrivacySettings] Field[email]`, hashed by default).

## How it works

The audit ([audit.md](../../bc/6.0/audit.md)) writes its records to files. The sink `useraudit`, registered for the
access channel, gets the same records once they are in the file and writes those about an account to the table, in
the same request. So nothing has to be instrumented twice: every path the audit covers is in the user audit log. A
batch the database refuses waits in the audit's spool and is tried again by the audit's cronjob part; an event is
never stored twice.

## The page

**Users > User audit log** (`user/audit`) lists the events, newest first, and narrows them by:

- **User**: a login or a user id. It matches the account the event was about **or** the user who acted, so one
  search shows what happened to an account and what that user did. Click a login in the list to search for it.
- **Action**: one event name, or a branch of them (`access.user` finds every `access.user.*` event).
- **Result**: done, refused or failed.
- **From** and **To**: days, both included.

**Export the list as CSV** downloads the whole filtered list (one line per event, the columns of the table, before and
after as JSON). The export itself is recorded (`data.export.csv`). Cells that a spreadsheet would run as a formula are
written as text.

The page needs the policy **user/audit**. Whoever holds it sees the events of every account, so give it only to roles
that may: administrators have it through `*/*`; a data protection officer gets a role with `user/audit` alone.

## Settings

`settings/audit.ini`:

```ini
[UserAuditSettings]
UserAudit=enabled
Events[]
Events[]=access.user.*
Events[]=access.role.assign
Events[]=access.role.unassign
Events[]=access.session.*
Events[]=access.passkey.*
Events[]=access.registration.*
Events[]=access.permission.refused
RecordIP=enabled
RetentionDays=730
MaxValueLength=2000
```

- `UserAudit=disabled` stops writing rows; the page still shows the rows there are, and the audit files keep every event.
- `Events[]` narrows what is kept. An event the audit itself does not record (`[AuditEventSettings]`) never reaches
  the table either.
- `RecordIP=disabled` never stores the address. With `enabled` it is stored as `[AuditPrivacySettings]
  Field[actor.ip]` leaves it: `truncate` (the default) keeps the network only, `hash` a hash, `off` nothing.
- `RetentionDays` is how long a row is kept; `0` keeps them all. The audit files have their own retention
  (`[AuditChannel_access] LiveDays` and `ArchiveDays`).
- `MaxValueLength` cuts a long before or after value.

## Retention

The cronjob part `useraudit` removes the rows older than `RetentionDays`. It runs in the **infrequent** group
(`php runcronjobs.php infrequent`) and on its own:

```bash
php runcronjobs.php useraudit
```

## Installing

A new installation has the table: it is in `share/db_schema.dba` (MySQL and MariaDB, PostgreSQL, SQLite, and Oracle
through ezoracle) and the kernel schemas. An installation made before 6.0.15 gets it from the update files
(`update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql`) or, on any engine, from:

```bash
php update/common/scripts/6.0/createuseraudittable.php --dry-run
php update/common/scripts/6.0/createuseraudittable.php
```

The script leaves an existing table alone, so it can run again. Until the table exists nothing is stored and the page
says how to create it. MongoDB keeps no user audit table; the audit files hold the events there.

## Example

On a development installation at `https://www.example.com`, an administrator opens
`https://www.example.com/admin/user/audit?user=member1&from=2026-10-01` and sees that an editor changed the member's
e-mail address on the editor siteaccess, that the member signed in twice from the same network, and that the same
editor was refused when trying to edit an administrator account.
