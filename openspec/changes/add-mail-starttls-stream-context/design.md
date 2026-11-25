# Design

## Context

See proposal.md - Why. Current state, read in the code (master `9bb906e`, plus the branches of the previous
changes of this series):

- `ezcMailTransportConnection::__construct( $server, $port, $options )` (`src/transports/transport_connection.php`
  80-125): `ssl` true → `stream_socket_client( "ssl://..." )` after an openssl check, else `tcp://`; no stream
  context; `stream_set_timeout()`. `private $connection` (the socket), `sendData()`, `getLine()`, `isConnected()`,
  `close()`. The SMTP transport does not use this class; it has `connectionOptions` (array, stream context,
  `src/options/smtp_options.php` 108-114) and `startTls()` with `stream_socket_enable_crypto( ..., true,
  STREAM_CRYPTO_METHOD_TLS_CLIENT )` (`smtp_transport.php` 706-720).
- `ezcMailTransportOptions` (`src/options/transport_options.php`): `timeout`, `ssl`. `ezcMailImapTransportOptions`
  and `ezcMailPop3TransportOptions` extend it; `ezcMailSmtpTransportOptions` also extends it and defines
  `connectionOptions` itself (its constructor sets the default before calling the parent).
- `ezcMailImapTransport::__construct()` (392-422) reads the greeting (`* OK`, possibly `[CAPABILITY ...]` inside),
  detects Gmail/Zoho, state `NOT_AUTHENTICATED`. `authenticate()` (579-640) sends `LOGIN` through `sendLogin()`
  without looking at capabilities. `capability()` (2448) exists; `add-mail-imap-processing` adds `serverSupports()`
  with a per-connection cache.
- `ezcMailPop3Transport::__construct()` (203-229) reads the greeting; `authenticate()` (366-415) USER/PASS or APOP;
  no `CAPA`.
- Kernel `expMailBounceReader::connect()` (`kernel/classes/mailpreferences/expmailbouncereader.php` 332-354) builds
  `ezcMailPop3TransportOptions`/`ezcMailImapTransportOptions` with `ssl` from `[BounceSettings] SSL` and nothing
  else. another change of the bounce reader edits `fetchRaw()`/`messageNumbers()`/`processMessage()`;
  `add-mail-xoauth2-configuration` (next) edits `connect()` for the auth method, on top of this change.
- Fake servers (`tests/local_imap/`, `tests/local_pop3/`, from `add-mail-imap-processing`): child processes with
  a JSON config; the key `tls` is reserved there for this change.
- Overlap: PRs #1-#4 and the other open mail changes touch `smtp_transport.php`/`smtp_options.php`, not the common
  options or the connection class; `smtp_options.php` keeps defining `connectionOptions` itself (same name, same
  type), so the parent's new definition is shadowed consistently. It builds on
  `add-mail-imap-processing` (its branch): the fake servers live there.

Architecture rules this change touches and how the design keeps them:

- *Changes to lib/ and kernel/ are surgical; the kernel part works with the released package.* One options builder
  in the reader with `property_exists()`-style feature checks (a `try/catch ( ezcBasePropertyNotFoundException )`
  around setting the new options), one warning, defaults unchanged. Comments self-contained (meant for upstream);
  a row in the kernel table of AGENTS.md.
- *Secrets and server-specific values do not go into versioned settings.* The new keys are empty or `disabled` in
  `settings/mailpreferences.ini`; the existing comment there already sends server, user and password to the
  unversioned override, and the new keys say the same for CA paths.
- *Tests never hit the network; no real keys in fixtures.* The certificate is generated at test time with
  `openssl_pkey_new()`/`openssl_csr_sign()` into a temporary file (never committed; `.gitignore` entry for
  `tests/local_*/tmp/` if files are written under the tree).
- *No new Composer dependency.* `ext-openssl` is checked at run time as today.

## Goals / Non-Goals

**Goals:** verified implicit TLS, STARTTLS/STLS, LOGINDISABLED for the mailbox transports; the bounce reader able
to use them by settings; all proven against the fake servers with a real TLS handshake.

**Non-Goals:** the SMTP transport; client certificates and pinning as tested features; the newsletter mailbox
object (`add-newsletter-mailbox-settings`); a change to `getLine()`/`sendData()`.

## Decisions

### D1 Options on the common class
`ezcMailTransportOptions` gets `connectionOptions` (array, default `array()`, the stream context in
`stream_context_create()` form, e.g. `array( 'ssl' => array( 'cafile' => ..., 'peer_name' => ... ) )`) and
`startTls` (bool, default false). Setting `startTls` true while `ssl` is true, or `ssl` true while `startTls` is
true, throws `ezcBaseValueException` naming both (checked in `__set()` of both cases). `ezcMailSmtpTransportOptions`
keeps its own `connectionOptions` case (identical semantics) and does not get `startTls` (it has `connectionType`).
Alternative: options only on the IMAP and POP3 option classes. Rejected: the connection class takes the common
options, and the two transports need the same two keys.

### D2 Connection class: context and upgrade
`ezcMailTransportConnection::__construct()`: `$context = stream_context_create( $this->options->connectionOptions )`
and `stream_socket_client( $uri, $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT, $context )` for both `ssl://` and
`tcp://` (an empty array gives a default context, so plain connections are unchanged). New method
`enableCrypto()`: `$method = connectionOptions['ssl']['crypto_method']` when set, else
`STREAM_CRYPTO_METHOD_TLS_CLIENT`; `stream_socket_enable_crypto( $this->connection, true, $method )`; `false` or
`0` → `ezcMailTransportException( "Could not start TLS on the connection to {$server}:{$port}." )` (the stream's
last error text appended when PHP gives one, never the credentials). `peer_name` etc. are read by PHP from the
context the socket was created with, so verification applies to the upgrade too. The class remembers `$server`
and `$port` for the messages. Alternative: subclassing the connection per transport. Rejected: one socket, one
upgrade, two callers.

### D3 IMAP: STARTTLS and LOGINDISABLED
`ezcMailImapTransport::__construct()`: after the greeting, when `options->startTls`: `{$tag} STARTTLS`, tagged
`OK` → `$this->connection->enableCrypto()`, anything else → `ezcMailTransportException( "The IMAP server refused
STARTTLS: {$response}." )` and `close()`. The capability cache of `add-mail-imap-processing` is cleared after the
upgrade (`$this->capabilities = null`), and a capability code found in the greeting (`[CAPABILITY ...]`) is stored
into the cache only when no upgrade follows. `authenticate( ..., AUTH_LOGIN )`: `serverSupports( 'LOGINDISABLED' )`
true → `ezcMailTransportException( "The IMAP server disables LOGIN on this connection (LOGINDISABLED); use
STARTTLS, implicit TLS or another authentication method." )` before `sendLogin()`. XOAUTH2 is not affected (SASL
mechanisms are governed by `AUTH=` capabilities, left as they are).

### D4 POP3: STLS
`ezcMailPop3Transport::__construct()`: after the greeting, when `startTls`: `STLS`; `+OK` → `enableCrypto()`;
`-ERR` → exception naming the refusal and `close()`. The APOP timestamp is taken from the greeting of the plain
connection (RFC 2595 allows it; the test covers APOP after STLS). No `CAPA` probing is added (a server that offers
STLS answers it; one that does not says `-ERR`, which is the error the caller gets).

### D5 Fake servers with TLS
Both fake servers get the config key `tls`: `none` (default), `starttls` (accept `STARTTLS`/`STLS` once, reply OK,
then `stream_socket_enable_crypto( $connection, true, STREAM_CRYPTO_METHOD_TLS_SERVER )`), `implicit` (enable
right after `accept`), `refuse` (answer `NO`/`-ERR` to the upgrade), plus `cert` (path of a PEM file with
certificate and key, used as `local_cert` of the server's context) and, for IMAP, `capabilities_plain` (the list
announced before an upgrade, e.g. with `LOGINDISABLED`) next to `capabilities` (after). The test case classes get
`selfSignedCertificate()` (CN `localhost`, 2048-bit RSA, valid one day, written to a temporary file, removed in
`tearDown()`; the test skips when `openssl_pkey_new()` is unavailable) and `tlsClientOptions( $certFile )`
returning `array( 'ssl' => array( 'cafile' => $certFile, 'peer_name' => 'localhost' ) )`. The connection goes to
`127.0.0.1`; `peer_name => 'localhost'` matches the certificate's CN/SAN, which is the point of the option.
A wrong peer name (`peer_name => 'other.invalid'`) is the negative case. The fake records whether the upgrade
happened (`state()['tls']`) and the commands received after it, so a test can assert that LOGIN travelled after
the upgrade.

### D6 Kernel: settings and options builder
`settings/mailpreferences.ini [BounceSettings]`: `StartTLS=disabled` (enabled: STARTTLS for IMAP, STLS for POP3;
not with `SSL=enabled`), `TLSMinimumVersion=` (empty = PHP's default; `1.2` or `1.3` set the crypto method to that
version and above), `TLSContextOptions[]` (`cafile`, `capath`, `peer_name`, `verify_peer`, `verify_peer_name`,
`allow_self_signed`; values go into the `ssl` context as given, `true`/`false` strings as booleans). New
`protected static function transportOptions( ezcMailTransportOptions $options )` in `expMailBounceReader`, called
by `connect()` for both protocols: applies the three settings; `StartTLS=enabled` with `SSL=enabled` logs one
error and keeps `SSL` (implicit TLS wins, the safer one); when setting `startTls` or `connectionOptions` throws
`ezcBasePropertyNotFoundException` (released package), one `eZDebug::writeWarning()` naming the setting and the
options are returned as built from `SSL` alone. The crypto method for `TLSMinimumVersion` is
`STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT` for `1.2` (the 1.3 constant guarded with
`defined()`, PHP 7.4+ has it) and `STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT` for `1.3`; other values log a warning and
set nothing. Test `tests/tests/kernel/classes/mailpreferences/MailPreferencesBounceTransportOptionsTest.php`
(no live installation; settings through `eZINI::setVariable()` and restored in `tearDown()`; a subclass exposing
`transportOptions()`): Scenario: Defaults, Scenario: Upgrade and CA file configured, Scenario: Package without the
options (a stub options class without the properties passed in, or RELEASED), the SSL/StartTLS conflict.

### D7 Documentation
Package: `docs/tutorial.txt` section "Using STARTTLS and verifying the server with POP3 and IMAP" next to
"Using SSL with POP3 and IMAP" (the two options, the `ssl` context keys, LOGINDISABLED, that the server's
certificate is verified by PHP's defaults). Kernel: the three keys in `doc/eng-us/features/6.0/mail-preferences.md`
(bounce reader section) and the kernel table row in `AGENTS.md`.

## Risks / Trade-offs

- [PHP verifies peers by default; an operator who turned verification off by habit gets failures] → the defaults
  are unchanged (no context = the same `ssl://` behaviour as before); the context only adds what is set.
- [A TLS handshake inside a fake server is the flakiest part of the suite] → the certificate is generated per test
  class, `timeout => 5`, the server exits after one connection; a failed handshake prints the OpenSSL error to the
  server's stderr, which the test case shows on failure.
- [`openssl` extension missing in a test image] → the TLS tests skip with a message; the plain tests still run.
- [The kernel reader is edited by three changes of this series] → this change adds one method and one call site in
  `connect()`; the next change adds its auth code beside it.

## Migration Plan

Package release, then the kernel PR (the kernel works with both package versions). Settings are opt-in. Rollback:
previous versions; no data.
