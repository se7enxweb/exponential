---
title: Notifications and mail from applications
audience: app-creator, developer
category: app-creation
order: 380
summary: Send mail from an application correctly - eZMail with a sender and a category, the mail gate and preferences, transports and the file transport for testing, notification event types and handlers, and templates for mail.
keywords: mail, eZMail, eZMailTransport, setCategory, mail gate, mail preferences, unsubscribe, notifications, event type, handler, file transport, testing, app creator
type: help
---
# Notifications and mail from applications

This page is for application creators whose application sends mail: confirmations, reminders, reports. Exponential
passes every mail through a **mail gate** that applies recipients' preferences, and has a notification system for
mail triggered by content events ([Mail preferences for developers](../../../guides/mail-preferences-developer.md),
[Notifications for developers](../../../guides/notifications-developer.md)).

## Contents

1. [Sending a mail](#sending-a-mail)
2. [Categories and the gate](#categories-and-the-gate)
3. [Transports and testing](#transports-and-testing)
4. [Notifications on content events](#notifications-on-content-events)
5. [Rules](#rules)

## Sending a mail

```php
$ini = eZINI::instance();
$mail = new eZMail();
$mail->setSender( $ini->variable( 'MailSettings', 'EmailSender' ) ?: $ini->variable( 'MailSettings', 'AdminEmail' ) );
$mail->addReceiver( $address );
$mail->setSubject( $subject );
$mail->setBody( $text );
$mail->setCategory( 'events' );
eZMailTransport::send( $mail );
```

Build the body from a template (`$tpl->fetch( 'design:expdemo/mail/reminder.tpl' )`) so sites can change the wording.

## Categories and the gate

- Every mail has a **category** (`setCategory()`); a category is **essential** (always sent: security, orders, legal)
  or **optional** (only to recipients who turned it on).
- `eZMailTransport::send()` hands every mail to `expMailGate::dispatch()`, which decides per recipient, adds the footer
  and unsubscribe headers, and logs the blocked ones.
- Mail without a category is sent unchanged and logged as uncategorized; give every mail a category.
- A category of your own is declared in settings ([3. A category of your own](../../../guides/mail-preferences-developer.md#3-a-category-of-your-own)).

## Transports and testing

`site.ini [MailSettings] Transport` is `sendmail`, `smtp` or `file`; the file transport writes each mail to a file,
which is how to test without sending ([10.4 Mail](../../../install/10-after-installing.md#104-mail-the-file-transport-and-real-sending),
[8. Testing without sending mail](../../../guides/mail-preferences-developer.md#8-testing-without-sending-mail)).
An override in `settings/override/` wins over a siteaccess setting: check which transport is really in effect with
`./console exp:ini where site.ini/MailSettings/Transport <siteaccess>` before a test.

## Notifications on content events

For "tell people when something is published", write a notification event type and a handler instead of sending mail
in a workflow: the notification system collects events, lets users subscribe and sends in the `frequent` cron part
([2. An event type and a handler](../../../guides/notifications-developer.md#2-an-event-type-and-a-handler-a-complete-example)).

## Rules

- Always set a sender; never a visitor's address as From.
- Test addresses end in reserved domains (`example.com`, `.invalid`); never mail real people from a test.
- Do not split recipient lists yourself; the gate does, with each recipient's own unsubscribe link.
- Slow or bulk mail goes through a queue or cron, not the web request.

## Related pages

- [Mail preferences](../../../features/6.0/mail-preferences.md)
- [Notifications](../../../features/6.0/notifications.md)
- [Queues with expqueue](queues-with-expqueue.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
