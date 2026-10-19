---
title: Rehearsing with the file transport
audience: government, admin
category: public-sector
order: 50
summary: How to rehearse the whole newsletter cycle (subscribe, confirm, send, unsubscribe) without sending a single real mail, using cjw_newsletter's file transport, and how to switch to real sending step by step.
keywords: file transport, rehearsal, test, eml, TransportMethodCronjob, TransportMethodPreview, TransportMethodDirectly, FileTransportMailDir, smtp, sendmail, newsletter test
type: help
---
# Rehearsing with the file transport

This page is for administrators. A fresh installation writes newsletter mail to files, so the whole process can be
rehearsed safely ([Newsletters with cjw_newsletter](../../../creators/website-creators/newsletters-with-cjw-newsletter.md#rehearsing-without-real-mail),
[Sending newsletters safely](../../../creators/website-creators/sending-newsletters.md)).

## Contents

1. [The settings](#the-settings)
2. [The rehearsal](#the-rehearsal)
3. [Switching to real mail](#switching-to-real-mail)
4. [Test addresses](#test-addresses)

## The settings

`cjw_newsletter.ini [NewsletterMailSettings]`, overridden in `settings/override/cjw_newsletter.ini.append.php`:

| Key | Used for | Rehearsal value |
|---|---|---|
| `TransportMethodCronjob` | editions sent by cron | `file` |
| `TransportMethodPreview` | test sends from the editor | `file` |
| `TransportMethodDirectly` | confirmations and info mails | `file` |
| `FileTransportMailDir` | where `file` writes one `.eml` per mail | `var/log/mail` (default) |

The directory must be writable by the web server user and the cron user.

## The rehearsal

1. Subscribe to a list on the public site with a test address; find the confirmation `.eml` in the mail directory.
2. Open the confirmation link from the file; the subscription becomes confirmed.
3. Create an edition, send a preview, schedule it with **Send**.
4. Run cron: `php runcronjobs.php -s <siteaccess> cjw_newsletter`; one `.eml` per recipient appears.
5. Open a file in a mail program; check the content, links, the unsubscribe link and the footer.
6. Unsubscribe with the link; check the status.

## Switching to real mail

1. `TransportMethodPreview=smtp`; send a test to your own address only.
2. Check the sender domain's SPF and DKIM at your mail provider.
3. Switch `TransportMethodDirectly` and `TransportMethodCronjob` to `smtp` (or `sendmail`).

Keep SMTP credentials in `settings/override/`, never in version control.

## Test addresses

Use addresses on a domain you control, or a reserved test domain such as `example.invalid` with the file transport, so
a mistake never mails real people. Never test with addresses of real subscribers.

## Related pages

- [Sending newsletters safely](../../../creators/website-creators/sending-newsletters.md)
- [Mail delivery](../../../hosting/operations/mail-delivery.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
