---
title: Supporter newsletter with cjw_newsletter
audience: non-profit, businesses, editor, admin
category: non-profits
order: 20
summary: Running a non-profit's supporter and volunteer newsletters with cjw_newsletter in Exponential - systems, lists and editions as content, double opt-in, a rehearsal that writes mail to files, switching to real sending, the cron parts, bounces and the blacklist, consent and one-click unsubscribe, and a monthly routine - with links to the full newsletter pages.
keywords: newsletter, supporter newsletter, cjw_newsletter, lists, editions, double opt-in, subscribe, unsubscribe, cron, mail queue, file transport, SMTP, bounces, blacklist, consent, volunteers list
type: help
---
# Supporter newsletter with cjw_newsletter

This page is for the communications lead and the administrator. `cjw_newsletter` is the newsletter system that comes
with Exponential: "you write newsletter editions as content; visitors subscribe to lists with a double opt-in;
editions are sent by cron in the background; bounces and a blacklist keep the lists clean; every send is archived"
([cjw_newsletter](../../../features/6.0/extensions/cjw_newsletter.md)). It is opened at **Newsletter** in the top
admin menu.

## Contents

1. [Lists a non-profit typically needs](#lists-a-non-profit-typically-needs)
2. [Setting up](#setting-up)
3. [Rehearse before sending for real](#rehearse-before-sending-for-real)
4. [Writing and sending an edition](#writing-and-sending-an-edition)
5. [Consent and unsubscribing](#consent-and-unsubscribing)
6. [Keeping lists clean](#keeping-lists-clean)
7. [Monthly routine](#monthly-routine)

## Lists a non-profit typically needs

| List | Who | Typical frequency |
|---|---|---|
| Supporters | Donors and friends who subscribed | Monthly or quarterly |
| Volunteers | Active volunteers | As needed: rotas, events |
| Press | Journalists | For announcements |
| A language list | Supporters in a second language | With each translated edition |

Lists are content objects (classes `cjw_newsletter_system`, `cjw_newsletter_list`, `cjw_newsletter_edition`) below
the node set in `[NewsletterSettings] RootFolderNodeId` (same source). Keep the number of lists small; every list is
work.

## Setting up

The administrator or partner:

1. Creates a newsletter system and its lists in the content tree.
2. Puts settings in `settings/override/cjw_newsletter.ini.append.php`.
3. Links the public subscribe page (`newsletter/subscribe`) from the site footer, the giving page's thank-you page
   and the volunteer pages.
4. Adds the cron parts once a rehearsal works:

```bash
php runcronjobs.php cjw_newsletter
php runcronjobs.php -s <siteaccess> cjw_newsletter_mailqueue_create
php runcronjobs.php -s <siteaccess> cjw_newsletter_mailqueue_process
```

Source: [cjw_newsletter](../../../features/6.0/extensions/cjw_newsletter.md),
[Newsletters with cjw_newsletter](../../creators/website-creators/newsletters-with-cjw-newsletter.md).

## Rehearse before sending for real

`cjw_newsletter.ini` can write mail to files instead of sending it (`TransportMethodCronjob=file` and the other two
transports, `FileTransportMailDir=var/log/mail`), which makes a complete rehearsal of subscribe, confirm, send and
unsubscribe possible without a real recipient. Then switch to `smtp` or `sendmail`
([Newsletter mail delivery](../../businesses/newsletters/newsletter-mail-delivery.md)).

For larger lists, a mail delivery service and correct sender domain records matter more than anything in the site;
that may be a budget line ([Budget lines](../grants/budget-lines-for-hosting-and-maintenance.md)).

## Writing and sending an edition

1. Create an edition in the list, as content: a subject, an introduction, a few stories with links to the site.
2. Send a test to yourself and a colleague (preview).
3. Schedule or send; cron queues and processes the mails.
4. Check the archive afterward.

Writing tips: one main message per edition, real text not images, alternative text on images, clear link texts
([Accessible campaigns and documents](../accessibility/accessible-campaigns-and-documents.md#newsletters)).
Details: [Writing and sending editions](../../businesses/newsletters/writing-and-sending-editions.md).

## Consent and unsubscribing

- Double opt-in: subscribers confirm by mail before they receive editions.
- A donation is not consent to the newsletter; offer it, never add donors yourself.
- Importing an old list needs a record of where the consent came from
  ([Opt-in and consent](../../businesses/newsletters/opt-in-and-consent.md)).
- Withdrawal must be as easy as consent: an unsubscribe link in every edition
  ([One-click unsubscribe](../../government/privacy/one-click-unsubscribe.md)).

> **Note**
> Basics, not legal advice.

## Keeping lists clean

After `BounceThresholdValue` bounces (default 3) a subscriber stops receiving; addresses on the blacklist never get
mail (same source). See [Bounces and list hygiene](../../businesses/newsletters/bounces-and-list-hygiene.md).

## Monthly routine

- [ ] Edition written, tested and sent on schedule.
- [ ] Confirmed subscriber count written down (useful for funders, [Measuring outcomes](../grants/measuring-outcomes-with-site-data.md)).
- [ ] Bounces looked at; blacklist requests handled.
- [ ] Cron ran (the archive shows the send).
- [ ] Subscriber exports, if any, deleted after use.

The general routine: [Newsletter routine](../../customers/growth/newsletter-routine.md).

## Related pages

- [Communications for non-profits](README.md)
- [Newsletters (Businesses)](../../businesses/newsletters/README.md)
- [Newsletter checklist](../../businesses/newsletters/newsletter-checklist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
