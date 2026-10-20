---
title: Orders day to day
audience: customer, admin, businesses
category: customer-success
order: 80
summary: Handling orders in a running Exponential store - the path of one order, the screens the team uses, who may change what, customer questions, archiving, and the mistakes that cause most support calls.
keywords: orders, order handling, order list, order view, order status, archive, customer questions, packing, shipping, shop/orderlist, shop/orderview
type: help
---
# Orders day to day

This page is for the store manager and the people who pack, ship and answer customers. It describes how a team works
with orders in Exponential every day. The screens and statuses are described for businesses on
[Orders and order statuses](../../businesses/stores/orders-and-order-statuses.md); the morning routine is on
[Daily store routine](daily-store-routine.md).

## The screens

| Screen | Address | Use it for | Needs |
|---|---|---|---|
| Store dashboard | `shop/dashboard` | What needs doing now | `shop/administrate` |
| Order list | `shop/orderlist` | All active orders, sortable by every column | `shop/administrate` |
| Order view | `shop/orderview/<id>` | One order: items, customer, address, status history | `shop/administrate` from the list |
| Archive | `shop/archivelist` | Finished orders taken out of the active list | `shop/administrate` |
| Customers | `shop/customerlist`, `shop/customerorderview` | Who bought, and one customer's orders | `shop/administrate` |

Source: `kernel/shop/module.php`. The order list sort is part of the address, for example
`/shop/orderlist/(sort)/created/(dir)/asc` for the oldest first, so each person can bookmark the view they work from
([Order list sorting](../../../features/6.0/order-list-sorting.md)).

## The path of one order

1. The order arrives as **Pending**, or as the status your payment step sets on payment
   ([Payment gateways](payment-gateways.md)).
2. Someone checks it: items and options, address, the customer's comment.
3. It is packed (**Packed**), held (**On hold**) or waits for goods (**Backordered**).
4. It leaves (**Shipped**) or waits at the counter (**Ready for pickup**).
5. It ends (**Completed** or **Delivered**), or is stopped (**Cancelled**).
6. After the end it may come back (**Return requested**, **Returned**, **Refunded**,
   **Partially refunded**); see [Returns and refunds](returns-and-refunds.md).

Your store will not use every status. Write down the path your team uses on [Order statuses](order-statuses.md).

## Who may change what

Changing a status needs `shop/setstatus`, which can be limited with `FromStatus` and `ToStatus`
(`kernel/shop/module.php`). Each change is kept in the order's status history
(`kernel/classes/ezorderstatushistory.php`).

| Person | A sensible limit |
|---|---|
| Packing | From Paid to Packed, and from Packed to Shipped |
| Customer service | To Return requested and Returned |
| Store manager | Every fulfillment status, Cancelled |
| Owner | Every status, including Refunded |

Ask your administrator to build these as roles ([Roles and policies](../../../guides/roles-and-policies.md)).

## Customer questions

| The customer says | Look at | Then |
|---|---|---|
| "Where is my order?" | Order view: status and status history | Give the status and, if shipped, the tracking number |
| "I lost the confirmation" | The order's receipt link, if permanent receipts are on | Send the link again ([Invoices and receipts](invoices-and-receipts.md)) |
| "I paid but have no order" | Your payment provider's account, then orders in Pending or Payment failed | See [Payment gateways](payment-gateways.md) |
| "Please change my address" | The order view | Agree in your team how changes are made before the order is Shipped |
| "Cancel my order" | The order's status | Set Cancelled; refund in the provider's account if it was paid |
| "I never got the mail" | The order mailbox copy, the customer's spam folder | See [Order confirmation mail](order-confirmation-mail.md) |

> **Note**
> A status change never moves money. Refunds are made in your payment provider's account; the status records that
> it happened.

## Archiving

Archive finished orders regularly (`shop/archiveorder`), for example once a week, those older than your returns
period. Archived orders leave the active list but are not deleted, and can be brought back (`shop/unarchiveorder`).
Removing orders (`shop/removeorder`) deletes them; your bookkeeping rules may require you to keep order records for
years, so archive rather than remove ([Data export and erasure](data-export-and-erasure.md)).

## Mistakes that cause support calls

- Statuses set at the end of the day instead of when the work happened.
- Orders left in Pending because nobody owns them on a given day.
- A payment step that marks orders with a status nobody uses; see the PayPal setting on [PayPal](paypal.md).
- Tracking numbers kept in one person's inbox.
- Test orders mixed with real ones; name test customers clearly and remove or cancel their orders.

## Related pages

- [Order statuses](order-statuses.md)
- [Daily store routine](daily-store-routine.md)
- [Orders and order statuses for businesses](../../businesses/stores/orders-and-order-statuses.md)
- [Basket, checkout and orders](../../creators/website-creators/basket-checkout-and-orders.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
