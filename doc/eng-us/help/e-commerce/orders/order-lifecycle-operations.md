---
title: Order lifecycle operations
audience: ecommerce, customer, businesses
category: e-commerce
order: 51
summary: The life of an Exponential order as a process you can operate - from temporary order to archive, the seventeen shipped statuses grouped by stage, which count as open, finished or without revenue, three example processes, and custom statuses.
keywords: order lifecycle, order process, order statuses, Pending, Paid, Shipped, Completed, Cancelled, Refunded, finished, open order, no revenue, waiting for customer, custom status, status 1000, process design
type: help
---
# Order lifecycle operations

This page is for merchants and store managers who design how orders move through an Exponential store, and for
developers who build on it. It turns the seventeen shipped statuses into processes you can adopt, and explains what
each status means for the dashboard's "open" orders and revenue.

## Contents

1. [The stages](#the-stages)
2. [What a status means to the system](#what-a-status-means-to-the-system)
3. [Three example processes](#three-example-processes)
4. [Custom statuses](#custom-statuses)
5. [Who moves the order](#who-moves-the-order)
6. [A status process worksheet](#a-status-process-worksheet)

## The stages

| Stage | What happens | Statuses |
|---|---|---|
| Checkout | Temporary order, not yet visible | (Pending, internal) |
| Placed | Activated, numbered, mailed | Pending |
| Payment | Waiting for, receiving or failing payment | Awaiting payment, Paid, Payment failed |
| Fulfillment | Preparing and sending | Processing, On hold, Backordered, Packed, Shipped, Ready for pickup |
| End | Finished one way or another | Delivered, Completed, Cancelled |
| After the end | Returns and refunds | Return requested, Returned, Partially refunded, Refunded |
| Archive | Out of the order list, kept for records | any status, archived flag set |

The temporary stage is described in [Temporary orders and activation](../checkout/temporary-orders-and-activation.md).

## What a status means to the system

`eZOrderStatus` (`kernel/classes/ezorderstatus.php`) has three helper lists that the Store dashboard uses
([Store dashboard and order statuses](../../../features/6.0/store-dashboard.md#order-statuses-for-the-whole-lifecycle)):

| Helper | Statuses | Effect on the dashboard |
|---|---|---|
| `finishedStatusIDs()` | Delivered, Completed, Cancelled, Partially refunded, Refunded | No longer an open order |
| `noRevenueStatusIDs()` | Payment failed, Cancelled, Refunded | Counted as orders, not in revenue, average or top products |
| `waitingForCustomerStatusIDs()` | Pending, Awaiting payment, Payment failed, Ready for pickup | Highlighted after the shorter waiting limit |

So the choice of status is not cosmetic: an order left in Shipped stays "open" forever, and a refund recorded as
Completed still counts as revenue.

## Three example processes

**Small parcel store**

```text
Pending -> Paid -> Packed -> Shipped -> Completed
                 \-> On hold (problem) -> Packed
Payment failed -> (customer retries) or Cancelled
```

**Click and collect**

```text
Pending -> Paid -> Processing -> Ready for pickup -> Completed
```

**Made to order with returns**

```text
Pending -> Awaiting payment -> Paid -> Processing -> Backordered -> Shipped -> Delivered
Delivered -> Return requested -> Returned -> Refunded or Partially refunded
```

Which status payment sets depends on the gateway: with `ezpaypal` it is `paypal.ini [OrderSettings] PaymentMadeOrderStatusID`
([PayPal payments](../../businesses/stores/paypal-payments.md)). Returns are handled as described in
[Returns and refunds](../../customers/commerce/returns-and-refunds.md).

## Custom statuses

Statuses with an id below 1000 are internal and cannot be removed; custom statuses start at 1000. Create them in
**Store > Order status** (`shop/status`, policy `shop/edit_status`). A custom status is not in any of the three helper
lists, so the dashboard treats it as open and as revenue. Use one when you need a step the shipped list does not have,
for example "Waiting for engraving", and not to rename a shipped meaning.

## Who moves the order

| Change | Usually made by |
|---|---|
| Pending to Paid | The payment gateway, automatically |
| Paid to Packed, Shipped | Packing staff, by hand |
| Shipped to Completed | Store manager, by hand, after delivery or a fixed period |
| To Cancelled, Refunded | Store manager or owner, after the money side is settled |

Every change is recorded in `ezorder_status_history` with the user and time, and, with the audit trail active, as
`commerce.order.status` ([Commerce audit trail](../operations/commerce-audit-trail.md)). Who may make which change is
set by policy ([Status transitions and permissions](status-transitions-and-permissions.md)). Automatic status changes
other than the gateway's need an event or script of your own.

## A status process worksheet

| Question | Your answer |
|---|---|
| Which status does payment set? | |
| Which statuses do packing staff set? | |
| When is an order Completed? | |
| Who may cancel, and only before which status? | |
| Which statuses mean "after the end"? | |
| When are orders archived? | |
| Which custom statuses, if any? | |

## Related pages

- [Order operations](README.md)
- [Order statuses (running your store)](../../customers/commerce/order-statuses.md)
- [Orders and order statuses (businesses)](../../businesses/stores/orders-and-order-statuses.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
