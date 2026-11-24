# Proposal: Newsletter — one classification for returned mail, the old code-based parser goes

Redmine #16783 (https://pm.team.in-mv.com/issues/16783), subticket of #16766.

Meant for **upstream** (se7enxweb/cjw_newsletter): newsletter code only (`extension/cjw_newsletter` in this tree,
carried to the package clone as the same diff). No kernel change. Written for any Exponential installation.

Depends on `fix-newsletter-bounce-handling` (Redmine #16765): the newsletter's bounce class knows the kinds
`delay` and `autoreply` and the kernel's classifier decides; this change removes what that decision made
redundant. Works with and without a later change of the bounce classification (a reason is passed through when the kernel
gives one).

## Why

Since 4.2.0 the kind of a returned message comes from the kernel's bounce classifier (`CjwNewsletterBounce::classify()`),
with the newsletter's own SMTP-code guess as the fallback. The mail parser of the mail accounts still carries the
classification from before: it looks at the second MIME part only (whatever it is), at the first recipient of a
delivery status only, ignores the `Status` field, matches status codes with an unescaped dot (`5.1.1` matches
`5x1y1`), and returns a bare code that the later classification then re-reads. A review on 24.11.2026 listed these
as defects; none of them changes the outcome today only because the kernel's answer wins whenever the kernel is
present — the parser's result is used for the mailbox item's `bounce_code` column and as the fallback. Two parsers
for the same message are one too many: every fix to the classification has to be made twice or is made once and
drifts.

## What Changes

- The mail parser of the mail accounts no longer classifies. It still reads the standard headers (subject, from,
  to, date) and the newsletter's own `X-CJWNL-*` headers, and it asks the newsletter's bounce class once for the
  classification of the whole message; the code it records with the item is that classification's detail.
- The fallback for installations without the kernel's classifier is reduced to one place in the bounce class: the
  diagnostic codes of every recipient of a delivery status and the first text part, read with the escaped status
  pattern the bounce class already has. The second MIME part is no longer assumed to be the status.
- Nothing changes in what is recorded or done for a delivery status notification, a feedback report, a delay
  notice or an automatic reply; tests over the fixtures prove the code, kind, action and recipient before and after.
- `final_recipient` of the parse result is the first address the classification found (today: the first
  recipient of the second part), which the item does not store (the column does not exist) — kept in the result for
  the parse log.

## Capabilities

### New Capabilities

None.

### Modified Capabilities

- `newsletter-bounce-mailbox` (added by `fix-newsletter-bounce-handling`): what a mail account records for a
  returned message and that one classification decides.

## Out of scope

- The classification itself (kinds, reasons, thresholds): kernel changes `fix-newsletter-bounce-handling`,
  another change of the bounce reader, a later change of the bounce classification.
- The mail-in path (subscribe/unsubscribe by mail) and the mailbox reading (`add-newsletter-mailbox-settings`).
- The admin views that show `bounce_code` (unchanged column, unchanged meaning: the status code or feedback type).

## Impact

- Newsletter (`extension/cjw_newsletter`, package `se7enxweb/cjw_newsletter`): `classes/cjwnewslettermailparser.php`
  (two private methods removed, one small fallback helper), `classes/deliverability/cjwnewsletterbounce.php` (the
  fallback in one place, the parser's classification reused), `classes/cjwnewslettermailboxitem.php` (one
  classification per item), `doc/deliverability.md`, CHANGELOG; tests in `tests/tests/extension/cjw_newsletter/`.
- No INI key, no table, no kernel change. Visible to administrators: nothing; the `bounce_code` of new items is
  the enhanced status code (`5.1.1`) where it was the SMTP reply (`550 5.1.1`) before, when the kernel classifier is
  present — as it already is since 4.2.0 for items the kernel classified.
