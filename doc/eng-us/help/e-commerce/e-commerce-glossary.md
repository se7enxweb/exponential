---
title: E-commerce glossary
audience: ecommerce, customer, businesses
category: e-commerce
order: 95
summary: The words of the E-commerce section explained - from product class, price field and product collection to temporary order, trigger, payment object, callback, shop account handler, dynamic VAT and expservices - each with the page that explains it in full.
keywords: glossary, e-commerce terms, product class, ezprice, ezmultiprice, product collection, basket, temporary order, order number, order item, status history, trigger, workflow event, payment gateway, payment object, callback, shop account handler, VAT rule, product category, dynamic VAT, handler, siteaccess, expservices
type: help
---
# E-commerce glossary

The words used in the E-commerce section, explained for merchants, store managers and developers. Each entry links to
the page that explains it in full. The business terms are also in the [Businesses glossary](../businesses/businesses-glossary.md)
and the store owners' terms in the [Customers glossary](../customers/customers-glossary.md).

## Contents

1. [Catalog and prices](#catalog-and-prices)
2. [Basket and checkout](#basket-and-checkout)
3. [Orders](#orders)
4. [Tax and shipping](#tax-and-shipping)
5. [Extending and integrating](#extending-and-integrating)

## Catalog and prices

| Term | Meaning | Read |
|---|---|---|
| Product class | A content class with a price field | [Catalog modeling](catalog/README.md) |
| `ezprice` | Price datatype with one amount, a VAT type and "including VAT" or not | [Price fields compared](catalog/price-fields-compared.md) |
| `ezmultiprice` | Price datatype with one amount per currency | same |
| Custom price | An `ezmultiprice` amount entered by hand | same |
| Automatic price | An `ezmultiprice` amount converted with exchange rates | same |
| Option datatypes | `ezoption`, `ezmultioption`, `ezmultioption2`, `ezrangeoption`: choices with optional extra prices | [Options and variants](catalog/options-and-variants.md) |
| Products overview | `shop/productsoverview`: products of a class with their prices | [Catalog modeling](catalog/README.md#the-products-overview-screen) |

## Basket and checkout

| Term | Meaning | Read |
|---|---|---|
| Basket | The customer's selection, tied to the session; `shop/basket` | [Basket and checkout flow](../businesses/stores/basket-and-checkout-flow.md) |
| Product collection | The items of a basket and later of the order, with names, prices, VAT and options | [Order operations](orders/README.md#an-order-in-one-table) |
| Operation | A shop step with trigger points: `addtobasket`, `updatebasket`, `confirmorder`, `checkout` | [Checkout operations step by step](checkout/checkout-operations-step-by-step.md) |
| Trigger | The point before or after an operation where a workflow runs | [Workflows on shop triggers](checkout/workflows-on-shop-triggers.md) |
| Workflow event | One step of a workflow, such as Simple shipping or Payment Gateway | same |
| Temporary order | The order record the checkout works on before it is placed | [Temporary orders and activation](checkout/temporary-orders-and-activation.md) |
| Activation | The moment a temporary order becomes a placed order with a number | same |
| Payment gateway | The class that takes payment for one provider | [Payment gateway event](checkout/payment-gateway-event.md) |
| Redirect gateway | A gateway that sends the customer to the provider and waits for the callback | same |
| Transparent gateway | A gateway that charges from your server in one request | same |
| Payment object | The record that keeps a redirect payment's state between the two visits | same |
| Callback | The provider's request to your site that confirms a payment | same |
| Multiplexer | A workflow event that runs another workflow when conditions match | [Multiplexer in the shop](checkout/multiplexer-in-the-shop.md) |

## Orders

| Term | Meaning | Read |
|---|---|---|
| Order number | The number a placed order gets at activation; quote it to customers | [Temporary orders and activation](checkout/temporary-orders-and-activation.md#order-id-and-order-number) |
| Order item | An extra line on an order, such as shipping | [Simple shipping event](checkout/simple-shipping-event.md) |
| Order status | The stage of an order; seventeen shipped, custom from 1000 | [Order lifecycle operations](orders/order-lifecycle-operations.md) |
| Status history | Every status change with user and time | same |
| `FromStatus`, `ToStatus` | Limitations of `shop/setstatus` | [Status transitions and permissions](orders/status-transitions-and-permissions.md) |
| Archive | Hiding a finished order from the order list, keeping it | [Archive, remove and cleanup](orders/archive-remove-and-cleanup.md) |
| Permanent receipt | `shop/orderreceipt/<token>`: a receipt link that never expires | [Order mail and receipts for operators](orders/order-mail-and-receipts-for-operators.md) |

## Tax and shipping

| Term | Meaning | Read |
|---|---|---|
| VAT type | A named percentage | [VAT rules and VAT types](../businesses/stores/vat-rules-and-vat-types.md) |
| Product category | A product's tax class, used by VAT rules | [Product categories and VAT rules](catalog/product-categories-and-vat-rules.md) |
| VAT rule | Which VAT type applies for a country and product categories | same |
| Dynamic VAT | "Determined by VAT charging rules": the VAT handler chooses at buying time | same |
| Simple shipping | The flat shipping workflow event | [Simple shipping event](checkout/simple-shipping-event.md) |
| Shipping handler | A class that calculates shipping from the basket | [Custom shipping handler](extending/custom-shipping-handler.md) |

## Extending and integrating

| Term | Meaning | Read |
|---|---|---|
| Handler | A class selected by a setting that the kernel calls in place of its own | [Extending the shop](extending/README.md) |
| Shop account handler | Decides the checkout's customer data form and reads it back | [Shop account handlers](extending/shop-account-handlers.md) |
| Confirm order handler | Sends the order mail | [Basket info and confirm order handlers](extending/basket-info-and-confirm-order-handlers.md) |
| Basket info handler | Adds shipping to the basket's totals per VAT rate | same |
| Exchange rates update handler | Fetches exchange rates | [Exchange rate handlers](extending/exchange-rate-handlers.md) |
| Siteaccess | One site of an installation, with its own settings and design | [Multi-store and multisite](operations/multi-store-and-multisite.md) |
| `expservices` | The extension that offers the shop's functions as remote services | [Headless commerce with expservices](operations/headless-commerce-with-expservices.md) |
| Commerce channel | The audit trail's channel for shop events | [Commerce audit trail](operations/commerce-audit-trail.md) |

## Related pages

- [E-commerce: start here](e-commerce-start-here.md)
- [E-commerce FAQ](e-commerce-faq.md)
- [Glossary](../../glossary.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
