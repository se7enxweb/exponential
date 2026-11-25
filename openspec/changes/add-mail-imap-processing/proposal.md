# Proposal

Redmine #16779 (https://pm.team.in-mv.com/issues/16779), subticket of #16766.

Meant for upstream (se7enxweb): package code of `se7enxweb/expcomponents-mail` only, as a pull request. Written for
any installation of the package. The kernel and the newsletter are not edited here; the changes that use the new
commands are `add-newsletter-mailbox-settings` (move processed messages) and, where it chooses to, the bounce
reader. Depends on the parser robustness change and `add-mail-delivery-reports` only for the branch it is built on
; it needs none of their code.

## Why

A mailbox reader that runs every few minutes needs four things the IMAP transport of the package does not give it
(verified on master `9bb906e`): the UIDVALIDITY of the selected mailbox (two `@todo` notes, `imap_transport.php`
131 and 1491; without it a reader cannot tell that a mailbox was recreated and its stored UIDs are stale), a way to
move a processed message (`MOVE`, RFC 6851; today only `COPY` plus a delete flag), an expunge that removes only the
messages it marked (`UID EXPUNGE`, RFC 4315; today `EXPUNGE` removes every message anyone flagged `\Deleted`), and
fetching "everything after UID n" without the `n:*` pitfall of RFC 3501 (the server answers the highest UID when
n is beyond it). Mailbox names go into `SELECT`, `EXAMINE`, `CREATE`, `RENAME`, `DELETE`, `COPY`, `APPEND` and
`LIST` unquoted beyond a pair of double quotes (`"{$mailbox}"`, lines 833, 839, 882, 922, 961, 1022, 2555): a name
with a quote or a backslash breaks the command, a non-ASCII name is sent raw instead of in modified UTF-7
(RFC 3501 5.1.3). The package's IMAP tests run against servers that no longer exist and are excluded from the suite;
nothing of the IMAP transport is tested today.

## What Changes

- **Fake IMAP and POP3 servers for tests**: scripted servers in PHP over a local socket (child process, like the
  SMTP fake of the open upstream PRs), reusable by later changes (STARTTLS, XOAUTH2). A smoke test of the
  existing IMAP and POP3 transports against them.
- **Mailbox state**: after `SELECT`/`EXAMINE` the transport exposes the mailbox's UIDVALIDITY, UIDNEXT and message
  count; a caller detects a recreated mailbox by a changed UIDVALIDITY.
- **MOVE**: `moveMessages( $messages, $destination )` uses `MOVE`/`UID MOVE` when the server lists the capability,
  and otherwise `COPY` + `STORE \Deleted` (the caller expunges as before). The capability list is read once and
  cached per connection.
- **UID EXPUNGE**: `expunge( $uids )` with a UID set sends `UID EXPUNGE` when the server lists UIDPLUS and falls
  back to a plain `EXPUNGE` (documented) otherwise; `expunge()` without arguments is unchanged.
- **Fetch from a known UID**: `fetchSinceUid( $uid )` returns the messages with a UID greater than the given one,
  as a UID-referencing set, empty when there are none (the `n:*` answer is filtered).
- **Mailbox names**: every command that takes a mailbox name quotes it per RFC 3501 (backslash and quote escaped)
  and encodes non-ASCII names in modified UTF-7; `listMailboxes()` decodes the names it returns. Two public
  helpers encode and decode.

No **BREAKING** change: existing methods keep their signatures and wire behaviour for ASCII names without quotes.

## Capabilities

### New Capabilities
- `mail/imap-mailbox-processing`: the IMAP operations a mailbox processor relies on: mailbox state, moving,
  selective expunge, incremental fetch by UID, and mailbox name encoding.

### Modified Capabilities
- none.

## Impact

- Package `se7enxweb/expcomponents-mail`: `src/transports/imap/imap_transport.php`,
  `src/options/imap_options.php` (no new option expected; see design), `src/tools.php` or a new
  `src/transports/imap/imap_mailbox_name.php` for the name encoding, `src/mail_autoload.php`; new
  `tests/local_imap/` (fake server, test case, tests) and `tests/local_pop3/` (fake server, test case, smoke test);
  `docs/tutorial.txt`. No new Composer dependency; PHP 7.1+ code.
- Exponential tree: no file changes; regression run of the mailbox-related tests with the changed package installed.
- Later changes: `add-mail-starttls-stream-context` and `add-mail-xoauth2-configuration` extend the fake servers;
  `add-newsletter-mailbox-settings` uses `moveMessages()` and `expunge( $uids )` when present.

## Out of scope

- STARTTLS, `LOGINDISABLED`, stream context options: `add-mail-starttls-stream-context`.
- XOAUTH2 for POP3 and the configuration of token sources: `add-mail-xoauth2-configuration`.
- Using the new commands in the kernel bounce reader or the newsletter: `add-newsletter-mailbox-settings`,
  another change of the bounce reader.
- IDLE, CONDSTORE/QRESYNC, SORT/THREAD extensions, the `listLimit` behaviour, the Gmail/Zoho response hacks.
