---
title: Club newsletters
audience: individual, website-creator, customer
category: website-creation
order: 1713
summary: How a volunteer sends a club newsletter with the cjw_newsletter extension that ships with Exponential - lists, double opt-in subscriptions, writing an edition, test sends, sending by cron, bounces and unsubscribes, what the server must provide, and what the extension does not do.
keywords: newsletter, club newsletter, cjw_newsletter, mailing list, subscribe, double opt-in, unsubscribe, edition, test send, cron, mail delivery, SMTP, bounces, blacklist, archive, consent
type: help
---
# Club newsletters

This page is for individuals who send a newsletter for a club or hobby group. `cjw_newsletter` ships with Exponential
(`se7enxweb/cjw_newsletter` in the root `composer.json`) and is a complete newsletter system: lists, subscriptions
with double opt-in, editions written as content, sending by cron, bounces and an archive
([cjw_newsletter: newsletters in the admin](../../../features/6.0/extensions/cjw_newsletter.md)).

## Contents

1. [Before you start: mail and cron](#before-you-start-mail-and-cron)
2. [The parts](#the-parts)
3. [Setting up a list](#setting-up-a-list)
4. [Getting subscribers](#getting-subscribers)
5. [Writing and sending an edition](#writing-and-sending-an-edition)
6. [After sending](#after-sending)
7. [What it does not do](#what-it-does-not-do)
8. [Checklist](#checklist)

## Before you start: mail and cron

Two things outside the extension decide whether your newsletter arrives:

| Need | Why | Ask |
|---|---|---|
| **A cron job** | Editions are queued and sent by the cronjobs `cjw_newsletter_mailqueue_create` and `cjw_newsletter_mailqueue_process` | "Can I run `runcronjobs.php` every few minutes?" ([Cronjobs](../../../guides/cronjobs.md)) |
| **Mail delivery** | The extension hands mail to `sendmail` or an SMTP server; it does not deliver by itself | "How many mails may I send per hour, and is my domain's sender set up (SPF, DKIM)?" |

See [Newsletter mail delivery](../../businesses/newsletters/newsletter-mail-delivery.md). Without both, set up
nothing else yet.

## The parts

| Part | What it is |
|---|---|
| Newsletter system | The container in the content tree |
| List | What people subscribe to ("Club news", "Match results") |
| Edition | One issue, written like an article |
| Subscriptions | Who is on which list, with a status (pending, confirmed, removed, bounced, blacklisted) |
| Public forms | Subscribe, confirm, change, unsubscribe |
| Archive | Every edition sent |

Open it at **Newsletter** in the top menu of the administration (`/newsletter/index`).

## Setting up a list

1. Create a newsletter system and one list ("Club news") in the newsletter part of the tree.
2. Set the sender name and address: the club's address on the club's domain, not a personal free-mail address.
3. Choose the output format and the design of the mail.
4. Link the subscribe page from the site's footer and the "Join" page.

Builder detail: [Newsletters with cjw_newsletter](../../creators/website-creators/newsletters-with-cjw-newsletter.md),
[Newsletter lists and subscriptions](../../creators/website-creators/newsletter-lists-and-subscriptions.md).

## Getting subscribers

- **Double opt-in**: a subscriber confirms by a link in a mail. Keep it; it proves consent and keeps typos out.
- **Never import a list of addresses** of people who did not ask, even members. Invite them to subscribe instead.
- If the club already has a list with documented consent, the extension can import it; check what consent it covers
  ([Opt-in and consent](../../businesses/newsletters/opt-in-and-consent.md)).

## Writing and sending an edition

1. Create an edition below the list. Write it like a page: a clear subject, short items, links to the site for the
   long versions.
2. **Preview** it.
3. **Send a test** to yourself and one other volunteer; open it on a phone.
4. **Schedule the send.** The cronjobs queue and send it in the background; large lists take a while.
5. Watch the send status in the administration.

Writing tips: [Writing and sending editions](../../businesses/newsletters/writing-and-sending-editions.md),
[Sending newsletters](../../creators/website-creators/sending-newsletters.md).

## After sending

- **Bounces**: addresses that bounce repeatedly stop receiving (after `BounceThresholdValue` bounces, default 3) when
  a bounce mailbox is configured ([Bounces and list hygiene](../../businesses/newsletters/bounces-and-list-hygiene.md)).
- **Unsubscribes** are handled by the public forms; never re-add someone who left.
- **Archive**: past editions remain in the archive; link it from the members' area if you like.

## What it does not do

From [cjw_newsletter for businesses](../../businesses/newsletters/cjw-newsletter-for-businesses.md#what-it-does-not-do):

- It does not deliver mail by itself.
- It does not count opens and clicks by default.
- It does not decide what is lawful for your list.

It also does not mail new blog posts automatically; each edition is written and sent by a person.

## Checklist

- [ ] Cron runs; mail leaves the server and arrives (tested to two different mail providers).
- [ ] Sender address on the club's domain.
- [ ] One list, double opt-in, subscribe link on the site.
- [ ] Test sends checked on a phone.
- [ ] Bounce mailbox configured, or bounces checked by hand.
- [ ] A second volunteer knows how to send.

## Related pages

- [Club sites](README.md)
- [Newsletters (businesses)](../../businesses/newsletters/README.md)
- [Newsletter checklist](../../businesses/newsletters/newsletter-checklist.md)
- [Newsletter routine (customers)](../../customers/growth/newsletter-routine.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
