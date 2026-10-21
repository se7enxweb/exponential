---
title: What a small business website needs
audience: local-business, businesses, customer
category: business-solutions
order: 11
summary: The five jobs a local business website does - be found, answer the basic questions, build trust, take contact, and sometimes sell - with the Exponential feature or outside service behind each and what you can leave out at the start.
keywords: small business website, local business, requirements, be found, opening hours, contact, trust, sell online, minimum website
type: help
---
# What a small business website needs

This page is for the owner of a small business deciding what the website must do. Most local sites do five jobs. Get
the first three right before you spend on the last two.

## Contents

1. [The five jobs](#the-five-jobs)
2. [Job 1: be found](#job-1-be-found)
3. [Job 2: answer the basic questions](#job-2-answer-the-basic-questions)
4. [Job 3: build trust](#job-3-build-trust)
5. [Job 4: take contact](#job-4-take-contact)
6. [Job 5: sell or book](#job-5-sell-or-book)
7. [What you can leave out at the start](#what-you-can-leave-out-at-the-start)
8. [Checklist](#checklist)

## The five jobs

| Job | Question a visitor has | Built in or extension | Outside service |
|---|---|---|---|
| Be found | "Is there a bakery near me?" | URL aliases, `xrowmetadata`, `bcgooglesitemaps`, `robots.txt` | a business listing on map and search platforms |
| Answer the basics | "Are they open now? Where are they?" | ordinary pages, `ezgmaplocation`, a map block | map tiles (Google with a key, or OpenStreetMap) |
| Build trust | "Are they any good?" | photos, team page, star ratings (`ezstarrating`) | reviews on outside platforms |
| Take contact | "Can I ask a question?" | information collector forms, `recaptcha` or `hcaptcha` | the CAPTCHA provider |
| Sell or book | "Can I order or book online?" | the built-in shop, `ezpaypal` | payment provider; a booking service for appointments |

## Job 1: be found

Most new customers of a local business find it through a search on a phone. Two things decide whether they find you:

- **Your listing on map and search platforms.** This is set up on those platforms, not on your website. See
  [Business listings outside Exponential](../getting-found/business-listings-outside-exponential.md).
- **Your own website.** It should have a clear title per page, your town and trade in the text, readable addresses
  and a sitemap. Exponential makes readable addresses from page names by itself
  ([URL aliases and nice URLs](../../creators/website-creators/url-aliases-and-nice-urls.md)); the rest is in
  [Local SEO basics](../getting-found/local-seo-basics.md).

## Job 2: answer the basic questions

Visitors want five facts within seconds: what you do, where you are, when you are open, how to get in touch, and what
it costs. Put them on the front page or one click away:

- **Address and map**: [Showing your location on a map](../maps-and-hours/showing-your-location-on-a-map.md).
- **Opening hours**: Exponential has no opening-hours datatype; the
  [opening hours page](../maps-and-hours/opening-hours-without-a-special-datatype.md) shows three ways to model them.
- **Phone and e-mail**: as text, with the phone number as a link that phones can dial.
- **Prices or a menu**: a page, a table, or products in a [small catalog](../simple-shop/a-small-catalog.md).

## Job 3: build trust

Real photos of your premises and your work, the names (or first names) of the team if they agree, how long you have
been in business, certificates and memberships. Exponential stores images and makes sized variations automatically
([Photos that sell](../marketing/photos-that-sell.md)). Star ratings on pages are possible with `ezstarrating`, but
written reviews are not built in ([Ratings and reviews](../contact-and-forms/ratings-and-reviews.md)).

## Job 4: take contact

A contact form built with the information collector stores every message and mails it to you
([A contact form for your business](../contact-and-forms/a-contact-form-for-your-business.md)). Protect it from spam
([Stopping spam](../contact-and-forms/stopping-spam.md)). A phone number and an e-mail address on every page are still
the quickest way for many people.

## Job 5: sell or book

- **Selling goods, vouchers or gift cards**: the shop built into the kernel does it, with PayPal through `ezpaypal`
  ([A simple shop](../simple-shop/README.md)).
- **Appointments, tables, courses with seats**: no booking module was found in the repository. Most small businesses
  link to a booking service ([What needs an outside service](../budget/what-needs-an-outside-service.md)).

## What you can leave out at the start

| Leave out | Add later when |
|---|---|
| A shop | you have products people ask to buy from a distance |
| A newsletter | you have a list of customers who asked to hear from you |
| A second language | visitors from abroad ask questions you could answer on the site |
| A blog or news section | you can write something every month |

Exponential lets you add each later without rebuilding the site: a shop is a few content classes and settings, a
language is a siteaccess and translations ([Adding a language](../../customers/growth/adding-a-language.md)).

## Checklist

- [ ] The five facts (what, where, when, contact, price) are decided.
- [ ] You know which of the five jobs your first version does.
- [ ] Outside services you need are on a list with an owner.
- [ ] The decisions are written into the [website brief](../templates/website-brief-for-a-local-business.md).

## Related pages

- [Planning your pages](planning-your-pages.md)
- [What you can build](../../creators/website-creators/what-you-can-build.md)
- [Online stores and small shops](../../businesses/stores/README.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
