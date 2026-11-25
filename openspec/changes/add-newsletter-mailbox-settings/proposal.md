# Proposal: Newsletter mail accounts — IMAP folder, connection timeout, a folder for processed messages

Redmine #16781 (https://pm.team.in-mv.com/issues/16781), subticket of #16766.

Meant for **upstream** (se7enxweb/cjw_newsletter): the whole change is newsletter code (`extension/cjw_newsletter`
in this tree, carried to the package clone as the same diff). No kernel change. Written for any Exponential
installation.

Depends on `fix-newsletter-bounce-handling` (Redmine #16765): the mailbox deletes messages only after they are
stored, through the seams `storeItem()`, `activeMailboxes()` and `createTransport()`; this change builds on that
code (the move of processed messages replaces the delete step) and on its test class. Depends on
`add-mail-imap-processing` for the IMAP `MOVE` of the mail package; without it (the released package) the newsletter
copies and deletes instead, so the change works with both.

## Why

A newsletter mail account (Newsletter > Mail accounts, `cjwnl_mailbox`) reads one folder of an IMAP mailbox, and
that folder is hard-coded: `Inbox`. A bounce mailbox whose provider sorts delivery reports into another folder, or
that is shared with other mail and filtered server-side, cannot be read. The connection timeout is fixed at 3
seconds, which a slow or distant server does not meet, and the only thing the account can do with a message it has
stored is to leave it or delete it — an administrator who wants to keep the returned mail for a look, but out of
the way of the next run, has no option. The kernel's own bounce reader already has a `Folder` setting.

## What Changes

- **The IMAP folder of a mail account is a setting of the account** (default `INBOX`, today's behaviour).
- **The connection timeout is a setting** of the newsletter (seconds, default 3, today's value).
- **A mail account can move its processed messages to a folder** instead of deleting them: after a message is
  stored (or found stored already), it is moved to the named folder; the folder is created when it is missing. With
  the mail package's `MOVE` support the move is one command; with the released package it is a copy and a delete.
  Without a processed folder the account deletes, or keeps, as before. POP3 accounts have no folders; the setting
  is IMAP only.
- The edit form of the account shows the two folder fields with their meaning; the database gains two columns
  with defaults, with an update file per engine for existing installations.

## Capabilities

### New Capabilities

None.

### Modified Capabilities

- `newsletter-bounce-mailbox` (added by `fix-newsletter-bounce-handling`): the folder that is read, the connection
  timeout and what happens to a processed message.

## Out of scope

- The mail package: `MOVE`, `UID EXPUNGE`, quoting and modified UTF-7 of folder names (`add-mail-imap-processing`).
  A folder name with spaces or non-ASCII characters works once that change is in the package; until then the
  server's refusal is logged for the account (per-mailbox errors of `fix-newsletter-bounce-handling`).
- STARTTLS and stream context options for the account (`add-mail-starttls-stream-context`); XOAUTH2 (#16720).
- A processed folder for the kernel's bounce reader (it has `AfterRead=keep|delete`; a later ticket if wanted).

## Impact

- Newsletter (`extension/cjw_newsletter`, package `se7enxweb/cjw_newsletter`): `classes/cjwnewslettermailbox.php`
  (definition, `connect()`, `fetchMails()`), the edit view `classes/runnable/views/newsletter/mailbox_edit.php`,
  the two templates `design/admin4/templates/newsletter/mailbox_edit.tpl` and
  `design/standard/templates/newsletter/mailbox_edit.tpl`, `settings/cjw_newsletter.ini` (one key),
  `share/db_schema.dba` and `sql/{mysql,postgresql,sqlite}/schema.sql` (two columns), a database update file per
  engine under `update/database/<engine>/4.3/`, `doc/deliverability.md`, CHANGELOG; tests in the Exponential tree
  (`tests/tests/extension/cjw_newsletter/`).
- Database: two columns on `cjwnl_mailbox` (`imap_folder`, `processed_folder`), both with a default, so the
  update runs on tables with rows; the installation's database is updated with the engine's file (and on a server
  before the deploy, see `deploy/README.md`).
- Visible to administrators: two fields on the mail account form; nothing changes for an account that leaves them
  at their defaults.
