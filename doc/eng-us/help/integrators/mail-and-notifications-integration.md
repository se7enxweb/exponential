---
title: Mail and notifications integration
audience: integrator, developer, admin
category: integration
order: 200
summary: Connecting Exponential to mail servers and mail providers - the sendmail, SMTP and file transports, a transport class of your own for a provider's HTTP API, the mail gate and preferences, notifications and their extension points, publishing without notification during imports, and testing without sending real mail.
keywords: mail, e-mail, MailSettings, Transport, TransportAlias, SMTP, sendmail, file transport, eZMail, eZMailTransport, mail provider, transactional mail, notification, notification.ini, eZNotificationTransport, mail gate, mail preferences, bounces, DebugSending, cjw_newsletter
type: help
---
# Mail and notifications integration

This page is for integrators who connect Exponential to a mail server, a transactional mail provider or a newsletter
service, and who must keep imports from flooding subscribers with notifications. Mail in Exponential goes through one
class, `eZMail`, and one sending function, `eZMailTransport::send()`, which picks the **transport** named in
`site.ini [MailSettings] Transport`. The developer reference is [Sending mail with eZMail](../extension-development/part-8-reference/ezmail.md).

## Contents

1. [The transports](#the-transports)
2. [SMTP to a mail provider](#smtp-to-a-mail-provider)
3. [A transport for a provider's HTTP API](#a-transport-for-a-providers-http-api)
4. [The mail gate and preferences](#the-mail-gate-and-preferences)
5. [Notifications](#notifications)
6. [Imports and notifications](#imports-and-notifications)
7. [Testing without sending](#testing-without-sending)

## The transports

`settings/site.ini [MailSettings]`:

| Setting | Meaning |
|---|---|
| `Transport` | `sendmail` (default), `smtp` or `file`, or an alias of your own |
| `TransportAlias[]` | Alias to class: `file` = `eZFileTransport`, `sendmail` = `eZSendmailTransport`, `smtp` = `eZSMTPTransport` |
| `TransportServer`, `TransportPort` (25), `TransportConnectionType` (empty, `ssl`, `tls`), `TransportUser`, `TransportPassword` | SMTP |
| `AdminEmail`, `EmailSender`, `EmailReplyTo` | Addresses; `EmailSender` is the default From |
| `DebugSending`, `DebugReceiverEmail` | Send every mail to one test address instead |

`eZSMTPTransport` sends through the Zeta Components mail library; `tls` means STARTTLS
([API and protocol standards, SMTP](../../standards/apis-and-protocols.md#smtp-rfc-5321-and-mail)).

## SMTP to a mail provider

Most providers accept SMTP. In `settings/override/site.ini.append.php` (never in Git):

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

Set up SPF, DKIM and DMARC for the sender domain at the provider; Exponential does not sign mail itself.

## A transport for a provider's HTTP API

When the provider's HTTP API is preferred (delivery events, templates on the provider side), write a transport class
in your extension and select it by alias. The mechanism is described in
[Handlers named by settings](../extension-development/part-3-extension-developer/handlers-named-by-settings.md):

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

`myintegration.ini` with `[MailProvider] Endpoint` and `ApiKey` is your extension's own settings file; the key goes in
`settings/override/myintegration.ini.append.php`. Return `false` on failure so the caller can log it.

## The mail gate and preferences

Whatever the transport, the kernel's **mail gate** (`expMailGate`, `kernel/classes/mailpreferences/`) applies on top:
recipients' e-mail preferences, consent and suppression are honored, and `List-Unsubscribe` and
`List-Unsubscribe-Post` headers are added (RFC 8058). Bounces are processed by the cronjob script `mailbounces.php`
(part `frequent`). An integration that sends mail on behalf of the site should use a category so recipients can
opt out ([E-mail preferences for developers](../../guides/mail-preferences-developer.md)).

## Notifications

Subscribers of a subtree get mail when content below it is published. The chain: an event (`eZNotificationEvent`)
is created by the publish operation, the cronjob part `frequent` (`notification.php`) hands pending events to the
handlers, the handlers build collections of messages, and a **notification transport** (`ezmail` by default) sends
them through the mail transport. Extension points ([Notifications for developers](../../guides/notifications-developer.md#5-extension-points)):

| Point | Integration use |
|---|---|
| A new handler in `notification.ini` | Send publications to a chat or a CRM instead of, or besides, mail |
| A new transport (`[TransportSettings] TransportPluginPath[]`) | Deliver notifications through another channel |
| `eZMailNotificationTransport::observe( $callback, $suppress )` | Watch or suppress notification mail, for example in tests |
| The filter `content/notification/create` | Decide whether a notification is created at all |

## Imports and notifications

An import that publishes thousands of objects must not send thousands of mails. Publish without notification:

```php
<?php
eZContentFunctions::createAndPublishObject( $params, false );          // no notification event
eZContentFunctions::updateAndPublishObject( $object, $params, false );
```

The second argument (`$notify`) is the publish operation's `notify` parameter, the same as **Publish without
notification** in the editor ([Publish without notification](../../features/6.0/publish-without-notification.md)).

## Testing without sending

On every non-production installation set `Transport=file` (mail is written below `var/log/mail`), and check that no
setting in `settings/override` forces another transport: an override wins over a siteaccess setting. Use addresses
on reserved domains (`user@example.invalid`) in test data. `DebugSending=enabled` with `DebugReceiverEmail` redirects
all mail to one address when you must test real delivery.

## Related pages

- [Sending mail with eZMail](../extension-development/part-8-reference/ezmail.md)
- [Notifications for developers](../../guides/notifications-developer.md)
- [Mail delivery for hosts](../hosting/operations/mail-delivery.md)
- [Notifications and mail for app creators](../creators/app-creators/notifications-and-mail.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
