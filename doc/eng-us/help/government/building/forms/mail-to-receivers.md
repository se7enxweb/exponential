---
title: Mail to receivers
audience: government, website-creator, admin
category: public-sector
order: 70
summary: Who gets a mail when a form built with Exponential's information collector is sent - the default receiver, a receiver per form, the mail templates, sending settings, testing with the file transport, and what not to put in mails.
keywords: mail, e-mail receiver, EmailReceiver, recipient attribute, collectedinfomail, SendEmail, MailSettings, Transport, file transport, SMTP, shared mailbox, testing
type: help
---
# Mail to receivers

This page is for website creators and administrators who decide where form submissions are mailed. Collections are
always stored in the database (**Setup > Collected information**); the mail is a copy for the team that handles them.
Sources: [Forms with the information collector](../../../creators/website-creators/forms-with-the-information-collector.md#mail-to-a-receiver)
and [10.4 Mail](../../../../install/10-after-installing.md#104-mail-the-file-transport-and-real-sending).

## Contents

1. [Who receives the mail](#who-receives-the-mail)
2. [The mail templates](#the-mail-templates)
3. [Sending settings](#sending-settings)
4. [Testing without sending](#testing-without-sending)
5. [Good practice](#good-practice)

## Who receives the mail

| Level | Setting | Use |
|---|---|---|
| Whether to mail at all | `collect.ini [EmailSettings] SendEmail` (enabled), `SendEmailList[<type>]` (polls: disabled) | per form type |
| Default receiver | `site.ini [InformationCollectionSettings] EmailReceiver` | a general mailbox |
| Per form | a `recipient` attribute (Email) on the form object, used by the ezwebin mail templates | each form mails its own team |
| In your own templates | `{set-block scope=root variable=email_receiver}...{/set-block}` in the mail template | any rule you need |

The shipped Feedback form class (`feedback_form`) has a **Recipient** attribute that editors fill; with the ezwebin
mail template (`extension/ezwebin/design/ezwebin/templates/content/collectedinfomail/feedback.tpl`) the mail goes there.

## The mail templates

| Template | Purpose |
|---|---|
| `content/collectedinfomail/<type>.tpl` | the mail body for the type (form, feedback, poll) |
| `content/collectedinfo/<type>.tpl` | the page the visitor sees after sending (with `Display=result`) |

Override them in your design ([Template overrides](../../../creators/website-creators/template-overrides.md)) to give
the mail a clear subject ("New report: <form name>"), the values in a readable order and a link to the collection in
the administration.

## Sending settings

`site.ini [MailSettings]`:

| Key | Values |
|---|---|
| `Transport` | `sendmail`, `SMTP` or `file` |
| `TransportServer`, `TransportPort`, `TransportConnectionType`, `TransportUser`, `TransportPassword` | your SMTP relay |
| `AdminEmail`, `EmailSender` | the sender address |

Use your organization's mail relay; the sender domain must be allowed to send (SPF, DKIM, DMARC are configured on the
mail side). Put passwords in `settings/override/` and keep them out of version control.

## Testing without sending

`Transport=file` writes each mail to `var/log/mail/` (or `[MailSettings] FileTransportDirectory`) instead of sending
it. Use it on test and staging installations, and check the files:

```bash
ls -lt var/log/mail/ | head
```

Use test addresses on a reserved domain (for example `example.invalid`) in test forms; never real people's
addresses.

## Good practice

| Practice | Why |
|---|---|
| Mail to shared team mailboxes, not personal ones | staff change; holidays |
| Keep the mail short, with a link to the collection, when submissions hold sensitive data | mail is copied, forwarded and kept long |
| Do not send the visitor's message back to the visitor's address automatically | anyone can type someone else's address; it turns your form into a spam relay |
| Remove the mailbox copies on the same schedule as the collections | [Retention of collected information](retention-of-collected-information.md) |

## Related pages

- [Building forms](README.md)
- [Information collector basics](information-collector-basics.md)
- [Mail preferences for administrators](../../../../guides/mail-preferences-administrator.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
