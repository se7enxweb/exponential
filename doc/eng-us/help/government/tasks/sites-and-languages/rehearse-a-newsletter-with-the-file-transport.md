---
title: Rehearse a newsletter with the file transport
audience: government, admin
category: public-sector
order: 350
summary: Task for administrators: run a whole newsletter cycle (subscribe, confirm, send, unsubscribe) with cjw_newsletter writing mail to .eml files, so no real person gets mail.
keywords: rehearsal, file transport, eml, cjw_newsletter, TransportMethodCronjob, FileTransportMailDir, test, newsletter
type: help
---
# Rehearse a newsletter with the file transport

This task is for administrators. Background:
[Rehearsing with the file transport](../../building/newsletters/rehearsing-with-the-file-transport.md).

## Steps

1. In `settings/override/cjw_newsletter.ini.append.php`, `[NewsletterMailSettings]`: `TransportMethodCronjob=file`,
   `TransportMethodPreview=file`, `TransportMethodDirectly=file`; `FileTransportMailDir=var/log/mail` (writable by the
   web server and cron users).
2. Subscribe on the public site with a test address on a domain you control (or `example.invalid`).
3. Open the confirmation `.eml` in the mail directory; open its link: the subscription is confirmed.
4. Create an edition, send a preview, **Send**.
5. Run `php runcronjobs.php -s <siteaccess> cjw_newsletter`; one `.eml` per recipient appears.
6. Open it in a mail program: content, links, footer, unsubscribe link.
7. Unsubscribe with the link; check the status in the list's subscriptions.

## Check

All mails are files; nothing left the server. The subscription went pending, confirmed, removed.

## Problems

| Problem | Fix |
|---|---|
| No files appear | directory permissions, or the cron part ran for another siteaccess |
| A real mail went out | another setting overrides the transport; check every override file |

## Related pages

- [Sending newsletters safely](../../../creators/website-creators/sending-newsletters.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
