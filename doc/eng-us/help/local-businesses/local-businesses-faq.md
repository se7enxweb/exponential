---
title: Local businesses FAQ
audience: local-business, businesses, customer
category: local-business
order: 2
summary: Short answers to the questions owners of shops, restaurants, trades, salons and practices ask about a website on Exponential - cost, doing it yourself, maps, opening hours, local search, forms and spam, reviews, selling, booking, newsletters, languages and running it - each with a link to the full page.
keywords: FAQ, questions, local business, small business, cost, opening hours, map, local SEO, booking, reviews, shop, newsletter, languages, hosting
type: help
---
# Local businesses FAQ

This page is for owners and staff of local businesses who want a quick answer. Each answer links to the page with the
details.

## Contents

1. [Cost and choice](#cost-and-choice)
2. [Being found](#being-found)
3. [Maps and opening hours](#maps-and-opening-hours)
4. [Contact, forms and reviews](#contact-forms-and-reviews)
5. [Selling and booking](#selling-and-booking)
6. [Marketing](#marketing)
7. [Running the site](#running-the-site)

## Cost and choice

**Is there a license fee?**
No. Exponential is free software under the GNU GPL, version 2 or later. You pay for work, hosting and outside services
([Budget](budget/README.md)).

**What will it cost?**
It depends on the work and the services you choose. [The cost picture](budget/the-cost-picture.md) lists every line,
and the [worked example](budget/worked-example-a-local-business-website.md) shows the arithmetic with example
assumptions. Fill in the [budget worksheet](budget/budget-worksheet.md) with real quotes.

**Can I build it myself?**
Editing pages, yes, after a short introduction. Installing, setting up and updating needs someone technical
([Doing it yourself or hiring help](getting-started/diy-or-hire-help.md)).

**Where do I find someone to build it?**
A local freelancer or an Exponential partner ([Working with a local partner or freelancer](getting-started/working-with-a-local-partner-or-freelancer.md)).

## Being found

**Will my site be on the first page of search results?**
Nobody can promise that. Exponential gives you readable addresses, per-page titles and descriptions, sitemaps and a
`robots.txt`; the rest is good content and accurate listings ([Local SEO basics](getting-found/local-seo-basics.md)).

**Does Exponential update my profile on map and search platforms?**
No. That listing is managed on each platform ([Business listings outside Exponential](getting-found/business-listings-outside-exponential.md)).

**Does the site print LocalBusiness structured data?**
Not out of the box. Some themes print breadcrumbs, articles and recipes; a LocalBusiness block is template work
([Structured data: what exists](getting-found/structured-data-what-exists.md)).

## Maps and opening hours

**Can I show a map?**
Yes: the `ezgmaplocation` field (Google Maps, needs an API key) or the Map block of the layouts editor (Google with a
key, OpenStreetMap without) ([Showing your location on a map](maps-and-hours/showing-your-location-on-a-map.md)).

**Is there an opening-hours field?**
No. Hours are modeled with a text block, a small class with a matrix, or a table
([Opening hours without a special datatype](maps-and-hours/opening-hours-without-a-special-datatype.md)).

**Are holiday hours automatic?**
No. You edit them by hand; a notice can be scheduled if your administrator set up date-based publishing
([Holiday and seasonal hours](maps-and-hours/holiday-and-seasonal-hours.md)).

**We have two shops. One site or two?**
Usually one, with a page per location ([Several locations](maps-and-hours/several-locations.md)).

## Contact, forms and reviews

**Can customers send me messages?**
Yes, with forms built on the information collector; messages are stored and mailed to you
([A contact form for your business](contact-and-forms/a-contact-form-for-your-business.md)).

**How do I stop spam?**
Form tokens are on by default; add reCAPTCHA or hCaptcha, both need keys from the provider
([Stopping spam](contact-and-forms/stopping-spam.md)).

**Can customers leave reviews on my site?**
Star ratings with `ezstarrating` and comments, yes; a written review system, no. Most reviews live on outside
platforms ([Ratings and reviews](contact-and-forms/ratings-and-reviews.md)).

## Selling and booking

**Can I sell online?**
Yes, the shop is built into the kernel, with PayPal through `ezpaypal` ([A simple shop](simple-shop/README.md)).
First read [Do you need a shop?](simple-shop/do-you-need-a-shop.md).

**Can customers book appointments or tables?**
Not with anything built in. Link to a booking service, or use a request form and confirm by phone
([What needs an outside service](budget/what-needs-an-outside-service.md)).

**Can my restaurant take food orders?**
Pre-orders for collection, yes, through the shop; live ordering with time slots, no
([Restaurant orders and what is not built in](simple-shop/restaurant-orders-and-what-is-not-built-in.md)).

**Can I sell gift vouchers?**
Yes, as products; voucher codes at checkout are not built in ([A small catalog](simple-shop/a-small-catalog.md#vouchers-and-gift-cards)).

**Does it connect to my till?**
No POS integration is built in.

## Marketing

**Can I send a newsletter?**
Yes, `cjw_newsletter` ships with Exponential; your host must be able to send mail
([Newsletters for local businesses](marketing/newsletters-for-local-businesses.md)).

**Can the site be in English and German for tourists?**
Yes, with translations and an address per language ([Multilingual for tourist areas](marketing/multilingual-for-tourist-areas.md)).

**Will it work on phones?**
The default public design is responsive; test it yourself with [Mobile visitors](marketing/mobile-visitors.md).

## Running the site

**How much time does it take each week?**
About fifteen minutes for a small site without a shop ([Weekly and monthly routine](running-it/weekly-and-monthly-routine.md)).

**Who makes the backups and updates?**
Your host or partner; ask for proof once a year ([Backups, updates and security](running-it/backups-updates-and-security.md)).

**What if my partner stops working with me?**
Keep the domain, an administrator account and the outside service accounts in the business's name, so another
partner can take over ([Taking over a site](../customers/partners-and-hosts/taking-over-a-site.md)).

## Related pages

- [Local businesses: start here](local-businesses-start-here.md)
- [Local businesses glossary](local-businesses-glossary.md)
- [Businesses FAQ](../businesses/businesses-faq.md)
- [Customers FAQ](../customers/customers-faq.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
