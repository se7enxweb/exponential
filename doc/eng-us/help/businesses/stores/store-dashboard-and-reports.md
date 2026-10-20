---
title: Store dashboard and reports
audience: businesses, admin, project-manager
category: business-solutions
order: 120
summary: What the Store dashboard tells a business owner - sales, customers, open orders, abandoned baskets, best sellers and setup advice - plus the order statistics and how to use them in a weekly review.
keywords: store dashboard, shop dashboard, reports, revenue, sales figures, open orders, abandoned baskets, best sellers, statistics, next steps, KPIs
type: help
---
# Store dashboard and reports

This page is for owners and managers who want to know how their store is doing without building reports. It explains
what the Store dashboard shows, how to read it, and how to use it in a short weekly review. The reference is
[Store dashboard and order statuses](../../../features/6.0/store-dashboard.md).

## Contents

1. [Opening the dashboard](#opening-the-dashboard)
2. [At a glance](#at-a-glance)
3. [Charts and rankings](#charts-and-rankings)
4. [Work to do](#work-to-do)
5. [How your shop works](#how-your-shop-works)
6. [Next steps](#next-steps)
7. [Order statistics](#order-statistics)
8. [A weekly review](#a-weekly-review)
9. [What the dashboard does not do](#what-the-dashboard-does-not-do)

## Opening the dashboard

Sign in to the administration and click **Store** (the tab was called Webshop before 30 September 2026). The dashboard
is the first entry of the shop menu; it needs the `shop/administrate` policy, like the order list and the statistics.
Titles and texts are translated, German included.

## At a glance

| Figure | What it tells you |
|---|---|
| Orders today, in 7 and in 30 days, with the trend against the 30 days before | Whether business is growing |
| Revenue and average order, per currency | What the orders are worth |
| Customers: new, returning, all time | Whether customers come back |
| Open orders, and how many wait too long | Whether the team keeps up |
| Baskets in progress and abandoned | Where customers drop out |
| Products, and products without a price | Catalog gaps |

All of this comes from the feature page. Totals use the arithmetic of the order list, so the dashboard and the list
agree. Failed, canceled and refunded orders count as orders but not as revenue
([Orders and order statuses](orders-and-order-statuses.md#what-finished-and-no-revenue-mean)).

## Charts and rankings

- **Revenue per day**, to see the effect of a campaign or a holiday.
- **Order status breakdown**, to see where orders pile up.
- **Best-selling products**, to decide what to promote and what to restock.

## Work to do

- Orders waiting for the shop, longest first.
- The latest orders, baskets and unconfirmed checkouts.
- The product classes with their price attribute.

An order is open until its status is finished, so canceled and refunded orders leave this list. Orders waiting for
the customer (for example Awaiting payment or Ready for pickup) are highlighted after a shorter limit.

## How your shop works

This section describes the checkout step by step as **this installation** is configured: add to basket, the basket,
the shop account handler, the confirmation page with shipping and VAT, payment (the workflows on the shop triggers and
the available gateways), the order mails and the receipt, and processing through the statuses. Each step names the
setting or admin page that changes it. Order statuses, VAT types, VAT rules, the countries orders came from,
currencies and discount groups follow.

> **Tip**
> Print this section and keep it with your store documentation. When a new person joins, or a partner takes over the
> setup, it shows how your store actually works today.

## Next steps

Suggestions worked out from your data and settings, most urgent first. The feature page names, among others:

- pending orders waiting more than two days;
- no payment step on checkout;
- a 0 percent VAT type in use;
- basket cleanup that would delete baskets kept in PHP sessions.

Treat this list as the store's own checklist: an empty list is a good sign.

## Order statistics

**Store > Statistics** (`shop/statistics`) shows sales per product for a year and month you choose
(`kernel/private/classes/views/shop/orderstatistics.php`). Use it for month-end figures and to compare seasons.

The dashboard computes its figures with a fixed set of about twenty aggregate queries whatever the size of the shop,
written to run the same on Oracle, MySQL, PostgreSQL and SQLite, so it stays quick as the order history grows
([Store dashboard and order statuses](../../../features/6.0/store-dashboard.md#what-is-on-the-page)).

## A weekly review

A suggested fifteen-minute routine for the owner:

1. Orders and revenue for 7 and 30 days: up, flat or down?
2. Open orders and orders waiting too long: is the team keeping up?
3. Abandoned baskets: did a change to shipping or prices make them rise?
4. Best sellers: are they on the home page and in the newsletter?
5. Next steps: anything new?
6. Payment failed orders: is the provider having problems?

A template for the monthly version is in [Monthly operations checklist](../templates/monthly-operations-checklist.md).

## What the dashboard does not do

- It does not export figures to a spreadsheet; the kernel's shop views have no CSV export of orders.
- It does not track visitors; for traffic see [Visitor statistics and analytics](../../creators/website-creators/statistics-and-analytics.md).
- It does not replace your accounting: revenue on the dashboard is order value, not booked income.

If you need exports or accounting links, plan them as an extension or integration
([What the built-in shop does](what-the-built-in-shop-does.md#what-is-not-included)).

## Related pages

- [Store dashboard and order statuses](../../../features/6.0/store-dashboard.md)
- [Order list sorting](../../../features/6.0/order-list-sorting.md)
- [Running a store day to day](running-a-store-day-to-day.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
