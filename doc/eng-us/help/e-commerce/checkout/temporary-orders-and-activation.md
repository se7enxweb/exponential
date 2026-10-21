---
title: Temporary orders and activation
audience: ecommerce, website-creator, app-creator
category: e-commerce
order: 42
summary: Why an Exponential order exists before the customer has paid - the temporary order created at checkout, how it shares the basket's product collection, what activation changes (order number, status history, mail, audit), and why temporary orders never appear in lists or statistics.
keywords: temporary order, is_temporary, activation, activate-order, order number, order_nr, ezorder_nr_incr, product collection, MyTemporaryOrderID, status history, Pending, unconfirmed checkout
type: help
---
# Temporary orders and activation

This page is for developers and store managers who wonder why an order can exist without an order number, or why a
payment provider shows a payment that the order list does not. It explains the temporary order: the record the
checkout works on until payment has finished.

## Contents

1. [What a temporary order is](#what-a-temporary-order-is)
2. [Its life](#its-life)
3. [What activation changes](#what-activation-changes)
4. [Where temporary orders show and where they do not](#where-temporary-orders-show-and-where-they-do-not)
5. [Order id and order number](#order-id-and-order-number)
6. [What this means for you](#what-this-means-for-you)

## What a temporary order is

When the customer checks out, `eZBasket::createOrder()` (`kernel/classes/ezbasket.php`) creates a row in `ezorder`
with `is_temporary = 1`, the status **Pending**, the current user and the basket's product collection. The basket
remembers the order (`order_id`) and the session remembers it as `MyTemporaryOrderID`. Basket and temporary order
share one product collection, so items are not copied.

## Its life

| Moment | State |
|---|---|
| Checkout button, customer data given | Temporary order created, `order_nr = 0` |
| Confirmation page | Shipping lines added as order items; still temporary |
| Cancel on the confirmation page | The basket comes back with its items |
| Payment started (redirect gateway) | A payment object is stored for the workflow process; still temporary |
| Payment confirmed, workflow continues | `activate-order` runs: the order is placed |
| Payment never confirmed | Remains temporary; never shown as an order |

## What activation changes

`eZOrder::activate()` (`kernel/classes/ezorder.php`), run by the `activate-order` method of the checkout operation:

1. `is_temporary` becomes `0`.
2. The order gets its **order number** from the separate counter table `ezorder_nr_incr`.
3. A first entry in the status history is created for the current status.
4. When the audit trail is active, the event `commerce.order.create` is recorded with the order number, total and
   currency, never the customer's address ([Commerce audit trail](../operations/commerce-audit-trail.md)).

Right after activation the checkout operation sends the order mail (`send-order-email`) and runs the after trigger of
`shop_checkout`. The basket is emptied when checkout finishes, unless `ClearBasketOnCheckout=enabled` emptied it
earlier ([site.ini shop settings walkthrough](../settings/site-ini-shop-settings-walkthrough.md)).

## Where temporary orders show and where they do not

Order list, archive, customer list, order statistics and counts all filter on `is_temporary = 0`
(`kernel/classes/ezorder.php`). Temporary orders therefore do not count as orders or revenue. The Store dashboard lists
"unconfirmed checkouts" under **Work to do**, which is where to look for checkouts that stopped half way
([Store dashboard and order statuses](../../../features/6.0/store-dashboard.md)).

Basket cleanup removes expired baskets and their product collections (`eZBasket::cleanupExpired()`); in the code
we checked it does not delete rows from `ezorder`.

## Order id and order number

| | Order id (`id`) | Order number (`order_nr`) |
|---|---|---|
| Given | When the temporary order is created | When the order is activated |
| Gaps | Yes: every abandoned checkout uses an id | Only placed orders get one |
| Shown to | Internal addresses such as `shop/orderview/<id>` | Customers, in the order view, mail and receipt |

Tell customers and staff to quote the order number. Remote services can look up an order by number
(`exporder::byNumber`, [Headless commerce with expservices](../operations/headless-commerce-with-expservices.md)).

## What this means for you

- A provider's payment without an order in the list means activation never ran: the callback did not arrive or the
  workflow did not continue ([Troubleshooting checkout](../operations/troubleshooting-checkout.md)).
- Events that need the order number or must run once per placed order belong on the after trigger of `shop_checkout`,
  not on the before trigger ([Workflows on shop triggers](workflows-on-shop-triggers.md)).
- Reports built on the `ezorder` table must filter `is_temporary = 0`, like the kernel does.

## Related pages

- [Checkout operations step by step](checkout-operations-step-by-step.md)
- [Order lifecycle operations](../orders/order-lifecycle-operations.md)
- [Orders and order statuses](../../businesses/stores/orders-and-order-statuses.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
