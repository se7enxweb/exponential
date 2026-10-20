---
title: What the built-in shop does
audience: businesses, sales-marketing, project-manager
category: business-solutions
order: 20
summary: A business owner's tour of the shop that ships with Exponential - its parts, the customer's path, the store staff's tools, and the things it does not do without an extension.
keywords: built-in shop, e-commerce features, webshop features, shop module, basket, checkout, orders, VAT, currencies, discounts, wishlist, limits
type: help
---
# What the built-in shop does

This page is for business owners and decision makers who want to know what they get when they sell with Exponential.
It describes every part of the shop that ships in the kernel, in plain language, and is honest about what is not
included. Each section links to the page with the details.

## Contents

1. [In one paragraph](#in-one-paragraph)
2. [The parts of the shop](#the-parts-of-the-shop)
3. [What your customers see](#what-your-customers-see)
4. [What your staff use](#what-your-staff-use)
5. [Extensions that add to it](#extensions-that-add-to-it)
6. [What is not included](#what-is-not-included)
7. [Why it suits small businesses](#why-it-suits-small-businesses)

## In one paragraph

The shop is part of Exponential itself, not an add-on: the README lists "Advanced e-Commerce Stores (Built in)"
([README.md](https://github.com/se7enxweb/exponential/blob/main/README.md)), and the shop module with all its views is defined in `kernel/shop/module.php`.
Products are content like any other page, with a price field. Customers fill a basket, check out, confirm, pay
through a payment gateway, and receive a confirmation mail. Staff manage orders, customers, VAT, currencies and
discounts in the **Store** tab of the administration, which opens with a dashboard
([Store dashboard and order statuses](../../../features/6.0/store-dashboard.md)).

## The parts of the shop

| Part | What it does for you | Read |
|---|---|---|
| Products | Any content class with a price field becomes sellable | [Product classes and catalog](product-classes-and-catalog.md) |
| Prices | One price, or one price per currency, with options that change the price | [Prices and currencies](prices-and-currencies.md) |
| VAT | Named VAT rates, and rules by customer country and product category | [VAT rules and VAT types](vat-rules-and-vat-types.md) |
| Countries | A grouped country list with the most used countries first | [Country groups and regions](country-groups-and-regions.md) |
| Shipping | A flat shipping cost workflow, or a shipping handler of your own | [Shipping and handling](shipping-and-handling.md) |
| Basket and checkout | Add to basket, quantities, customer data, confirmation, payment | [Basket and checkout flow](basket-and-checkout-flow.md) |
| Orders | Order list, order view, seventeen statuses, archive, customers | [Orders and order statuses](orders-and-order-statuses.md) |
| Receipts and mail | A confirmation mail, and optionally a permanent receipt link | [Order receipts and confirmation mail](order-receipts-and-confirmation-mail.md) |
| Dashboard | Figures, charts, work to do and setup advice on one page | [Store dashboard and reports](store-dashboard-and-reports.md) |
| Payment | Payment gateways run as a workflow step at checkout | [Payment gateways for businesses](payment-gateways-for-businesses.md) |
| Discounts | Percentage discounts for groups of customers | [Discounts and wishlists](discounts-and-wishlists.md) |
| Wishlist | Signed-in customers keep products for later | [Discounts and wishlists](discounts-and-wishlists.md) |

## What your customers see

1. A product page with an **Add to basket** button.
2. A basket with items, quantities and totals.
3. A form for their name, e-mail and address (or a sign-in).
4. A confirmation page with shipping and VAT.
5. The payment provider's page or form.
6. The placed order, a confirmation mail, and if you turn it on, a receipt link that never expires.

The shipped designs carry these pages: the ezwebin and simple themes ship the shop templates
([Running a shop: overview](../../creators/website-creators/shop-overview.md)), and the standard design has the full
set of `shop/` templates in `design/standard/templates/shop/`.

## What your staff use

- **Store dashboard**: orders today, in 7 and 30 days, revenue per currency, open orders, abandoned baskets,
  best-selling products, and "Next steps" suggestions worked out from your data and settings.
- **Order list**: sortable by every column ([Order list sorting](../../../features/6.0/order-list-sorting.md)).
- **Order view** with status changes and status history (`eZOrderStatusHistory`, `kernel/classes/ezorderstatushistory.php`).
- **Archive** for finished orders, **customer list**, **statistics** per year and month.
- **VAT types and rules**, **currencies**, **product categories**, **discount groups**.
- **Products overview**: the products and their prices in one list (`shop/productsoverview`).

Every action needs a policy, so you choose who sees what ([Store planning for small businesses](store-planning-for-small-businesses.md#people-and-roles)).
The audit trail records commerce events: orders, payments (never card data), VAT, currencies and discounts
([Audit trail](../../../features/6.0/audit-trail.md)).

## Extensions that add to it

| Extension | Adds | Status in the project |
|---|---|---|
| `ezpaypal` | PayPal payments | required by the root `composer.json` (`se7enxweb/ezpaypal`) |
| `ezauthorize` | Card payments through Authorize.Net | suggested in `composer.json` |
| `ezstarrating` | Star ratings, usable on products | [ezstarrating](../../../features/6.0/extensions/ezstarrating.md) |
| `ezfind` | Solr search with facets | suggested in `composer.json` |
| `cjw_newsletter` | Newsletters to customers | [cjw_newsletter](../../../features/6.0/extensions/cjw_newsletter.md) |
| `xrowmetadata` | Page metadata and sitemaps | [xrowmetadata](../../../features/6.0/extensions/xrowmetadata.md) |

## What is not included

Being honest saves money. The technical overview names these as things to plan as extensions or integrations:
**stock management, invoices in an accounting system, shipping labels and marketplaces**
([Running a shop: overview](../../creators/website-creators/shop-overview.md)). In the kernel code we also found:

- no coupon or voucher codes: discounts are tied to customer groups, not to codes typed at checkout;
- no export of orders to a spreadsheet file from the order list;
- no automatic delivery of a purchased file after payment
  ([Digital products and downloads](digital-products-and-downloads.md));
- only one shipping method shipped: a flat amount per order
  ([Shipping and handling](shipping-and-handling.md)).

Each of these can be added with an extension, a workflow event or a custom handler, which is application creator
work ([App creators: start here](../../creators/app-creators/app-creators-start-here.md)) or a task for a partner
([Choosing a partner](../choosing/choosing-a-partner.md)).

## Why it suits small businesses

- One system for the website and the store: products are pages, so editors use the same tools for both.
- No separate shop product to license: Exponential is GPL v2 or later (`composer.json`, `license`
  `GPL-2.0-or-later`); see [What you do not pay for](../costs/what-you-do-not-pay-for.md).
- The dashboard tells a small team what needs attention, without reports to build.
- Long-lived code: the shop's classes carry copyright lines going back to 1998
  (for example `kernel/classes/ezvattype.php`), and the 6.0.15 release still added to it
  ([Changelog 6.0.15](../../../../changelogs/6.0/6.0.15.md)).

## Related pages

- [Running a shop: overview](../../creators/website-creators/shop-overview.md)
- [How businesses sell online](../how-businesses-sell-online.md)
- [Store FAQ](store-faq.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
