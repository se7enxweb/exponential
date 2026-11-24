# Design

## Context

See proposal.md - Why. Current state, read in the code:

- Package: `ezcMailImapTransport::authenticate( $user, $password, $method )` (`imap_transport.php` 579-640) sends
  `AUTHENTICATE XOAUTH2 <base64("user=U\1auth=Bearer T\1\1")>` for `AUTH_XOAUTH2` and, on a non-OK reply, sends an
  empty line and reads the final reply (the challenge handling of the Google protocol). `ezcMailSmtpTransport`
  has `authXOAuth2()` (1029) chosen through `preferredAuthMethod` or the server's `AUTH` list. `ezcMailPop3Transport`
  (`pop3_transport.php`): `AUTH_PLAIN_TEXT = 1`, `AUTH_APOP = 2`; `ezcMailPop3TransportOptions::authenticationMethod`
  accepts any numeric value (`pop3_options.php` 15-21). The fake servers of the previous changes accept `LOGIN`
  / `USER`+`PASS` / `APOP`; the SMTP fake of PR #1-#4 (`tests/local_smtp/`) lives on those PR branches, not on
  the base of this change.
- Kernel SMTP: `eZSMTPTransport::sendMail()` (`lib/ezutils/classes/ezsmtptransport.php` 24-90) reads
  `[MailSettings] TransportUser/TransportPassword/TransportAuthMethod`, maps the method with `authMethod()`
  (`XOAUTH2` accepted) and passes the password as the XOAUTH2 token. Bounce reader `expMailBounceReader::connect()`
  (`expmailbouncereader.php` 332-354): `authenticate( $user, $password )` with the default method for both
  protocols; the previous change added `transportOptions()` beside it. Newsletter
  `CjwNewsletterMailbox::connect()` (`cjwnewslettermailbox.php` 302-420): `authenticate( $userName, $password )`;
  the account lives in table `cjwnl_mailbox` (`email`, `server`, `port`, `user_name`, `password`, `type`, `is_ssl`,
  ...); `fix-newsletter-bounce-handling` adds `loggableSettings()` masking the password in the log.
- Settings: `settings/site.ini [MailSettings]` has `TransportAuthMethod=` (empty: the library chooses);
  `settings/mailpreferences.ini [BounceSettings]` has `User`, `Password` with the comment that they go into the
  unversioned override; `cjw_newsletter.ini` has no mailbox section (accounts are in the table).
- Overlap: the open mail PRs touch only the SMTP transport, which this change does not edit (SMTP XOAUTH2 exists;
  its test here uses the SMTP fake only if present on the base, else a scripted stand-in, D7). The base branch
  is the branch of `add-mail-starttls-stream-context` (its branch), the top of this series.

Architecture rules this change touches and how the design keeps them:

- *Secrets live outside version control.* Tokens never enter a versioned file; the shipped keys are empty; a token
  file path is itself configuration of the unversioned override. The helper never logs a token. The guard test of
  D4 fails when a versioned settings file sets a token source of the form `env:` with a literal token or a
  non-empty `TransportPassword` together with `TransportAuthMethod=XOAUTH2`.
- *lib/ and kernel/ edits are surgical, work with the released package, no decision IDs (meant for upstream).*
  One helper class, one call per transport, `method_exists`/constant checks for the POP3 mechanism; a row in the
  kernel table of AGENTS.md.
- *Tests never write to a site database.* The newsletter test builds a `CjwNewsletterMailbox` object in memory
  and connects to the fake server; nothing is stored.
- *No new Composer dependency.* None.

## Goals / Non-Goals

**Goals:** XOAUTH2 on all three protocols of the package, proven locally; one token-source helper used by SMTP,
bounce reader and newsletter; defaults unchanged.

**Non-Goals:** token refresh, OAUTHBEARER, the newsletter's own SMTP settings, a UI field for the auth method in
the newsletter mailbox form (the per-account INI keeps the table and the form untouched; a field is a later
newsletter ticket).

## Decisions

### D1 POP3 XOAUTH2 in the package
`ezcMailPop3Transport::AUTH_XOAUTH2 = 3`; `authenticate()` branch: `AUTH XOAUTH2 ` + base64 of
`"user={$user}\x01auth=Bearer {$password}\x01\x01"`; reply `+OK` → logged in; reply starting with `+ ` (a
challenge, base64 JSON) → send an empty line, read the final `-ERR`; any `-ERR` →
`ezcMailTransportException( "The POP3 server did not accept the XOAUTH2 token: {$reply}." )` (the reply line
never carries the token; the message does not either). `ezcMailPop3TransportOptions::__set( 'authenticationMethod' )`
keeps accepting numeric values (1-3 documented). Alternative: `AUTH PLAIN` SASL framework. Rejected: not requested;
XOAUTH2 is the mechanism of the providers in question.

### D2 Fake servers: XOAUTH2
IMAP fake: `AUTHENTICATE XOAUTH2 <b64>` with config `tokens` (`user => token`): match → tagged OK; mismatch →
`+ <base64 of {"status":"401"}>`, read one line, then tagged `NO AUTHENTICATE failed`; capability `AUTH=XOAUTH2`
listed when `tokens` is configured. POP3 fake: `AUTH XOAUTH2 <b64>` likewise with `+OK`/`+ <b64>`/`-ERR`; `CAPA`
lists `SASL XOAUTH2` when configured. Both log the decoded user of the initial response but never the token
(`state()['auth_user']`), so tests can assert the token was not sent in the clear elsewhere.

### D3 Kernel helper `eZMailTokenSource`
New `lib/ezutils/classes/ezmailtokensource.php`, row in `autoload/ezp_kernel.php` by hand. Static
`resolve( $source, $fallback, $user, $context )` returns the token string or `''`:
- `''` → `$fallback` (the configured password/token value);
- `file:<path>` → `trim( file_get_contents() )`; relative paths against `eZSys::rootDir()`; missing, unreadable or
  empty file → `''` and `eZDebug::writeError( "<context>: token file <path> is missing or empty" )`; a file
  readable by everyone gets one warning (like a key file);
- `env:<NAME>` → `getenv()`; empty → `''` and an error naming the variable;
- `class:<Class>` → `class_exists( $class ) && method_exists( $class, 'accessToken' )` → `(string) $class::accessToken( $user, $context )`;
  else `''` with an error naming the class (a `Throwable` from the method is caught and logged by class and
  exception class, not message);
- any other form → `''` and an error naming the form.
`$context` is a short label (`smtp`, `bounces`, `newsletter mailbox <user>`) for the log line. No token is ever
part of a log line or an exception message; the test proves it with a token value that must not appear in the
captured log.

### D4 Kernel settings and call sites
- `settings/site.ini [MailSettings] TransportTokenSource=` (comment: used when `TransportAuthMethod=XOAUTH2`; the
  forms of D3; empty = `TransportPassword` is the token). `eZSMTPTransport::sendMail()`: when the method resolves
  to XOAUTH2, `$password = eZMailTokenSource::resolve( setting, $password, $user, 'smtp' )`; `''` → return false
  after `eZDebug::writeError( 'SMTP: no XOAUTH2 token available' )` (no connection attempt).
- `settings/mailpreferences.ini [BounceSettings] AuthMethod=` (empty = `login` for IMAP, `plain` for POP3; `apop`
  for POP3; `xoauth2`) and `TokenSource=`. `expMailBounceReader::connect()`: `authMethod()` maps the setting to the
  transport constant (`ezcMailImapTransport::AUTH_XOAUTH2`, `ezcMailPop3Transport::AUTH_APOP`, and
  `ezcMailPop3Transport::AUTH_XOAUTH2` when `defined()` - otherwise `ezcMailTransportException( 'the installed mail
  package has no XOAUTH2 for POP3' )` before any command); the token through D3 with context `bounces`; `''` →
  exception naming the token source (caught by `run()` like every connect error, so the status file shows it).
- Guard test `tests/tests/lib/ezutils/eZMailTokenSourceSettingsGuardTest.php` (pattern of the DKIM guard idea):
  versioned INI files from `git ls-files` must not set `TransportTokenSource`/`TokenSource` to a value with the
  form `env:`/`file:` pointing into the repository, nor a non-empty `TransportPassword` with
  `TransportAuthMethod=XOAUTH2`.

### D5 Newsletter: per-account settings, no schema change
`cjw_newsletter.ini [MailboxAuthSettings]`: `AuthMethod[]` and `TokenSource[]` keyed by the account's user name
(`AuthMethod[reader@example.org]=xoauth2`, `TokenSource[reader@example.org]=file:/srv/tokens/reader.token`), with
the comment that both belong in the unversioned override. `CjwNewsletterMailbox::connect()` calls a new protected
`authenticationFor( $type, $userName, $password )` returning `array( $method, $secret )`: default
`array( null, $password )` (today's `authenticate( $user, $password )`); `xoauth2` → the transport's constant
(IMAP; POP3 when `defined()`, else a connect error string "the mail package has no XOAUTH2 for POP3") and the
token through `eZMailTokenSource::resolve()` when `class_exists( 'eZMailTokenSource' )` (older kernel: the
password field as token, one warning); an empty token → the error string "no token for mailbox <user>" (the
existing error-string return path of `connect()`). The logged settings go through `loggableSettings()` of
`fix-newsletter-bounce-handling`, which masks the password; the token is never put into `$settingArray`.
Alternative: a column `auth_method` in `cjwnl_mailbox` with a form field. Rejected for this change: a schema
update on every installation and a form change in two designs for one new value; the INI keeps the data model
and the admin form untouched and the secret-bearing part out of the database.

### D6 Kernel and newsletter tests
- `tests/tests/lib/ezutils/eZMailTokenSourceTest.php` (lib suite, no DB): Scenario: Token from a file, Token from
  a class (a test class in the file), Missing file, Static value, `env:` set and unset, unknown form, the captured
  log never contains the token (`eZDebug` output captured through `eZDebug::instance()->` message list or a
  `$log` seam of the helper).
- `tests/tests/lib/ezutils/eZSMTPTransportXoauth2Test.php` (lib): with `TransportAuthMethod=XOAUTH2` and a token
  file, a subclass seam that captures the `ezcMailSmtpTransportOptions`/password handed to the package transport
  (as `eZSMTPTransportTest` already stubs the transport) shows the file's token; with an empty source `sendMail()`
  returns false and the log names the source; settings set with `eZINI::setVariable()` and restored.
- `tests/tests/kernel/classes/mailpreferences/MailPreferencesBounceAuthTest.php` (kernel-classes, no DB):
  Scenario: Bounce reader with XOAUTH2 over IMAP (the fake IMAP server script of the package, started from
  `vendor/se7enxweb/expcomponents-mail/tests/local_imap/` with the changed package installed, skipped when absent: `connect()` exposed
  through a subclass; the fake's log has `AUTHENTICATE XOAUTH2` and no `LOGIN`), Scenario: Bounce reader with
  XOAUTH2 over POP3 on an old package (`defined()` seam forced false: exception text names the package), Scenario:
  Token missing, Scenario: Defaults unchanged (`authMethod()` gives the defaults).
- `tests/tests/extension/cjw_newsletter/cjwNewsletterMailboxAuthTest.php` (suite `cjw_newsletter`; the mailbox
  object built in memory with `new CjwNewsletterMailbox( array( ... ) )`, no store): Scenario: Newsletter mailbox
  with XOAUTH2 (fake IMAP server, token file in a temp dir; the newsletter log file contains neither the token
  nor the password), Scenario: Defaults unchanged (`authenticationFor()` returns null method and the password),
  Scenario: Token missing (connect returns the error string, no command in the fake's log). Skips when the fake
  server script is absent (RELEASED) except the two in-memory scenarios.

### D7 SMTP XOAUTH2 test in the package
The SMTP fake lives on PR branches #1-#4, not on master. `tests/local_smtp/` is therefore not on this change's
base. The SMTP scenario is tested with a scripted stand-in: a subclass of `ezcMailSmtpTransport` in the test that
overrides `sendData()`/`getData()` to replay a server script (EHLO with `250-AUTH XOAUTH2`, `235` after `AUTH
XOAUTH2 <b64>`) and records the commands - no socket. When the branch does carry `tests/local_smtp/` (after a
rebase onto a merged PR), the test may be switched to the fake; a comment says so.

### D8 Documentation
Package: `docs/tutorial.txt`, "Using stronger authentication methods with the SMTP transports" gets the POP3
and IMAP XOAUTH2 paragraph (initial response, challenge, where the token comes from is the application's job).
Kernel: new `doc/eng-us/features/6.0/mail-oauth2.md` (the three settings groups, the token source forms, the
refresh job outside the CMS, the cache rule after INI changes, which package version has POP3 XOAUTH2);
`mail-preferences.md` links to it; kernel table row in `AGENTS.md`. Newsletter: `doc/deliverability.md` section
"OAuth 2.0 for bounce mailboxes" and a README line.

## Risks / Trade-offs

- [Token file readable by other users on the server] → one warning on world-readable mode, documented in
  `mail-oauth2.md`; the file path is the operator's choice.
- [A class token source runs arbitrary installation code] → only a class name from the unversioned settings, the
  same trust level as the settings themselves; exceptions are caught and logged by class.
- [Per-account INI keyed by user name] → two accounts with the same user name on different servers share the
  setting; documented; a later newsletter ticket may add a column.
- [Three changes edit the bounce reader's `connect()`] → this one adds `authMethod()` and the token line only;
  the options builder of the previous change is called as it is.

## Migration Plan

Package release first (POP3 XOAUTH2); the kernel and newsletter PRs work with the released package (IMAP and
SMTP XOAUTH2 exist there; POP3 XOAUTH2 is reported as unavailable). Settings are opt-in; the cache must be cleared
after setting them (`rm -f var/cache/ini/*.php`, `php bin/php/ezcache.php --clear-all`). Rollback: previous versions.
