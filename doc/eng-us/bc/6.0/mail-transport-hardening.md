# Mail: sendmail envelope sender, tip-a-friend sender, SMTP failures and random identifiers (6.0.15)

From 6.0.15 on, the mail layer hands sendmail only a plain envelope sender, `content/tipafriend` sends as the site,
an SMTP failure of any kind is a failed send instead of an error page, and MIME boundaries and Message-Ids are random.

## What changes

- **sendmail `-f`.** With `site.ini [MailSettings] SendmailEnvelopeSender=enabled` (the default) the sender was
  passed as `-f'<address>'`. PHP's `mail()` runs `escapeshellcmd()` over the whole option string, which undoes that
  quoting (the pattern of PHPMailer CVE-2016-10033). Measured: the sender
  `"a' -X/tmp/x.php -OQueueDirectory=/tmp '"@example.com` passes `eZMail::validate()` (a quoted local part), and
  sendmail got `-X/tmp/x.php` and `-OQueueDirectory=/tmp` as arguments of their own. Such a sender is reachable
  through `content/tipafriend` and through a form of `content/collectinformation` that sets the sender. Now `-f` is
  given only for an address of letters, digits and `._%+=-` before a single `@` and a host name after it
  (`eZSendmailTransport::isShellSafeAddress()`), without extra quoting; any other sender is not handed to sendmail,
  which then uses its own default, and a warning goes to the debug log. Ordinary addresses are not affected.
- **Tip a friend.** Without `site.ini [TipAFriend] FromEmail` (the default) the mail used to be sent with the
  visitor's address as sender and envelope sender. It is now sent with the site's address, the first valid one of
  `[MailSettings] EmailSender` and `AdminEmail` (written as `address` or `Name <address>`), and the visitor's address
  goes into `Reply-To`. The display name of the sender is then `[SiteSettings] SiteName` (control characters and
  quotes removed), not the name the visitor typed, so a visitor cannot make the mail arrive as
  "Bank support <site address>". A reply still reaches the visitor and the mail passes SPF and DMARC checks of the
  site's domain. Only when neither setting is a valid address is the visitor's address the sender, as before. With
  `FromEmail` set, `Reply-To` is added as well.
- **SMTP failures.** `eZSMTPTransport::sendMail()` caught only `ezcMailException`. Measured with a fake SMTP server on
  127.0.0.1 that offers only `AUTH NTLM`: `ezcMailSmtpTransport::authNtlm()` threw
  `ezcBaseExtensionNotFoundException('mcrypt')`, which reached the request (registration, forgotten password). Every
  other error is now logged and the send returns `false`.
- **SMTP authentication method.** New `site.ini [MailSettings] TransportAuthMethod` picks the method (`PLAIN`,
  `LOGIN`, `CRAM-MD5`, `DIGEST-MD5`, `NTLM`, `XOAUTH2`, any case). Empty, the default, keeps the library's choice;
  an unknown value logs a warning and is ignored.
- **Credentials in clear.** A user name and password sent over an unencrypted connection (`TransportConnectionType`
  empty or `tcp`) to a host other than loopback log a warning. Nothing is refused.
- **Random identifiers.** The `multipart/alternative` part of a mail built from an HTML template got the library's
  boundary (`date( "YmdGHjs" ) . ':' . getmypid() . ':' . counter`), and every mail sent by SMTP got a Message-Id of
  the same parts: the PHP process id was in every mail. The boundary is now the random one of the raw body, and a
  mail without a Message-Id of its own gets `<32 hexadecimal characters@sender domain>` (`localhost` without a usable
  sender domain) for each generation. One set with `eZMail::setMessageID()` stays.
- **A mail without sender.** `eZMail::senderText()` of a mail without a sender gave "Trying to access array offset on
  false" and "mb_encode_mimeheader(): Passing null to parameter #1" (PHP 8.5). It now returns `''` quietly.
- **Not changed.** The newsletter (`cjw_newsletter`) sends through its own transports and its own return path, and
  notifications use `EmailSender` as before; for plain sender addresses nothing changes for either.

## Settings

| Setting | Default | Meaning |
|---|---|---|
| `site.ini [MailSettings] TransportAuthMethod` | empty | the SMTP authentication method; empty = the strongest the server offers |

## What to do when you upgrade

- A site whose SMTP server offers `NTLM` (common with Microsoft Exchange) could not send by SMTP at all; set
  `TransportAuthMethod=LOGIN` (or `PLAIN`) and use `TransportConnectionType=tls` or `ssl`.
- A sendmail setup that relied on a sender address with a quoted local part or an address literal as envelope sender
  now gets sendmail's default envelope sender; set `EmailSender` to a plain address.
- Tip-a-friend mail now comes from the site's address. Set `[TipAFriend] FromEmail` to choose another one.

## Code and tests

- `eZSendmailTransport::isShellSafeAddress()` (`lib/ezutils/classes/ezsendmailtransport.php`),
  `eZSMTPTransport::authMethod()` and `sendsCredentialsInClear()` (`lib/ezutils/classes/ezsmtptransport.php`),
  `Tipafriend::siteSender()` and `siteSenderName()` (`kernel/private/classes/views/content/tipafriend.php`),
  `expMailTemplateBody` (`kernel/classes/mail/expmailtemplatebody.php`), `ezpMail::messageIdHost()`
  (`kernel/private/classes/ezpmail.php`), `eZMail::composeEmailName()` and `encodeMimeHeader()`.
- Tests without a database: `eZSendmailEnvelopeSenderTest`, `eZSMTPTransportTest` (with the fake server
  `tests/tests/lib/ezutils/fixtures/smtp_fake_server.php`), `eZMailTest`, `expMailTemplateBodyTest`,
  `ezpMailMessageIdTest`, `expTipafriendSenderTest`.
