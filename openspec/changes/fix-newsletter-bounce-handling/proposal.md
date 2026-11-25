# Proposal: Newsletter bounce mailbox — five defects in reading and judging returned mail

Redmine #16765 (https://pm.team.in-mv.com/issues/16765). Meant for **upstream**
(se7enxweb): the newsletter part goes to `se7enxweb/cjw_newsletter`, the kernel part to `se7enxweb/exponential`.
Written for any Exponential installation.

## Why

A read-only review of the receiving side (24.11.2026) found five defects in how the newsletter reads its bounce
mailboxes and how returned mail is judged. Two of them have effects outside the installation: the mailbox password
is written into the newsletter log in plain text, and a "delivery delayed" notice makes the newsletter send the same
edition to the recipient a second time. The other three lose mail or bounce data silently.

## What Changes

- **Password in the log.** The newsletter's mailbox connection logs its settings on a failed login (error) and on a
  successful connect (debug), password included. The log entries keep every other setting; the password is replaced
  by a mask.
- **"Delayed" is not a bounce.** A delivery status notification whose recipients are only `delayed` (RFC 3464: the
  message is still being tried) is classified by the kernel's bounce reader as its own kind, not as a soft bounce.
  The newsletter records nothing for it and schedules no resend; the bounce reader counts it apart.
  **BREAKING** for code that relies on the current answer: `expMailBounceReader::classify()` returns the new kind
  `delay` where it returned `soft` so far; a bounce listener sees that kind.
- **Messages are deleted only after they are stored.** With "delete mails from server" on, a message is marked for
  deletion when it is fetched, so a message whose storing failed (or that came back empty) is deleted as well. The
  mailbox now deletes exactly the messages it stored (or already had), after storing them.
- **One mailbox failing does not stop the others.** An error while fetching from one mailbox ends the whole run, so
  later mailboxes are not read; the log names the wrong step ("parseMail-failed"). The failing mailbox is reported
  with its id and the run goes on with the next one.
- **Automatic replies are not soft bounces.** A message without a delivery status or feedback report whose headers
  mark it as an automatic reply (RFC 3834 `Auto-Submitted`, or `Precedence: bulk/junk/list`, as the newsletter's
  mail-in already checks) is no longer judged from codes in its text, so an out-of-office reply quoting "450" no
  longer counts as a soft bounce.

## Capabilities

### New Capabilities

- `mail-bounce-classification`: how the kernel's bounce reader judges a returned message (hard, soft, delay,
  complaint, none) and what it counts.
- `newsletter-bounce-mailbox`: how the newsletter reads its bounce mailboxes, what it logs, what it deletes, and what
  it does with each kind of returned message.

### Modified Capabilities

None (no existing spec covers bounces or the newsletter mailboxes).

## Out of scope

- Everything else from the review of 24.11.2026, to be planned separately:
  - package side (`se7enxweb/expcomponents-mail`): IMAP UIDVALIDITY, unknown charsets, robustness of the DSN parser,
    STARTTLS and stream context for IMAP/POP3, parser limits, mailbox name encoding, report parts for ARF and
    RFC 6533;
  - IMAP `MOVE`/`UID EXPUNGE`, a "processed" folder instead of deleting;
  - a size limit for the kernel's bounce reader, non-ASCII and xtext addresses in its address match;
  - finer classification (DMARC and policy codes, soft bounces with addresses, thresholds over several runs);
  - a configurable connection timeout and IMAP folder for newsletter mailboxes;
  - the old code-based classification left in the newsletter's mail parser.
- XOAUTH2 for the mailboxes: Redmine #16720.
- Sending side (DKIM, SMTPUTF8, bulk sender headers, VERP, one-click unsubscribe): the changes
  `add-mail-dkim-signing`, `add-mail-smtputf8`, `add-mail-bulk-sender-headers`.

## Impact

- Newsletter (`extension/cjw_newsletter`, package `se7enxweb/cjw_newsletter`): mailbox class, bounce class, tests,
  `doc/deliverability.md`, CHANGELOG.
- Kernel: `kernel/classes/mailpreferences/expmailbouncereader.php` (a new kind and a counter), the bounce reader's
  status and its test fixture expectations. No INI key, no database change.
- Behaviour visible to administrators: the bounce reader's counts gain "delayed"; the newsletter's log no longer shows
  the mailbox password; fewer soft bounces and resends.
