---
title: The built-in shop
audience: cms-developer
category: cms-development
order: 35
summary: For CMS developers, a short and factual overview of the shop in the Exponential kernel. It covers products as content with price datatypes, the basket, checkout and orders, VAT, currencies and discounts, payment through workflows and gateway extensions, the Store dashboard, and where to read further.
keywords: shop, webshop, store, e-commerce, products, ezprice, ezmultiprice, basket, checkout, orders, VAT, currencies, discounts, wish list, payment gateway, ezpaypal, ezauthorize, shop.ini, paymentgateways.ini, Store dashboard, order receipts
type: help
---
# The built-in shop

This page is for developers asked whether a site can sell things. The Exponential kernel has a shop module: products
are content objects, and baskets, orders, VAT, currencies and discounts are built in. This page says what is there and
where it is documented, so you can judge whether it fits a project.

## Contents

1. [What the shop is made of](#what-the-shop-is-made-of)
2. [Products are content](#products-are-content)
3. [Basket, checkout and orders](#basket-checkout-and-orders)
4. [Payment](#payment)
5. [VAT, currencies and discounts](#vat-currencies-and-discounts)
6. [The Store in the administration](#the-store-in-the-administration)
7. [Templates](#templates)
8. [Is it the right tool?](#is-it-the-right-tool)

## What the shop is made of

| Part | Where |
|---|---|
| The `shop` module: basket, checkout, confirm order, order list and view, wish list, VAT, currencies, discounts, statistics | `kernel/shop/` |
| Settings: currencies, exchange rates, shipping, basket, VAT, order view and receipts | `settings/shop.ini` |
| Payment gateway framework | `kernel/shop/paymentgateways/`, `settings/paymentgateways.ini` |
| Price and option datatypes | `ezprice`, `ezmultiprice`, `ezoption`, `ezmultioption`, `ezmultioption2`, `ezrangeoption`, `ezproductcategory` |
| Shop triggers for workflows | `workflow.ini [OperationSettings]`: `before_shop_confirmorder`, `shop_checkout`, `shop_addtobasket`, `shop_updatebasket` |

## Products are content

A product is an object of a class with a price attribute: `ezprice` (one price with a VAT type) or `ezmultiprice`
(a price per currency). Options and variants use the option datatypes; `ezproductcategory` groups products for VAT
rules. Everything else (descriptions, images, relations, translations, sections, URLs) works as for any content, so a
product catalog is a content model like any other ([Designing a content model](designing-a-content-model.md)).

## Basket, checkout and orders

Visitors add products to the basket (`shop/add`, `shop/basket`), then check out: the shop account handler collects
the customer's details, `shop/confirmorder` shows the order with shipping and VAT, payment runs, and the order is
stored with a status. Order statuses, the order list and permanent order receipts are managed in the administration
([Order receipts](../../features/6.0/order-receipts.md), [Order list sorting](../../features/6.0/order-list-sorting.md)).

## Payment

Payment is a workflow on the shop triggers with the **Payment gateway** event; a gateway is a class in a gateway
directory listed in `paymentgateways.ini`. The root `composer.json` requires `ezpaypal` (a redirect to PayPal) and
suggests `ezauthorize` (a payment form on the site, without a redirect, according to its description there). The framework
for writing a gateway is documented in the kernel's shop reference ([Payment gateways](../../shop/index.html)) and the
event type in [Workflows and triggers](workflows-and-triggers.md). **Simple shipping** adds a shipping cost through
another event type.

## VAT, currencies and discounts

`shop.ini` holds the currency settings, exchange rates (with an ECB exchange rate handler), shipping and VAT
handling; VAT types and rules, currencies and discount groups are edited in the administration. Country lists for
customers can be grouped ([The grouped country list](../../features/6.0/country-groups.md)).

## The Store in the administration

The **Store** tab has a dashboard with orders, revenue per currency, open orders, baskets, best sellers, the checkout
as configured on the installation, and suggestions; it needs the `shop/administrate` policy
([Store dashboard and order statuses](../../features/6.0/store-dashboard.md)). `expservices` also offers commerce
services (products, basket and checkout, orders, VAT, currencies, discounts) over HTTP
([Remote services: expservices](../../features/6.0/remote-services-expservices.md)).

## Templates

The shop's pages use templates in `shop/` of the design chain; override them in your design extension like any
template, and draw prices in product templates with `attribute_view_gui` or with the `l10n( 'currency' )` operator
([Dates, numbers and l10n](../designers/templates/dates-numbers-and-l10n.md)).

## Is it the right tool?

The built-in shop suits a catalog that lives in the CMS with a straightforward checkout: products editors maintain as
content, one or a few currencies, payment through a gateway extension. Check early, against your requirements, how
taxes, shipping rules, stock, invoicing and the payment provider you need are covered, and plan development where they
are not. Linking from the site to an external shop is the alternative when commerce is the main business of the site
([Mapping content: products](../../install/migration/content-model-mapping.md#9-comments-forms-products)).

## Related pages

- [Choosing datatypes](choosing-datatypes.md)
- [Handlers named by settings](../extension-development/part-3-extension-developer/handlers-named-by-settings.md): the shop's handler points
- [Workflows and triggers](workflows-and-triggers.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
