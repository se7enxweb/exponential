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

> Bei RedirectAfterPublish=uri gibt dies an, auf welche URI der Benutzer  
> nach dem Veröffentlichen eines Knotens weitergeleitet wird.  
> Beispiel: RedirectAfterPublishUri=/content/dashboard  
> wenn leer: Weiterleitung zum Elternknoten  
> RedirectAfterPublishUri=  

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Transport

> Die Art des Transports für E-Mails, kann sendmail, SMTP oder file sein  
> Windows-Benutzer sollten wahrscheinlich SMTP verwenden  
> Zum Debuggen können Sie file verwenden, um Mails in eine Logdatei zu schreiben  

Standard: `sendmail`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[MailSettings]
Transport=sendmail
*/ ?>
```

## TransportAlias

> Eine Liste möglicher Handler zum Senden von Mails; mit der Einstellung Transport geben Sie an, welcher verwendet wird  

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

> Konfiguration für SMTP  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[MailSettings]
TransportServer=
*/ ?>
```

## TransportConnectionType

> Verbindungstyp bei SMTP: <empty>(keine Verschlüsselung)|ssl|sslv2|sslv3|tls  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[MailSettings]
TransportConnectionType=
*/ ?>
```

## TransportPort

> Der Standardport für SMTP ist 25, der Standardport für sicheres SMTP (SSL, TLS) 465  

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

> Der FQDN, verwendet vom erweiterten HELO  
> Siehe Abschnitt "4.1.1.1  Extended HELLO (EHLO) or HELLO (HELO)" von http://www.faqs.org/rfcs/rfc2821.html  

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

> Die Adresse, die als Header From der E-Mail gesetzt wird, sofern  
> nicht die Template-Variable email\_sender sie überschreibt.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[MailSettings]
EmailSender=
*/ ?>
```

## EmailReplyTo

> Die Adresse, die als Header Reply-To der E-Mail gesetzt wird, sofern  
> nicht die Template-Variable email\_reply\_to sie überschreibt. Wenn nicht gesetzt, wird die Adresse  
> des Absenders verwendet, entweder aus dem Template oder aus der Einstellung EmailSender.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[MailSettings]
EmailReplyTo=
*/ ?>
```

## UserAgent

> Der Header User-Agent jeder über eZMail gesendeten Mail (Transporte sendmail, SMTP und  
> file), z. B. der Name des Portals; nur ASCII, er wird nicht kodiert.  
> Leer: "Exponential, Version <version>" (sendmail, file) und der Standard der  
> Mail-Bibliothek (SMTP), wie bisher. eZMail::setUserAgent() überschreibt ihn weiterhin pro Mail.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[MailSettings]
UserAgent=
*/ ?>
```

## AllowedCharsets

> Welche Zeichensätze direkt gesendet werden dürfen; die, die nicht passen,  
> werden vor dem Senden in OutputCharset umgewandelt.  

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

> In welchen Zeichensatz E-Mails umgewandelt werden, wenn sie nicht im richtigen Format sind  

Standard: `utf-8`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[MailSettings]
OutputCharset=utf-8
*/ ?>
```

## ContentType

> Inhaltstyp von Mails, zum Beispiel text/plain oder text/html  

Standard: `text/plain`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[MailSettings]
ContentType=text/plain
*/ ?>
```

## HeaderLineEnding

> Die Zeilenenden der Header in Mailnachrichten. Wenn Sie  
> Steuerzeichen wie LF oder CR verwenden müssen, verwenden Sie ihren URL-kodierten Wert, zum  
> Beispiel %0A für LF und %0D für CR. Die Standardeinstellung ist das, was RFC 2045  
> verlangt: CRLF  
> Enthält sie auto, versucht das System, das Zeilenende  
> anhand des aktuellen Betriebssystems zu bestimmen.  

Standard: `auto`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[MailSettings]
HeaderLineEnding=auto
*/ ?>
```

## SendmailOptions

> Setzen Sie hier zusätzliche Optionen für sendmail.  
> -f <sender> ist im SafeMode von PHP der Standard.  
> Bitte verwenden Sie eine Zeile pro (Unter-)Option, z. B.  
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

> Ob sendmail den Absender mit -f <sender> als Umschlag-Absender erhält.  
> disabled: kein -f. Eine Mail ohne eigenen Absender (keine Absenderadresse und kein  
> zusätzlicher Header From) erhält einen Header From mit EmailSender, sonst AdminEmail, wenn eine  
> davon eine gültige Adresse ist ("address" oder "Name <address>"). Nötig für msmtp mit  
> --read-envelope-from, das -f zusammen mit dieser Option ablehnt und den  
> Umschlag-Absender aus dem Header From liest.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[MailSettings]
SendmailEnvelopeSender=enabled
*/ ?>
```

## SendmailInsertUndisclosedRecipient

> Erlaubt, undisclosed-recipients in das "TO:" der E-Mail-Header einzufügen.  
> Da dies kein Standard ist, lehnen manche MTAs es ab; dann können Sie es auf "disabled" setzen  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[MailSettings]
SendmailInsertUndisclosedRecipient=enabled
*/ ?>
```

## DebugSending

> Wenn aktiviert, werden alle E-Mails an DebugReceiverEmail statt an ihre ursprünglichen Empfänger gesendet  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[MailSettings]
DebugSending=disabled
*/ ?>
```

## DebugReceiverEmail

> Empfängeradresse, die verwendet wird, wenn DebugSending aktiviert ist  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[MailSettings]
DebugReceiverEmail=
*/ ?>
```

## ExcludeHeaders

> Header, die aus der gesendeten Nachricht entfernt werden, ohne Beachtung der Groß-/Kleinschreibung, nur bei SMTP relevant  
> Sie können hier bcc hinzufügen, wenn Ihr Mailserver bcc-Empfänger nicht voreinander verbirgt  

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
