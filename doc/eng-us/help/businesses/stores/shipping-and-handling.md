---
title: Shipping and handling
audience: businesses, admin, website-creator
category: business-solutions
order: 70
summary: What shipping options an Exponential store has out of the box - the flat simple shipping workflow and custom shipping handlers - and how a business chooses and grows its shipping model.
keywords: shipping, shipping cost, delivery, handling fee, simple shipping, ezsimpleshipping, shipping handler, free shipping, pickup, parcel, shipping labels
type: help
---
# Shipping and handling

This page is for business owners who decide how customers pay for delivery. It describes the shipping tools that ship
with Exponential, what each can and cannot do, and how to grow from a flat shipping fee to rules by zone or weight.
The setup is described in [Basket, checkout and orders](../../creators/website-creators/basket-checkout-and-orders.md).

## Contents

1. [Two shipping tools](#two-shipping-tools)
2. [The flat shipping workflow](#the-flat-shipping-workflow)
3. [A shipping handler of your own](#a-shipping-handler-of-your-own)
4. [Common shipping models](#common-shipping-models)
5. [Pickup and local delivery](#pickup-and-local-delivery)
6. [What is not included](#what-is-not-included)
7. [Checklist](#checklist)

## Two shipping tools

| Tool | What it does | Effort |
|---|---|---|
| Simple shipping workflow event (`ezsimpleshipping`) | Adds one fixed shipping line to every order | Configuration only |
| Shipping handler | Calculates shipping with your own rules, shown in the basket and on confirmation | Developer work |

Both are named in [Running a shop: overview](../../creators/website-creators/shop-overview.md#setting-up-a-shop-in-order):
"a shipping handler or the simple shipping workflow in `workflow.ini`".

## The flat shipping workflow

The simple shipping event type is in `kernel/classes/workflowtypes/event/ezsimpleshipping/`. What its code does:

- It reads the cost and the line's description from `settings/workflow.ini`, `[SimpleShippingWorkflow]`. The shipped
  values are `ShippingCost=12` and `ShippingDescription=Shipping`; your builder sets your own in an override.
- It adds the cost once per order, as an extra order line of the type `ezsimpleshipping`, and does not add it twice
  if the step runs again.
- It converts the amount into the order's currency.

It runs as a workflow on the trigger `before_shop_confirmorder`, so the customer sees the shipping cost on the
confirmation page before paying ([Basket, checkout and orders](../../creators/website-creators/basket-checkout-and-orders.md#workflow-triggers),
[Workflows](../../../guides/workflows.md)).

> **Note**
> The flat workflow charges the same amount for one small item and for a full basket, and for every country. That is
> fine for many small stores with similar parcels. If your parcels vary a lot, plan a shipping handler.

## A shipping handler of your own

`settings/shop.ini` has a `[ShippingSettings]` block with a `Handler` setting (commented out in the shipped file) and
directories where handlers are looked up, including `extension/<your_extension>/shippinghandlers`. The comments in
that file describe the naming: a handler called `ezcustom` is the class `eZCustomShippingHandler`. The kernel's
`eZShippingManager` (`kernel/classes/ezshippingmanager.php`) asks the handler for the shipping information of a basket,
updates it when the basket changes, and removes it when the basket is emptied.

No shipping handler is shipped with the kernel: the default `kernel/classes/shippinghandlers` directory named in
`shop.ini` does not exist in this release. A handler is written by a developer or a partner, usually as part of an
extension ([Packaging as an extension](../../creators/app-creators/packaging-as-an-extension.md)).

What a handler can base its cost on is up to you: weight fields on products, the customer's country, the basket total,
or a call to a carrier's rate service.

## Common shipping models

| Model | With the shipped tools | Needs a handler |
|---|---|---|
| One flat fee per order | yes, simple shipping | no |
| No shipping cost at all (services, files) | yes, leave the shipping workflow out | no |
| Free shipping above an order value | no | yes |
| Cost by zone or country | no | yes |
| Cost by weight or number of items | no | yes, plus a weight field on products |
| Live carrier rates | no | yes, plus the carrier's service |
| Customer chooses standard or express | no | yes, plus template work |

## Pickup and local delivery

The shipped order statuses include **Ready for pickup** (status 11) and **Shipped** (status 10)
([Store dashboard and order statuses](../../../features/6.0/store-dashboard.md#order-statuses-for-the-whole-lifecycle)).
A shop that offers pickup can use them to tell staff and customers where an order stands, even with flat or no shipping
cost. Ready for pickup counts as "waiting for the customer", so the dashboard highlights it after the shorter waiting
limit.

## What is not included

The technical overview lists shipping labels among the things to plan as extensions or integrations
([Running a shop: overview](../../creators/website-creators/shop-overview.md#what-the-shop-does-not-do)). Tracking
numbers, label printing and carrier booking happen outside Exponential unless you add them. Many small businesses
start by creating labels in the carrier's own portal and noting the tracking number on the order.

## Checklist

- [ ] Shipping model chosen: none, flat, or rules (handler).
- [ ] With the flat workflow: cost and description set in an override of `[SimpleShippingWorkflow]`.
- [ ] The workflow is connected to `before_shop_confirmorder`.
- [ ] Shipping cost and its VAT treatment confirmed with your tax adviser.
- [ ] A test order shows the shipping line on the confirmation page, in each active currency.
- [ ] Customers can read your shipping terms before checkout.

## Related pages

- [Basket, checkout and orders](../../creators/website-creators/basket-checkout-and-orders.md)
- [Basket and checkout flow](basket-and-checkout-flow.md)
- [Orders and order statuses](orders-and-order-statuses.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
