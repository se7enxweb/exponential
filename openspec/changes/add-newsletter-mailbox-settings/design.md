# Design: Newsletter mail accounts — IMAP folder, connection timeout, processed folder

See proposal.md (Why, What Changes) and `specs/newsletter-bounce-mailbox/spec.md` for the requirements.

## Context

Verified against the tree on 24.11.2026 (`extension/cjw_newsletter`, 4.2.20 plus the admin4 port; the package
package repository is 4.3.0 — the class, view and template files
this change touches are byte-identical in both, `settings/cjw_newsletter.ini` and the four schema files differ):

- `CjwNewsletterMailbox` (`classes/cjwnewslettermailbox.php`, 686 lines): `definition()` (40–90) lists the columns
  of `cjwnl_mailbox` (email, server, port, user_name, password, type, is_activated, is_ssl,
  delete_mails_from_server, last_server_connect). `connect()` (322–436): `$options->timeout = 3` for IMAP (346) and
  POP3 (352), `selectMailbox( 'Inbox' )` (399) — the findings hold. `fetchMails()` (450–585) fetches with the
  delete flag and expunges; `fix-newsletter-bounce-handling` (D3) restructures it: fetch without the flag, store
  through `storeItem()`, then delete the added and known numbers and expunge for IMAP. This change extends that
  final step.
- The edit view `classes/runnable/views/newsletter/mailbox_edit.php` (86–133): the field list with defaults, the
  flag cast, the port check, `storeMailboxData()`. Templates `design/admin4/…/mailbox_edit.tpl` (radio and text
  fields per column) and `design/standard/…/mailbox_edit.tpl`.
- Schema: `share/db_schema.dba` is the source of truth; `sql/{mysql,postgresql,sqlite}/schema.sql` are made from
  it; `cjwNewsletterSchemaTest` (tree) checks that every table and column of the dba is in the three schema files
  and that the SQLite schema creates exactly the dba. Upgrades live in `update/database/<engine>/<major>/
  dbupdate-<from>-to-<to>.sql` (the package's 4.3.0 file adds `cjwnl_edition_send.segment_id` with `ALTER TABLE …
  ADD COLUMN`); the tree has the 4.2 file only. (Noted, not fixed here: `cjwnl_mailbox_item.message_size` is in the
  dba and the schema files of both tree and package but in no update file.)
- Mail package (released): `ezcMailImapTransport::selectMailbox( $name )`, `copyMessages( $numbers, $destination )`,
  `createMailbox( $name )`, `listMailboxes()`, `delete()`, `expunge()`; `ezcMailImapTransportOptions::timeout`.
  `add-mail-imap-processing` adds `MOVE` (RFC 6851) with fallback and `UID EXPUNGE`; its method names are in that
  change's design (not fixed at the time of this plan).
- Settings: `cjw_newsletter.ini [BounceSettings]` (`BounceThresholdValue`, `MaxMessageSize`).
- Tests: `tests/tests/extension/cjw_newsletter/cjwNewsletterMailboxTest.php` (created by
  `fix-newsletter-bounce-handling` 3.1: fake transport, unstored mailbox objects, `storeItem()` subclass),
  `cjwNewsletterBounceTest.php` (fake transport `cjwNewsletterFakeMailTransport`), `cjwNewsletterSchemaTest.php`.
  `cjwNewsletterTestCase` binds the siteaccess `admin` and skips without an installed site; the new tests use
  fakes and unstored objects only.

## Goals / Non-Goals

- Goal: folder, timeout and processed folder as settings; the move with and without the package's `MOVE`; the
  schema change with upgrade files; the form; tests without a server or a database.
- Non-goal: changing the mail package; folder name quoting (the package's job); a processed folder for POP3 or for
  the kernel's reader.

## Architecture rules touched

- *New functionality goes into an extension*: all code in `extension/cjw_newsletter`; no lib/ or kernel/ edit.
- *Configuration is INI*: one key, `cjw_newsletter.ini [BounceSettings] MailboxConnectTimeout=3`, in the
  extension's settings (not a secret; overridable per site). The folders are per account and live in the account's
  row, like server and user name.
- *Database tables*: two columns on `cjwnl_mailbox` with defaults, in the dba, the three schema files and an update
  file per engine; on this fork's server the update runs before the deploy (`deploy/README.md`, D5).
- *The kernel part must work with the released package*: the move uses the package's `MOVE` only when the method
  exists, else copy + delete (D3).
- *Tests never write to a site database*: fakes and unstored objects (D6).

## Decisions

### D1 Folders are columns of the account, the timeout is an INI key

`cjwnl_mailbox` gains `imap_folder varchar(255) NOT NULL DEFAULT ''` (empty = `INBOX`) and `processed_folder
varchar(255) NOT NULL DEFAULT ''` (empty = no move). Both in `definition()` with `'default' => ''`, so an object
built without them (the tests, older rows) behaves as today. The timeout is `[BounceSettings]
MailboxConnectTimeout` (seconds, default 3, `max( 1, (int) )`), read in `connect()` for IMAP and POP3: a timeout
describes the site's network, not one account, and one key is enough to fix a slow server; a column would be a
third field to explain on the form. Alternative rejected: the folders as INI keys — they differ per account (one
provider sorts reports into "Bounces", another does not).

### D2 `connect()` selects the account's folder

`selectMailbox( $this->attribute( 'imap_folder' ) !== '' ? … : 'INBOX' )`; the `selectBox-failed` log entry gains
`mailbox_id` and the folder. `INBOX` instead of `Inbox`: RFC 3501 makes `INBOX` case-insensitive, the canonical
spelling is what the kernel's reader uses.

### D3 The processed folder replaces the delete step

In `fetchMails()`, the final step of `fix-newsletter-bounce-handling` D3 becomes `finishMessages( $transport, array
$numbers )`: with `type === 'imap'` and a processed folder → `moveMessages( $transport, $numbers, $folder )`; else
delete when `delete_mails_from_server`, as the base change does. `moveMessages()` (protected, the seam for the
tests): when the transport has the move method of `add-mail-imap-processing` — `moveMessages( $messages,
$destination )`, its design D4, read on 24.11.2026; checked with `method_exists( $transport, 'moveMessages' )`, so
the released package (without it) takes the fallback — call it for the numbers; else `copyMessages( $numbers, $folder )`,
then `delete()` per number and `expunge()`. A failure of the first move attempt that names the folder as missing
(the package throws `ezcMailTransportException`; the message is not parsed — the account simply tries
`createMailbox( $folder )` once and repeats the move) is retried once after `createMailbox()`; a second failure is
logged (`move-failed`, mailbox id, folder, error) and nothing is deleted, so the messages are read again as
"exists" by the next run. The move happens whether or not `delete_mails_from_server` is set: a named processed
folder is the stronger statement. Why not `copyMessages` always: `MOVE` is atomic on the server, a copy + delete
leaves a duplicate when the delete fails; the fallback is for the released package only.

### D4 The form

`mailbox_edit.php` (the view class): `imap_folder` and `processed_folder` join the field list (89–90), trimmed, at
most 255 characters; an error when both are set and equal after `strtoupper()` (`INBOX` equals `inbox`); the
check of the fields moves into a static `fieldsFromPost( array $post ): array( data, errors )` so a test calls it
without HTTP. Both templates (`admin4`, `standard`) show the two fields under the type radio, with a note that they
apply to IMAP only and that an empty folder means the inbox, an empty processed folder means delete or keep as set
below. Labels in English with the existing i18n context `cjw_newsletter/mailbox_edit`; the translation files are
not touched (the maintainer's release step).

### D5 Schema and upgrade

`share/db_schema.dba` (the two columns of `cjwnl_mailbox` in the dba's array form, next to
`delete_mails_from_server`), the three `sql/*/schema.sql`, and `update/database/{mysql,postgresql,sqlite}/4.3/
dbupdate-4.3.0-to-4.3.1.sql` with two `ALTER TABLE cjwnl_mailbox ADD COLUMN …` each (the package's next version;
the tree gets the same file so that its installations run the same statements). `cjwNewsletterSchemaTest` gains
`testTheMailboxFolderColumnsAreInEveryUpgradeFile` (the three files name both columns with their defaults).
`deploy/README.md` (fork): one line under the database steps — run the engine's 4.3.1 file before the deploy that
brings this change, like the utf8mb4 conversion. Why an update file and not `ALTER` at runtime: the package has
never altered tables at runtime, and the schema test's rule is "dba = schema files = upgrades".

### D6 Tests

`cjwNewsletterMailboxTest` (fake transport of the base change extended with `selectMailbox`, `copyMessages`,
`createMailbox`, the move method under a configurable name, recording calls and able to throw on the first move):
`testTheAccountsFolderIsSelected` (IMAP account with `imap_folder` "Bounces" → `selectMailbox( 'Bounces' )`; empty →
`'INBOX'`), `testTheConnectTimeoutComesFromTheSettings` (`createTransport()` seam receives options with timeout 20
after `setIni()`), `testProcessedMessagesAreMovedWithTheMoveCommand`, `testProcessedMessagesAreCopiedAndDeleted
WithoutIt` (same numbers, `copyMessages` + `delete` + `expunge` recorded), `testAFailedMessageIsNotMoved`,
`testTheProcessedFolderIsCreatedWhenMissing` (first move throws, `createMailbox` recorded, second move succeeds),
`testAFailedMoveDeletesNothing`, `testWithoutAProcessedFolderTheDeleteFlagRules`, `testPop3IgnoresTheFolders`.
View: `testTheFormRefusesTheSameFolderTwice` on `fieldsFromPost()`. Schema: D5.

## Risks / Trade-offs

- [`add-mail-imap-processing` renames `moveMessages()` before it is applied] → the name is used in one place
  (`method_exists()` in the seam) and in the fake; task 3.1 checks it against that change's design; the fallback
  path is tested independently.
- [A folder name with spaces or non-ASCII on the released package] → the server refuses `SELECT`; logged per
  account (base change D5); fixed by `add-mail-imap-processing` (quoting, modified UTF-7).
- [Copy + delete leaves a duplicate when the delete fails] → the next run finds the message as "exists" and
  deletes it; the copy in the processed folder is a duplicate at worst.
- [An installation that runs the update twice] → it fails, like the 4.2 file (documented in `upgrade-4.2.md`; the
  new file says so in its header).
- [The clone is 4.3.0: the diff of `cjw_newsletter.ini` and the schema files does not apply cleanly] → task 5.1
  applies with `--reject` and resolves those hunks by hand; the class, view and template hunks apply as they are.

## Migration Plan

Update the database with the engine's 4.3.1 file (before the deploy on a server), then deploy the extension. Rows
keep their defaults (inbox, no move), so nothing changes until an administrator fills the fields. Rollback: revert
the code; the two columns stay, harmless. Upstream: one PR to `se7enxweb/cjw_newsletter` from the clone's branch.
