---
title: Order operations
audience: ecommerce, customer, businesses
category: e-commerce
order: 50
summary: Operating orders in an Exponential store beyond the daily routine - the order lifecycle in data and statuses, status transitions limited by policy, archive against remove against purge, and the order mail and receipts from an operator's point of view.
keywords: order operations, order lifecycle, order status, status transitions, FromStatus, ToStatus, archive, remove order, purge, order mail, receipts, store operations
type: help
---
# Order operations

This part is for store managers, merchants and the developers who support them. The daily handling of orders is in
[Orders day to day](../../customers/commerce/orders-day-to-day.md) and the statuses in plain language in
[Order statuses](../../customers/commerce/order-statuses.md). These pages explain what the system does underneath,
so you can design a status process, grant the right permissions and choose safely between archiving and deleting.

## Contents

1. [The pages of this part](#the-pages-of-this-part)
2. [An order in one table](#an-order-in-one-table)
3. [Where orders are handled](#where-orders-are-handled)

## The pages of this part

| Page | Answers |
|---|---|
| [Order lifecycle operations](order-lifecycle-operations.md) | The stages of an order, the seventeen statuses as a process, revenue and "open" rules |
| [Status transitions and permissions](status-transitions-and-permissions.md) | How `FromStatus` and `ToStatus` limit who may change what, with role designs |
| [Archive, remove and cleanup](archive-remove-and-cleanup.md) | What archiving, removing and the purge of all orders do to the data, and the audit events |
| [Order mail and receipts for operators](order-mail-and-receipts-for-operators.md) | Who gets which mail, from which address, and how receipts help customer service |

## An order in one table

| Part | Stored in | Notes |
|---|---|---|
| The order | `ezorder` | id, order number, user, e-mail, created, status, archived flag, temporary flag, customer data (`data_text_1`) |
| Product lines | `ezproductcollection_item` (via the order's product collection) | name, price, VAT, discount, count at the time of buying |
| Chosen options | `ezproductcollection_item_opt` | name, value, price of each option |
| Extra lines | `ezorder_item` | shipping and other lines added by events |
| Status history | `ezorder_status_history` | status, who changed it, when |
| Currency | `ezproductcollection.currency_code` | the currency the order was placed in |

Because the product lines keep the name and price at the time of buying, later changes to a product never change an
existing order.

## Where orders are handled

| Screen | Address | Policy |
|---|---|---|
| Order list | `shop/orderlist` | `shop/administrate` |
| Order view | `shop/orderview/<id>` | `shop/buy` for customers' own orders; staff manage from the Store tab |
| Status change | `shop/setstatus` | `shop/setstatus` |
| Status list | `shop/status` | `shop/edit_status` |
| Archive | `shop/archivelist`, `shop/archiveorder`, `shop/unarchiveorder` | `shop/administrate` |
| Remove | `shop/removeorder` | `shop/administrate` |
| Customers | `shop/customerlist`, `shop/customerorderview` | `shop/administrate` |

## Related pages

- [Orders and order statuses](../../businesses/stores/orders-and-order-statuses.md)
- [Store dashboard and order statuses](../../../features/6.0/store-dashboard.md)
- [Order list sorting](../../../features/6.0/order-list-sorting.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
