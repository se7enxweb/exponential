---
title: cjw_newsletter for businesses
audience: businesses, sales-marketing, project-manager
category: business-solutions
order: 20
summary: What the cjw_newsletter extension shipped with Exponential does in business terms - its parts, its history, what 4.2.0 added, what it costs, what it does not do, and where the details are.
keywords: cjw_newsletter, newsletter software, email marketing, newsletter features, deliverability, editorial, statistics, SMS newsletter, CSV import, business
type: help
---
# cjw_newsletter for businesses

This page is for decision makers who want to know whether the newsletter system that comes with Exponential is
enough for their organization. It describes the extension's parts and features in business terms, says plainly what
it does not do, and points to the technical documentation for every claim.

## Contents

1. [In short](#in-short)
2. [Where it comes from](#where-it-comes-from)
3. [The parts in business terms](#the-parts-in-business-terms)
4. [What 4.2.0 added](#what-420-added)
5. [What it costs](#what-it-costs)
6. [What it does not do](#what-it-does-not-do)
7. [Questions to ask before you decide](#questions-to-ask-before-you-decide)

## In short

- It is part of the installation: `se7enxweb/cjw_newsletter` is required by Exponential's `composer.json`.
- Editions are content in the content tree, so editors use the tools they already know.
- Subscribers confirm by mail (double opt-in); every optional mail carries an unsubscribe link through the e-mail
  preferences of the site ([E-mail preferences](../../../features/6.0/mail-preferences.md)).
- Sending runs in the background through cron, with an archive of every send.
- Everything can be rehearsed without sending a single mail, by writing mail to files.

Source: [cjw_newsletter](../../../features/6.0/extensions/cjw_newsletter.md).

## Where it comes from

The extension was written by the CJW Network, coolscreen.de, JAC Systeme and Webmanufaktur between 2007 and 2015. The
se7enxweb project took it over for PHP 8.5 and Exponential 6 in August 2026 (4.0.0.0 on 13 August). Releases 4.1.0 to
4.1.17 made it run on PHP 8, on persistent workers (Velocity) and on every database, and closed defects found by a
systematic review; 4.2.0 followed on 5 October 2026
([cjw_newsletter](../../../features/6.0/extensions/cjw_newsletter.md)). For a business this means a long-lived tool
with a maintained current line, not a new experiment.

## The parts in business terms

| Business need | Part of the extension | Where it is |
|---|---|---|
| Several newsletters for different audiences | Newsletter systems and lists (content classes `cjw_newsletter_system`, `cjw_newsletter_list`) | Content tree below `RootFolderNodeId` |
| An issue with its articles | Editions (`cjw_newsletter_edition`, articles `cjw_newsletter_article`) | Below a list |
| People can sign up and leave on their own | Public views `newsletter/subscribe`, `configure`, `unsubscribe` | Pages of your site |
| Knowing who receives what | Subscription and user lists with a status per subscription | Newsletter menu of the administration |
| Not mailing addresses that bounce or object | Bounce mailboxes, blacklist, the site's suppression list | Newsletter menu, Setup > E-mail preferences |
| Moving subscribers in and out | CSV import and export | Newsletter menu |
| Sending without blocking the website | Cron parts `cjw_newsletter_mailqueue_create` and `cjw_newsletter_mailqueue_process` | Server cron |
| An overview for the person in charge | Dashboard at `/newsletter/index` (4.1.17) with problems and what to do about each | Newsletter menu |

Sources: [cjw_newsletter, The parts](../../../features/6.0/extensions/cjw_newsletter.md#the-parts) and
[Newsletters with cjw_newsletter](../../creators/website-creators/newsletters-with-cjw-newsletter.md#the-parts).

## What 4.2.0 added

The extension's own guides describe each area (folder `doc/` of the extension, indexed by its `doc/README.md`):

| Area | What it adds for a business |
|---|---|
| Deliverability | Hard bounces and complaints go on the site's suppression list; soft bounces are retried; rate limits and batches; test groups; subscribe and unsubscribe by e-mail; suppression import |
| Editorial | Recurring sends, article pools and an article picker, approval of editions in the collaboration inbox |
| Rendering | Placeholders, parts only some subscribers get, interests, one edition in several languages, three more skins, plain text views, preview as a subscriber |
| Statistics | Opens and clicks per person only with consent, anonymous totals otherwise, reports, A/B subject tests |
| SMS | Newsletters by SMS through a provider-neutral transport, a code double opt-in, STOP |
| Import, export, migration | CSV import with a column mapping, subscriber export, migration from the old eznewsletter extension |

Source: [cjw_newsletter, The features of 4.2.0](../../../features/6.0/extensions/cjw_newsletter.md#the-features-of-420).
Tracking and SMS are off until they are switched on in `settings/override/cjw_newsletter.ini.append.php` (same page).

## What it costs

There is no license fee: "Using Exponential under the terms of the GNU GPL is free of charge" (README, "License"),
and the extension's own license is the GNU GPL v2 or later (`doc/README.md` of the extension). What you pay for is
work and services you choose to buy, plus the mail service that delivers the messages
([Licensing for buyers](../../business/licensing-for-buyers.md#no-license-fees-and-what-buyers-do-pay-for)). For a
cost model with your own figures, see Total cost of ownership.

## What it does not do

Be clear about these points before you promise anything to a marketing team:

- **It does not deliver mail by itself.** It hands mail to `sendmail` or an SMTP server. Whether mail reaches inboxes
  depends on that server, your sender domain records (SPF, DKIM) and your content
  ([Sending newsletters safely](../../creators/website-creators/sending-newsletters.md)).
- **It is not a hosted mailing service.** You or your hosting partner run cron, the bounce mailbox and the mail
  transport ([Newsletter mail delivery](newsletter-mail-delivery.md)).
- **It does not count opens and clicks by default.** Tracking is switched off in the shipped settings, and
  per-person numbers need each person's consent
  ([statistics.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/statistics.md)).
- **It does not decide what is lawful.** It records consent and offers unsubscribe; whether your list and wording
  meet your law is for your lawyer or data protection officer.
- **SMS needs a provider.** The extension ships a file transport for tests, a generic HTTP/JSON transport and a
  preset for Twilio-style APIs; you need an account with a provider to send real messages
  ([sms.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/sms.md)).

## Questions to ask before you decide

| Question | Where the answer is |
|---|---|
| Can we send from our own domain and mail provider? | [Newsletter mail delivery](newsletter-mail-delivery.md) |
| Can we bring our existing subscribers? | [Lists and subscriptions](lists-and-subscriptions.md#importing-and-exporting) |
| Can a manager approve each edition? | [Writing and sending editions](writing-and-sending-editions.md#approval-before-sending) |
| How do we prove consent? | [Opt-in and consent](opt-in-and-consent.md) |
| What happens with addresses that bounce? | [Bounces and list hygiene](bounces-and-list-hygiene.md) |
| Can we test everything without mailing customers? | [Newsletter mail delivery](newsletter-mail-delivery.md#rehearsal-first) |

## Related pages

- [Newsletters for businesses](README.md)
- [Planning a newsletter](newsletter-planning.md)
- [cjw_newsletter feature page](../../../features/6.0/extensions/cjw_newsletter.md)
- [Feature highlights for customers](../../business/feature-highlights-for-customers.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
