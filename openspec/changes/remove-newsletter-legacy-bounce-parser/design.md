# Design: One classification for returned mail, the old code-based parser goes

See proposal.md (Why, What Changes) and `specs/newsletter-bounce-mailbox/spec.md` for the requirements.

## Context

Verified against the tree on 24.11.2026 (`extension/cjw_newsletter`, identical in the package clone 4.3.0 for the
three class files):

- `CjwNewsletterMailParser` (`classes/cjwnewslettermailparser.php`, 429 lines): `parse()` (149–204) parses the
  raw message with `ezcMailParser` and returns `getHeaders()` + `getCjwHeaders()`. `getHeaders()` (212–235) reads
  Subject/From/To/Date and calls `fetchErrorByStatus()` (244–302): `fetchParts()[1]` must be an
  `ezcMailDeliveryStatus` (249), only `recipients[0]` is read (256–262), `Action` and `Status` are commented out
  (259–260), the code comes from `Diagnostic-Code` through `standardParser()` (310–391), whose patterns use an
  unescaped dot (`'(5[0-9][0-9] [0-9].[0-9].[0-9])'`, 348, 354, 360). Without a status part, the first part must
  be `ezcMailText` (289). All findings hold. `getCjwHeaders()` (398–426) scans the first 200 raw lines for
  `x-cjwnl-*`; `maxMessageSize()`, `getTmpDir()`, `tmpDirIsWritable()` stay.
- `CjwNewsletterMailboxItem::parseMail()` (357–369) → `saveParsedInfos( $parsed )` (400–500): stores `from`, `to`,
  `subject`, `error_code` (as `bounce_code`), `email_send_date`; looks up the send item or user from the
  `x-cjwnl-*` keys; then calls `CjwNewsletterBounce::classify( raw, error_code )` a second time over the raw
  message, overwrites `bounce_code` with the classification's detail or kind when the parser found no code
  (451–455), and `handle()`s. `isBounce()` (515–531) is "`bounce_code` is not empty/0".
- `CjwNewsletterBounce::classify( $raw, $code )` (83–96): kernel first; `kindOfCode( $code )` as the fallback;
  `statusOf()` (363–370) has the escaped patterns; `handleMailboxItem( $item, $parsed )` (339–358) classifies with
  `$parsed['error_code']` and maps the send item/user from `$parsed` — a third call path over the same message
  with **no caller** in the extension's classes, modules, cronjobs, bin scripts or the tree's tests (grep on
  24.11.2026: only its definition). After `fix-newsletter-bounce-handling` D4 `classify()` also knows `delay`/`autoreply`.
- Admin templates show `bounce_code` (`mailbox_item_list.tpl`, `mailbox_item_view.tpl`, admin4 and standard);
  no template reads `final_recipient`.
- Tests: `cjwNewsletterBounceTest` (`testBounceWithTheSendItemHashBouncesTheItemAndTheUser`,
  `testMailThatIsNoBounceHasNoBounceCode`, `testSoftBounceCodeAndGarbage`, `testHeadersAreFoundInAnyCase`, …; skips
  without the installed site), `cjwNewsletterDeliverabilityTest` (fixtures `fixtures/deliverability/dsn-hard.eml`,
  `dsn-soft.eml`, `arf-complaint.eml`, plus the base change's `dsn-delayed.eml`, `auto-reply-450.eml`,
  `dsn-hard-auto-submitted.eml`).

## Goals / Non-Goals

- Goal: one classification per message; the parser records, the bounce class judges; the fallback without the
  kernel in one place and correct for any part order and any recipient; proof that outcomes do not change.
- Non-goal: changing the kinds, the actions, the `bounce_code` column or the templates; touching the mail-in path.

## Architecture rules touched

- *New functionality goes into an extension*: all code in `extension/cjw_newsletter`; no kernel edit.
- *Configuration is INI*: no key. *Database*: no change (`final_recipient` has no column; it stays in the result
  array only).
- *Tests never write to a site database*: the proof tests run `CjwNewsletterMailParser` on items built in memory
  (D4) and skip the database-backed cases as the existing tests do.

## Decisions

### D1 The parser records, the bounce class judges

`CjwNewsletterMailParser::parse()` returns `subject`, `from`, `to`, `email_send_date`, the `x-cjwnl-*` keys, and
`classification` = `CjwNewsletterBounce::classify( $raw )` (one call, `$code` omitted), `error_code` =
`classification['detail']` (or `'0'` when the kind is none/delay/autoreply, the value `isBounce()` treats as
"no bounce"), `bounce_kind`, `bounce_reason` (when present) and `final_recipient` = the first of
`classification['addresses']`. `fetchErrorByStatus()` and `standardParser()` are removed; `getHeaders()` keeps the
four headers. The parser keeps the `ezcMailParser` step because the date and the headers come from the parsed
mail and the tmp-dir handling stays.

### D2 One fallback, in the bounce class

`CjwNewsletterBounce::classify( $raw, $code = '' )`: when the kernel is absent or says none and `$code` is empty,
`$code = self::codeInMessage( $raw )`: a delivery-status part's blocks (found with the same light MIME walk the base
change added for `isAutomaticReply()` — the top-level headers parser extended to parts, or, when
`expMailBounceReader::parts()` exists, that), every recipient's `Status`, else `Diagnostic-Code`, else the first
text part, passed through `statusOf()` (escaped patterns). `kindOfCode()` then decides as today. The automatic-reply
check of the base change runs before (a quoted "450" in an out-of-office reply stays `autoreply`). The result gains
`source => 'text'` for this path so the parse log tells it from `code` (a code the caller passed, as `sendFailed()`
does).

### D3 `saveParsedInfos()` reuses the parser's classification; `handleMailboxItem()` goes

`saveParsedInfos()` takes `$parsed['classification']` when present instead of calling `classify()` again (older
callers that pass a result without it get the call as before), and no longer overwrites `bounce_code` (the parser
already records the detail). `handleMailboxItem()` is removed (no caller; `saveParsedInfos()` is the item flow). The item flow is: parse once →
record → look up send item/user → `handle()`.

### D4 Tests prove nothing changes

`tests/tests/extension/cjw_newsletter/cjwNewsletterMailParserTest.php` (new, `cjwNewsletterTestCase`): a mailbox
item built in memory (`new CjwNewsletterMailboxItem( array( … ) )` with `getRawMailMessageContent()` served from a
temporary file — the class reads its file path from the row; the test writes the fixture to `var/tests/…` and sets
the path fields) for each fixture of `fixtures/deliverability/` and the kernel's `fixtures/bounces/` (read-only,
via the kernel test directory): a table of expected `error_code`, `bounce_kind`, `final_recipient` per fixture,
written **before** the parser is changed from the current output (task 1.2 records it) and asserted unchanged
after; `testTheSecondPartIsNotAssumed` (a DSN with the status as third part, kernel absent simulated by a subclass
seam `kernelAvailable()` in the bounce class → 5.1.1 hard); `testStatusDotsAreEscaped` ("5x1y1" → no code);
`testEveryRecipientIsRead`. `cjwNewsletterBounceTest` and `cjwNewsletterDeliverabilityTest` stay green (their
database cases skip without the site, as on the baseline).

## Risks / Trade-offs

- [`bounce_code` of items without the kernel changes form: the SMTP reply (`550 5.1.1`) becomes the enhanced
  status (`5.1.1`) when `statusOf()` finds one] → `isBounce()` and the templates read it as a string; the kind is
  the same; noted in the CHANGELOG.
- [A caller of `CjwNewsletterMailParser::parse()` outside the extension expects `error_code` only] → the key stays;
  the new keys are additive.
- [The light MIME walk of D2 misses an exotic DSN] → it is the no-kernel fallback only; with the kernel (every
  Exponential 6.0.15+) `expMailBounceReader::classify()` decides.

## Migration Plan

No data, INI or table change. Deploy with the newsletter. Rollback: revert. Upstream: one PR to
`se7enxweb/cjw_newsletter` from the clone's branch, stacked on the bounce-handling PR while that is open.
