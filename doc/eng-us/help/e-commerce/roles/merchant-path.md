---
title: Merchant path
audience: ecommerce, businesses, customer
category: e-commerce
order: 11
summary: A reading and decision path for the merchant who owns an Exponential store - the decisions only the owner can make, in order, with the page that explains each and what to hand to the developer.
keywords: merchant, store owner, decisions, payment provider, VAT, prices, currencies, discounts, budget, launch, handover, path
type: help
---
# Merchant path

This page is for the merchant: the owner or manager who is responsible for what the store sells, at what price and
under which rules. It lists the decisions only you can make, in the order they come up, and what each decision gives
your developer or agency to work with.

## Contents

1. [Before you start](#before-you-start)
2. [Decision 1 What you sell and how it is modeled](#decision-1-what-you-sell-and-how-it-is-modeled)
3. [Decision 2 Prices and currencies](#decision-2-prices-and-currencies)
4. [Decision 3 Tax](#decision-3-tax)
5. [Decision 4 Customer data and accounts](#decision-4-customer-data-and-accounts)
6. [Decision 5 Shipping](#decision-5-shipping)
7. [Decision 6 Payment](#decision-6-payment)
8. [Decision 7 Order process and statuses](#decision-7-order-process-and-statuses)
9. [Decision 8 What you add beyond the kernel](#decision-8-what-you-add-beyond-the-kernel)
10. [After launch](#after-launch)

## Before you start

Read [What the built-in shop does](../../businesses/stores/what-the-built-in-shop-does.md) and
[Store planning for small businesses](../../businesses/stores/store-planning-for-small-businesses.md). They describe
the shop for a decision maker. This path assumes you have decided to sell with Exponential.

## Decision 1 What you sell and how it is modeled

| You decide | It gives the developer | Read |
|---|---|---|
| Kinds of product (physical, digital, services) and the fields each needs | The product classes | [Catalog modeling in depth](../catalog/catalog-modeling-in-depth.md) |
| Whether customers choose options (size, color) that change the price | Option fields | [Options and variants](../catalog/options-and-variants.md) |
| How the catalog is grouped for customers | The folder structure | [Catalog modeling](../catalog/README.md) |

## Decision 2 Prices and currencies

One currency or several? If several, do you set every price by hand or let exchange rates convert? The answer chooses
between the `ezprice` and `ezmultiprice` fields ([Price fields compared](../catalog/price-fields-compared.md)) and the
`shop.ini` currency settings ([shop.ini walkthrough](../settings/shop-ini-walkthrough.md)).

## Decision 3 Tax

One VAT rate for everything, a few named rates per product, or rates by customer country and product category. The
third needs VAT rules and a VAT handler switched on by the developer
([Product categories and VAT rules](../catalog/product-categories-and-vat-rules.md)). The tax law itself is for your
tax adviser ([VAT rules and VAT types](../../businesses/stores/vat-rules-and-vat-types.md)).

## Decision 4 Customer data and accounts

Must customers create an account, may they buy as guests, and which address fields do you need? The shop account
handler implements the answer ([shopaccount.ini walkthrough](../settings/shopaccount-ini-walkthrough.md),
[Customer accounts and guest checkout](../../businesses/stores/customer-accounts-and-guest-checkout.md)).

## Decision 5 Shipping

A flat amount per order is shipped with the kernel (the Simple shipping workflow event). Anything else, by weight, by
country or by carrier rate, is a shipping handler a developer writes
([Simple shipping event](../checkout/simple-shipping-event.md), [Custom shipping handler](../extending/custom-shipping-handler.md)).

## Decision 6 Payment

PayPal (`ezpaypal`), Authorize.Net (`ezauthorize`) or a provider for which a gateway has to be written. A redirect
gateway keeps card data off your site; a card form on your site raises your card security duties
([Payment gateways for businesses](../../businesses/stores/payment-gateways-for-businesses.md),
[Card payments and PCI basics](../../businesses/stores/card-payments-and-pci-basics.md)).

## Decision 7 Order process and statuses

Which of the seventeen shipped statuses you use, and who may move an order from one to the next
([Order lifecycle operations](../orders/order-lifecycle-operations.md),
[Status transitions and permissions](../orders/status-transitions-and-permissions.md)).

## Decision 8 What you add beyond the kernel

Stock, coupon codes, accounting export, shipping labels and automatic download delivery are not in the kernel. Decide
which you need at launch and budget them as extension work ([Decision checklists](../operations/decision-checklists.md)).

## After launch

Hand the daily work to the store manager ([Store manager path](store-manager-path.md)) and keep a yearly rhythm
([Operations calendar](../operations/operations-calendar.md)). Monthly figures come from the dashboard and statistics
([Store figures and exports](../operations/store-figures-and-exports.md)).

## Related pages

- [Paths by role](README.md)
- [Store launch checklist](../../businesses/stores/store-launch-checklist.md)
- [First 30 days as an owner](../../customers/first-30-days-as-owner.md)
- [Worked example: small store](../../businesses/costs/worked-example-small-store.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
