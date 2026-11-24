# Proposal

Redmine #16778 (https://pm.team.in-mv.com/issues/16778), subticket of #16766.

Meant for upstream (se7enxweb): the package part goes to `se7enxweb/expcomponents-mail`, the kernel part to
`se7enxweb/exponential`. Written for any Exponential installation; no siteaccess, customer or product detail of
this fork in any artifact. Depends on the parser robustness change (the delivery-status parser this change extends;
it comes first) and, for the kernel part, on another change of the bounce reader and
`fix-newsletter-bounce-handling` only in the sense that the kernel edit is made on top of what they left in
`expMailBounceReader` (it does not need their code).

## Why

The mail package parses `message/delivery-status` into a part with per-recipient fields, but a feedback-loop report
(`message/feedback-report`, RFC 5965 ARF: a spam complaint) and the international forms of RFC 6533
(`message/global-delivery-status`, `message/global`, `message/global-headers`) fall through to the generic file
parser (`src/parser/interfaces/part_parser.php` 170-184): the report lands as a temporary file on disk, and a caller
has to parse it again by hand. That is why the kernel's bounce reader carries a complete MIME and field parser of
its own (`kernel/classes/mailpreferences/expmailbouncereader.php` 396-584). Recipient addresses in a status report
are typed (`rfc822;`, `utf-8;`) and may be encoded (RFC 3461 xtext, RFC 6533 `\x{..}`); nothing in the package
decodes them.

## What Changes

- **Package, new parts**: `message/feedback-report` is parsed into a feedback-report part with its fields
  (feedback type, original recipients, reported URIs, ...); `message/global-delivery-status` is parsed like
  `message/delivery-status`; `message/global` like `message/rfc822`; `message/global-headers` like
  `text/rfc822-headers`. In a `multipart/report` with `report-type=feedback-report` the machine-readable part is the
  feedback-report part.
- **Package, helpers**: the delivery-status part answers, per recipient, the action (lower case), the status code
  (`d.d.d`) and the recipient address, decoded from its address type: `rfc822;` with RFC 3461 xtext (`+2B` is `+`),
  `utf-8;` with RFC 6533 `\x{HH}` sequences; the feedback-report part answers its type and its original
  recipients. A static decoder is public for callers that hold a raw field value.
- **Kernel**: the bounce reader uses the package's report parsing when the installed package has it and falls back
  to its own parser otherwise, with the same classification for the same message (the released package has no
  feedback-report part yet). A test proves both paths give the same result on the bundled fixtures.

No **BREAKING** change: a mail that was parsed into a file part for these types now yields a structured part; the
file part's `fileName` for them is gone (it was a temporary file removed at shutdown, never a documented contract).

## Capabilities

### New Capabilities
- `mail/delivery-reports`: parsing of delivery status notifications and feedback reports, the typed recipient
  address decoding, and the bounce reader's use of it.

### Modified Capabilities
- none.

## Impact

- Package `se7enxweb/expcomponents-mail`: new `src/parts/feedback_report.php`,
  `src/parser/parts/feedback_report_parser.php`, their rows in `src/mail_autoload.php` (the Composer classmap finds
  them without it, the row keeps the package's own autoload complete); `src/parser/interfaces/part_parser.php`
  (type mapping); `src/parts/delivery_status.php` (helpers, Content-Type kept on generation); tests and fixtures
  under `tests/parser/`; `docs/tutorial.txt`. No new Composer dependency; PHP 7.1+ code.
- Kernel: `kernel/classes/mailpreferences/expmailbouncereader.php` (one parsing seam, one package adapter),
  a new DB-free test under `tests/tests/kernel/classes/mailpreferences/`, two fixtures, a paragraph in
  `doc/eng-us/features/6.0/mail-preferences.md`, a row in the kernel table of `AGENTS.md`.
- Newsletter: no edit; `CjwNewsletterBounce::classify()` calls the kernel reader and benefits.

## Out of scope

- Other changes of the bounce reader: another change of the bounce reader.
- Reasons per bounce kind, soft-bounce thresholds and the non-ASCII address match of the reader's `addresses()`:
  a later change of the bounce classification.
- Removing the newsletter's old code-based parser: `remove-newsletter-legacy-bounce-parser`.
- Generating ARF reports (the package only parses them).
