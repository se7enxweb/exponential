---
title: Newsletter mail delivery
audience: businesses, admin, hosting
category: business-solutions
order: 70
summary: How newsletter mail leaves an Exponential site - the three transports per kind of mail, a complete rehearsal that writes mail to files, switching to SMTP or sendmail step by step, sender domain records, rate limits and batches, and the cron parts that do the work.
keywords: newsletter delivery, mail transport, smtp, sendmail, file transport, rehearsal, SPF, DKIM, rate limit, throttle, batches, cronjob, mail queue, deliverability
type: help
---
# Newsletter mail delivery

This page is for managers who need to understand what has to be in place before a newsletter goes out, and for the
administrator or hosting partner who puts it there. It explains the transports, how to rehearse without sending
anything, how to switch to real mail safely, and how to stay within the limits of a mail provider.

## Contents

1. [The three transports](#the-three-transports)
2. [Rehearsal first](#rehearsal-first)
3. [From rehearsal to real mail](#from-rehearsal-to-real-mail)
4. [Sender domain and reputation](#sender-domain-and-reputation)
5. [Rate limits and batches](#rate-limits-and-batches)
6. [The cron parts](#the-cron-parts)
7. [Who does what](#who-does-what)
8. [Delivery checklist](#delivery-checklist)

## The three transports

`cjw_newsletter.ini [NewsletterMailSettings]` sets one transport per kind of mail
([Sending newsletters safely](../../creators/website-creators/sending-newsletters.md#the-transports)):

| Setting | Used for | Shipped value |
|---|---|---|
| `TransportMethodCronjob` | The editions cron sends | `file` |
| `TransportMethodPreview` | Test sends from the editor | `sendmail` |
| `TransportMethodDirectly` | Subscription confirmations and info mails | `file` |

Each method is `file` (nothing leaves the server; one `.eml` file per mail), `smtp` or `sendmail`. The extension's
transports are separate from the site's own mail settings. Values belong in
`settings/override/cjw_newsletter.ini.append.php`, and the SMTP password "in `settings/override/`, never in version
control" (same page).

> **Warning**
> The shipped preview transport is `sendmail`; the comment in `cjw_newsletter.ini` says "the extension default really
> sends". For a rehearsal set all three methods to `file`.

## Rehearsal first

The file transport makes a complete rehearsal possible: "subscribe, double opt-in, send, cron, unsubscribe" without a
mail server and without a real recipient
([cjw_newsletter](../../../features/6.0/extensions/cjw_newsletter.md#rehearse-the-whole-thing-without-sending-a-single-mail)).

```ini
[NewsletterMailSettings]
TransportMethodCronjob=file
TransportMethodPreview=file
TransportMethodDirectly=file
FileTransportMailDir=var/log/mail
```

Each recipient gets one uniquely named file `<date>-<id>-<recipient>.eml` in `FileTransportMailDir`. The directory
must be writable by the web server user and by the cron user, and a directory that cannot be written is an error, not
a silent loss (same page). Since 4.1.17 the dashboard shows the outbox and its last mail for the file transport.

A rehearsal script for a business:

1. Subscribe with a test address on the public site; open the confirmation `.eml` and its link.
2. Write a short test edition and schedule it.
3. Let cron run; check that one file per confirmed test subscriber appears.
4. Open a file in a mail program and check the look, the links and the unsubscribe link.
5. Unsubscribe through the link and confirm the status changed.

Use test addresses that cannot reach a real person (for example under the reserved domain `example.invalid`).

## From rehearsal to real mail

Follow the order of the website creator's guide
([Sending newsletters safely](../../creators/website-creators/sending-newsletters.md#from-rehearsal-to-real-mail)):

1. Rehearse with `file` for all three methods; read the `.eml` files.
2. Switch `TransportMethodPreview` to `smtp` and send a test to your own address only.
3. Check how it looks in several mail programs, and that links and the unsubscribe link work.
4. Make sure your sender domain is set up at your mail provider (SPF, DKIM).
5. Switch `TransportMethodDirectly` and `TransportMethodCronjob` to `smtp`.

SMTP needs `SmtpTransportServer`, `SmtpTransportPort`, `SmtpTransportUser`, `SmtpTransportPassword` and
`SmtpTransportConnectionType` (empty, `ssl`, `sslv2`, `sslv3` or `tls`)
([cjw_newsletter](../../../features/6.0/extensions/cjw_newsletter.md#rehearse-the-whole-thing-without-sending-a-single-mail)).

## Sender domain and reputation

Mail from a domain without SPF and DKIM records is more likely to be treated as spam; "otherwise mail ends up as spam"
(website creator's guide, step 4). These records are set at your DNS and mail provider, not in Exponential. Ask your
provider or hosting partner to set them up and to confirm in writing which servers may send for your domain.

The subject prefix of newsletter mails is `EmailSubjectPrefix`; empty means `[Newsletter <host of SiteURL>]` since
4.1.16 ([cjw_newsletter, Settings you will use](../../../features/6.0/extensions/cjw_newsletter.md#settings-you-will-use)).
Most businesses set their own prefix or none.

## Rate limits and batches

Many mail providers limit how many messages an account may send per minute or hour. Release 4.2.0 can keep to such
limits ([deliverability.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/deliverability.md)):

| Setting (`[ThrottleSettings]`) | Shipped | Meaning |
|---|---|---|
| `Throttle` | `disabled` | `enabled`: each send goes out in batches within the limits |
| `BatchSize` | `200` | Mails per batch |
| `PauseBetweenBatches` | `0` | Seconds before the next batch of the same send |
| `MaxPerMinute[smtp]`, `MaxPerHour[smtp]` | commented out | Limits per transport; 0 = no limit |

When a limit is reached the send stops and is resumed by the next run of the queue, from where it stopped. A transport
can also be paused for some minutes on Newsletter > Rate limits and batches; a paused transport sends nothing, even
with throttling disabled (same guide). Ask your provider for its limits and set them a little lower.

## The cron parts

Sending happens in cron, not in the browser
([Newsletters with cjw_newsletter](../../creators/website-creators/newsletters-with-cjw-newsletter.md#setting-it-up)):

```text
*/10 * * * *  cd /path/to/installation && php runcronjobs.php -q -s site cjw_newsletter
25   * * * *  cd /path/to/installation && php runcronjobs.php -q -s site cjw_newsletter_mailbox
```

The part `cjw_newsletter` runs `cjw_newsletter_mailqueue_create.php` and `cjw_newsletter_mailqueue_process.php`
(`settings/cronjob.ini.append.php` of the extension); `cjw_newsletter_mailbox` reads bounce mailboxes. General cron
setup: [Cronjobs](../../../guides/cronjobs.md).

## Who does what

| Task | Business | Administrator or hosting partner |
|---|---|---|
| Choose the mail provider and sender address | Decides | Advises |
| SPF and DKIM records | Approves | Sets up |
| Transports and SMTP settings | | Sets up, keeps the password out of version control |
| Cron parts and bounce mailbox | | Sets up and monitors |
| Rehearsal | Takes part, signs off | Runs |
| Rate limits | Tells the provider's limits | Configures |

## Delivery checklist

- [ ] All three transports on `file`; rehearsal passed and signed off.
- [ ] Sender address and domain chosen; SPF and DKIM set up at the provider.
- [ ] Preview transport on `smtp`; a test to an internal address checked in several mail programs.
- [ ] Provider limits known and `[ThrottleSettings]` set if needed.
- [ ] Cron parts `cjw_newsletter` and `cjw_newsletter_mailbox` running for the right siteaccess.
- [ ] Bounce mailbox configured ([Bounces and list hygiene](bounces-and-list-hygiene.md)).
- [ ] Remaining transports switched to `smtp` on a planned day, with someone watching the first send.

## Related pages

- [Newsletters for businesses](README.md)
- [Writing and sending editions](writing-and-sending-editions.md)
- [Sending newsletters safely](../../creators/website-creators/sending-newsletters.md)
- [After installing: mail](../../../install/10-after-installing.md#104-mail-the-file-transport-and-real-sending)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
