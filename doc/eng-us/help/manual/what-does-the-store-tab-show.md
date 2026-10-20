---
title: What does the Store tab show?
audience: admin, customer
category: manual
order: 100
summary: The Store tab opens the store dashboard - orders today and in the last 30 days, orders that wait, revenue per currency, how checkout is set up and what to do next - and its left menu leads to orders, products, discounts, VAT and currencies.
keywords: store, shop, webshop, store dashboard, shop/dashboard, orders, revenue, checkout, shop/administrate
context: shop/dashboard
type: help
---
# What does the Store tab show?

The **Store** tab opens the store dashboard: how the shop is doing, which orders need you, and how checkout is set up
on this installation. The tab was called Webshop in older releases.

## Who can do this

Users whose role has the policy `shop/administrate`. Without it the **Store** tab is not shown at all.

## Steps

1. Click **Store** in the top menu.
   The **Store dashboard** opens.
2. Read **At a glance**: orders today, in 7 and in 30 days, revenue and average order per currency, customers, open
   orders and baskets.
3. Look at **Orders** for the orders that wait, longest first. Click an order to open it.
4. Read **How your shop works**: the checkout steps as this installation is configured, each with the page or setting
   that changes it.
5. Work through **Next steps**: suggestions worked out from your data, most urgent first.
6. Use the **Shop** menu on the left for the lists behind the figures: **Orders**, **Products overview**,
   **Product statistics**, **Customers**, **Discounts**, **Archive**, **Order status**, **VAT types**, **VAT rules**
   and **Currencies**.

![The Store dashboard with the sections At a glance and Orders, and the Shop menu on the left](images/store-dashboard.webp)

## What can go wrong

- **The Store tab is missing.** Your role lacks `shop/administrate`, or the tab was left out of the menu settings for
  this siteaccess. Ask an administrator.
- **The figures are all zero.** The shop has had no orders yet. The dashboard counts only the orders of this
  installation's database.
- **A number differs from your payment provider's report.** The dashboard counts orders, not payments. Refunds and
  cancellations made only at the provider are not known to Exponential until the order status is changed.

## Related pages

- [Store dashboard and order statuses](../../features/6.0/store-dashboard.md)
- [Orders day to day](../customers/commerce/orders-day-to-day.md)
- [Daily store routine](../customers/commerce/daily-store-routine.md)
- [Store reports](../customers/commerce/store-reports.md)
- [User manual](README.md)

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
