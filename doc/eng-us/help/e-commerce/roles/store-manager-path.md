---
title: Store manager path
audience: ecommerce, customer, businesses
category: e-commerce
order: 12
summary: A path for the store manager who runs an Exponential store every day - the Store tab screens, the permissions to ask for, how orders move, what the dashboard warns about, and where the routines and checklists are.
keywords: store manager, shop staff, customer service, Store tab, dashboard, order list, order view, order status, archive, customers, returns, permissions, daily work
type: help
---
# Store manager path

This page is for the store manager: the person who opens the **Store** tab every morning, handles orders, answers
customers and keeps the order list clean. It shows which screens you use, which permissions to ask for, and where the
routines are written down.

## Contents

1. [Your screens](#your-screens)
2. [The permissions to ask for](#the-permissions-to-ask-for)
3. [How an order reaches you](#how-an-order-reaches-you)
4. [Moving an order forward](#moving-an-order-forward)
5. [When something looks wrong](#when-something-looks-wrong)
6. [Your routines](#your-routines)

## Your screens

All screens are views of the shop module and live under the **Store** tab of the administration.

| Screen | Address | Use it for |
|---|---|---|
| Dashboard | `shop/dashboard` | The first look of the day: open orders, orders waiting too long, abandoned baskets, "Next steps" |
| Order list | `shop/orderlist` | All active orders; sortable by every column ([Order list sorting](../../../features/6.0/order-list-sorting.md)) |
| Order view | `shop/orderview/<id>` | One order: items, totals, customer data, status and status history |
| Archive | `shop/archivelist` | Finished orders moved out of the order list |
| Customers | `shop/customerlist`, `shop/customerorderview` | Who bought, and every order of one customer |
| Statistics | `shop/statistics` | Products sold and totals per year and month |
| Products overview | `shop/productsoverview` | Products of a class with their prices in one list |

What each dashboard section shows is in [Store dashboard and reports](../../businesses/stores/store-dashboard-and-reports.md).

## The permissions to ask for

| You need to | Ask for |
|---|---|
| See the dashboard, orders, customers and statistics; archive orders | `shop/administrate` |
| Change order statuses | `shop/setstatus`, possibly limited by `FromStatus` and `ToStatus` |
| Nothing in VAT, currencies or discounts | not `shop/setup` |

The limitations are explained in [Status transitions and permissions](../orders/status-transitions-and-permissions.md).

## How an order reaches you

An order appears in the order list only after checkout has finished. Before that it is a **temporary order** that you
do not see in the list; the dashboard counts such cases as unconfirmed checkouts. When checkout completes, the order
gets its order number, a first entry in its status history and the order mail goes out
([Temporary orders and activation](../checkout/temporary-orders-and-activation.md)).

## Moving an order forward

1. Open the order from the dashboard's "Work to do" list or the order list.
2. Check payment: with PayPal, the paid status is the one set in `paypal.ini`
   ([PayPal payments](../../businesses/stores/paypal-payments.md)).
3. Set the next status. Each change is recorded with who made it and when, and is shown in the order's history.
4. When the order is finished (Delivered, Completed, Cancelled, Partially refunded or Refunded), archive it.

The full map of statuses and what each means for revenue is in
[Order lifecycle operations](../orders/order-lifecycle-operations.md).

## When something looks wrong

| You see | Likely cause | Read |
|---|---|---|
| Customer paid but no order in the list | The provider's callback did not reach the site | [Troubleshooting checkout](../operations/troubleshooting-checkout.md) |
| No shipping line on orders | The shipping workflow is not on the confirmation trigger | [Simple shipping event](../checkout/simple-shipping-event.md) |
| You cannot pick a status | Your `setstatus` policy does not allow that transition | [Status transitions and permissions](../orders/status-transitions-and-permissions.md) |
| VAT shows as unknown | VAT rules without a VAT handler, or the customer has no country | [Product categories and VAT rules](../catalog/product-categories-and-vat-rules.md) |

## Your routines

The routines are in the Running your store section; follow them as written:
[Daily store routine](../../customers/commerce/daily-store-routine.md),
[Store weekly checklist](../../customers/commerce/store-weekly-checklist.md),
[Store monthly checklist](../../customers/commerce/store-monthly-checklist.md),
[Returns and refunds](../../customers/commerce/returns-and-refunds.md).

## Related pages

- [Paths by role](README.md)
- [Orders day to day](../../customers/commerce/orders-day-to-day.md)
- [Order statuses](../../customers/commerce/order-statuses.md)
- [Operations calendar](../operations/operations-calendar.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
