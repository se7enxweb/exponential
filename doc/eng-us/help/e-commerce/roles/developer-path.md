---
title: Developer path
audience: ecommerce, app-creator, website-creator
category: e-commerce
order: 13
summary: A path for the developer who builds or extends an Exponential store - the code map of the shop, the extension points with their settings, the RAD wizards that generate handler skeletons, and the order in which to read the technical pages.
keywords: e-commerce developer, shop code, kernel/shop, eZOrder, eZBasket, eZVatType, operations, workflow events, handlers, payment gateway, shipping handler, VAT handler, RAD, handler wizard, expservices
type: help
---
# Developer path

This page is for developers who build an Exponential store or extend the shop: in-house developers and the developers
of an agency. It gives the code map of the shop, every extension point with the setting that selects it, and a
reading order through the technical pages.

## Contents

1. [The code map](#the-code-map)
2. [Extension points at a glance](#extension-points-at-a-glance)
3. [Generators that save time](#generators-that-save-time)
4. [Reading order](#reading-order)
5. [Rules of thumb](#rules-of-thumb)

## The code map

| What | Where |
|---|---|
| Shop module: views, policy functions, limitations | `kernel/shop/module.php` |
| The views' code (entry files call classes) | `kernel/shop/*.php`, `kernel/private/classes/views/shop/` |
| Shop operations (what triggers exist and what runs in between) | `kernel/shop/operation_definition.php`, `kernel/shop/ezshopoperationcollection.php` |
| Template fetch functions (`fetch( 'shop', ... )`) | `kernel/shop/function_definition.php`, `kernel/shop/ezshopfunctioncollection.php` |
| Orders, items, statuses, status history | `kernel/classes/ezorder.php`, `ezorderitem.php`, `ezorderstatus.php`, `ezorderstatushistory.php` |
| Baskets and product collections | `kernel/classes/ezbasket.php`, `ezproductcollection.php`, `ezproductcollectionitem.php`, `ezproductcollectionitemoption.php` |
| VAT | `kernel/classes/ezvattype.php`, `ezvatrule.php`, `ezvatmanager.php`, `vathandlers/` |
| Shipping and basket info | `kernel/classes/ezshippingmanager.php`, `basketinfohandlers/` |
| Shop accounts and the order mail | `kernel/classes/ezshopaccounthandler.php`, `shopaccounthandlers/`, `ezconfirmorderhandler.php`, `confirmorderhandlers/` |
| Discounts and wishlist | `kernel/classes/ezdiscount*.php`, `ezuserdiscountrule.php`, `ezwishlist.php` |
| Payment base classes | `kernel/shop/classes/ezpaymentgateway.php`, `ezredirectgateway.php`, `ezpaymentobject.php`, `ezpaymentcallbackchecker.php` |
| Workflow event types used by the shop | `kernel/classes/workflowtypes/event/ezpaymentgateway/`, `ezsimpleshipping/`, `ezmultiplexer/` |
| Currencies and exchange rates | `kernel/shop/classes/ezcurrencydata.php`, `ezcurrencyconverter.php`, `exchangeratehandlers/` |
| Shop templates of the standard design | `design/standard/templates/shop/` |

## Extension points at a glance

| You want to | Extension point | Selected by | Page |
|---|---|---|---|
| Charge shipping by your own rules | Shipping handler | `shop.ini [ShippingSettings] Handler` | [Custom shipping handler](../extending/custom-shipping-handler.md) |
| Decide VAT by your own rules | VAT handler | `shop.ini [VATSettings] Handler` | [Custom VAT handler](../extending/custom-vat-handler.md) |
| Take payment with another provider | Payment gateway | `paymentgateways.ini`, a payment gateway event | [Payment gateway development](../extending/payment-gateway-development.md) |
| Collect different customer data | Shop account handler | `shopaccount.ini [AccountSettings] Handler` | [Shop account handlers](../extending/shop-account-handlers.md) |
| Change the order mail | Confirm order handler | `shopaccount.ini [ConfirmOrderSettings] Handler` | [Basket info and confirm order handlers](../extending/basket-info-and-confirm-order-handlers.md) |
| Change basket totals | Basket info handler | `shop.ini [BasketInfoSettings] Handler` | same |
| Fetch exchange rates elsewhere | Exchange rates update handler | `shop.ini [ExchangeRatesSettings] ExchangeRatesUpdateHandler` | [Exchange rate handlers](../extending/exchange-rate-handlers.md) |
| A price field of your own | Price datatype | `site.ini [ShopSettings] ProductDatatypeStringList[]` | [Custom price datatypes](../extending/custom-price-datatypes.md) |
| Run code at a shop step | Workflow event type on a shop trigger | `workflow.ini` | [Workflows on shop triggers](../checkout/workflows-on-shop-triggers.md) |

## Generators that save time

The RAD tools in **Setup** write working skeletons into an extension:

- `setup/handlerextension/<kind>` (policy `setup/setup`) has recipes for a payment gateway (redirect and transparent),
  a VAT handler, a shipping handler, a basket info handler and an exchange rate handler, each with the methods to
  write and the setting it needs ([Extensions](../../../guides/extensions.md#63-the-handler-wizard-step-by-step)).
- `setup/workflowevent` writes a workflow event type
  ([Writing a workflow event type](../../creators/app-creators/writing-a-workflow-event-type.md)).

Check a generated file against the loader paths described on each extension page before you rely on it, and run
`php bin/php/checkclasses.php --quiet-ok` afterwards.

## Reading order

1. [Running a shop: overview](../../creators/website-creators/shop-overview.md) and
   [Basket, checkout and orders](../../creators/website-creators/basket-checkout-and-orders.md): the parts.
2. [Checkout operations step by step](../checkout/checkout-operations-step-by-step.md) and
   [Temporary orders and activation](../checkout/temporary-orders-and-activation.md): what runs when.
3. [Shop settings walkthroughs](../settings/README.md): every setting.
4. [Workflows on shop triggers](../checkout/workflows-on-shop-triggers.md) and
   [Payment gateway event](../checkout/payment-gateway-event.md).
5. The page of the extension point you need in [Extending the shop](../extending/README.md).
6. [Testing a store](../operations/testing-a-store.md) and [Troubleshooting checkout](../operations/troubleshooting-checkout.md).

## Rules of thumb

- Put shop settings in `settings/override/` or a siteaccess folder, never in the shipped `settings/*.ini`
  ([Shop settings walkthroughs](../settings/README.md#where-shop-settings-go)).
- Put handlers and gateways in an extension of your own, not in `kernel/`
  ([Packaging as an extension](../../creators/app-creators/packaging-as-an-extension.md)).
- Never log or store card data; the audit trail records payments without it, and so should your code
  ([Security for applications](../../creators/app-creators/security-for-applications.md)).
- Test every change with a full order in the provider's test mode ([Testing a store](../operations/testing-a-store.md)).

## Related pages

- [Paths by role](README.md)
- [App creators: start here](../../creators/app-creators/app-creators-start-here.md)
- [Workflows, triggers and events](../../creators/app-creators/workflows-and-events.md)
- [Headless commerce with expservices](../operations/headless-commerce-with-expservices.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
