---
title: E-commerce offers
audience: reseller, partner, sales-marketing
category: partnership
order: 240
summary: How to sell store offers on the shop built into Exponential - what the shop does (products, prices and currencies, VAT, basket and checkout, orders, dashboard, payment gateways, discounts), the payment extensions, what is not included and must be built or bought, card payments and PCI, and an offer checklist.
keywords: e-commerce, shop, store, products, basket, checkout, orders, VAT, currencies, payment gateway, PayPal, ezpaypal, ezauthorize, discounts, store dashboard, PCI, not included, stock, coupons
type: help
---
# E-commerce offers

This page is for resellers who sell online stores built on Exponential. The shop is part of Exponential itself:
`README.md` lists "Advanced e-Commerce Stores (Built in)", and all shop screens are views of one module,
`kernel/shop/module.php`. Sell what it does, and be clear about what it does not.

## Contents

1. [What the built-in shop does](#what-the-built-in-shop-does)
2. [Payment](#payment)
3. [What is not included](#what-is-not-included)
4. [Packaging a store offer](#packaging-a-store-offer)
5. [Offer checklist](#offer-checklist)

## What the built-in shop does

| Part | What it does | Page for the customer |
|---|---|---|
| Products | Any content class with a price field becomes sellable | [Product classes and catalog](../../businesses/stores/product-classes-and-catalog.md) |
| Prices | One price, or one price per currency, with options that change the price | [Prices and currencies](../../businesses/stores/prices-and-currencies.md) |
| VAT | Named VAT rates, and rules by customer country and product category | [VAT rules and VAT types](../../businesses/stores/vat-rules-and-vat-types.md) |
| Shipping | A flat shipping cost workflow, or a shipping handler of your own | [Shipping and handling](../../businesses/stores/shipping-and-handling.md) |
| Basket and checkout | Add to basket, quantities, customer data, confirmation, payment | [Basket and checkout flow](../../businesses/stores/basket-and-checkout-flow.md) |
| Orders | Order list, order view, seventeen statuses, archive, customers | [Orders and order statuses](../../businesses/stores/orders-and-order-statuses.md) |
| Dashboard | Orders today, in 7 and 30 days, revenue per currency, open orders, abandoned baskets, best sellers | [Store dashboard and order statuses](../../../features/6.0/store-dashboard.md) |
| Discounts and wishlists | Percentage discounts for groups of customers; signed-in customers keep products for later | [Discounts and wishlists](../../businesses/stores/discounts-and-wishlists.md) |
| Permissions | Shop functions `buy`, `administrate`, `setup`, `edit_status` and `setstatus` | [Running your store](../../customers/commerce/README.md) |

Source: [What the built-in shop does](../../businesses/stores/what-the-built-in-shop-does.md).

## Payment

| Extension | Adds | Status in the project |
|---|---|---|
| `ezpaypal` | PayPal payments | Required by the root `composer.json` (`se7enxweb/ezpaypal`) |
| `ezauthorize` | Card payments through Authorize.Net | Suggested in the root `composer.json` |

Payment gateways run as a workflow step at checkout
([Payment gateways for businesses](../../businesses/stores/payment-gateways-for-businesses.md)). Card data and PCI
obligations depend on the gateway and on how payment is taken: read
[Card payments and PCI basics](../../businesses/stores/card-payments-and-pci-basics.md) before you promise anything,
and leave the PCI assessment to the customer and their payment provider.

## What is not included

From [What the built-in shop does](../../businesses/stores/what-the-built-in-shop-does.md#what-is-not-included):

- stock management, invoices in an accounting system, shipping labels and marketplaces;
- coupon or voucher codes (discounts are tied to customer groups);
- export of orders to a spreadsheet file from the order list;
- automatic delivery of a purchased file after payment;
- more than one shipping method out of the box (a flat amount per order).

Each can be added with an extension, a workflow event or a custom handler. Sell them as project work with their
own price, or leave them out and say so. Never put them in an offer as if they were standard.

## Packaging a store offer

| Layer | Content |
|---|---|
| Setup | Product classes, VAT types and rules, currencies, payment gateway, confirmation mail, store pages in the design |
| Run (monthly) | Hosting sized for peaks, updates, backups including orders, monitoring of checkout |
| Store support | Order status questions, VAT changes, gateway problems |
| Add-ons | Custom shipping handler, stock integration, accounting export, as project work |
| Training | Store staff: orders, statuses, dashboard ([Daily store routine](../../customers/commerce/daily-store-routine.md)) |

Peak seasons need capacity planning and a code freeze
([Store performance at peak times](../../businesses/stores/store-performance-at-peak-times.md)).

## Offer checklist

- [ ] The customer's requirements compared with the table of what is and is not included.
- [ ] Payment gateway chosen, its fees and PCI scope explained by the customer's payment provider.
- [ ] VAT rules for the customer's countries configured and checked by their accountant.
- [ ] Orders and customer data included in backups and in the data processing terms
      ([Data protection for resellers](../responsibilities/data-protection.md)).
- [ ] Store staff roles limited to the shop functions they need.
- [ ] Launch checklist done ([Store launch checklist](../../businesses/stores/store-launch-checklist.md)).

## Related pages

- [What the built-in shop does](../../businesses/stores/what-the-built-in-shop-does.md)
- [Running your store](../../customers/commerce/README.md)
- [Running a shop: overview](../../creators/website-creators/shop-overview.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
