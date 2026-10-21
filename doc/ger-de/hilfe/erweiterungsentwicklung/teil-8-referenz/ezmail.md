---
title: E-Mails mit eZMail versenden
audience: developer
category: extension-development
order: 250
summary: E-Mails mit eZMail und eZMailTransport::send() versenden - Empfänger, Absender, Betreff, Text, Inhaltstyp - über die Transporte der Website, mit einem Template für den Text und dem Datei-Transport für Tests.
keywords: E-Mail, Mail versenden, Empfänger, Absender, Mail-Transport, Datei-Transport, Tests, eZMail, eZMailTransport, send, setReceiver, addReceiver, setSender, setSubject, setBody, setContentType, MailSettings, Transport, file transport, AdminEmail, EmailSender, validate
type: help
---
# E-Mails mit eZMail versenden

Teil 8, Referenz 21. `eZMail` (`lib/ezutils/classes/ezmail.php`) ist eine Nachricht; `eZMailTransport::send()`
(`lib/ezutils/classes/ezmailtransport.php`) versendet sie mit dem Transport, den die Website konfiguriert hat.
Verwenden Sie sie statt PHPs `mail()`: Dann gelten die SMTP-Einstellungen, die Absenderadresse, der Zeichensatz und die
Mail-Einstellungen der Website.

## Eine Nachricht

```php
<?php
$ini = eZINI::instance( 'site.ini' );
$sender = $ini->variable( 'MailSettings', 'EmailSender' ) ?: $ini->variable( 'MailSettings', 'AdminEmail' );

$tpl = eZTemplate::factory();
$tpl->setVariable( 'order_id', (int)$orderID );
$body = $tpl->fetch( 'design:myext/mail/order_shipped.tpl' );

$mail = new eZMail();
$mail->setSender( $sender );
$mail->setReceiver( $customerEmail, $customerName );
$mail->setSubject( ezpI18n::tr( 'extension/myext', 'Your order has shipped' ) );
$mail->setBody( $body );
$mail->setContentType( 'text/plain' );

if ( !eZMailTransport::send( $mail ) )
{
    eZDebug::writeError( 'The shipping mail could not be sent', __METHOD__ );
}
```

| Methode | Verwendung |
|---|---|
| `setReceiver( $email, $name = false )`, `addReceiver( ... )` | die An-Adressen |
| `addCc( ... )`, `addBcc( ... )` | Kopien |
| `setSender( $email, $name = false )` | die Von-Adresse |
| `setReplyTo( $email, $name = false )` | die Antwortadresse |
| `setSubject( $newSubject )`, `setBody( $newBody )` | Betreff und Text |
| `setContentType( $type = false, $charset = false, ... )` | `text/plain` oder `text/html` |
| `eZMail::validate( $address )` (statisch) | ob eine Adresse wohlgeformt ist |
| `eZMailTransport::send( eZMail $mail, ?string $transportType = null )` (statisch) | sie versenden; gibt bei Erfolg `true` zurück |

## Die Mail-Einstellungen der Website

`site.ini [MailSettings]`:

| Einstellung | Bedeutung |
|---|---|
| `Transport` | `sendmail` (Standard), `smtp` oder `file` |
| `TransportServer`, `TransportPort`, `TransportConnectionType`, `TransportUser`, `TransportPassword` | für SMTP |
| `AdminEmail` | die Adresse des Website-Administrators |
| `EmailSender` | die Standard-Absenderadresse |
| `EmailReplyTo` | die Standard-Antwortadresse |
| `ContentType` | der Standard-Inhaltstyp |

Ihre Erweiterung sollte diese nicht setzen; das tut die Website. Braucht Ihre Erweiterung einen eigenen Absender,
machen Sie ihn zu einer Einstellung Ihrer eigenen INI-Datei, mit `EmailSender` als Vorgabe.

## Testen, ohne zu versenden

Setzen Sie auf einer Entwicklungskopie `Transport=file`. E-Mails werden dann in Dateien in `var/log/mail` (oder im
Verzeichnis aus `[MailSettings] FileTransportDirectory`) geschrieben statt versendet, sodass Sie sie lesen können und
niemand eine Test-Mail erhält. Prüfen Sie, dass keine Einstellung in `settings/override` einen anderen Transport
erzwingt: Ein Override gewinnt gegen eine Siteaccess-Einstellung. Verwenden Sie in Tests erfundene Adressen auf
reservierten Domains (`user@example.invalid`).

## Templates für den Text

Halten Sie den Text in einem Template (`design:myext/mail/...tpl`) mit Übersetzungszeichenketten, damit Websites den
Wortlaut mit einem Override ändern und Übersetzer ihn übersetzen können. Die Benachrichtigungs-Mails des Kernels tun
dasselbe.

## Die Empfänger respektieren

- Versenden Sie nur E-Mails, die der Empfänger erwartet (er hat bestellt, abonniert oder angefragt).
- Beachten Sie die Mail-Einstellungen des Benutzers, wenn Ihre E-Mail eine Benachrichtigung ist
  ([Mail-Einstellungen](../../../bc/6.0/e-mail-einstellungen.md)).
- Setzen Sie nie eine vollständige Adressliste in An oder Cc; eine E-Mail je Empfänger oder Bcc.
- Protokollieren Sie weder Mail-Texte noch Adressen.

## Verwandte Seiten

- Zurück: [ezformtoken und CSRF-Schutz](ezformtoken-und-csrf.md)
- Weiter: [Cache-Tags, Cache-Blöcke und das Leeren von Caches](cache-tags-und-leeren.md)
- [Durch Einstellungen benannte Handler](../teil-3-erweiterungsentwickler/handler-ueber-einstellungen-benennen.md) (ein eigener Mail-Transport)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
