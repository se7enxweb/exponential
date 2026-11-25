# Design

## Context

See proposal.md - Why. Current state, read in the code:

- Package (`se7enxweb/expcomponents-mail`, master `9bb906e`, plus the branch of the parser robustness change):
  `ezcMailPartParser::createPartParserForHeaders()` (`src/parser/interfaces/part_parser.php` 170-184) maps
  `message/rfc822` to `ezcMailRfc822DigestParser`, `message/delivery-status` to `ezcMailDeliveryStatusParser` and
  every other `message/*` subtype to `ezcMailFileParser`. `ezcMailDeliveryStatus` (`src/parts/delivery_status.php`)
  holds `message` (an `ezcMailHeadersHolder`) and `recipients` (an `ArrayObject` of holders), has no accessor
  beyond the raw fields, and `generateHeaders()` always sets `Content-Type: message/delivery-status`.
  `ezcMailMultipartReportParser` keeps the first three parts as readable, machine and original part, whatever
  their classes. `ezcMail::fetchParts( $filter )` walks a parsed mail and returns parts by class name;
  `ezcMailVariableSet` wraps a string for the parser.
- After the parser robustness change (D1 there) the delivery-status parser is a line state machine: field lines,
  unfolding, blank lines closing a section. The feedback-report parser of this change follows the same line rules
  (one section, repeated fields kept).
- Kernel: `expMailBounceReader::classify( $raw )` (`kernel/classes/mailpreferences/expmailbouncereader.php`
  396-470) splits the raw message with its own `parts()`, looks for `message/feedback-report` first (complaint;
  `not-spam` is none; addresses from `Original-Rcpt-To`, else the To of the embedded `message/rfc822` or
  `text/rfc822-headers`), then for `message/delivery-status` or `message/global-delivery-status` (blocks split on
  blank lines; hard when `Action: failed` and the status is in `[BounceSettings] HardStatusCodes[]`, soft
  otherwise; `fix-newsletter-bounce-handling` adds `delayed` as kind delay). `addresses()` (578-587) keeps only
  ASCII addresses that `eZMail::validate()` accepts; it strips the `type;` prefix with a regular expression and
  decodes nothing. Tests: `tests/tests/kernel/classes/mailpreferences/MailPreferencesIntegrationTest.php`
  `testBounceClassification` reads `fixtures/bounces/*.eml` (needs a live installation:
  `ezpLiveInstallation::requireOrSkip()`); the fixtures are `arf-abuse`, `arf-headers-only`, `arf-not-spam`,
  `dsn-failed-policy`, `dsn-hard-user-unknown`, `dsn-mixed-recipients`, `dsn-soft-delayed`, `plain-auto-reply`
  (plus the two delay fixtures of `fix-newsletter-bounce-handling`).
- The kernel bounce reader is edited by two changes before this one
  (`fix-newsletter-bounce-handling`: kind delay; another change of the bounce reader: edits in `processMessage()`/`fetchRaw()`/`messageNumbers()`). This change touches `classify()` and adds one
  method next to it; the rules of the DSN loop are taken over as they stand at that time, not rewritten.
- Open upstream PRs #1-#4 and the other open mail changes touch none of the files of this change.
  The package work builds on the parser robustness change,
  because the delivery-status parser and the fixture layout of that change are built on; the PR is stacked on it.

Architecture rules this change touches and how the design keeps them:

- *Changes to lib/ and kernel/ are surgical; the kernel part must work with the released package.* The kernel
  edit is one feature check and one adapter method; `class_exists( 'ezcMailFeedbackReport' )` and
  `method_exists( 'ezcMailDeliveryStatus', 'getRecipientAddress' )` false means the own parser (today's path).
  Kernel code is meant for upstream: self-contained comments, no decision IDs; a row in the kernel table of
  AGENTS.md as long as the PR is open.
- *Tests never write to a site database.* The new kernel test is DB-free (static methods, INI defaults, fixtures).
- *No new Composer dependency in a package.* None.
- *No fork detail in artifacts meant for upstream.* The kernel part is written for any installation.

## Goals / Non-Goals

**Goals:** structured parts for ARF and RFC 6533 reports; recipient helpers with typed decoding; the kernel reader
on the package parts when available, provably equal to its own parser on the fixtures.

**Non-Goals:** generating reports; changing which message is hard, soft, delay or complaint (that logic stays
where it is and is reused by both paths); the reader's `addresses()` filter (ASCII only; the non-ASCII match is
a later change of the bounce classification); the newsletter.

## Decisions

### D1 Feedback-report part and parser
New `src/parts/feedback_report.php` (`ezcMailFeedbackReport extends ezcMailPart`): property `fields`
(`ezcMailHeadersHolder`; a field that occurs more than once holds an array of strings, a single one a string - the
holder stores what the parser sets), `generateHeaders()` sets `Content-Type: message/feedback-report` unless a
Content-Type header is already set, `generateBody()` writes the fields (one line per value), `getFeedbackType()`
(lower case, `''` when absent), `getOriginalRecipients()` (array of trimmed `Original-Rcpt-To` values, each run
through the decoder of D3 with type `rfc822`). Autoload rows in `src/mail_autoload.php`.
New `src/parser/parts/feedback_report_parser.php` (`ezcMailFeedbackReportParser extends ezcMailPartParser`): the
same line rules as the delivery-status parser of the parser robustness change D1 (field line, unfold, header
length cap), one section, repeated names collected into an array; `finish()` returns the part with `size` and the
part headers (`parsePartHeaders()` for Content-Disposition like the other parsers). The two parsers keep their
rules in step; a shared base was considered and rejected as a refactor of a parser that is itself being changed
in the previous change.

### D2 Type mapping
`createPartParserForHeaders()`: `message/feedback-report` → `ezcMailFeedbackReportParser`;
`message/global-delivery-status` → `ezcMailDeliveryStatusParser`; `message/global` → `ezcMailRfc822DigestParser`;
`message/global-headers` → `ezcMailTextParser( $subType, $headers )` (an `ezcMailText` with subType
`global-headers`, as `text/rfc822-headers` is an `ezcMailText` with subType `rfc822-headers`). The depth and count
limits of the previous change apply to the new mapping as to the old (a `message/global` is a message parser).
`ezcMailDeliveryStatus::generateHeaders()` and `ezcMailFeedbackReport::generateHeaders()` set their Content-Type
only when none is set, so a parsed `message/global-delivery-status` generates as such. The parsers hand the parsed
Content-Type to the part through `setHeaders( $headers->getCaseSensitiveArray() )` as the text parser does.

### D3 Recipient decoding
Public static `ezcMailDeliveryStatus::decodeRecipient( $value )` returns `array( 'type' => <lower case>,
'address' => <decoded> )`: split at the first `;` (no `;` → type `''`, the whole trimmed value as address); type
`rfc822`: RFC 3461 xtext, `+HH` with two upper-case hex digits becomes the byte (`+2B` → `+`; `user+shop` has no
hex pair after the plus and stays); type `utf-8`: RFC 6533 `\x{H..H}` becomes the UTF-8 character (`\x{FC}` → `ü`),
other text unchanged; any other type: trimmed value after the prefix. Instance helpers:
`getRecipientAction( $index )` (lower case, `''`), `getRecipientStatus( $index )` (first `\d\.\d{1,3}\.\d{1,3}`
match, `''`), `getRecipientAddress( $index )` (decoded address of Final-Recipient, else Original-Recipient, `''`).
Alternative: decode inside the parser. Rejected: the raw field stays available for callers that need it (the
kernel keeps the detail text), and the decoder serves values from other sources (the ARF Original-Rcpt-To).

### D4 Kernel: one seam, one adapter, same decision code
`expMailBounceReader`:
- `classify( $raw )` is split in two: `reports( $raw )` produces a neutral structure
  `array( 'feedback' => null | array( 'type' => string, 'recipients' => string[], 'embedded_to' => string[] ),
  'dsn' => null | array( array( 'address' => string, 'action' => string, 'status' => string ), ... ) )`, and the
  decision loop that follows works on it exactly as the present code works on the parsed parts (hard/soft/delay
  rules, `addresses()`, `isHardStatus()` untouched). `reports()` chooses `reportsFromPackage()` when
  `self::$parserSource` is `'package'` or (`'auto'` and the feature check of the Context holds), else
  `reportsFromOwnParser()` (the present `parts()`-based code moved into it).
- `reportsFromPackage( $raw )`: `ezcMailParser` with default options over an `ezcMailVariableSet`; a
  `try/catch( Throwable )` returns `null`, which makes `reports()` fall back to the own parser for that message.
  Feedback: the first `ezcMailFeedbackReport` of `fetchParts()` (with `includeDigests`), its type and
  `getOriginalRecipients()`; embedded To: the `To` header of the first `ezcMailRfc822Digest` or the text of the
  first `ezcMailText` with subType `rfc822-headers`/`global-headers` parsed by `parseHeaders()`. DSN: the first
  `ezcMailDeliveryStatus`, each recipient through the three helpers of D3 (the recipient value goes to
  `addresses()` as before, so the ASCII filter still decides what counts).
- `protected static $parserSource = 'auto'` with `setParserSourceForTest( $source )` next to
  `setStatusFileForTest()`; the test runs every fixture with `'own'` and `'package'` and compares.
The split keeps the diff small for the two changes that edit the same file before this one (the decision loop
keeps its lines; only its input changes).

### D5 Kernel test without a database
New `tests/tests/kernel/classes/mailpreferences/MailPreferencesBounceParserTest.php` (no `requireOrSkip()`; the
root bootstrap gives `eZINI` with the shipped `mailpreferences.ini` defaults and `eZMail::validate()`): Scenario:
Same result on the bundled bounces (every `fixtures/bounces/*.eml` through both sources, `assertSame` on the
array), Scenario: Package without the report parts (`'own'` forced: the result equals the documented expectation
of `testBounceClassification` for `dsn-hard-user-unknown.eml` and `arf-abuse.eml`), Scenario: Encoded recipient in
a hard bounce (new fixture `dsn-hard-xtext-recipient.eml`, `Final-Recipient: rfc822; user+2Bshop@example.com`,
status 5.1.1: address `user+shop@example.com` from the package path; the own path gives `user+2bshop@example.com`
after lower-casing - this one fixture is excluded from the equality loop with a comment, it is what the package
path adds). A second new fixture `dsn-global-utf8-recipient.eml` (`message/global-delivery-status`,
`Final-Recipient: utf-8; j\x{FC}rgen@example.org`) proves the package path parses it; its address is dropped by
`addresses()` in both paths today (kind soft/hard without address as the present code does), which the test pins
until a later change of the bounce classification widens the filter. The test skips when `class_exists(
'ezcMailFeedbackReport' )` is false (RELEASED package) except for the own-parser scenario.

### D6 Documentation
Package: `docs/tutorial.txt` section "Parsing delivery status notifications and feedback reports" (part classes,
helpers, decoding, the `report-type` mapping) after "Parsing a message set". Kernel:
`doc/eng-us/features/6.0/mail-preferences.md` gets a paragraph on the two parsing paths and the package version
that carries the report parts; the kernel table of `AGENTS.md` gets a row (file, what changed, guard
`MailPreferencesBounceParserTest`, "Upstream-PR offen").

## Risks / Trade-offs

- [The package parser writes attachment parts of a bounce (an original message with a PDF) to disk] → only for
  `application/*` and similar parts inside the original; the directory is removed at shutdown
  (`ezcMailParserShutdownHandler`); the reader processes at most `MaxMessages` per run. Documented in D6.
- [Both paths drift apart when later changes edit only one] → the equality test of D5 runs in `kernel-classes`
  without a database, so it is blocking in CI.
- [A parse exception in the package path hides a message] → `reportsFromPackage()` returns `null` on any throwable
  and the own parser handles the message; the exception is logged once with `eZDebug::writeWarning()`.
- [Repeated-field representation of the feedback part (string or array)] → `getOriginalRecipients()` normalises;
  documented in the tutorial.

## Migration Plan

Package release (Owner), then the kernel PR; the kernel works with both package versions, so order does not
matter for deployment. Rollback: previous package or kernel version, no data involved.
