# Tasks

Abbreviations (used in every check below):

- **WORKTREE** = a working copy of this repository on a branch for this change. **PKG** = a clone of
  `se7enxweb/cjw_newsletter` on a branch for this change. Newsletter edits are made in WORKTREE
  `extension/cjw_newsletter` and carried to PKG as the same diff.
- **DB** = `EXP_TEST_DB_USER=… EXP_TEST_DB_PASSWORD=…`, an account that may create the throwaway test databases.
- **KMAIL** = `cd WORKTREE && rm -f var/cache/ini/*.php && DB vendor/bin/phpunit --no-coverage --testsuite kernel-classes --filter 'MailPreferences'`.
- **NL** = `cd WORKTREE && rm -f var/cache/ini/*.php && DB vendor/bin/phpunit --no-coverage --testsuite cjw_newsletter`
  (append `--filter <Test>` for one class). **NLLINT** = `cd WORKTREE && deploy/ci/lint-extensions.sh` where present,
  else `php -l` over the changed files.
- Commits: `Added:`/`Fixed:`/`Updated:`.

## 1. Workspace and baseline

- [ ] 1.1 Create WORKTREE (with `vendor/` installed and `php bin/php/ezpgenerateautoloads.php -e`) and PKG with their
  branches. Check: `git -C WORKTREE status --short` is clean; both branches checked out.
- [ ] 1.2 Baseline: KMAIL and NL, numbers (tests, skipped, failures) written into this file under 1.2. Check: both runs
  complete; KMAIL shows `testBounceClassification` passing with `dsn-soft-delayed.eml` = soft (the expectation 2.1
  changes).

## 2. Kernel: kind "delay" (D2)

- [ ] 2.1 `expMailBounceReader`: constant `DELAY`, the DSN loop of `classify()` treats `delayed` as delay (hard, then
  soft, then delay), `emptyCounts()` and `processMessage()` count `delayed`, listeners get it. Fixtures
  `dsn-delayed-and-hard.eml`, `dsn-delayed-and-soft.eml` next to `dsn-soft-delayed.eml`; in `testBounceClassification`
  the expectation for `dsn-soft-delayed.eml` becomes `delay` (detail 4.2.2, no address) and the two new fixtures are
  hard and soft; a new test that a run over a delay notice and a hard bounce counts 1 delayed / 1 hard and suppresses
  only the hard address, and that a listener receives kind `delay`. Write the tests first and see them fail.
  Check: KMAIL green except the baseline of 1.2 (Scenario: Only delayed recipients, Scenario: Delayed and failed in one
  notification, Scenario: Delayed and temporarily failed, Scenario: A run with a delay notice, Scenario: A listener
  sees the kind).
- [ ] 2.2 Outputs: `kernel/private/classes/cronjobs/mailbounces.php`, `kernel/private/classes/commands/mailbounces.php`
  (both lines) and `kernel/private/classes/commands/mailstatus.php` print the delayed count; the command's help text
  names it. Check: `php WORKTREE/console exp:mail:bounces --help` (or the command's dry run with
  `runFiles()` over the fixtures, as its existing test does) shows "delayed"; KMAIL green.
- [ ] 2.3 Docs: the doc page that lists the bounce reader's kinds (`grep -rln "complaint" doc/eng-us | xargs grep -ln bounce`)
  names `delay`, with a **BREAKING** note for listeners that relied on `soft`. Check: `grep -rn "delay" <that page>`.

## 3. Newsletter: no password in the log (D1)

- [ ] 3.1 `CjwNewsletterMailbox::loggableSettings()` masks a non-empty password as `***`; `connect()` logs that copy in
  `authenticate-failed` and `connect-ok`. Test in `tests/tests/extension/cjw_newsletter/cjwNewsletterMailboxTest.php`
  (new): a failed login against `127.0.0.1` on a closed port with password `s3cret-test` leaves no `s3cret-test` in the
  newsletter log file and does show `***`; the helper keeps an empty password empty. Check: NL `--filter
  cjwNewsletterMailboxTest` green, the test red before the fix (Scenario: Failed login, Scenario: Successful connect
  with debug logging).

## 4. Newsletter: one failing mailbox does not stop the others (D5)

- [ ] 4.1 `collectMailsFromActiveMailboxes()` records `connect failed: …` / `fetch failed: …` per mailbox id, logs step
  and mailbox id, disconnects, continues. The mailbox list comes through a protected seam (`activeMailboxes()`
  wrapping `fetchAllActiveMailboxes()`) so the test can pass two fake mailboxes, the first throwing in `fetchMails()`.
  Test in `cjwNewsletterMailboxTest`: the result has both ids, the first a string starting `fetch failed:`, the second
  its status array. Check: NL `--filter cjwNewsletterMailboxTest` green, red before (Scenario: The first of two
  mailboxes fails while fetching).

## 5. Newsletter: delete only what is stored (D3)

- [ ] 5.1 `fetchMails()` fetches without the delete flag, stores through the new protected `storeItem()`, then deletes
  the added and known message numbers and calls `expunge()` for IMAP. Test with a fake transport recording `delete()`
  and `expunge()` and a subclass whose `storeItem()` fails for one message: that message is not deleted and is in
  `failed`; added and known ones are deleted; with `delete_mails_from_server` off nothing is deleted; for type `pop3`
  no `expunge()`. Check: NL `--filter cjwNewsletterMailboxTest` green, red before (Scenario: Storing fails, Scenario:
  Stored and known messages, Scenario: IMAP and POP3).
- [ ] 5.2 `extension/cjw_newsletter/doc/deliverability.md`, section on bounce mailboxes: messages are deleted after
  they are stored; a message that cannot be stored stays. Check: `grep -n "after they are stored" WORKTREE/extension/cjw_newsletter/doc/deliverability.md`.

## 6. Newsletter: delay notices and automatic replies are not bounces (D4)

- [ ] 6.1 One helper for the automatic-message rule, used by `CjwNewsletterMailIn::rejectReason()` and the new
  `CjwNewsletterBounce::isAutomaticReply( $raw )`; kinds `delay` and `autoreply` in `CjwNewsletterBounce`;
  `classify()` and `handle()` as in D4. Tests in `cjwNewsletterDeliverabilityTest`: the kernel fixture
  `dsn-soft-delayed.eml` and `plain-auto-reply.eml` (extended copy with "450" and `X-CJWNL` headers in the text) leave
  send item and user unchanged and schedule no retry; a DSN with a hard failure and `Auto-Submitted: auto-replied` is
  hard; the mail-in tests stay green. Check: NL green against the baseline of 1.2 plus the new tests, red before
  (Scenario: Delay notice for a sent edition, Scenario: Out-of-office reply quoting a reply code, Scenario: A real
  reply marked as not automatic, Scenario: A bounce that carries an automatic-reply header).
- [ ] 6.2 `doc/deliverability.md` (kinds delay and autoreply, what happens to them) and the package CHANGELOG entry for
  groups 3–6. Check: `grep -n autoreply WORKTREE/extension/cjw_newsletter/doc/deliverability.md`; NLLINT 0 errors.

## 7. Integration

- [ ] 7.1 KMAIL and NL green against 1.2 plus the new tests; root suites `kernel-classes` and `lib` with a cold INI
  cache (`vendor/bin/phpunit --no-coverage --testsuite kernel-classes`, `… --testsuite lib`) not worse than the
  documented baseline; NLLINT 0 errors; `openspec validate fix-newsletter-bounce-handling --strict`. Check: numbers in
  this file under 7.1.
- [ ] 7.2 Carry the newsletter diff to PKG and commit there. Check: `git -C PKG diff <base> --stat` lists the same
  newsletter files as WORKTREE's diff under `extension/cjw_newsletter`.

## 8. Owner

- [ ] 8.1 Pull requests: the newsletter part to `se7enxweb/cjw_newsletter`, the kernel part (group 2) to
  `se7enxweb/exponential`; Redmine #16765.

## Workflow follow-up

- After both merges: archive the change.
