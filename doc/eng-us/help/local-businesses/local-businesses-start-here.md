---
title: "Local businesses: start here"
audience: local-business, businesses, customer
category: local-business
order: 1
summary: The landing page for small and single-location businesses - shops, restaurants, cafes, trades, salons and practices - that want a website people find, trust and use, with a realistic cost picture, local search, maps, opening hours, a simple shop, contact forms, newsletters and a plain list of what needs an outside service.
keywords: local business, small business, single location, shop, restaurant, cafe, trades, salon, practice, local SEO, map, opening hours, contact form, simple shop, newsletter, budget, partner, freelancer, start here
featured: true
type: help
---
# Local businesses: start here

This section is for small businesses with one address, or a handful of them: a shop on the high street, a restaurant
or cafe, a plumber or electrician, a hair salon, a dental or physiotherapy practice, a bakery, a bike repair shop. You
want a website that people nearby find when they search, that tells them when you are open and how to reach you, that
lets them send a message or buy something, and that does not eat your evenings.

The pages are written for the owner or the one person who looks after the website. They say in plain language what
Exponential does, what it does not do, what you can do yourself and when it is worth paying someone. Each page links
to the technical page for whoever does the setup.

> **Note**
> Every feature described here was checked in the Exponential repository (`composer.json`, `extension/`, `settings/`)
> or in the existing documentation, and the source is named or linked. No prices of real providers, no statistics
> and no customer names appear in this section. Where a page uses numbers, they are labeled **example assumptions**
> for you to replace with your own.

## Contents

1. [What you get, in one minute](#what-you-get-in-one-minute)
2. [What you do outside Exponential](#what-you-do-outside-exponential)
3. [The sections of this guide](#the-sections-of-this-guide)
4. [Your first two weeks](#your-first-two-weeks)
5. [If you are not sure this is for you](#if-you-are-not-sure-this-is-for-you)
6. [Neighboring sections](#neighboring-sections)

## What you get, in one minute

| You want | What Exponential has | Read |
|---|---|---|
| Pages you can edit from a browser | the administration and the editor, no programming for everyday changes | [Editing content](../editing-content.md) |
| To be found in local search | readable addresses, per-page titles and descriptions (`xrowmetadata`), XML sitemaps (`bcgooglesitemaps`), a shipped `robots.txt` | [Getting found](getting-found/README.md) |
| A map with your address | the `ezgmaplocation` datatype, and a map block in the layouts editor | [Maps and hours](maps-and-hours/README.md) |
| Opening hours on every page | no special datatype; a small content class or a text block does it | [Opening hours without a special datatype](maps-and-hours/opening-hours-without-a-special-datatype.md) |
| A contact form that does not fill with spam | the information collector, form tokens, reCAPTCHA or hCaptcha | [Contact and forms](contact-and-forms/README.md) |
| To sell a few products, vouchers or gift cards | the shop built into the kernel, PayPal through `ezpaypal` | [A simple shop](simple-shop/README.md) |
| A newsletter for regulars | `cjw_newsletter` | [Newsletters for local businesses](marketing/newsletters-for-local-businesses.md) |
| Star ratings on pages | `ezstarrating` (stars only, not written reviews) | [Ratings and reviews](contact-and-forms/ratings-and-reviews.md) |
| A site in two or three languages for visitors | multilingual content and language addresses | [Multilingual for tourist areas](marketing/multilingual-for-tourist-areas.md) |
| No license fee | free software under the GNU GPL, version 2 or later | [Licensing for buyers](../business/licensing-for-buyers.md) |

All of the extensions named above are listed in the `require` block of the project's `composer.json`, so they are
part of the standard distribution. Each still has to be switched on and set up for your site.

## What you do outside Exponential

Some things a local business needs are not website features at all, or need a service of their own. The full list is
in [What needs an outside service](budget/what-needs-an-outside-service.md). The short version:

- **Your listing on map and search platforms** (a business profile with photos, hours and reviews) is created and
  kept up to date on those platforms, not in Exponential.
- **Appointment booking and table reservations**: no booking module was found in the repository. Link to a booking
  service, or have a partner build one.
- **Online ordering for a restaurant** (delivery slots, kitchen tickets, table ordering): not built in.
- **A point-of-sale (POS) connection** between the till and the website: not built in.
- **Map tiles and CAPTCHA checks** come from outside providers; some need an API key.
- **Payment processing** is done by a payment provider such as PayPal; Exponential connects to it.

## The sections of this guide

| Section | What it covers |
|---|---|
| [Getting started](getting-started/README.md) | what a small business site needs, planning the pages, doing it yourself or hiring help, hosting |
| [Getting found](getting-found/README.md) | local search basics, titles and descriptions, sitemaps, addresses, structured data, outside listings, visitor numbers |
| [Maps and hours](maps-and-hours/README.md) | a map of your location, opening hours, holiday hours, several locations |
| [A simple shop](simple-shop/README.md) | whether you need a shop, a small catalog, payments, VAT and pickup, restaurants |
| [Contact and forms](contact-and-forms/README.md) | a contact form, spam, quote requests, ratings, messages and privacy |
| [Marketing](marketing/README.md) | events and specials, newsletters, photos, visitors from abroad, phones |
| [Budget](budget/README.md) | the cost picture, a worked example, a worksheet, outside services |
| [Running it](running-it/README.md) | the weekly routine, seasonal changes, backups and updates |
| [Checklists](checklists/README.md) | launch checklist, yearly review |
| [Templates](templates/README.md) | a website brief, an opening hours template |

Short answers are in the [FAQ](local-businesses-faq.md), the words in the [glossary](local-businesses-glossary.md)
and a suggested order of reading in the [reading path](local-businesses-reading-path.md).

## Your first two weeks

| Days | Do | Read |
|---|---|---|
| 1-2 | Write down what the site must do: be found, show hours, take messages, sell? | [What a small business website needs](getting-started/what-a-small-business-website-needs.md) |
| 3 | Fill in the brief | [Website brief for a local business](templates/website-brief-for-a-local-business.md) |
| 4 | Decide who builds and who runs it | [Doing it yourself or hiring help](getting-started/diy-or-hire-help.md) |
| 5 | Put numbers on it, as your own assumptions | [Budget worksheet](budget/budget-worksheet.md) |
| 6-7 | Choose hosting | [Choosing hosting for a small business](getting-started/choosing-hosting-for-a-small-business.md) |
| 8-10 | Plan your pages and your opening hours | [Planning your pages](getting-started/planning-your-pages.md), [Opening hours template](templates/opening-hours-content-template.md) |
| 11-14 | Prepare launch and local search | [Launch checklist](checklists/launch-checklist.md), [Local SEO basics](getting-found/local-seo-basics.md) |

## If you are not sure this is for you

Exponential is a full content management system, not a page-builder subscription. That has advantages for a small
business (no license fee, your content stays yours, the site can grow into a shop or a second language) and a cost:
someone has to install it, host it and keep it updated. If nobody in your business wants that job, plan to pay a host
or a partner for it from the start. [Doing it yourself or hiring help](getting-started/diy-or-hire-help.md) walks
through the choice honestly.

## Neighboring sections

| Section | For |
|---|---|
| [Businesses: start here](../businesses/businesses-start-here.md) | larger organizations; stores, intranets, member sites |
| [Online stores and small shops](../businesses/stores/README.md) | the full guide to the built-in shop |
| [Customers: start here](../customers/customers-start-here.md) | owners of a running Exponential site |
| [Store owner's section](../customers/commerce/README.md) | daily and weekly routines for a running shop |
| [Website creators](../creators/website-creators/website-creators-start-here.md) | whoever builds the site |
| [Non-profits](../non-profits/non-profits-start-here.md) | charities, clubs and community groups |
| [Individuals](../individuals/individuals-start-here.md) | one person with a site of their own |
| [Non-technical people](../non-technical-people/non-technical-people-start-here.md) | anyone who would rather not read technical pages |

## Related pages

- [Local businesses FAQ](local-businesses-faq.md)
- [Local businesses reading path](local-businesses-reading-path.md)
- [Local businesses glossary](local-businesses-glossary.md)
- [Businesses: start here](../businesses/businesses-start-here.md)
- [Customers: start here](../customers/customers-start-here.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
