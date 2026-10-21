---
title: VAT, shipping and pickup
audience: local-business, businesses, customer
category: local-business
order: 44
summary: VAT and delivery for a local business shop on Exponential - VAT types and rules, prices shown with tax, the flat simple shipping workflow, pickup in the shop with the Ready for pickup status, local delivery by your own staff, and what needs a shipping handler.
keywords: VAT, sales tax, VAT types, VAT rules, shipping, flat shipping, ezsimpleshipping, pickup, click and collect, local delivery, Ready for pickup, shipping handler, local business
type: help
---
# VAT, shipping and pickup

This page is for the owner of a local business shop who decides how taxes and delivery work. It summarizes the
choices and links to the full pages. Tax rules are set by your country; agree them with your accountant. This is not
tax advice.

## Contents

1. [VAT in plain words](#vat-in-plain-words)
2. [Three ways to get goods to customers](#three-ways-to-get-goods-to-customers)
3. [Pickup in the shop](#pickup-in-the-shop)
4. [Local delivery by your own staff](#local-delivery-by-your-own-staff)
5. [Shipping by parcel](#shipping-by-parcel)
6. [Checklist](#checklist)

## VAT in plain words

The shop has **VAT types** (a name and a percentage, for example a standard and a reduced rate) and, for selling to
several countries, **VAT rules** by customer country and product category
([VAT rules and VAT types](../../businesses/stores/vat-rules-and-vat-types.md)). Each product's price field names
its VAT type and whether the price is entered with or without VAT.

For a local business that sells in its own country, one or two VAT types are usually enough: for example food at a
reduced rate and other goods at the standard rate. Your accountant tells you which rate applies to which product.

Routine checks: [VAT checklist](../../customers/commerce/vat-checklist.md) and
[VAT types and rules for store owners](../../customers/commerce/vat-types-and-rules.md).

## Three ways to get goods to customers

| Way | With the shipped tools | Extra work |
|---|---|---|
| Pickup in the shop | yes: no shipping line, status **Ready for pickup** | tell customers where and when |
| Local delivery by your staff | yes: a flat fee with the simple shipping workflow | a delivery area rule is not built in |
| Parcel by a carrier | yes: a flat fee | rates by weight or zone need a shipping handler |

Source: [Shipping and handling](../../businesses/stores/shipping-and-handling.md).

## Pickup in the shop

The shipped order statuses include **Ready for pickup** (status 11), and the Store dashboard highlights orders waiting
for the customer ([Store dashboard and order statuses](../../../features/6.0/store-dashboard.md)). A pickup routine:

1. The order comes in; staff prepare it.
2. Staff set the status to Ready for pickup and tell the customer (a short mail or call).
3. On collection, staff set the order to delivered or completed (as your status path defines,
   [Order statuses](../../customers/commerce/order-statuses.md)).
4. Uncollected after the agreed days: call, then cancel and refund as your terms say.

Put the pickup address, hours and what to bring (order number, ID for age-restricted goods) on the checkout page and
in the confirmation mail ([Order confirmation mail](../../customers/commerce/order-confirmation-mail.md)).

## Local delivery by your own staff

The simple shipping workflow (`ezsimpleshipping`) adds one fixed amount to every order
(`workflow.ini [SimpleShippingWorkflow]`, `ShippingCost`, `ShippingDescription`). For delivery in town, set it to your
local delivery fee. What it cannot do:

- refuse addresses outside your area: say the area clearly before checkout, and check the address on each order;
- offer a choice between pickup (free) and delivery (fee) in the same shop: that needs a shipping handler or template
  work by a developer;
- book a delivery time slot: not built in; agree the time by phone or mail.

## Shipping by parcel

A flat fee covers most small parcels. Free shipping above an order value, rates by zone or weight, or carrier rates
need a **shipping handler** written by a developer; none ships with the kernel
([Shipping and handling](../../businesses/stores/shipping-and-handling.md#a-shipping-handler-of-your-own)). Labels
and tracking numbers are made in the carrier's own portal ([Shipping and handling for store owners](../../customers/commerce/shipping-and-handling.md)).

## Checklist

- [ ] VAT types set and confirmed by your accountant.
- [ ] Each product has the right VAT type.
- [ ] Delivery way chosen: pickup, local delivery, parcel, or a combination that the builder can support.
- [ ] Flat fee and its description set (if used).
- [ ] Pickup address, hours and rules on checkout page and in the confirmation mail.
- [ ] A test order for each way shows the right totals.

## Related pages

- [Country groups and regions](../../businesses/stores/country-groups-and-regions.md)
- [Basket and checkout flow](../../businesses/stores/basket-and-checkout-flow.md)
- [Store launch checklist](../../businesses/stores/store-launch-checklist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
