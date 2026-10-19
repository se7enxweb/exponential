---
title: Shipping and handling
audience: customer, admin, businesses
category: customer-success
order: 130
summary: The shipping routine of a running Exponential store - packing and shipping through the order statuses, tracking numbers, pickup orders, changing the flat shipping cost, and when to ask your partner for shipping rules.
keywords: shipping, handling, packing, shipped, tracking number, pickup, ready for pickup, shipping cost, simple shipping, shipping handler, carrier
type: help
---
# Shipping and handling

This page is for the people who pack and ship, and for the owner who sets shipping terms. It covers the daily
routine and the changes a running store makes. What shipping tools the shop has, and how to grow from a flat fee to
rules by zone or weight, is on [Shipping and handling for businesses](../../businesses/stores/shipping-and-handling.md).

## What your store charges

Out of the box the shop has one shipping tool: the simple shipping workflow event, which adds one fixed shipping line
to every order, once, converted into the order's currency. It runs before the confirmation page, so the customer sees
the cost before paying. Its amount and label come from `settings/workflow.ini [SimpleShippingWorkflow]`, shipped as
`ShippingCost=12` and `ShippingDescription=Shipping`; your partner sets yours in an override
([Shipping and handling for businesses](../../businesses/stores/shipping-and-handling.md)).

Anything more, such as free shipping above an order value, rates by country or weight, or a choice of express, needs a
shipping handler written by a developer (`settings/shop.ini [ShippingSettings]`).

## The packing and shipping routine

| Step | Status to set (`kernel/classes/ezorderstatus.php` names) | Who |
|---|---|---|
| Order checked and paid | Paid, or your payment step's status | Store manager |
| Something is missing | Backordered, or On hold with a note | Packing |
| Parcel ready | Packed | Packing |
| Handed to the carrier | Shipped | Packing |
| Delivered, or no complaint after your agreed period | Completed | Store manager |

Statuses are described on [Order statuses](order-statuses.md). Limit what the packing role may set with `FromStatus`
and `ToStatus` on `shop/setstatus` (`kernel/shop/module.php`).

## Tracking numbers

The shop has no field for a tracking number by default
([Running a store day to day](../../businesses/stores/running-a-store-day-to-day.md)). Agree on one place and
use it every time, for example:

- the carrier's own portal, searchable by your order number;
- a shared shipping log with order number, carrier, tracking number and date;
- a field added to the order process by your partner, if you ship a lot.

Whatever you choose, customer service must be able to find the number from the order number alone.

## Pickup orders

The shipped status **Ready for pickup** (11) counts as "waiting for the customer", so the dashboard highlights such
orders after the shorter waiting limit ([Store dashboard and order statuses](../../../features/6.0/store-dashboard.md)).

- [ ] Tell the customer the order is ready, and how long you keep it.
- [ ] Check the highlighted pickup orders each day and remind the customer.
- [ ] Set Completed when collected; agree what happens to orders never collected.

## Changing the shipping cost

The flat cost is a setting, so a change is a request to your partner or administrator:

1. Decide the new amount, and confirm its VAT treatment with your tax adviser.
2. Ask for the change to `[SimpleShippingWorkflow] ShippingCost` (and the label, if needed) in an override.
3. Agree the moment it goes live, and update your shipping terms page at the same time.
4. Place a test order: the confirmation page shows the new shipping line, in each active currency.

## When the flat fee is not enough

Signs that you need shipping rules:

- many orders where the shipping cost is far from what the carrier charges you;
- customers asking for express delivery or free shipping above an amount;
- orders from countries with very different delivery costs.

Ask your partner for a shipping handler, and give them your carrier's price list, the countries you ship to and the
rule you want ([Shipping and handling for businesses](../../businesses/stores/shipping-and-handling.md) lists the
common models). Labels, carrier booking and tracking stay outside Exponential unless they are added
([Running a shop: overview](../../creators/website-creators/shop-overview.md)).

## Monthly checks

- [ ] Shipping charged to customers compared with the carrier's invoices.
- [ ] Orders that stayed in Packed or Shipped for long: lost parcels, or statuses not set?
- [ ] Your shipping terms page still matches what the shop charges.

## Related pages

- [Shipping and handling for businesses](../../businesses/stores/shipping-and-handling.md)
- [Orders day to day](orders-day-to-day.md)
- [Returns and refunds](returns-and-refunds.md)
- [Basket, checkout and orders](../../creators/website-creators/basket-checkout-and-orders.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
