---
title: Restaurant orders and what is not built in
audience: local-business, businesses, customer
category: business-solutions
order: 45
summary: What a restaurant, cafe or takeaway can do with an Exponential site - menu pages, specials, vouchers, catering requests, a pre-order shop for collection - and what is not built in - table reservations, delivery time slots, kitchen tickets, table ordering, POS connection - with the usual ways to fill each gap.
keywords: restaurant, cafe, takeaway, menu, online ordering, table reservation, delivery, kitchen, POS, vouchers, catering, allergens, outside service, local business
type: help
---
# Restaurant orders and what is not built in

This page is for owners of restaurants, cafes, takeaways and bakeries. Exponential does the website part well: menus,
specials, events, photos, vouchers, a contact form. Live food ordering and reservations are a different kind of
software, and they are **not built in**. Searched in `composer.json` and `extension/` on 20 October 2026: no booking,
reservation, delivery-slot or POS extension.

## Contents

1. [What works well](#what-works-well)
2. [What is not built in](#what-is-not-built-in)
3. [Ways to fill the gaps](#ways-to-fill-the-gaps)
4. [The menu page](#the-menu-page)
5. [Allergens and dietary notes](#allergens-and-dietary-notes)
6. [Checklist](#checklist)

## What works well

| Need | How | Read |
|---|---|---|
| Menu with prices | folder with articles, or a matrix table | [The menu page](#the-menu-page) |
| Daily or weekly specials | an article or event, published and hidden by date | [Events and specials](../marketing/events-and-specials.md) |
| Opening hours, holiday closures | opening hours object, notices | [Opening hours without a special datatype](../maps-and-hours/opening-hours-without-a-special-datatype.md) |
| Gift vouchers | a small shop with PayPal | [A small catalog](a-small-catalog.md#vouchers-and-gift-cards) |
| Catering and group requests | an information collector form | [Quote and request forms](../contact-and-forms/quote-and-request-forms.md) |
| Pre-orders for collection (cakes, holiday menus) | the shop, with pickup | [VAT, shipping and pickup](vat-shipping-and-pickup.md#pickup-in-the-shop) |
| Events (wine evening, live music) | the `event` class in an event calendar | [Events and specials](../marketing/events-and-specials.md) |
| Newsletter for regulars | `cjw_newsletter` | [Newsletters for local businesses](../marketing/newsletters-for-local-businesses.md) |

## What is not built in

| Need | Status | Why it matters |
|---|---|---|
| Table reservations with availability | not built in | needs live availability per time and table |
| Delivery or collection time slots | not built in | the shop has no time-slot choice at checkout |
| Orders sent to the kitchen (printer, screen) | not built in | orders appear in the Store tab and in mail only |
| Ordering from the table by QR code | not built in | |
| Connection to the till (POS) | not built in | prices and sales are not synchronized |
| Stock of dishes ("only 5 left") | not built in | the shop has no stock counting |
| Delivery area check by address | not built in | |

The shop is a general web shop: it is fine for vouchers and pre-orders a day ahead, but not made for hot food ordered
for the next thirty minutes.

## Ways to fill the gaps

| Gap | Usual way | Effort |
|---|---|---|
| Reservations | a reservation service; a "Book a table" button linking to it, or its widget embedded by the builder | small, plus the service's fee |
| Delivery and takeaway ordering | an ordering or delivery platform; link to your page there | small, plus commission |
| Simple pre-orders | the built-in shop with pickup, orders checked each morning | moderate |
| Phone reservations only | phone number prominent on every page, as a dialable link | none |
| A custom booking module | developed by a partner as an extension | large; ask for a quote ([Integrations planning](../../customers/growth/integrations-planning.md)) |

An embedded widget loads scripts from another company: mention it in the privacy note, and if the site sends a
Content Security Policy the builder must allow the service's hosts
([Content Security Policy](../../../features/6.0/content-security-policy.md)).

## The menu page

- A folder "Menu" with one article per course (starters, mains, desserts), or one page with a matrix table
  (dish, description, price).
- Keep prices in one place: the menu page, not also in a PDF that will be outdated. If you do offer a PDF, replace it
  whenever prices change ([Files and downloads](../../creators/website-creators/files-and-downloads.md)).
- Write the menu as text, not only as a photo of the printed menu: text is read by search engines and screen readers.
- Seasonal menu: prepare it as a draft and publish it on the day
  ([Seasonal changes](../running-it/seasonal-changes.md)).

## Allergens and dietary notes

Many countries require allergen information for food. Keep it with each dish (a text field or a column in the
table) and keep it current when recipes change. What you must show is a legal question for your adviser; Exponential
only stores and shows what you type.

## Checklist

- [ ] Menu as text, prices in one place.
- [ ] Reservations: service chosen, or phone shown prominently.
- [ ] Ordering: platform link, pre-order shop, or none, decided.
- [ ] Vouchers in the shop, if wanted.
- [ ] Allergen information current.
- [ ] Outside services listed in the privacy note.

## Related pages

- [What needs an outside service](../budget/what-needs-an-outside-service.md)
- [Do you need a shop?](do-you-need-a-shop.md)
- [Events and specials](../marketing/events-and-specials.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
