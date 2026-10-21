---
title: "A simple shop for a local business"
audience: local-business, businesses, customer
category: business-solutions
order: 40
summary: The pages for a local business that wants to sell a little online with the shop built into Exponential - deciding whether a shop is worth it, a small catalog, PayPal and Authorize.Net payments, VAT, shipping and pickup, and what restaurants need that is not built in - linking to the full store guides.
keywords: simple shop, small shop, online store, vouchers, gift cards, built-in shop, ezpaypal, ezauthorize, VAT, shipping, pickup, click and collect, restaurant, local business
type: help
---
# A simple shop for a local business

This group of pages is for a local business that wants to sell a few things online: gift vouchers, a range of
products, preserves from the restaurant, a course place, a repair package. Exponential has a shop **built into the
kernel**: products are content with a price field, visitors fill a basket, check out and pay through a payment
gateway, and orders are handled in the **Store** tab of the administration
([Running a shop: overview](../../creators/website-creators/shop-overview.md)).

These pages give the small-business view and then link to the complete guides, which are not repeated here:

- [Online stores and small shops](../../businesses/stores/README.md): planning and setting up a store;
- [The store owner's section](../../customers/commerce/README.md): running it day to day.

## Contents

1. [The pages in this group](#the-pages-in-this-group)
2. [What the built-in shop gives a small business](#what-the-built-in-shop-gives-a-small-business)
3. [What it does not do](#what-it-does-not-do)

## The pages in this group

| Page | One line |
|---|---|
| [Do you need a shop?](do-you-need-a-shop.md) | questions that decide whether online selling pays for a local business |
| [A small catalog](a-small-catalog.md) | products, vouchers and services with the shipped product class |
| [Payments: PayPal and Authorize.Net](payments-paypal-and-authorize-net.md) | the payment extensions and what a small business must arrange |
| [VAT, shipping and pickup](vat-shipping-and-pickup.md) | taxes, a flat shipping fee, collection in the shop |
| [Restaurant orders and what is not built in](restaurant-orders-and-what-is-not-built-in.md) | what a restaurant or cafe can and cannot do with the shop |

## What the built-in shop gives a small business

| Part | Built in or extension | Source |
|---|---|---|
| Products with prices, in one or several currencies | built in (`ezprice`, `ezmultiprice`) | [Products, prices and VAT](../../creators/website-creators/products-prices-and-vat.md) |
| Basket, checkout, order confirmation mail | built in | [Basket, checkout and orders](../../creators/website-creators/basket-checkout-and-orders.md) |
| VAT types and rules | built in | [VAT rules and VAT types](../../businesses/stores/vat-rules-and-vat-types.md) |
| A flat shipping fee | built in workflow (`ezsimpleshipping`) | [Shipping and handling](../../businesses/stores/shipping-and-handling.md) |
| Order statuses, including **Ready for pickup** | built in | [Orders and order statuses](../../businesses/stores/orders-and-order-statuses.md) |
| Store dashboard with open orders and abandoned baskets | built in | [Store dashboard and reports](../../businesses/stores/store-dashboard-and-reports.md) |
| Permanent receipts | built in | [Order receipts and confirmation mail](../../businesses/stores/order-receipts-and-confirmation-mail.md) |
| PayPal | `ezpaypal`, in the standard `composer.json` | [PayPal payments](../../businesses/stores/paypal-payments.md) |
| Authorize.Net card payments | `ezauthorize`, a suggested Composer package | [Card payments and PCI basics](../../businesses/stores/card-payments-and-pci-basics.md) |

## What it does not do

From [Running a shop: overview](../../creators/website-creators/shop-overview.md#what-the-shop-does-not-do) and the
store pages: stock management, invoices in an accounting system, shipping labels and marketplaces are planned as
extensions or integrations. For a local business also: no connection to a till (POS), no table or delivery-slot
booking, no coupon codes ([What needs an outside service](../budget/what-needs-an-outside-service.md)).

## Related pages

- [Local businesses: start here](../local-businesses-start-here.md)
- [Store planning for small businesses](../../businesses/stores/store-planning-for-small-businesses.md)
- [Online store: start here](../../customers/types/online-store-start-here.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
