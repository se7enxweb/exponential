---
title: "E-commerce: start here"
audience: ecommerce, customer, businesses
category: e-commerce
order: 1
summary: The hub for everyone who sells online with Exponential - merchants, store managers, e-commerce developers and agencies - with a path per role, the deeper topics the store pages leave out (catalog modeling, shop settings, checkout workflows, order operations, extending the shop, testing, multisite, reporting) and links into the Online stores and Running your store sections.
keywords: e-commerce, ecommerce, online store, webshop, shop, merchant, store manager, e-commerce developer, agency, catalog, checkout, workflow, payment gateway, shipping handler, VAT handler, orders, reporting, multisite, start here
featured: true
type: help
---
# E-commerce: start here

This section is for people who sell online with Exponential: merchants who own a store, store managers who run it
every day, developers who build and extend it, and agencies that deliver stores to clients. It is the e-commerce hub
of the help: it sends you to the right existing page for each question and adds the pages nobody else covers, such as
how the checkout runs internally, how to read every shop setting, how to extend the shop with your own handlers, and
how to test, report on and operate a store over a year.

> **Note**
> Every statement in this section was checked against the Exponential repository: the shop module
> (`kernel/shop/module.php`), the shop classes in `kernel/classes/` and `kernel/shop/classes/`, the shipped settings
> (`settings/shop.ini`, `settings/site.ini`, `settings/shopaccount.ini`, `settings/workflow.ini`,
> `settings/paymentgateways.ini`) and the payment extensions `ezpaypal` and `ezauthorize`. Where something is not in
> the code, the page says so instead of promising it.

## Contents

1. [The two sections this hub builds on](#the-two-sections-this-hub-builds-on)
2. [Pick your path](#pick-your-path)
3. [What this section adds](#what-this-section-adds)
4. [The shop in one diagram](#the-shop-in-one-diagram)
5. [Every part of this section](#every-part-of-this-section)
6. [Quick answers](#quick-answers)
7. [Where else to look](#where-else-to-look)

## The two sections this hub builds on

Two sections of the help already cover the store from a business point of view. Read them first; this section links
into them and does not repeat them.

| Section | For | What it covers |
|---|---|---|
| [Online stores and small shops](../businesses/stores/README.md) | Owners and decision makers before and at launch | What the built-in shop does, planning, products, prices, VAT, shipping, checkout, payments, the launch checklist |
| [Running your store](../customers/commerce/README.md) | Owners and staff after launch | Daily, weekly and monthly routines, orders, returns, VAT checks, GDPR requests, store reports |

The technical setup pages for website creators are [Running a shop: overview](../creators/website-creators/shop-overview.md),
[Products, prices and VAT](../creators/website-creators/products-prices-and-vat.md),
[Basket, checkout and orders](../creators/website-creators/basket-checkout-and-orders.md) and
[Payment gateways](../creators/website-creators/payment-gateways.md).

## Pick your path

| You are | Your path | Typical first question |
|---|---|---|
| A merchant who owns the store | [Merchant path](roles/merchant-path.md) | "What will it cost me to run, and what must I decide?" |
| A store manager who runs orders | [Store manager path](roles/store-manager-path.md) | "Which screens do I use every day, and who may change which status?" |
| A developer who builds or extends the shop | [Developer path](roles/developer-path.md) | "Where do I hook in shipping, VAT or a payment provider?" |
| An agency or partner delivering stores | [Agency path](roles/agency-path.md) | "How do I scope, build, test and hand over a store?" |

All four paths are explained side by side in [Paths by role](roles/README.md). The whole section in reading order is
the [E-commerce reading path](e-commerce-reading-path.md).

## What this section adds

The store sections answer "what" and "how often". This section answers "how exactly" and "what if":

- **Catalog modeling in depth**: choosing between `ezprice` and `ezmultiprice`, options, product categories for VAT,
  and the folder structure of a catalog ([Catalog modeling](catalog/README.md)).
- **Shop settings walkthroughs**: every shop-related setting, file by file, with the shipped value and when to change
  it ([Shop settings walkthroughs](settings/README.md)).
- **The checkout from the inside**: the four shop operations, the temporary order, the workflow triggers, and the
  payment gateway, simple shipping and multiplexer events ([Checkout and workflows](checkout/README.md)).
- **Order operations**: the lifecycle of an order in the database, status transitions limited by `FromStatus` and
  `ToStatus`, archive versus remove, and the order mail handler ([Order operations](orders/README.md)).
- **Extending the shop**: shipping, VAT, basket info, shop account, confirm order and exchange rate handlers, payment
  gateways of your own and custom price datatypes ([Extending the shop](extending/README.md)).
- **Operating a store**: testing, accessibility, multisite, landing pages with Layouts, analytics, reporting and
  export, product imports, headless commerce, the commerce audit trail, a yearly calendar, decision checklists and
  troubleshooting ([Operating a store](operations/README.md)).

## The shop in one diagram

```text
Product page --(shop/add, trigger shop_addtobasket)--> Basket (shop/basket, trigger shop_updatebasket)
   --> Customer data (shop/userregister or shop/register, chosen by shopaccount.ini)
   --> Confirmation (shop/confirmorder, trigger before_shop_confirmorder: shipping)
   --> Checkout (shop/checkout, trigger shop_checkout: payment gateway)
   --> Order activated: order number, status history, order mail, receipt link
   --> Store tab: dashboard, order list, statuses, archive, statistics
```

Each step is explained in [Checkout operations step by step](checkout/checkout-operations-step-by-step.md).

## Every part of this section

| Part | Pages |
|---|---|
| [Paths by role](roles/README.md) | merchant, store manager, developer, agency |
| [Catalog modeling](catalog/README.md) | modeling in depth, price fields compared, options and variants, product categories and VAT rules |
| [Shop settings walkthroughs](settings/README.md) | `shop.ini`, `site.ini [ShopSettings]`, `shopaccount.ini`, `workflow.ini`, `paymentgateways.ini` |
| [Checkout and workflows](checkout/README.md) | the operations, temporary orders, triggers, payment gateway, simple shipping, multiplexer |
| [Order operations](orders/README.md) | lifecycle, status permissions, archive and cleanup, order mail and receipts |
| [Extending the shop](extending/README.md) | shipping, VAT, payment gateways, shop accounts, basket info and confirm order, exchange rates, price datatypes |
| [Operating a store](operations/README.md) | testing, accessibility, multisite, Layouts, analytics, reporting, imports, headless, audit, calendar, checklists, troubleshooting |
| Reference | [FAQ](e-commerce-faq.md), [Glossary](e-commerce-glossary.md), [Reading path](e-commerce-reading-path.md) |

## Quick answers

- **Is the shop an add-on?** No. It is part of the kernel: the shop module and its views are in
  `kernel/shop/module.php` ([What the built-in shop does](../businesses/stores/what-the-built-in-shop-does.md)).
- **Which payment providers?** The repository has two payment extensions, `ezpaypal` (PayPal) and `ezauthorize`
  (Authorize.Net). Others are written as gateways ([Payment gateway development](extending/payment-gateway-development.md)).
- **Does it charge VAT by country?** Yes, with VAT rules, once a VAT handler is switched on
  ([Product categories and VAT rules](catalog/product-categories-and-vat-rules.md)).
- **What is not included?** Stock management, coupon codes, accounting invoices, shipping labels and automatic file
  delivery are not in the kernel ([E-commerce FAQ](e-commerce-faq.md#what-the-shop-does-not-do)).

## Where else to look

| Topic | Page |
|---|---|
| Workflows in general | [Workflows](../../guides/workflows.md) |
| Roles and policies | [Roles and policies](../../guides/roles-and-policies.md) |
| The Store dashboard and the seventeen order statuses | [Store dashboard and order statuses](../../features/6.0/store-dashboard.md) |
| Permanent receipts | [Permanent order receipts](../../features/6.0/order-receipts.md) |
| Selling in several languages and sites | [Multilingual businesses](../businesses/multilingual/README.md), [Multi-site brands](../businesses/multisite/README.md) |
| Building extensions | [App creators: start here](../creators/app-creators/app-creators-start-here.md) |

## Related pages

- [Online stores and small shops](../businesses/stores/README.md)
- [Running your store](../customers/commerce/README.md)
- [Businesses: start here](../businesses/businesses-start-here.md)
- [Customers: start here](../customers/customers-start-here.md)
- [E-commerce reading path](e-commerce-reading-path.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
