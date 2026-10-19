---
title: Orders and order statuses
audience: businesses, admin, editor
category: business-solutions
order: 100
summary: How orders are handled in an Exponential store - the order list and order view, the seventeen shipped statuses from Pending to Refunded, custom statuses, who may change them, archiving and customers.
keywords: orders, order status, order list, order view, pending, paid, shipped, completed, canceled, cancelled, refunded, returns, archive, custom status, setstatus
type: help
---
# Orders and order statuses

This page is for shop managers and order handlers. It explains where orders appear, what each shipped order status
means, how to model your own process with them, and how to limit who may change a status. The reference is
[Store dashboard and order statuses](../../../features/6.0/store-dashboard.md).

## Contents

1. [Where orders live](#where-orders-live)
2. [The shipped statuses](#the-shipped-statuses)
3. [What finished and no revenue mean](#what-finished-and-no-revenue-mean)
4. [Mapping your process](#mapping-your-process)
5. [Custom statuses](#custom-statuses)
6. [Who may change a status](#who-may-change-a-status)
7. [Archiving and removing orders](#archiving-and-removing-orders)
8. [Customers](#customers)

## Where orders live

| Screen | Address | Needs |
|---|---|---|
| Store dashboard | `shop/dashboard` | `shop/administrate` |
| Order list | `shop/orderlist` | `shop/administrate` |
| Order view | `shop/orderview/<id>` | `shop/buy` for the customer's own order; staff use it from the list |
| Archive | `shop/archivelist` | `shop/administrate` |
| Customers | `shop/customerlist`, `shop/customerorderview` | `shop/administrate` |
| Statistics | `shop/statistics` | `shop/administrate` |

Source: `kernel/shop/module.php`. The order list sorts by every column (ID, customer, both totals, time, status), and
the sort is kept in the address, so a link such as "largest orders first" can be bookmarked
([Order list sorting](../../../features/6.0/order-list-sorting.md)).

## The shipped statuses

Since 6.0.15 every new installation, and every installation after the 6.0.15 database update, has seventeen statuses
([Store dashboard and order statuses](../../../features/6.0/store-dashboard.md#order-statuses-for-the-whole-lifecycle)):

| Stage | Statuses (id) |
|---|---|
| New | Pending (1), Processing (2) |
| Payment | Awaiting payment (4), Paid (5), Payment failed (6) |
| Fulfillment | On hold (7), Backordered (8), Packed (9), Shipped (10), Ready for pickup (11) |
| End | Delivered (3), Completed (12), Cancelled (13) |
| After the end | Return requested (14), Returned (15), Partially refunded (16), Refunded (17) |

Before that release only Pending, Processing and Delivered were shipped, and shops invented their own.

## What finished and no revenue mean

Each status carries three meanings that the dashboard uses (same page):

| Meaning | Statuses | Effect |
|---|---|---|
| Finished | Delivered, Completed, Cancelled, Partially refunded, Refunded | The order is no longer "open" |
| No revenue | Payment failed, Cancelled, Refunded | Counted as an order but not as revenue |
| Waits for customer | Pending, Awaiting payment, Payment failed, Ready for pickup | Highlighted after a shorter waiting limit |

This means the totals on the dashboard stay honest: a refunded order does not inflate revenue, and a canceled order
does not wait in the to-do list.

## Mapping your process

Pick the statuses you need and agree on them with the team. Examples, from simple to detailed:

| Store type | Status path |
|---|---|
| Services, no shipping | Pending, Paid, Completed |
| Small parcel shop | Pending, Paid, Packed, Shipped, Completed |
| Shop with pickup | Paid, Packed, Ready for pickup, Completed |
| Shop with returns | ... Shipped, Return requested, Returned, Refunded |

The PayPal extension sets a status when a payment is confirmed, `paypal.ini [OrderSettings] PaymentMadeOrderStatusID`;
see [PayPal payments](paypal-payments.md) for how to align it with these statuses.

> **Tip**
> Write your status path on one page and pin it next to whoever packs the parcels. A status is only useful if
> everyone sets it the same way.

## Custom statuses

Statuses with an id below 1000 are internal and cannot be removed by mistake; custom statuses start at 1000. Statuses
are managed in `shop/status` (policy `shop/edit_status`). If you used one of the numbers 4 to 17 for a custom status
before 6.0.15, check the status list after the update
([Store dashboard and order statuses](../../../features/6.0/store-dashboard.md#upgrading-an-existing-shop)).

## Who may change a status

Changing a status needs `shop/setstatus`, which has two limitations, `FromStatus` and `ToStatus`
(`kernel/shop/module.php`). That lets you build roles such as:

- **Packing**: may move orders from Paid to Packed and from Packed to Shipped;
- **Customer service**: may move orders to Return requested and Returned;
- **Owner**: may set any status, including Refunded.

Each change is kept in the order's status history (`kernel/classes/ezorderstatushistory.php`), and the audit trail
records commerce events ([Audit trail](../../../features/6.0/audit-trail.md)). Roles are set up as described in
[Roles and policies](../../../guides/roles-and-policies.md).

> **Note**
> Setting a status in Exponential does not move money. A refund is made in your payment provider's account; the status
> records that it was done.

## Archiving and removing orders

Finished orders can be archived (`shop/archiveorder`), which takes them out of the active list without deleting them,
and brought back (`shop/unarchiveorder`). Orders can also be removed (`shop/removeorder`). Keep in mind that your
bookkeeping and tax rules may require you to keep order records for years; archive rather than remove, and see
Data retention and deletion.

## Customers

The customer list shows who bought and how much; the customer order view lists one customer's orders. Both need
`shop/administrate`. The dashboard counts new, returning and all-time customers
([Store dashboard and reports](store-dashboard-and-reports.md)).

## Related pages

- [Store dashboard and order statuses](../../../features/6.0/store-dashboard.md)
- [Order receipts and confirmation mail](order-receipts-and-confirmation-mail.md)
- [Running a store day to day](running-a-store-day-to-day.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
