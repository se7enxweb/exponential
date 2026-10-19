---
title: Mobile strategy
audience: businesses, project-manager, sales-marketing
category: business-solutions
order: 10
summary: How a business decides between a responsive website, its own app on the REST API, or both, with Exponential as the one content source - the options, what each costs in work and operations, editors on phones, and the questions to answer first.
keywords: mobile strategy, mobile app, responsive website, headless, hybrid, REST API, app, phones, editors on phones, decision, business
type: help
---
# Mobile strategy

This page is for owners and managers who are asked "do we need an app?". It sets out the options Exponential
supports, from a website that works well on phones to a native app that reads the same content through the API, and
helps you decide with questions about your customers rather than about technology. It contains no market figures;
measure your own visitors before you decide.

## Contents

1. [Start from what customers do](#start-from-what-customers-do)
2. [The three options](#the-three-options)
3. [Why one content source matters](#why-one-content-source-matters)
4. [What an app adds to your workload](#what-an-app-adds-to-your-workload)
5. [Editors on phones](#editors-on-phones)
6. [Questions to answer first](#questions-to-answer-first)

## Start from what customers do

Before choosing a channel, find out from your own data:

- [ ] Which share of visits and orders comes from phones (your analytics; Exponential's statistics options are on
      [Visitor statistics and analytics](../../creators/website-creators/statistics-and-analytics.md)).
- [ ] What customers do on a phone: look up opening hours, read news, reorder, check an order, browse a catalog.
- [ ] What they would do more often with an app: offline use, notifications, a loyalty card, scanning.
- [ ] Which partners or marketplaces they already use, where your content could appear instead
      ([Selling through networks and partners](selling-through-networks-and-partners.md)).

If customers mainly look things up and buy occasionally, a fast responsive site usually serves them. If they return
daily and need features of the phone, an app may be worth its cost.

## The three options

| Option | How it works with Exponential | Good for |
|---|---|---|
| **Responsive website** | The site's design adapts to the screen; the default public design is described as responsive | Most businesses; the first step in any case |
| **Hybrid**: website plus app | The site stays in Exponential's templates; an app reads the same content through the REST API | Businesses with a loyal, returning audience |
| **Headless**: front end of your own | A separate front end (app, single-page application, static site generator) renders all content from the API | Teams with their own front-end developers |

The app creators' documentation recommends: "Many projects keep the site in templates and add an app on the API.
That keeps preview, layouts and SEO simple for the site, and gives the app the same content. Start hybrid unless there
is a reason not to" ([Headless Exponential](../../creators/app-creators/headless-exponential.md#hybrid-templates-and-api-together)).

## Why one content source matters

Whichever option you choose, editors keep one place to work: content classes, editing, versions, translations,
workflows and permissions stay in Exponential, and every API call is checked against the user's policies
([Headless Exponential](../../creators/app-creators/headless-exponential.md#what-you-keep-and-what-you-replace)).
For a business that means:

- one product description, price and photo, shown on the site and in the app;
- one approval process, one translation process;
- one place to correct an error.

## What an app adds to your workload

| Item | Website only | With an app |
|---|---|---|
| Design and front-end code | One design | The design plus the app's own screens, per platform |
| Preview of unpublished content | Built in, with the site's templates | The app needs its own preview; the app creators' page calls this "the part teams forget" |
| Releases | Deploy the site | Deploy the site, and publish app updates through app stores |
| Credentials | Editors' accounts | Plus the app's credentials, rotated and revoked ([Apps and API keys](apps-and-api-keys.md)) |
| Availability | The site must answer | The site and the API must answer ([App backend reliability](app-backend-reliability.md)) |
| Search engine visibility | Pages are indexed | App content is not indexed; the site still carries SEO |

Ask any agency quoting for an app to cover every row of this table.

## Editors on phones

Editors can work from phones too:

- The **admin4** design has a phone layout with a hamburger menu that "always fits the visible screen", tiles of
  44 px for the tabs, and a sign-in page "fast on phones" with 16 px text "so mobile Safari does not zoom"
  ([admin4 design](../../../features/6.0/admin4-design.md)).
- **admin3** is the classic administration "with a sidebar that collapses, menus that work by touch, and content
  forms that stack instead of overflowing" ([The responsive admin design](../../../features/6.0/admin3-responsive-admin.md)).

That matters for small teams: a news item, an opening-hours change or a price correction can be made away from the
desk.

## Questions to answer first

- [ ] What will customers do on a phone that they cannot do well today?
- [ ] Does the website already work on phones? (If not, fix that first: [Responsive sites](responsive-sites.md).)
- [ ] Which content and functions would the app need: read only, or also orders, accounts, forms?
- [ ] Who builds and maintains the app, for how many platforms, and for how long?
- [ ] How will the app sign users in ([Apps and API keys](apps-and-api-keys.md))?
- [ ] Who preview-checks content before it reaches the app?
- [ ] What happens to customers when the API is unavailable ([App backend reliability](app-backend-reliability.md))?

## Related pages

- [Responsive sites](responsive-sites.md)
- [Headless with the REST API](headless-with-the-rest-api.md)
- [Mobile checklist](mobile-checklist.md)
- [Headless Exponential](../../creators/app-creators/headless-exponential.md)
- [How businesses sell online](../how-businesses-sell-online.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
