---
title: Simple shipping event
audience: ecommerce, website-creator, businesses
category: e-commerce
order: 45
summary: The flat shipping workflow event of Exponential in detail - what eZSimpleShippingType adds to an order, currency conversion, why it is added only once, its VAT, its limits, and when to replace it with a shipping handler.
keywords: simple shipping, ezsimpleshipping, eZSimpleShippingType, ShippingCost, ShippingDescription, flat shipping, order item, shipping VAT, convertAdditionalPrice, before_shop_confirmorder, shipping handler
type: help
---
# Simple shipping event

This page is for developers and merchants who use the flat shipping that ships with the kernel. The event
**Simple shipping** (`ezsimpleshipping`, `kernel/classes/workflowtypes/event/ezsimpleshipping/ezsimpleshippingtype.php`)
adds one shipping line with a fixed amount to every order. The business view of shipping is in
[Shipping and handling](../../businesses/stores/shipping-and-handling.md); this page describes exactly what the event
does.

## Contents

1. [What the event does](#what-the-event-does)
2. [Currency](#currency)
3. [VAT on the shipping line](#vat-on-the-shipping-line)
4. [What it cannot do](#what-it-cannot-do)
5. [Templates](#templates)
6. [When to move to a shipping handler](#when-to-move-to-a-shipping-handler)

## What the event does

1. It may only be connected to `shop` `confirmorder` before; the class restricts itself with `setTriggerTypes()`.
2. When the confirmation page is prepared, it reads `workflow.ini [SimpleShippingWorkflow] ShippingCost` and
   `ShippingDescription`.
3. It looks at the order's items. If one of type `ezsimpleshipping` is already there, it does nothing; so reloading
   the confirmation page does not add shipping twice.
4. Otherwise it adds an **order item** (`eZOrderItem`) with the description, the amount and the type
   `ezsimpleshipping`, and accepts.

Order items are separate from product items: the order's totals add them (`orderTotalIncVAT()`, `orderTotalExVAT()`
in `kernel/classes/ezorder.php`), and templates list them through the order's `order_items`.

## Currency

The amount is in the shop's currency. Before it is stored, it is converted to the order's currency with
`eZShopFunctions::convertAdditionalPrice()`, so a customer who shops in another currency (with `ezmultiprice`) sees the
converted amount.

## VAT on the shipping line

The event creates the order item with a description, a price and a type only, so the item keeps the table's defaults
for VAT (`vat_value` 0, `is_vat_inc` 0 in `ezorder_item`): the shipping line carries no VAT. Whether shipping should
carry VAT in your country, and how it must be shown, is a question for your tax adviser. If shipping must carry VAT,
or VAT that depends on the products, use a shipping handler: its `getShippingInfo()` returns `vat_value` and
`is_vat_inc` per shipping item ([Custom shipping handler](../extending/custom-shipping-handler.md)).

## What it cannot do

| Need | Simple shipping |
|---|---|
| Free shipping above an amount | No |
| Rates by country or zone | No |
| Rates by weight or size | No |
| Several methods for the customer to choose | No |
| Pickup without shipping cost | No (the line is always added) |
| One flat amount per order | Yes |

## Templates

The standard design's confirmation, order view and order mail templates list order items, so the shipping line shows
without template work. If you build your own checkout templates, list `$order.order_items` with each item's
description and price.

## When to move to a shipping handler

Move as soon as one of the "No" rows above matters to your customers. A handler is selected in
`shop.ini [ShippingSettings] Handler` and calculates shipping from the basket. Remove the simple shipping workflow from
the trigger when you do, so customers are not charged twice. The RAD handler wizard has a shipping handler recipe
([Developer path](../roles/developer-path.md#generators-that-save-time)).

## Related pages

- [Checkout and workflows](README.md)
- [workflow.ini for the shop](../settings/workflow-ini-for-the-shop.md)
- [Shipping and handling (running your store)](../../customers/commerce/shipping-and-handling.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
