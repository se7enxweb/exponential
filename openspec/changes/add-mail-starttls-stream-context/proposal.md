# Proposal

Redmine #16780 (https://pm.team.in-mv.com/issues/16780), subticket of #16766.

Meant for upstream (se7enxweb): the package part goes to `se7enxweb/expcomponents-mail`, the small kernel part
(settings of the bounce reader) to `se7enxweb/exponential`. Written for any installation; no siteaccess, customer
or product detail of this fork in any artifact. Depends on `add-mail-imap-processing` (the fake IMAP and POP3
servers of its `tests/local_imap/` and `tests/local_pop3/` are extended here with TLS; it comes first).

## Why

The IMAP and POP3 transports of the package connect either in the clear (`tcp://`) or with implicit TLS
(`ssl://`, option `ssl`), and `ezcMailTransportConnection` (`src/transports/transport_connection.php` 98-104)
opens the socket without a stream context: no CA file, no peer name, no minimum TLS version, no way to pin or to
relax verification - the SMTP transport has all of that in `connectionOptions` and a STARTTLS mode, the mailbox
transports have none. Servers that offer port 143/110 with `STARTTLS`/`STLS` only cannot be used encrypted, and a
server that announces `LOGINDISABLED` (RFC 3501 6.2.3: do not send `LOGIN` on this connection) still gets the
password in the clear from `authenticate()`. Verified on master `9bb906e` and the open upstream PRs (which touch
only the SMTP transport).

## What Changes

- **Package, stream context**: a `connectionOptions` option on the common transport options (the same name and
  shape as the SMTP transport's), passed as the stream context to implicit TLS connections and to the TLS upgrade;
  `ssl` sub-options `cafile`, `capath`, `peer_name`, `verify_peer`, `verify_peer_name`, `allow_self_signed`,
  `crypto_method` work as PHP documents them. Default: an empty context, so behaviour is unchanged.
- **Package, STARTTLS**: an option `startTls` (default false) on the common transport options. IMAP sends
  `STARTTLS` after the greeting, POP3 sends `STLS`, both upgrade the socket to TLS with the context and fail with a
  clear error when the server refuses or the handshake fails. The capability list is re-read after the upgrade.
  `ssl` and `startTls` together are refused.
- **Package, LOGINDISABLED**: when the IMAP server lists `LOGINDISABLED` (in the greeting's capability code or on
  `CAPABILITY`), `authenticate()` with the LOGIN method fails before sending credentials, with an error that names
  the reason; other methods are unaffected.
- **Kernel**: the bounce reader's `[BounceSettings]` gets `StartTLS` (default `disabled`), `TLSMinimumVersion`
  (empty = PHP default) and `TLSContextOptions[]` (key=value pairs of the `ssl` context); the reader passes them to
  the transport when the installed package knows the options and ignores them with one warning otherwise. The
  shipped defaults change nothing.

No **BREAKING** change.

## Capabilities

### New Capabilities
- `mail/mailbox-transport-security`: how IMAP and POP3 connections are encrypted and verified (implicit TLS with a
  context, STARTTLS/STLS, LOGINDISABLED), and the bounce reader's settings for it.

### Modified Capabilities
- none.

## Impact

- Package `se7enxweb/expcomponents-mail`: `src/options/transport_options.php`,
  `src/transports/transport_connection.php`, `src/transports/imap/imap_transport.php` (constructor, `authenticate()`,
  capability cache reset), `src/transports/pop3/pop3_transport.php` (constructor, `CAPA`), the fake servers of
  `tests/local_imap/` and `tests/local_pop3/` (TLS modes, a self-signed certificate made at test time), new tests,
  `docs/tutorial.txt`. No new Composer dependency (`ext-openssl` is checked at run time as today for `ssl`).
- Kernel: `settings/mailpreferences.ini [BounceSettings]` (three keys), `kernel/classes/mailpreferences/expmailbouncereader.php`
  (`connect()`: one options builder), a DB-free test, `doc/eng-us/features/6.0/mail-preferences.md`, a row in the
  kernel table of `AGENTS.md`.
- Newsletter: no edit (its mailbox connection is `add-newsletter-mailbox-settings`; STARTTLS for newsletter
  mailboxes is a follow-up there).

## Out of scope

- SMTP: it has STARTTLS and `connectionOptions` already; untouched.
- XOAUTH2 and the token configuration: `add-mail-xoauth2-configuration`.
- Certificate pinning by fingerprint, client certificates (they work through the context's `local_cert` but are
  not tested here), DANE.
- Newsletter mailbox settings: `add-newsletter-mailbox-settings`.
