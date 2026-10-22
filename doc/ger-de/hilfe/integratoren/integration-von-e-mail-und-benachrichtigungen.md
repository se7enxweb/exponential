---
title: Integration von E-Mail und Benachrichtigungen
audience: integrator, developer, admin
category: integration
order: 200
summary: Exponential mit Mailservern und E-Mail-Anbietern verbinden - die Transporte sendmail, SMTP und Datei, eine eigene Transportklasse für die HTTP-API eines Anbieters, das Mail-Gate und die Präferenzen, Benachrichtigungen und ihre Erweiterungspunkte, Veröffentlichen ohne Benachrichtigung bei Importen und Testen, ohne echte E-Mails zu senden.
keywords: E-Mail, Mail, MailSettings, Transport, TransportAlias, SMTP, sendmail, Datei-Transport, eZMail, eZMailTransport, E-Mail-Anbieter, Transaktions-E-Mail, Benachrichtigung, notification.ini, eZNotificationTransport, Mail-Gate, E-Mail-Präferenzen, Rückläufer, DebugSending, cjw_newsletter, mail, e-mail, file transport, mail provider, transactional mail, notification, mail gate, mail preferences, bounces
type: help
---
# Integration von E-Mail und Benachrichtigungen

Diese Seite ist für Integratoren, die Exponential mit einem Mailserver, einem Anbieter für Transaktions-E-Mails oder
einem Newsletter-Dienst verbinden und verhindern müssen, dass Importe Abonnenten mit Benachrichtigungen überfluten.
E-Mails laufen in Exponential über eine Klasse, `eZMail`, und eine Sendefunktion, `eZMailTransport::send()`, die den
in `site.ini [MailSettings] Transport` genannten **Transport** wählt. Die Referenz für Entwickler ist
[E-Mails mit eZMail senden](../erweiterungsentwicklung/teil-8-referenz/ezmail.md).

## Inhalt

1. [Die Transporte](#die-transporte)
2. [SMTP zu einem E-Mail-Anbieter](#smtp-zu-einem-e-mail-anbieter)
3. [Ein Transport für die HTTP-API eines Anbieters](#ein-transport-für-die-http-api-eines-anbieters)
4. [Das Mail-Gate und die Präferenzen](#das-mail-gate-und-die-präferenzen)
5. [Benachrichtigungen](#benachrichtigungen)
6. [Importe und Benachrichtigungen](#importe-und-benachrichtigungen)
7. [Testen, ohne zu senden](#testen-ohne-zu-senden)

## Die Transporte

`settings/site.ini [MailSettings]`:

| Einstellung | Bedeutung |
|---|---|
| `Transport` | `sendmail` (Standard), `smtp` oder `file` oder ein eigener Alias |
| `TransportAlias[]` | Alias zu Klasse: `file` = `eZFileTransport`, `sendmail` = `eZSendmailTransport`, `smtp` = `eZSMTPTransport` |
| `TransportServer`, `TransportPort` (25), `TransportConnectionType` (leer, `ssl`, `tls`), `TransportUser`, `TransportPassword` | SMTP |
| `AdminEmail`, `EmailSender`, `EmailReplyTo` | Adressen; `EmailSender` ist der Standard-Absender (From) |
| `DebugSending`, `DebugReceiverEmail` | jede E-Mail stattdessen an eine Testadresse senden |

`eZSMTPTransport` sendet über die Mail-Bibliothek der Zeta Components; `tls` bedeutet STARTTLS
([API- und Protokollstandards, SMTP](../../standards/apis-und-protokolle.md#smtp-rfc-5321-und-mail)).

## SMTP zu einem E-Mail-Anbieter

Die meisten Anbieter akzeptieren SMTP. In `settings/override/site.ini.append.php` (nie in Git):

```ini
[MailSettings]
Transport=smtp
TransportServer=smtp.example.com
TransportPort=587
TransportConnectionType=tls
TransportUser=<provider user>
TransportPassword=<provider password or key>
EmailSender=noreply@example.com
```

Richten Sie SPF, DKIM und DMARC für die Absenderdomain beim Anbieter ein; Exponential signiert E-Mails nicht selbst.

## Ein Transport für die HTTP-API eines Anbieters

Wenn die HTTP-API des Anbieters bevorzugt wird (Zustellereignisse, Vorlagen auf Seite des Anbieters), schreiben Sie in
Ihrer Erweiterung eine Transportklasse und wählen Sie sie über einen Alias. Der Mechanismus ist in
[Über Einstellungen benannte Handler](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/handler-ueber-einstellungen-benennen.md)
beschrieben:

```php
<?php
/**
 * Sends mail through the provider's HTTP API.
 * site.ini [MailSettings] TransportAlias[myprovider]=myIntegrationMailTransport, Transport=myprovider
 */
class myIntegrationMailTransport extends eZMailTransport
{
    public function sendMail( eZMail $mail )
    {
        $receivers = array();
        foreach ( $mail->receiverElements() as $receiver )
        {
            $receivers[] = $receiver['email'];
        }
        $ini = eZINI::instance( 'myintegration.ini' );
        $payload = json_encode( array( 'to' => $receivers, 'subject' => $mail->subject(), 'text' => $mail->body() ) );
        $curl = curl_init( $ini->variable( 'MailProvider', 'Endpoint' ) );
        curl_setopt_array( $curl, array(
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTPHEADER => array( 'Content-Type: application/json',
                                         'Authorization: Bearer ' . $ini->variable( 'MailProvider', 'ApiKey' ) ),
        ) );
        curl_exec( $curl );
        $status = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
        curl_close( $curl );
        if ( $status < 200 || $status >= 300 )
        {
            eZDebug::writeError( "Mail provider answered $status", __METHOD__ );
            return false;
        }
        return true;
    }
}
```

`myintegration.ini` mit `[MailProvider] Endpoint` und `ApiKey` ist die eigene Einstellungsdatei Ihrer Erweiterung;
der Schlüssel gehört in `settings/override/myintegration.ini.append.php`. Geben Sie bei einem Fehler `false` zurück,
damit der Aufrufer ihn protokollieren kann.

## Das Mail-Gate und die Präferenzen

Unabhängig vom Transport greift darüber das **Mail-Gate** des Kernels (`expMailGate`, `kernel/classes/mailpreferences/`):
E-Mail-Präferenzen, Einwilligung und Sperrlisten der Empfänger werden beachtet, und die Header `List-Unsubscribe` und
`List-Unsubscribe-Post` werden ergänzt (RFC 8058). Rückläufer werden vom Cronjob-Skript `mailbounces.php` (Teil
`frequent`) verarbeitet. Eine Integration, die im Namen der Website E-Mails sendet, sollte eine Kategorie verwenden,
damit Empfänger sich abmelden können ([E-Mail-Präferenzen für Entwickler](../../anleitungen/mail-einstellungen-entwickler.md)).

## Benachrichtigungen

Abonnenten eines Teilbaums bekommen E-Mails, wenn darunter Inhalte veröffentlicht werden. Die Kette: Ein Ereignis
(`eZNotificationEvent`) wird von der Veröffentlichungsoperation angelegt, der Cronjob-Teil `frequent`
(`notification.php`) übergibt ausstehende Ereignisse an die Handler, die Handler bauen Sammlungen von Nachrichten, und
ein **Benachrichtigungstransport** (standardmäßig `ezmail`) sendet sie über den E-Mail-Transport. Erweiterungspunkte
([Benachrichtigungen für Entwickler](../../anleitungen/benachrichtigungen-entwickler.md#5-erweiterungspunkte)):

| Punkt | Nutzen für Integrationen |
|---|---|
| Ein neuer Handler in `notification.ini` | Veröffentlichungen statt oder zusätzlich zu E-Mails an einen Chat oder ein CRM senden |
| Ein neuer Transport (`[TransportSettings] TransportPluginPath[]`) | Benachrichtigungen über einen anderen Kanal zustellen |
| `eZMailNotificationTransport::observe( $callback, $suppress )` | Benachrichtigungs-E-Mails beobachten oder unterdrücken, zum Beispiel in Tests |
| Der Filter `content/notification/create` | entscheiden, ob überhaupt eine Benachrichtigung angelegt wird |

## Importe und Benachrichtigungen

Ein Import, der Tausende Objekte veröffentlicht, darf keine Tausende E-Mails senden. Veröffentlichen Sie ohne
Benachrichtigung:

```php
<?php
eZContentFunctions::createAndPublishObject( $params, false );          // no notification event
eZContentFunctions::updateAndPublishObject( $object, $params, false );
```

Das zweite Argument (`$notify`) ist der Parameter `notify` der Veröffentlichungsoperation, derselbe wie **Ohne
Benachrichtigung veröffentlichen** im Editor ([Ohne Benachrichtigung veröffentlichen](../../funktionen/6.0/veroeffentlichen-ohne-benachrichtigung.md)).

## Testen, ohne zu senden

Setzen Sie auf jeder Installation außerhalb der Produktion `Transport=file` (E-Mails werden unter `var/log/mail`
geschrieben), und prüfen Sie, dass keine Einstellung in `settings/override` einen anderen Transport erzwingt: Ein
Override gewinnt gegen eine Siteaccess-Einstellung. Verwenden Sie in Testdaten Adressen auf reservierten Domains
(`user@example.invalid`). `DebugSending=enabled` mit `DebugReceiverEmail` leitet alle E-Mails an eine Adresse um,
wenn Sie die echte Zustellung testen müssen.

## Verwandte Seiten

- [E-Mails mit eZMail senden](../erweiterungsentwicklung/teil-8-referenz/ezmail.md)
- [Benachrichtigungen für Entwickler](../../anleitungen/benachrichtigungen-entwickler.md)
- [E-Mail-Zustellung für Hoster](../hosting/betrieb/mailzustellung.md)
- [Benachrichtigungen und E-Mail für App-Ersteller](../ersteller/anwendungsentwickler/benachrichtigungen-und-mail.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
