# Design

## Context

See proposal.md - Why. Current state, read in `src/transports/imap/imap_transport.php` (master `9bb906e`):

- The transport talks through `ezcMailTransportConnection` (`protected $connection`), tags commands with
  `getNextTag()`, reads until the tagged line with `getResponse( $tag )` (which discards every untagged line on
  the way) and classifies lines with `responseType()` (OK/NO/BAD at offset 6, `* ` untagged, `+ ` continuation).
  `selectMailbox()` (818-848) sends `SELECT "{$mailbox}"` / `EXAMINE "{$mailbox}"` and keeps only the tagged
  result, so `* OK [UIDVALIDITY n]`, `* OK [UIDNEXT n]` and `* n EXISTS` are read and dropped.
- `capability()` (2448-2476) sends `CAPABILITY` and returns the list as an array; nothing caches it.
- `copyMessages()` (1011-1030) sends `{$uid}COPY {$messages} "{$destination}"`; `delete()` (1294-1312) sends
  `{$uid}STORE {$msgNum} +FLAGS (\Deleted)`; `expunge()` (2492-2506) sends `EXPUNGE` and takes no argument.
- `searchMailbox( $criteria )` (1790-1827) sends `{$uid}SEARCH {$criteria}` and returns an `ezcMailImapSet` with
  the transport's `uidReferencing`; `listUniqueIdentifiers()` (1499-1545) maps sequence numbers to UIDs with
  `UID SEARCH`. The `$uid` prefix is `UID ` when the option `uidReferencing` is true (`ezcMailImapTransportOptions`,
  `src/options/imap_options.php`: `uidReferencing`, `listLimit`).
- Mailbox names: `"{$mailbox}"` in `selectMailbox`, `createMailbox` (872), `renameMailbox` (907), `deleteMailbox`
  (946), `copyMessages`, `append` (2537); `listMailboxes()` (698-731) strips the quotes of the `* LIST` lines and
  decodes nothing. `sendLogin()` already escapes user and password with `addcslashes( ..., '\"' )`.
- `ezcMailImapSet` (`src/transports/imap/imap_set.php`) fetches one message per `nextMail()` with `{$uid}FETCH n
  RFC822` and reads the literal; it takes the connection, the list of numbers and `uidReferencing`.
- Tests: `tests/transports/transport_imap_test.php` and `transport_pop3_test.php` target `mta1.ez.no` and use
  `getMock()` (gone since PHPUnit 6); `phpunit.xml.dist` excludes `tests/transports`. The open upstream PRs #1-#4
  carry `tests/local_smtp/fake_smtp_server.php` (child process started with `proc_open`, prints `PORT n`, replies
  from a JSON config, logs commands to stdout as JSON) and `fake_smtp_test_case.php` (`ezcMailFakeSmtpServer`,
  `ezcMailTransportSmtpLocalTestCase`). `tests/local_*/` is inside the suite (suffix `_test.php`).
- Overlap: PRs #1-#4 and the other open mail changes touch no file of this change. It builds on
  `add-mail-delivery-reports` (its branch), so that the installed package carries every package
  change of this series; the diffs are disjoint and the Owner may rebase the PR onto master.

Architecture rules this change touches and how the design keeps them:

- *Meant for upstream: no fork detail, no decision IDs in code.* Package code and tests only; comments self-contained.
- *Tests never hit the network.* Fake servers on `127.0.0.1:0` in a child process; the dead-host tests under
  `tests/transports` stay excluded and untouched.
- *No new Composer dependency.* The fake servers are plain PHP scripts; modified UTF-7 is implemented with
  `mb_convert_encoding( ..., 'UTF-16BE' )` when mbstring exists and a small hand-written UTF-8 → UTF-16BE conversion
  otherwise (the package does not require mbstring).
- *A kernel part must work with the released package.* No kernel part here; the users of the new methods
  (`add-newsletter-mailbox-settings`) check `method_exists()`.

## Goals / Non-Goals

**Goals:** the five operations of the spec on the IMAP transport, tested end to end against a fake server that
later changes reuse; the fake POP3 server with a smoke test so STARTTLS and XOAUTH2 can be tested there too.

**Non-Goals:** rewriting the response reader (`getResponse()` keeps discarding untagged lines; the few commands
that need untagged data read it themselves, as `listMessages()` does); IDLE, CONDSTORE, QRESYNC; changing
`ezcMailImapSet`; the POP3 transport beyond the smoke test; replacing the excluded legacy transport tests.

## Decisions

### D1 Fake IMAP server as a child process with a scripted mailbox
`tests/local_imap/fake_imap_server.php`, started by `ezcMailFakeImapServer` in `tests/local_imap/fake_imap_test_case.php`
exactly like the SMTP fake (`proc_open`, `PORT n` on stdout, JSON config in `argv[1]`, JSON log on stdout at the
end). Config: `greeting` (default `* OK fake IMAP ready`), `capabilities` (default `IMAP4rev1 UIDPLUS MOVE`),
`users` (`user => password` for LOGIN), `mailboxes` (`name => array( 'uidvalidity' => int, 'uidnext' => int,
'messages' => array( array( 'uid' => int, 'flags' => array, 'raw' => string ) ) )`), `sequence` (replies for
unknown commands). Commands implemented: `CAPABILITY`, `NOOP`, `LOGOUT`, `LOGIN`, `LIST`, `SELECT`, `EXAMINE`,
`CREATE`, `DELETE`, `RENAME`, `SEARCH`/`UID SEARCH` (criteria `ALL`, `UNDELETED`, `UNSEEN`, `UID <set>`, and a
bare `<set>`; sets `n`, `n:m`, `n:*`, comma lists; `n:*` answers the highest UID when n is beyond it, as RFC 3501
says), `FETCH`/`UID FETCH` (items `RFC822`, `RFC822.SIZE`, `FLAGS`), `STORE`/`UID STORE` (`+FLAGS`, `-FLAGS`),
`COPY`/`UID COPY`, `MOVE`/`UID MOVE` (only when the capability is configured, else `BAD`), `EXPUNGE`,
`UID EXPUNGE` (only with UIDPLUS, else `BAD`). Mailbox names in commands are unquoted and modified-UTF-7-decoded
by the server, so a test can assert both the raw command (log) and the decoded name (state). The log holds every
command line; the final state (mailboxes, UIDs, flags) is printed with the log, and `ezcMailFakeImapServer::state()`
returns it. A `tls` key is reserved for the STARTTLS change (`none` here). Alternative: an in-process fake with
`stream_select()`. Rejected: the transport reads blocking; a child process is what the SMTP fake does and it will
carry a TLS handshake later.

### D2 Fake POP3 server
`tests/local_pop3/fake_pop3_server.php` and `fake_pop3_test_case.php` (`ezcMailFakePop3Server`,
`ezcMailTransportPop3LocalTestCase`) with the same shape: `greeting` (with an APOP timestamp when `apop` is
configured), `capabilities` (`CAPA` answer, default `TOP UIDL USER`), `users`, `messages` (array of raw strings with
UIDL ids), commands `CAPA`, `USER`, `PASS`, `APOP`, `STAT`, `LIST`, `UIDL`, `RETR`, `TOP`, `DELE`, `NOOP`, `RSET`,
`QUIT`; `tls` reserved. Smoke test `tests/local_pop3/transport_pop3_local_test.php`.

### D3 Mailbox state from the SELECT response
`selectMailbox()` reads the lines itself until the tagged line (as `listMessages()` does for `* SEARCH`), collecting
`[UIDVALIDITY n]`, `[UIDNEXT n]` and `* n EXISTS` into `$uidValidity`, `$uidNext`, `$messageCount` (null before and
after a failed selection; also reset in `disconnect()`). Exposed through `__get()`/`__isset()` as read-only
properties `uidValidity`, `uidNext`, `messageCount` next to `options` (docblock `@property-read`). Alternative:
a `status()`-style method with out parameters. Rejected: the values belong to the selected mailbox and change only
on selection; a property is what callers compare with a stored value.

### D4 Capability cache and MOVE
`protected $capabilities = null`; `serverSupports( $name )` fills it with `capability()` on first use (per
connection; reset on `disconnect()`) and compares case-insensitively. `moveMessages( $messages, $destination )`:
state must be selected (read-write); with MOVE: `{$uid}MOVE {$messages} "<name>"`, tagged OK or
`ezcMailTransportException`; without: `copyMessages()` then `{$uid}STORE {$messages} +FLAGS (\Deleted)`. A failed
COPY (`NO [TRYCREATE]` or any NO) throws before anything is flagged. Returns true. The caller expunges, as with
`delete()`.

### D5 UID EXPUNGE
`expunge( $uids = null )`: null → `EXPUNGE` as before; a UID set string (`"4,7"` or `"4:7"`) with UIDPLUS →
`UID EXPUNGE {$uids}`; without UIDPLUS → `EXPUNGE` (documented in the method and the tutorial: "without UIDPLUS
every message flagged deleted goes"). A caller that must not expunge foreign messages can check
`serverSupports( 'UIDPLUS' )` first.

### D6 Fetch after a known UID
`fetchSinceUid( $uid, $deleteFromServer = false )`: `UID SEARCH UID <uid+1>:*`, result filtered to UIDs greater
than `$uid` (the `n:*` answer for an n beyond the highest UID is the highest UID itself and is dropped), sorted
ascending, returned as `ezcMailImapSet` with `uidReferencing => true` whatever the transport option says (the
caller asked by UID). `$uid` must be an int >= 0 (`ezcBaseValueException`). Alternative: tell callers to use
`searchMailbox( "UID n:*" )`. Rejected: that is the pitfall itself.

### D7 Mailbox names: quoting and modified UTF-7
New `src/transports/imap/imap_mailbox_name.php` (`ezcMailImapMailboxName`, autoload row): `encode( $utf8 )` →
modified UTF-7 (RFC 3501 5.1.3: `&` → `&-`, runs of non-ASCII → `&<modified base64 of UTF-16BE>-` with `,` for
`/` and no padding), `decode( $wire )`, `quote( $utf8 )` → `"` + encoded name with `\` and `"` escaped + `"`.
Used by `selectMailbox`, `createMailbox`, `renameMailbox`, `deleteMailbox`, `copyMessages`, `moveMessages`,
`append`, `listMailboxes` (reference and pattern quoted; returned names decoded). UTF-16BE: `mb_convert_encoding()`
when available, otherwise a loop over the UTF-8 code points (the package must not require mbstring). An ASCII name
without `"` or `\` is sent byte for byte as before. `selectedMailbox` keeps the UTF-8 name the caller passed.

### D8 Tests
`tests/local_imap/transport_imap_local_test.php` (`ezcMailTransportImapLocalTest`: the smoke scenario and the
existing operations `listMailboxes`, `listMessages`, `fetchByMessageNr`, `searchMailbox`, `listUniqueIdentifiers`,
`fetchFlags`/`setFlag`, `status()` against the fake), `transport_imap_processing_test.php`
(`ezcMailTransportImapProcessingTest`: D3-D6 scenarios), `imap_mailbox_name_test.php` (`ezcMailImapMailboxNameTest`:
D7 unit cases including the RFC examples `~peter/mail/&U,BTFw-/&ZeVnLIqe-` and the round trip) and
`transport_imap_mailbox_name_test.php` (the wire scenarios through the fake, asserting the raw command log).
Every test uses `127.0.0.1`, `timeout => 5`, and stops the server in `tearDown()`.

## Risks / Trade-offs

- [A fake server is not a real one] → the fake follows RFC 3501/4315/6851 answers literally and is small enough to
  read; the Owner task names a live check against a real server.
- [`selectMailbox()` now parses untagged lines it used to skip] → only bracketed codes and `EXISTS` are read; any
  other untagged line is still skipped, the tagged result decides as before.
- [Capability cache goes stale after STARTTLS] → the STARTTLS change resets it when the connection changes
  (noted there); here it is reset on disconnect.
- [Modified UTF-7 without mbstring] → the hand-written conversion is covered by the unit test with mbstring
  disabled through a seam (`$useMbstring` static).

## Migration Plan

Package release (Owner). No settings, no data. Rollback: previous package version.
