# Design: Newsletter bounce mailbox — five defects

See proposal.md (Why, What Changes) and the two specs for the requirements.

## Context

- Newsletter package `se7enxweb/cjw_newsletter`, installed as `extension/cjw_newsletter`.
- `CjwNewsletterMailbox` (`classes/cjwnewslettermailbox.php`):
  - `connect()` builds `$settingArray` with `'password' => $password` (line ~334) and logs it whole in
    `writeError( 'authenticate-failed' )` (~386) and `writeDebug( 'connect-ok' )` (~409).
  - `collectMailsFromActiveMailboxes()` (~160–230) loops over the active mailboxes; both `catch` blocks end with
    `return $e->getMessage()`, which ends the loop; the inner one logs as `CjwNewsletterMailparser::parse` /
    `parseMail-failed` although it caught a fetch error. The caller `CjwNewsletterRunner` (~310) already accepts a
    string per mailbox id as that mailbox's error.
  - `fetchMails()` (~450–580) calls `$transport->fetchByMessageNr( $messageId, $mailboxDeleteMailsFromServer )`, which
    marks the message deleted while fetching (IMAP `\Deleted`, POP3 `DELE`); `expunge()` for IMAP (and `QUIT` on
    `disconnect()` for POP3) then removes it whether `addMailboxItem()` succeeded or the string was empty.
- `CjwNewsletterBounce` (`classes/deliverability/cjwnewsletterbounce.php`): `classify()` asks
  `expMailBounceReader::classify()` and falls back to `kindOfCode()` on the code the newsletter's parser found in the
  text; `handle()` treats every kind that is not none/hard/complaint as soft (resend, `soft_bounce_count`,
  `setBounced( false )`). `CjwNewsletterMailIn::rejectReason()` (`classes/deliverability/cjwnewslettermailin.php` ~408)
  already recognises automatic messages (`Auto-Submitted` not `no`, `Precedence: bulk/junk/list`).
- Kernel `expMailBounceReader::classify()` (`kernel/classes/mailpreferences/expmailbouncereader.php` ~430–470): in the
  DSN loop, `delivered/relayed/expanded` are skipped, `failed` with a hard status is hard, everything else (including
  `delayed`) sets `$soft`. `processMessage()` counts per kind (`emptyCounts()`: messages, hard, soft, complaints,
  other, suppressed, errors) and hands non-hard messages to `[BounceSettings] MessageListeners[]`. The counts are
  printed by the cronjob part `mailbounces` and the commands `exp:mail:bounces` and `exp:mail:status`.
- Tests: kernel `tests/tests/kernel/classes/mailpreferences/MailPreferencesIntegrationTest.php::testBounceClassification`
  reads `fixtures/bounces/*.eml` and **asserts `dsn-soft-delayed.eml` (Action: delayed, 4.2.2) is `soft`**;
  `plain-auto-reply.eml` exists. Newsletter: `tests/tests/extension/cjw_newsletter/cjwNewsletterDeliverabilityTest.php`
  (suite `cjw_newsletter` in `phpunit.xml`, not in the CI workflow; skips without an installed site).

## Goals / Non-Goals

- Goal: the five defects of the proposal, each with a test that fails before the fix.
- Non-goal: changing the mail package, the IMAP `EXPUNGE` semantics (it still removes every `\Deleted` message of
  the folder), the parser of the newsletter, any INI key or table.

## Architecture rules touched

- *New functionality goes into an extension*: the only kernel edit is in the bounce reader itself
  (`expMailBounceReader`), which owns the classification; comments carry no ticket or decision ID.
- *Configuration is INI*: no key added.
- *Tests never write to a site database*: the newsletter tests that store mailbox items use the existing
  `cjwNewsletterTestCase` (skips without an installed test site); the new mailbox tests use a fake transport and a
  storage seam, no database (D3).

## Decisions

### D1 Mask the password where the settings are built

`connect()` keeps the real password for `authenticate()` and logs a copy of `$settingArray` with `password` replaced
by `'***'` when it is non-empty (empty stays empty, so a missing password is still visible). One helper
`loggableSettings()` used by both log calls. Alternative: drop the key — rejected, "password set or not" is useful
when a login fails.

### D2 Kernel: kind `delay`

`expMailBounceReader::DELAY = 'delay'`. In the DSN loop, `action === 'delayed'` sets `$delayed = true` and is not
counted as soft; after the loop: hard first, then soft, then delay (with the first delayed status as detail, no
addresses), then none. `emptyCounts()` gains `delayed`; `processMessage()` counts it and hands it to the listeners
like a soft bounce (no suppression). The command and cronjob outputs add "delayed". The kernel test's expectation for
`dsn-soft-delayed.eml` becomes `delay`; two new fixtures: delayed + failed-hard, delayed + failed-soft.
Alternative: return `none` with detail `delayed` — rejected, an older newsletter would then fall back to the codes
in the text and still find 4.2.2 (soft), and the run counts would hide it under "other". Compatibility: an older
newsletter receiving `delay` treats it as soft (today's behaviour, no worse); D4 is the newsletter side.

### D3 Delete after store, by message number

`fetchMails()` fetches with `fetchByMessageNr( $messageId, false )` and collects the numbers to delete: those whose
item was added, and those already known (`exists`, the same rule as today for messages skipped before fetching, which
were never deleted — they are added to the list too, so a known message does not stay on the server for ever).
After the loop, when `delete_mails_from_server` is set: `$transport->delete( $number )` for each, then `expunge()` for
IMAP; POP3 removes them at `QUIT` (`disconnect()`), as before. Failed and empty messages are never deleted.
For the test, the transport is any object with the methods used (`listUniqueIdentifiers`, `listMessages`,
`fetchByMessageNr`, `delete`, `expunge`), set through the existing `TransportObject` property (a test subclass), and
storing goes through a new protected method `storeItem()` wrapping `CjwNewsletterMailboxItem::addMailboxItem()`, so
the test can make it fail without a database.
Alternative: keep deleting while fetching and "undelete" failures (IMAP `-FLAGS \Deleted`, POP3 `RSET`) — rejected,
`RSET` undoes every deletion of the session and IMAP undelete is not in the transport.

### D4 Newsletter: no bounce for `delay` and automatic replies

`CjwNewsletterBounce` gains the kinds `delay` and `autoreply`. `classify()`: a kernel answer `delay` is returned as
it is; when the kernel says none (or is absent) and `isAutomaticReply( $raw )` is true, the result is `autoreply`
before `kindOfCode()` is asked. `isAutomaticReply()` reads the top-level headers of the raw message with the same rule
as `CjwNewsletterMailIn::rejectReason()` (`Auto-Submitted` present and not `no`, or `Precedence` bulk/junk/list); the
rule moves into one static helper both use. A message with a DSN or ARF never reaches it, because the kernel answer
wins (spec scenario "A bounce that carries an automatic-reply header"). `handle()` returns action `none` for both
kinds before touching send item or user. Without the kernel class the newsletter's own fallback only knows codes, so
`delay` cannot come from there; the automatic-reply check still applies.

### D5 Per-mailbox errors, the loop goes on

Both `catch` blocks in `collectMailsFromActiveMailboxes()` store `'<step> failed: ' . $e->getMessage()` under the
mailbox id, log `writeError( 'CjwNewsletterMailbox::collectMailsFromActiveMailboxes', 'mailbox', '<step>-failed',
array( 'mailbox_id' => …, 'error-code' => … ) )` with step `fetch` or `connect`, disconnect when connected, and
`continue`. The runner already prints a string result as that account's error and marks the run not ok.

## Risks / Trade-offs

- [A listener or report that counts `soft` sees fewer] → named as **BREAKING** in the proposal and in the kernel
  ChangeLog and doc page; the counts gain `delayed`.
- [IMAP `expunge()` still removes `\Deleted` messages flagged by others in the same folder] → unchanged from today,
  out of scope (proposal).
- [A mailbox item stored but the delete fails (connection lost)] → the message stays and is "exists" next run, then
  deleted; no loss.
- [Header rule for automatic replies too broad: some providers send real DSNs without a `message/delivery-status`
  part but with `Auto-Submitted`] → those were judged from text codes before and are now ignored; logged with kind
  `autoreply` so they can be found. Accepted: a missed soft bounce costs less than a wrong resend.

## Migration Plan

No data or INI change. Deploy with the newsletter and the kernel together (same tree). Rollback: revert; nothing to
undo in the database. Upstream: one PR to `se7enxweb/cjw_newsletter` (D1, D3, D4, D5, tests, doc), one to
`se7enxweb/exponential` (D2, fixtures, test); the newsletter PR works with the old kernel too (D2 compatibility).
