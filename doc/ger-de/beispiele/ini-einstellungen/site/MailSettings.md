---
title: site.ini [MailSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [MailSettings] in site.ini: Transport, TransportAlias, TransportServer, TransportConnectionType, TransportPort, TransportUser, TransportPassword, SenderHost ..."
keywords: Einstellung, site.ini, MailSettings, INI, setting, Transport, TransportAlias, TransportServer, TransportConnectionType, TransportPort, TransportUser, TransportPassword, SenderHost, AdminEmail, EmailSender, EmailReplyTo, UserAgent, AllowedCharsets, OutputCharset, ContentType, HeaderLineEnding, SendmailOptions, SendmailEnvelopeSender, SendmailInsertUndisclosedRecipient, DebugSending, DebugReceiverEmail, ExcludeHeaders
type: generated
generated: exp:help:examples
---
# site.ini [MailSettings]

Der Block `[MailSettings]` von `settings/site.ini`, mit 22 Einstellung(en).

> If RedirectAfterPublish=uri, indicates to which URI user will be redirected  
> after publishing a node.  
> example : RedirectAfterPublishUri=/content/dashboard  
> if empty : will be redirected to parent node  
> RedirectAfterPublishUri=  

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Transport

> The type of transport for emails, can be either: sendmail, SMTP or file  
> Window users should probably use SMTP  
> For debug purposes you can use file for sending mail to a log file  

Standard: `sendmail`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[MailSettings]
Transport=sendmail
*/ ?>
```

## TransportAlias

> A list of possible handlers for sending mails, use the setting Transport to specify which one to use  

Standard: ein Hash mit 3: `file=eZFileTransport`, `sendmail=eZSendmailTransport`, `smtp=eZSMTPTransport`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[MailSettings]
# ein leeres "TransportAlias[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
TransportAlias[]
TransportAlias[file]=eZFileTransport
TransportAlias[sendmail]=eZSendmailTransport
TransportAlias[smtp]=eZSMTPTransport
*/ ?>
```

## TransportServer

> Configuration for SMTP  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[MailSettings]
TransportServer=
*/ ?>
```

## TransportConnectionType

> Connection type in SMTP: <empty>(no encryption)|ssl|sslv2|sslv3|tls  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[MailSettings]
TransportConnectionType=
*/ ?>
```

## TransportPort

> Default SMTP port is 25, default Secure SMTP port(SSL,TLS) is 465  

Standard: `25`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[MailSettings]
TransportPort=25
*/ ?>
```

## TransportUser

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[MailSettings]
TransportUser=
*/ ?>
```

## TransportPassword

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[MailSettings]
TransportPassword=
*/ ?>
```

## SenderHost

> The FQDN, used by extended HELO  
> See part "4.1.1.1  Extended HELLO (EHLO) or HELLO (HELO)" of http://www.faqs.org/rfcs/rfc2821.html  

Standard: `localhost`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[MailSettings]
SenderHost=localhost
*/ ?>
```

## AdminEmail

Standard: `nospam@example.com`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[MailSettings]
AdminEmail=nospam@example.com
*/ ?>
```

## EmailSender

> The address which will be set as the From header of the e-mail, unless  
> overridden by the template variable email\_sender.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[MailSettings]
EmailSender=
*/ ?>
```

## EmailReplyTo

> The address which will be set as the Reply-To header of the e-mail, unless  
> overridden by the template variable email\_reply\_to. If not set, the senders  
> address will be used, either from the template, or the EmailSender setting.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[MailSettings]
EmailReplyTo=
*/ ?>
```

## UserAgent

> The User-Agent header of every mail sent through eZMail (sendmail, SMTP and file  
> transports), e.g. the name of the portal; ASCII only, it is not encoded.  
> Empty: "Exponential, Version <version>" (sendmail, file) and the default of the  
> mail library (SMTP), as before. eZMail::setUserAgent() still overrides it per mail.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[MailSettings]
UserAgent=
*/ ?>
```

## AllowedCharsets

> Which charset are allowed to send directly, those that do not match  
> is converted to OutputCharset before being sent.  

Standard: eine Liste mit 5: `us-ascii`, `utf-8`, `iso-8859-1`, `iso-8859-15`, `cp1252`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[MailSettings]
# ein leeres "AllowedCharsets[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AllowedCharsets[]
AllowedCharsets[]=us-ascii
AllowedCharsets[]=utf-8
AllowedCharsets[]=iso-8859-1
AllowedCharsets[]=iso-8859-15
AllowedCharsets[]=cp1252
*/ ?>
```

## OutputCharset

> Which charset to convert emails to if they are not in correct format  

Standard: `utf-8`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[MailSettings]
OutputCharset=utf-8
*/ ?>
```

## ContentType

> Content type of mails, for instance text/plain or text/html  

Standard: `text/plain`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[MailSettings]
ContentType=text/plain
*/ ?>
```

## HeaderLineEnding

> The line endings used for headers in mail messages. If you need to use  
> control characters like LF or CR you need to use it's urlencoded value, for  
> example %0A for LF and %0D for CR. The default setting is what RFC 2045  
> requires: CRLF  
> If it contains auto the system will try to determine the line-ending  
> based on the current operating system.  

Standard: `auto`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[MailSettings]
HeaderLineEnding=auto
*/ ?>
```

## SendmailOptions

> Set additional sendmail options here.  
> -f <sender> is default in PHP SafeMode.  
> Please use a line for every (sub)option. e.g.  
> SendmailOptions\[\]=-r  
> SendmailOptions\[\]=muchspam@example.com  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[MailSettings]
# ein leeres "SendmailOptions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SendmailOptions[]
*/ ?>
```

## SendmailEnvelopeSender

> Whether sendmail is given the sender as envelope sender with -f <sender>.  
> disabled: no -f. A mail without a sender of its own (no sender address and no  
> From extra header) gets a From header with EmailSender, else AdminEmail, when one  
> of them is a valid address ("address" or "Name <address>"). Needed for msmtp with  
> --read-envelope-from, which refuses -f together with that option and reads the  
> envelope sender from the From header.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[MailSettings]
SendmailEnvelopeSender=enabled
*/ ?>
```

## SendmailInsertUndisclosedRecipient

> Allows inserting undisclosed-recipients into the "TO:" of email headers.  
> As this is not a standard, some MTA might reject it, in that case you can set it to "disabled"  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[MailSettings]
SendmailInsertUndisclosedRecipient=enabled
*/ ?>
```

## DebugSending

> If enabled, all emails will be sent to DebugReceiverEmail instead of to their original recipient(s)  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[MailSettings]
DebugSending=disabled
*/ ?>
```

## DebugReceiverEmail

> Receiver email address to use when DebugSending is enabled  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[MailSettings]
DebugReceiverEmail=
*/ ?>
```

## ExcludeHeaders

> Headers to exclude from the sent message, case insensitive, only relevant when using SMTP  
> You might want to add bcc here, if your mail server does not hide bcc recipients from each other  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[MailSettings]
# ein leeres "ExcludeHeaders[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ExcludeHeaders[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
