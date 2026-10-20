---
title: Sending mail with eZMail
audience: developer
category: extension-development
order: 250
summary: Send mail with eZMail and eZMailTransport::send() - receivers, sender, subject, body, content type - using the site's transports, a template for the body, and the file transport for tests.
keywords: eZMail, eZMailTransport, send, setReceiver, addReceiver, setSender, setSubject, setBody, setContentType, MailSettings, Transport, file transport, AdminEmail, EmailSender, validate
type: help
---
# Sending mail with eZMail

Part 8, reference 21. `eZMail` (`lib/ezutils/classes/ezmail.php`) is a message; `eZMailTransport::send()`
(`lib/ezutils/classes/ezmailtransport.php`) sends it with the transport the site configured. Use them instead of
PHP's `mail()`: the site's SMTP settings, sender address, character set and mail preferences then apply.

## A message

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

| Method | Use |
|---|---|
| `setReceiver( $email, $name = false )`, `addReceiver( ... )` | the To addresses |
| `addCc( ... )`, `addBcc( ... )` | copies |
| `setSender( $email, $name = false )` | the From address |
| `setReplyTo( $email, $name = false )` | the Reply-To address |
| `setSubject( $newSubject )`, `setBody( $newBody )` | subject and body |
| `setContentType( $type = false, $charset = false, ... )` | `text/plain` or `text/html` |
| `eZMail::validate( $address )` (static) | whether an address is well formed |
| `eZMailTransport::send( eZMail $mail, ?string $transportType = null )` (static) | send it; returns `true` on success |

## The site's mail settings

`site.ini [MailSettings]`:

| Setting | Meaning |
|---|---|
| `Transport` | `sendmail` (default), `smtp` or `file` |
| `TransportServer`, `TransportPort`, `TransportConnectionType`, `TransportUser`, `TransportPassword` | for SMTP |
| `AdminEmail` | the site administrator's address |
| `EmailSender` | the default From address |
| `EmailReplyTo` | the default Reply-To address |
| `ContentType` | the default content type |

Your extension should not set these; the site does. If your extension needs its own sender, make it a setting of
your own INI file, with `EmailSender` as the default.

## Testing without sending

On a development copy, set `Transport=file`. Mails are then written to files in `var/log/mail` (or the directory of
`[MailSettings] FileTransportDirectory`) instead of being sent, so you can read them and nobody receives a test mail.
Check that no setting in `settings/override` forces another transport: an override wins over a siteaccess setting. Use made-up addresses on reserved domains
(`user@example.invalid`) in tests.

## Templates for the body

Keep the text in a template (`design:myext/mail/...tpl`) with translation strings, so sites can change the wording
with an override and translators can translate it. The kernel's own notification mails do the same.

## Respect the recipients

- Send only mail the recipient expects (they ordered, subscribed or asked).
- Respect the user's mail preferences where your mail is a notification ([Mail preferences](../../../bc/6.0/mail-preferences.md)).
- Never put a full address list into To or Cc; one mail per recipient, or Bcc.
- Do not log mail bodies or addresses.

## Related pages

- Previous: [ezformtoken and CSRF protection](ezformtoken-and-csrf.md)
- Next: [Cache tags, cache blocks and clearing caches](cache-tags-and-clearing.md)
- [Handlers named by settings](../part-3-extension-developer/handlers-named-by-settings.md) (a mail transport of your own)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
