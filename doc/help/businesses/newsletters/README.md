---
title: Newsletters for businesses
audience: businesses, sales-marketing, editor, admin
category: business-solutions
order: 1
summary: The landing page of the newsletters section - what a business, shop or organization gets from the cjw_newsletter extension that ships with Exponential, who each page is for, and the order to read them in.
keywords: newsletter, email marketing, cjw_newsletter, mailing list, subscribers, double opt-in, editions, bounces, business, landing page
type: help
---
# Newsletters for businesses

This section is for business owners, marketing managers and the people who run a newsletter for a store, a library, a
non-profit or a small group. It explains, in plain language, what the newsletter system that comes with Exponential
does, what to decide before the first send, and how to keep a list healthy and lawful. The technical pages for whoever
sets it up are linked from every page, so nothing here repeats the configuration in detail.

## Contents

1. [What you get](#what-you-get)
2. [Who this section is for](#who-this-section-is-for)
3. [The pages of this section](#the-pages-of-this-section)
4. [Suggested reading order](#suggested-reading-order)
5. [What this section does not cover](#what-this-section-does-not-cover)
6. [Neighbouring sections](#neighbouring-sections)

## What you get

Exponential installs the `cjw_newsletter` extension with the product: it is in the `require` block of the
installation's `composer.json` (`se7enxweb/cjw_newsletter`), so there is no separate product to buy or license. The
extension describes itself as "a complete newsletter system for Exponential"
([cjw_newsletter](../../../features/6.0/extensions/cjw_newsletter.md)):

- you write newsletter editions as content, with drafts and preview like any other page;
- visitors subscribe to lists with a double opt-in;
- editions are sent by cron in the background;
- bounces and a blacklist keep the lists clean;
- every send is archived.

Release 4.2.0 (5 October 2026) added deliverability features (suppression list, retries, rate limits, test groups),
editorial features (recurring sends, article pools, approval of editions), consent-based statistics, SMS and a CSV
import with a column mapping. It works through the e-mail preferences of Exponential 6.0.15 instead of keeping a
consent system of its own ([cjw_newsletter, The features of 4.2.0](../../../features/6.0/extensions/cjw_newsletter.md#the-features-of-420)).

> **Note**
> The newsletter is opened at **Newsletter** in the top menu of the administration (`/newsletter/index`). Since
> release 4.1.17 that start page is a dashboard with the lists, editions, last sends, the transport and a list of
> problems to fix ([cjw_newsletter](../../../features/6.0/extensions/cjw_newsletter.md#dashboard-commands-and-background-runs-4117)).

## Who this section is for

| You are | Start with |
|---|---|
| An owner or manager deciding whether to run a newsletter on the site | [Planning a newsletter](newsletter-planning.md) |
| The person who will write and send editions | [Writing and sending editions](writing-and-sending-editions.md) |
| Responsible for data protection or consent | [Opt-in and consent](opt-in-and-consent.md) |
| The administrator or hosting partner | [Newsletter mail delivery](newsletter-mail-delivery.md), then the technical pages linked there |

## The pages of this section

| Page | What it answers |
|---|---|
| [Planning a newsletter](newsletter-planning.md) | Goals, audience, frequency, roles and the decisions to take before any setup |
| [cjw_newsletter for businesses](cjw-newsletter-for-businesses.md) | What the extension does, in business terms, and what it does not do |
| [Lists and subscriptions](lists-and-subscriptions.md) | How systems, lists and subscribers fit together, statuses, import and export |
| [Opt-in and consent](opt-in-and-consent.md) | Double opt-in, the consent log, unsubscribe links, statistics only with consent |
| [Writing and sending editions](writing-and-sending-editions.md) | Editions as content, test groups, approval, recurring sends, aborting a send |
| [Bounces and list hygiene](bounces-and-list-hygiene.md) | Hard and soft bounces, the blacklist and suppression list, keeping a list clean |
| [Newsletter mail delivery](newsletter-mail-delivery.md) | Transports, rehearsing without real mail, rate limits, sender domain records |
| [Newsletter checklist](newsletter-checklist.md) | One checklist from planning to the first real send and the monthly routine |

## Suggested reading order

1. [Planning a newsletter](newsletter-planning.md): decide what you want before anyone configures anything.
2. [cjw_newsletter for businesses](cjw-newsletter-for-businesses.md): learn what the tool can and cannot do.
3. [Opt-in and consent](opt-in-and-consent.md): the rules every list must follow.
4. [Lists and subscriptions](lists-and-subscriptions.md): structure your audience.
5. [Newsletter mail delivery](newsletter-mail-delivery.md): rehearse without sending, then go live deliberately.
6. [Writing and sending editions](writing-and-sending-editions.md): the routine of each issue.
7. [Bounces and list hygiene](bounces-and-list-hygiene.md): keep the list healthy over time.
8. [Newsletter checklist](newsletter-checklist.md): tick it off before the first send.

## What this section does not cover

- The step-by-step configuration of classes, cronjobs and transports: that is in
  [Newsletters with cjw_newsletter](../../creators/website-creators/newsletters-with-cjw-newsletter.md),
  [Newsletter lists and subscriptions](../../creators/website-creators/newsletter-lists-and-subscriptions.md) and
  [Sending newsletters safely](../../creators/website-creators/sending-newsletters.md).
- The site's other e-mail (notifications, account mail): see
  [E-mail preferences: the user's guide](../../../features/6.0/mail-preferences.md) and
  [the administrator's guide](../../../guides/mail-preferences-administrator.md).
- Legal advice. The consent pages describe what the software records; what your law requires is a question for your
  lawyer or data protection officer.

## Neighbouring sections

- Members: member sites often send a members' newsletter.
- [Stores](../stores/README.md): promoting a store, including by newsletter.
- Security and data protection: GDPR basics and consent for forms.
- Non-profits and Associations and small groups: newsletters
  for supporters and members.
- Government organizations have their own section: Governments: start here.

## Related pages

- Businesses: start here
- [Newsletters with cjw_newsletter](../../creators/website-creators/newsletters-with-cjw-newsletter.md)
- [cjw_newsletter feature page](../../../features/6.0/extensions/cjw_newsletter.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
