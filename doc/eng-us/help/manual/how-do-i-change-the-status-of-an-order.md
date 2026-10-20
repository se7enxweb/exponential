---
title: How do I change the status of an order?
audience: admin, customer
category: manual
order: 110
summary: Find the order in Store > Orders, choose its new status in the Status column and click Apply changes; open the order first to check its products, addresses and history.
keywords: order, order status, order list, shop/orderlist, pending, processing, delivered, shipped, apply changes, shop/setstatus
context: shop/orderlist
type: help
---
# How do I change the status of an order?

Choose the new status in the order list and apply it. Every change is kept in the order's status history.

## Who can do this

Users whose role has `shop/administrate` to see the order list, and `shop/setstatus` to change a status. The policy
`shop/setstatus` can be limited to certain statuses, so that, for example, a packer may only set **Shipped**.

## Steps

1. Click **Store**, then **Orders** in the left menu.
   The list shows **ID**, **Customer**, **Total (ex. VAT)**, **Total (inc. VAT)**, **Time** and **Status**.
2. Click a column heading to sort by it, for example **Time** to see the oldest orders first.
3. Click the order's **ID** to check the products, the addresses and the status history, then go back to the list.
4. In the **Status** column of the order, choose the new status.
5. Click **Apply changes**.
   The list shows the new status. If the shop sends a mail for that status, it goes out now.

![The order list with the columns ID, Customer, Total, Time and Status, and the buttons Archive selected and Apply changes](images/store-order-list.webp)

## What can go wrong

- **The status menu is grayed out or missing.** Your role may see orders but not set this status. Ask an
  administrator for `shop/setstatus`.
- **You changed several menus but only some were saved.** Click **Apply changes** before you leave the page or go to
  another page of the list.
- **The status you need does not exist.** An administrator adds it under **Store** > **Order status**. Statuses with an
  identifier below 1000 come with Exponential and cannot be removed.

## Related pages

- [Order statuses](../customers/commerce/order-statuses.md)
- [Orders day to day](../customers/commerce/orders-day-to-day.md)
- [Sort the order list by any column](../../features/6.0/order-list-sorting.md)
- [How do I archive finished orders?](how-do-i-archive-finished-orders.md)
- [User manual](README.md)

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
