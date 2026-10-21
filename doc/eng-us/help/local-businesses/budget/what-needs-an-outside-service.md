---
title: What needs an outside service
audience: local-business, businesses, customer
category: local-business
order: 74
summary: The plain list for local businesses of what Exponential does not do by itself - business listings on map and search platforms, appointment booking, table reservations, restaurant ordering, POS integration, stock, coupon codes, map tiles, CAPTCHA, payment processing, mail delivery, analytics - with what was searched, the usual way to fill each gap and who to ask.
keywords: outside service, not built in, booking, appointments, reservations, online ordering, POS, point of sale, Google Business Profile, business listing, map provider, CAPTCHA, payment provider, integration, local business
type: help
---
# What needs an outside service

This page is for the owner of a local business who wants to know, before spending money, what an Exponential site
will **not** do on its own. Each line was checked in the repository: the `require` and `suggest` blocks of
`composer.json`, the `extension/` directory and the existing documentation, on 20 October 2026.

## Contents

1. [Not website features at all](#not-website-features-at-all)
2. [Not built in](#not-built-in)
3. [Built in, but relying on an outside provider](#built-in-but-relying-on-an-outside-provider)
4. [How to connect an outside service](#how-to-connect-an-outside-service)
5. [Who to ask](#who-to-ask)

## Not website features at all

| Need | Where it is done |
|---|---|
| Your business profile on map and search platforms (for example Google Business Profile), with hours, photos, reviews | on each platform, by you ([Business listings outside Exponential](../getting-found/business-listings-outside-exponential.md)) |
| Written customer reviews on review platforms | on those platforms ([Ratings and reviews](../contact-and-forms/ratings-and-reviews.md)) |
| Your mailboxes | a mail provider ([Choosing hosting for a small business](../getting-started/choosing-hosting-for-a-small-business.md#domain-mail-and-the-website)) |
| Bookkeeping, invoices, tax returns | your accounting software and accountant ([Invoices and receipts](../../customers/commerce/invoices-and-receipts.md)) |

## Not built in

No extension for these was found:

| Need | Usual way | Read |
|---|---|---|
| Appointment booking with free times (salons, practices, trades) | a booking service, linked or embedded | [Quote and request forms](../contact-and-forms/quote-and-request-forms.md#appointment-requests-are-not-bookings) |
| Table reservations | a reservation service | [Restaurant orders and what is not built in](../simple-shop/restaurant-orders-and-what-is-not-built-in.md) |
| Online food ordering with time slots, kitchen tickets, table ordering | an ordering or delivery platform | same |
| Connection to the till (POS) | an integration built by a partner, or none | [Integrations planning](../../customers/growth/integrations-planning.md) |
| Stock counting | an extension or integration | [What the built-in shop does](../../businesses/stores/what-the-built-in-shop-does.md) |
| Coupon or voucher codes at checkout | an extension; vouchers by hand meanwhile | [A small catalog](../simple-shop/a-small-catalog.md#vouchers-and-gift-cards) |
| Shipping labels, carrier rates, tracking | the carrier's portal, or a shipping handler | [Shipping and handling](../../businesses/stores/shipping-and-handling.md) |
| Event tickets with seat limits, iCal files | a ticketing service | [Events and specials](../marketing/events-and-specials.md#what-is-not-built-in) |
| An opening-hours datatype, "open now" | modeled with existing datatypes; "open now" by template | [Opening hours without a special datatype](../maps-and-hours/opening-hours-without-a-special-datatype.md) |
| LocalBusiness structured data | template work | [Structured data: what exists](../getting-found/structured-data-what-exists.md) |
| Live chat | a chat service's script | ask your builder |

## Built in, but relying on an outside provider

| Feature | Provider | Key or account |
|---|---|---|
| Map in `ezgmaplocation` | Google Maps | Google Maps JavaScript API key ([Showing your location on a map](../maps-and-hours/showing-your-location-on-a-map.md)) |
| Map block, Google variant | Google Maps Embed | `GoogleMapsApiKey`; the OpenStreetMap variant needs none |
| CAPTCHA | Google reCAPTCHA or hCaptcha | site and secret key ([Stopping spam](../contact-and-forms/stopping-spam.md)) |
| Payments | PayPal (`ezpaypal`) or Authorize.Net (`ezauthorize`) | merchant account ([Payments](../simple-shop/payments-paypal-and-authorize-net.md)) |
| Analytics through `bcwebsitestatistics` | Google Analytics | tracking identifier ([Measuring visits](../getting-found/measuring-visits.md)) |
| Newsletter delivery | your server's mail transport or an SMTP provider | sender domain records ([Newsletters for local businesses](../marketing/newsletters-for-local-businesses.md)) |
| Exponential Intelligence drafts | an AI provider chosen by the administrator | provider key ([Exponential Intelligence](../../customers/growth/exponential-intelligence.md)) |

## How to connect an outside service

| Way | Effort | Note |
|---|---|---|
| A link or button ("Book now") | minutes, by an editor | the simplest and most robust |
| An embedded widget (script or frame) | small, by the builder | privacy note; Content Security Policy must allow it ([Content Security Policy](../../../features/6.0/content-security-policy.md)) |
| An integration through the REST API or remote services | a project | [REST API for owners](../../customers/growth/rest-api-for-owners.md), [Remote services](../../customers/growth/remote-services-expservices.md) |
| A new extension | a project; consider giving it back to the community | [Contribute extensions back](../../contribute-extensions-back.md) |

## Who to ask

- For links and widgets: your builder or partner.
- For integrations and new extensions: an Exponential partner ([Implementation partners](../../partners/implementation-partners.md)).
- If something here should be built for everyone: [Feature requests and sponsored features](../../partners/feature-requests-and-sponsored-features.md).

## Related pages

- [The cost picture](the-cost-picture.md)
- [Integrations planning](../../customers/growth/integrations-planning.md)
- [Store FAQ](../../businesses/stores/store-faq.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
