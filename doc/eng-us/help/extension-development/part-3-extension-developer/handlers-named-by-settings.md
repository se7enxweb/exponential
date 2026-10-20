---
title: Handlers named by settings
audience: developer, app-creator
category: extension-development
order: 290
summary: Replace one part of the system with a class chosen by a setting: getHandlerClass(), a verified mail transport, and the handler points for login, session, search, shop and more.
keywords: handler, getHandlerClass, ezpExtensionOptions, TransportAlias, mail transport, eZMailTransport, login handler, LoginHandler, session handler, search engine, binary file handler, payment gateway, VAT handler, shipping handler, handlerextension
type: help
---
# Handlers named by settings

Part 3, chapter 29. Many parts of the system are **handlers**: a class chosen by a setting. The kernel ships a default,
and an extension can provide another and select it with one line of settings. Mail transports, login, sessions,
search, binary file storage, payment gateways, VAT and shipping are all handlers. This chapter shows the mechanism with
a mail transport; resolving it was checked with the kernel loaded (nothing was sent).

## The mechanism

`eZExtension::getHandlerClass( ezpExtensionOptions $options )` (`lib/ezutils/classes/ezextension.php`) reads a setting,
looks up a class name, and creates the object. The mail system uses it like this (`eZMailTransport::send()`,
`lib/ezutils/classes/ezmailtransport.php`):

```php
<?php
$options = new ezpExtensionOptions( array( 'iniFile'      => 'site.ini',
                                           'iniSection'   => 'MailSettings',
                                           'iniVariable'  => 'TransportAlias',
                                           'handlerIndex' => strtolower( $transportType ) ) );
$transportClass = eZExtension::getHandlerClass( $options );
```

`[MailSettings] Transport` names the alias (`sendmail` by default), and `TransportAlias[]` maps aliases to classes:
`file` to `eZFileTransport`, `sendmail` to `eZSendmailTransport`, `smtp` to `eZSMTPTransport`.

## A mail transport of your own

```php
<?php
/**
 * A mail transport that sends nothing: it writes the receivers and subject to var/log/mysite_mail.log.
 * Selected with site.ini [MailSettings] Transport=mysitelog and TransportAlias[mysitelog]=mysiteLogMailTransport.
 */
class mysiteLogMailTransport extends eZMailTransport
{
    public function sendMail( eZMail $mail )
    {
        $receivers = array();
        foreach ( $mail->receiverElements() as $receiver )
        {
            $receivers[] = $receiver['email'];
        }
        eZLog::write( 'Mail to ' . implode( ', ', $receivers ) . ': ' . $mail->subject(), 'mysite_mail.log' );
        return true;
    }
}
```

Select it in your extension's `settings/site.ini.append.php`:

```ini
[MailSettings]
TransportAlias[mysitelog]=mysiteLogMailTransport
```

and, on a development installation, in that server's `settings/override/site.ini.append.php`:
`Transport=mysitelog`. With the alias added, `getHandlerClass()` returned an object of `mysiteLogMailTransport`.

Note that the kernel's mail gate (`expMailGate`, the e-mail preferences) still applies on top of any transport.

## Handler points

The RAD catalogue lists every handler point with its setting and contract; the **Handler extension** wizard writes a
class for each at `/setup/handlerextension/<kind>`. The kinds offered there:

| Area | Kinds |
|---|---|
| Content and rich text | `xmlinput`, `xmloutput`, `attributeoperator`, `publishfilter`, `urlfilter` |
| Storage and infrastructure | `dbhandler`, `cluster`, `dfsbackend`, `dfsdbbackend`, `binaryfile`, `search`, `session`, `staticcache`, `inicache`, `mail` |
| Users and requests | `login`, `mobilefilter` |
| REST | `restprovider`, `restprerouting`, `restrequestfilter`, `restresultfilter`, `restresponsefilter`, `restprefix`, `restroutefilter` |
| Ajax and notifications | `ajaxfunction`, `notificationtype`, `notificationhandler` |
| Packages | `packagehandler`, `packagecreation`, `packageinstall` |
| Shop | `paymentgateway`, `paymentgatewaydirect`, `vat`, `shipping`, `basketinfo`, `exchangerate` |

A few registrations, from [Extension points](../../../bc/6.0/rad-extension-points.md):

| Handler | Registered by | Contract |
|---|---|---|
| Login | `site.ini [UserSettings] LoginHandler[]` and `ExtensionDirectory[]` | extends `eZUser`, implements `loginUser()` |
| Session | `site.ini [Session] Handler` | a session handler class |
| Search engine | `site.ini [SearchSettings] SearchEngine` | implements `ezpSearchEngine` |
| Mail | `site.ini [MailSettings] Transport` and `TransportAlias[]` | extends `eZMailTransport`, implements `sendMail()` |

## Rules

- Extend the kernel's base class or implement its interface; copy the default handler's behavior for everything you
  do not change.
- Ship the alias in your extension, but let each installation choose it in its own settings: a handler for one server
  (a mail relay, a cluster backend) is not a default for everyone.
- Test with the default handler switched back: removing your extension must leave a working system.

## Related pages

- Previous: [Events, listeners and filters](events-and-filters.md)
- Next: [Kernel class overrides: the last resort](kernel-overrides-the-last-resort.md)
- [With RAD: handlers and kernel overrides](tutorials/rad-handlers-and-kernel-overrides.md)
- [Sending mail with eZMail](../part-8-reference/ezmail.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
