---
title: Sending newsletters safely
audience: website-creator, admin
category: website-creation
order: 510
summary: Send newsletters without surprises - test with the file transport, switch to SMTP or sendmail, the sender domain, the mail queue cronjobs, aborting a send, the archive, and a pre-send checklist.
keywords: send newsletter, mail transport, smtp, sendmail, file transport, mail queue, cronjob, abort, archive, deliverability, SPF, DKIM, cjw_newsletter, website creator
type: help
---
# Sending newsletters safely

This page is for website creators and administrators who send newsletters for real. A newsletter goes to many people at
once and cannot be called back, so rehearse first and switch on real sending deliberately.

## Contents

1. [The transports](#the-transports)
2. [From rehearsal to real mail](#from-rehearsal-to-real-mail)
3. [How a send runs](#how-a-send-runs)
4. [Before every send](#before-every-send)
5. [When something goes wrong](#when-something-goes-wrong)

## The transports

`cjw_newsletter.ini [NewsletterMailSettings]`, overridden in `settings/override/cjw_newsletter.ini.append.php`:

| Key | Used for |
|---|---|
| `TransportMethodCronjob` | the editions cron sends |
| `TransportMethodPreview` | test sends from the editor |
| `TransportMethodDirectly` | subscription confirmations and info mails |
| `FileTransportMailDir` | where `file` writes one `.eml` per mail (default `var/log/mail`) |
| `SmtpTransportServer`, `SmtpTransportPort`, `SmtpTransportUser`, `SmtpTransportPassword`, `SmtpTransportConnectionType` | the SMTP connection |

Each method is `file`, `smtp` or `sendmail`. The extension's transports are separate from the site's own mail
settings ([10.4 Mail](../../../install/10-after-installing.md#104-mail-the-file-transport-and-real-sending)).

## From rehearsal to real mail

1. Rehearse with `file` for all three methods; read the `.eml` files.
2. Switch `TransportMethodPreview` to `smtp` and send a test to your own address only.
3. Check how it looks in several mail programs, and that links and the unsubscribe link work.
4. Make sure your sender domain is set up at your mail provider (SPF, DKIM); otherwise mail ends up as spam.
5. Switch `TransportMethodDirectly` and `TransportMethodCronjob` to `smtp`.

Keep the SMTP password in `settings/override/`, never in version control.

## How a send runs

1. **Send** schedules an edition.
2. `cjw_newsletter_mailqueue_create` builds the queue: one entry per subscriber.
3. `cjw_newsletter_mailqueue_process` sends what waits.
4. The edition is archived (`newsletter/archive`).

`php runcronjobs.php -s <siteaccess> cjw_newsletter` runs both steps; the crontab of
[Newsletters with cjw_newsletter](newsletters-with-cjw-newsletter.md#setting-it-up) runs it every ten minutes.

## Before every send

- [ ] Test send received and checked.
- [ ] Subject, preheader and sender name right.
- [ ] Every link works and points to the public site.
- [ ] Images have alternative texts; the mail reads well without images.
- [ ] The right list is selected.
- [ ] The send time suits your readers.

## When something goes wrong

| Problem | Do |
|---|---|
| Wrong edition scheduled | abort it with `newsletter/send_abort` before cron processes the queue |
| Nothing is sent | check that cron runs the parts for the right siteaccess, and the transport is not `file` |
| Mail is marked as spam | sender domain records, content, and the sending server's reputation |
| Many bounces | check the bounce mailbox; clean the list |

## Related pages

- [Newsletters with cjw_newsletter](newsletters-with-cjw-newsletter.md)
- [Newsletter lists and subscriptions](newsletter-lists-and-subscriptions.md)
- [Cronjobs](../../../guides/cronjobs.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
