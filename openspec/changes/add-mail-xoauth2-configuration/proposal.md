# Proposal

Redmine: **#16720** (existing ticket, `https://pm.team.in-mv.com/issues/16720`), point 3 of its scope: the
configuration of XOAUTH2 for the mail connections of the CMS. Points 1 and 2 of the ticket (XOAUTH2 in the
package for SMTP and IMAP) are already in the package; this change verifies them against the fake servers and
adds the missing POP3 side.

Meant for upstream (se7enxweb): the package part goes to `se7enxweb/expcomponents-mail`, the kernel part to
`se7enxweb/exponential`, the newsletter part to `se7enxweb/cjw_newsletter`. Written for any installation; the
artifacts say "a settings file that is not in version control".
Depends on `add-mail-starttls-stream-context` (branch and fake servers; it comes first) and, for the
newsletter part, on `fix-newsletter-bounce-handling` (its `connect()` changes are in the tree already).

## Why

Large providers turn off password login for IMAP, POP3 and SMTP and require OAuth 2.0 bearer tokens (SASL
XOAUTH2). The package authenticates with XOAUTH2 over IMAP (`ezcMailImapTransport::AUTH_XOAUTH2`,
`imap_transport.php` 262/596/662) and SMTP (`authXOAuth2()`, `smtp_transport.php` 1029) but not over POP3
(`pop3_transport.php` 366-415: plain and APOP only) - verified on master `9bb906e`. In the CMS, SMTP already takes
`[MailSettings] TransportAuthMethod=XOAUTH2` with the token as `TransportPassword`; the bounce reader
(`expMailBounceReader::connect()`) always calls `authenticate( $user, $password )` with the default method, and the
newsletter mailbox (`CjwNewsletterMailbox::connect()`) likewise. A bearer token is short-lived, so a static
password field is the wrong place for it: the token must come from somewhere that is refreshed outside the CMS
(a file written by a refresh job, a class, an environment variable), and it must never be logged.

## What Changes

- **Package**: `AUTH XOAUTH2` for the POP3 transport (RFC 5034 SASL, the Google/Microsoft XOAUTH2 mechanism:
  `AUTH XOAUTH2 <base64>`, the server's `+ <base64 JSON>` error challenge answered with an empty line); the IMAP
  and SMTP XOAUTH2 paths get tests against the fake servers (success, rejected token with the challenge, the
  mechanism absent from the capabilities). The fake IMAP and POP3 servers learn `AUTHENTICATE XOAUTH2`/`AUTH XOAUTH2`.
- **Kernel, token source**: one helper that resolves a configured token source to a bearer token: empty (the
  configured password/token value itself), `file:<path>` (the trimmed content of a file, path absolute or
  relative to the installation root), `env:<NAME>` (an environment variable), `class:<Class>` (a static method
  of a class of the installation that returns the token for a user). A missing or empty token is logged (without
  the token) and the connection is not attempted.
- **Kernel, settings**: `site.ini [MailSettings] TransportTokenSource` for SMTP next to `TransportAuthMethod`;
  `mailpreferences.ini [BounceSettings] AuthMethod` (`login` default for IMAP, `plain` for POP3; `apop`;
  `xoauth2`) and `TokenSource` for the bounce reader. When the installed package has no POP3 XOAUTH2, the reader
  reports it instead of sending the token as a password.
- **Newsletter**: `cjw_newsletter.ini [MailboxAuthSettings]` with `AuthMethod[<user name>]` and
  `TokenSource[<user name>]` per mailbox account (the mailbox table is unchanged); `CjwNewsletterMailbox::connect()`
  authenticates accordingly through the kernel helper; the token never appears in the newsletter log.

No **BREAKING** change: every default is the present behaviour.

## Capabilities

### New Capabilities
- `mail/mailbox-oauth-authentication`: XOAUTH2 over POP3, IMAP and SMTP in the package; token sources and the
  authentication settings of the CMS's SMTP transport, bounce reader and newsletter mailboxes.

### Modified Capabilities
- none.

## Impact

- Package: `src/transports/pop3/pop3_transport.php`, `src/options/pop3_options.php`
  (`authenticationMethod` accepts the new constant), fake servers under `tests/local_imap/` and `tests/local_pop3/`,
  new tests, `docs/tutorial.txt`.
- Kernel: new `lib/ezutils/classes/ezmailtokensource.php` with its hand-written row in `autoload/ezp_kernel.php`;
  `lib/ezutils/classes/ezsmtptransport.php` (token source before the transport is built);
  `kernel/classes/mailpreferences/expmailbouncereader.php` (`connect()`: method and token); `settings/site.ini`,
  `settings/mailpreferences.ini`; tests in the `lib` and `kernel-classes` suites; `doc/eng-us/features/6.0/`
  (new page `mail-oauth2.md`), a row in the kernel table of `AGENTS.md`.
- Newsletter (`extension/cjw_newsletter`): `settings/cjw_newsletter.ini`, `classes/cjwnewslettermailbox.php`,
  a test in `tests/tests/extension/cjw_newsletter/`, `doc/deliverability.md`, README; carried to the newsletter
  clone as the same diff.

## Out of scope

- The OAuth 2.0 authorization flow and token refresh: outside the CMS (a refresh job writes the file, or the
  named class fetches it); the ticket says so.
- XOAUTH2 for the newsletter's own SMTP settings (`SmtpTransport*` in `cjw_newsletter.ini`): a follow-up ticket;
  the newsletter may already route through the site's `[MailSettings]`.
- STARTTLS and stream contexts: `add-mail-starttls-stream-context`. New IMAP commands: `add-mail-imap-processing`.
- OAUTHBEARER (RFC 7628): not offered by the package; a later ticket.
