---
title: How businesses sell online with Exponential
audience: businesses, sales-marketing, project-manager
category: business-solutions
order: 30
summary: The three channels through which an organization earns money with Exponential, website applications, mobile apps and networks of sites and partners, what the software provides for each, and how they work together on one content repository.
keywords: sell online, channels, e-commerce, website application, mobile app, headless, REST API, feeds, RSS, ATOM, OPML, syndication, partner network, marketplace, newsletter, multisite, payments, PayPal, omnichannel
type: help
---
# How businesses sell online with Exponential

This page is for owners and marketing managers who want to know how Exponential turns content into revenue. It
describes three channels, website applications, mobile apps and networks, says what the software provides for each
with its source, and shows how one content repository serves all three.

## Contents

1. [One repository, three channels](#one-repository-three-channels)
2. [Channel 1: website applications](#channel-1-website-applications)
3. [Channel 2: mobile apps](#channel-2-mobile-apps)
4. [Channel 3: networks of sites and partners](#channel-3-networks-of-sites-and-partners)
5. [What drives visitors to each channel](#what-drives-visitors-to-each-channel)
6. [Why availability is part of the sale](#why-availability-is-part-of-the-sale)
7. [A planning table](#a-planning-table)

## One repository, three channels

Exponential separates content from presentation ("Separation of content and presentation layer", `README.md`,
"Main Exponential features"). A product, an article or a member record is stored once, as content of a class you
define, and each channel presents it in its own way:

| Channel | Presents the content as | Built with |
|---|---|---|
| Website applications | HTML pages, a store, signed-in areas | Templates, designs, Exponential Layouts |
| Mobile apps | JSON for an app | The REST API |
| Networks | Feeds and synchronized copies on other sites | RSS, ATOM, iTunes and OPML feeds; the `syndication` extension; multisite |

Editors work in one place. A price or a description changed once is changed in every channel, which removes a whole
class of errors (a product sold on the site at a price the app no longer shows).

## Channel 1: website applications

A website application is more than a brochure: customers act on it. Exponential ships the parts most businesses need:

| Need | Provided by | Read |
|---|---|---|
| Sell products | "Advanced e-Commerce Stores (Built in)": products, basket, orders, VAT, currencies | [Online stores](stores/README.md) |
| Take payments | Payment gateways through the workflow system, for example PayPal with `ezpaypal` | [Payment gateways for businesses](stores/payment-gateways-for-businesses.md) |
| Collect leads and requests | "Contact forms" and the information collector | [Forms with the information collector](../creators/website-creators/forms-with-the-information-collector.md) |
| Sell access | Registration and members-only areas through roles and sections | [Member sites](members/README.md) |
| Serve partners and dealers | Extranets on the same installation | [Extranets and partner portals](extranets/README.md) |
| Build landing pages fast | Exponential Layouts, a visual page builder with rules that map layouts to content | [The layouts editor](../layouts-editor.md) |

Sources: `README.md`, "Main Exponential features", "Exponential Layouts".

## Channel 2: mobile apps

Exponential has "A headless CRUD REST API" (`README.md`). Its reads answer at `/api/ezp/v1/...` and
`/api/ezp/v2/...`, writes at v2, and "Every read and write checks the user's policies"; clients authenticate with
OAuth2 or a personal API key ([REST API v1 and v2](../../guides/rest-api-v1-and-v2.md)).

For a business that means:

- an app shows the same products, articles and events as the website, edited in the same place;
- the app only sees what the account it uses is allowed to see, because the same roles and policies apply;
- the app does not need its own content back end, which is one system less to host and secure.

A mobile-friendly website is often the first step and needs no app at all. The options are compared in
[Mobile strategy](mobile-and-headless/mobile-strategy.md); the technical side is in
[Headless with the REST API](mobile-and-headless/headless-with-the-rest-api.md) and
[Headless Exponential](../creators/app-creators/headless-exponential.md).

## Channel 3: networks of sites and partners

Many organizations earn through other people's sites and through several sites of their own:

| Network | Provided by | Read |
|---|---|---|
| Feed readers, podcast apps, aggregators | "RSS feeds", "ATOM feeds", "iTunes RSS feeds", "OPML feeds" | [RSS feeds](../../guides/rss-feeds.md), [Feeds and syndication](mobile-and-headless/feeds-and-syndication.md) |
| Your own several brands or countries | "Multi-site support" from one installation | [Multi-site brands](multisite/README.md) |
| Partner and dealer sites that carry your content | The `syndication` extension exports part of the content tree as a feed that another installation imports, one-way or two-way, including "large e-commerce networks" | [syndication](../../features/6.0/extensions/syndication.md) |
| Other systems and services | Remote services and the REST API | [Remote services](mobile-and-headless/remote-services.md) |

How to plan these is in [Selling through networks and partners](mobile-and-headless/selling-through-networks-and-partners.md).

## What drives visitors to each channel

The software provides the tools; the strategy is yours. The tools that exist in the repository:

- **Newsletters** with `cjw_newsletter`: lists, subscriptions and editions ([Newsletters](newsletters/README.md)).
- **Search engines**: readable URL aliases, sitemaps, `robots.txt` and metadata
  ([SEO basics](../creators/website-creators/seo-basics.md), [Sitemaps](../creators/website-creators/sitemaps.md)).
- **Many languages**: content in the languages of your customers ([Multilingual businesses](multilingual/README.md)).
- **Feeds** that other sites and apps pick up (above).

Marketing pages for people who sell Exponential itself are a different subject; they are in
[Sales and marketing](../business/sales-and-marketing-start-here.md).

## Why availability is part of the sale

Every channel above ends at the same installation. If it is down or slow, the website, the app and the partner sites
fail together. That is why the [Reliability](reliability/README.md) section matters to a sales manager as much as to
the person who runs the server, and why a business should know its
[cost of downtime](costs/cost-of-downtime-worksheet.md) before it chooses hosting.

## A planning table

Copy this and fill it in for your organization.

```text
CHANNEL PLAN

Channel                 | Now? | Revenue or saving it brings | Content it needs   | Owner
------------------------|------|-----------------------------|--------------------|---------
Website store           |      |                             |                    |
Signed-in customer area |      |                             |                    |
Partner or dealer area  |      |                             |                    |
Mobile app (REST)       |      |                             |                    |
Feeds (RSS, ATOM)       |      |                             |                    |
Partner sites (syndic.) |      |                             |                    |
Newsletter              |      |                             |                    |
Other brands / countries|      |                             |                    |

Cost of one hour unavailable (all channels): <from the cost of downtime worksheet>
```

## Related pages

- [Businesses: start here](businesses-start-here.md)
- [Online stores](stores/README.md)
- [Mobile and headless](mobile-and-headless/README.md)
- [Feature highlights mapped to customer needs](../business/feature-highlights-for-customers.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
