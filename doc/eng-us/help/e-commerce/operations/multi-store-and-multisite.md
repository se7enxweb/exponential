---
title: Multi-store and multisite
audience: ecommerce, website-creator, businesses
category: e-commerce
order: 73
summary: Running several store fronts with Exponential - what is shared when siteaccesses share one database (orders, VAT, currencies, discounts, statuses), what can differ per siteaccess (currency, basket name, templates, handlers), how to tell orders apart, and when separate installations are the better choice.
keywords: multi-store, multisite, several shops, siteaccess, store front, brands, shared orders, per-site settings, currency per site, multilingual store, separate installation, shared database
type: help
---
# Multi-store and multisite

This page is for merchants, agencies and developers who plan more than one store front: several brands, countries or
languages. Exponential serves several sites from one installation through siteaccesses
([Setting up several sites in one installation](../../creators/website-creators/multisite-setup.md)). The shop works in
that setup, but some of its data is shared by every site on the same database, and that decides the design.

## Contents

1. [Shared and separate](#shared-and-separate)
2. [What can differ per siteaccess](#what-can-differ-per-siteaccess)
3. [Telling orders apart](#telling-orders-apart)
4. [Three designs](#three-designs)
5. [Checklist](#checklist)

## Shared and separate

Shop data lives in database tables, not per site. Siteaccesses on one database share:

| Shared | Note |
|---|---|
| Orders, order numbers, statuses and the status list | `ezorder` has no site column; one order list, one number sequence |
| VAT types, VAT rules, product categories | One tax setup |
| Currencies and exchange rates | One currency list |
| Discount groups and rules | One set |
| Customers (user accounts) | One user base, unless sites use separate user trees and policies |
| Products | Shared or separate, as you place them in the content tree |
| The Store dashboard | Figures for all sites together |

Baskets belong to sessions, so they follow the visitor's session on the site they shop on.

## What can differ per siteaccess

Settings in `settings/siteaccess/<siteaccess>/` override the installation's for that site
([Shop settings walkthroughs](../settings/README.md#where-shop-settings-go),
[Per-site settings and logs](../../businesses/multisite/per-site-settings-and-logs.md)):

| Can differ | Setting or place |
|---|---|
| Default currency | `shop.ini [CurrencySettings] PreferredCurrency` |
| Basket address name | `shop.ini [BasketSettings] BasketViewName` |
| Redirect after add to basket | `site.ini [ShopSettings] RedirectAfterAddToBasket` |
| Shop and order mail templates, product pages | the siteaccess's design ([Per-brand designs](../../businesses/multisite/per-brand-designs.md)) |
| Sender and shop address of the order mail | `site.ini [MailSettings]` |
| Language of the store | the siteaccess's languages ([Multilingual businesses](../../businesses/multilingual/README.md)) |

Workflows and triggers are stored in the database and are the same for all sites. If two sites need different payment
providers, select both gateways in the payment event and let the customer choose, or write an event that selects by
site.

## Telling orders apart

Because orders carry no site, decide how staff tell them apart:

- separate product folders per site, so the products on the order show where it came from;
- the order currency, when each site sells in its own currency;
- a field added by your own shop account handler or an event on the after trigger of `shop_checkout` that records
  the siteaccess ([Shop account handlers](../extending/shop-account-handlers.md)).

## Three designs

| Design | Fits | Watch out for |
|---|---|---|
| One store, several languages | One business, one tax setup | Prices and options per translation ([Catalog modeling in depth](../catalog/catalog-modeling-in-depth.md#translations)) |
| Several brand fronts, one business | Shared stock and staff, one accountant | One order list for all brands; tell orders apart as above |
| Separate businesses or tax setups | Different companies, VAT registrations or providers | Use separate installations or databases; shared tables cannot be split later without work |

## Checklist

- [ ] Decide per item of the "Shared" table whether sharing is acceptable.
- [ ] Set per-site currency, mail sender and templates in siteaccess settings.
- [ ] Decide how staff recognize a site's orders before launch.
- [ ] Test a full order on every site ([Testing a store](testing-a-store.md)).

## Related pages

- [Multi-site brands](../../businesses/multisite/README.md)
- [One installation, many sites](../../businesses/multisite/one-installation-many-sites.md)
- [Multi-brand checklist](../../businesses/multisite/multi-brand-checklist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
