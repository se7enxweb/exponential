---
title: Store reports
audience: customer, businesses, project-manager
category: customer-success
order: 190
summary: Turning the Store dashboard and the product statistics of an Exponential store into a reporting routine - what to read daily, weekly and monthly, a one-page monthly report for the owner, and what the reports cannot tell you.
keywords: store reports, store dashboard, product statistics, sales figures, monthly report, revenue, open orders, abandoned baskets, best sellers, KPIs, reporting routine
type: help
---
# Store reports

This page is for the owner and the store manager who report on the store. The shop has two places with figures: the
Store dashboard and the product statistics. This page turns them into a routine and a simple monthly report. What
each figure means is explained on [Store dashboard and reports for businesses](../../businesses/stores/store-dashboard-and-reports.md).

## The two sources

| Source | Address | Shows | Needs |
|---|---|---|---|
| Store dashboard | `shop/dashboard` | Orders today, 7 and 30 days with trend; revenue and average order per currency; customers new, returning, all time; open orders; baskets in progress and abandoned; products without a price; revenue per day; status breakdown; best sellers | `shop/administrate` |
| Product statistics | `shop/statistics` | Sales per product for a year and month you choose | `shop/administrate` |

Sources: [Store dashboard and order statuses](../../../features/6.0/store-dashboard.md), `kernel/shop/module.php`,
`kernel/private/classes/views/shop/orderstatistics.php`. Totals use the arithmetic of the order list, and failed,
canceled and refunded orders count as orders but not as revenue.

## A reporting routine

| When | Who | Reads | Looks for |
|---|---|---|---|
| Daily | Store manager | At a glance, Work to do | Open orders waiting too long; a sudden drop in orders ([Daily store routine](daily-store-routine.md)) |
| Weekly | Owner | 7-day figures, abandoned baskets, best sellers, Next steps | Trends; effects of changes and campaigns |
| Monthly | Owner, bookkeeper | Product statistics for the month; 30-day figures | Month figures for the books; seasonal comparison |
| Yearly | Owner | Product statistics per month across the year | Best and worst months; ranges to grow or drop |

## A one-page monthly report

Copy and fill in once a month. Take figures from the dashboard and the product statistics; write down where each
figure came from, so next month's is comparable.

```text
STORE MONTHLY REPORT - [store name] - [month, year]       Prepared by: ________

Orders in the month: ______      Previous month: ______
Revenue (per currency): ______   Previous month: ______
Average order: ______
New customers: ______            Returning customers: ______
Open orders at month end: ______ Of which waiting too long: ______
Abandoned baskets (dashboard): ______
Refunded or canceled orders: ______

Best sellers (product statistics):
1. ______________  2. ______________  3. ______________

What changed this month (prices, shipping, campaigns, site changes):
__________________________________________________________________

Next steps shown by the dashboard, and what we did:
__________________________________________________________________

Decisions for next month:
__________________________________________________________________
```

## Reading the figures with care

- **Revenue is order value, not booked income.** It is what customers ordered, not what reached your bank after fees
  and refunds; your bookkeeping stays the reference ([Store dashboard and reports for businesses](../../businesses/stores/store-dashboard-and-reports.md)).
- **Statuses drive the numbers.** Open orders and waiting times are only as accurate as the statuses your team sets
  ([Order statuses](order-statuses.md)).
- **Test orders count.** Cancel or remove test orders, or they appear in the figures.
- **Compare like with like.** A month with a campaign or a holiday is not a normal month; note it in the report.

## What the reports do not cover

- No export to a spreadsheet: the shop views have no CSV export of orders. Copy figures by hand, or ask your partner
  for an export ([Store dashboard and reports for businesses](../../businesses/stores/store-dashboard-and-reports.md)).
- No visitor tracking: for traffic and conversion, see
  [Visitor statistics and analytics](../../creators/website-creators/statistics-and-analytics.md).
- No stock figures: the shop has no stock management.

## Related pages

- [Store dashboard and reports for businesses](../../businesses/stores/store-dashboard-and-reports.md)
- [Store dashboard and order statuses](../../../features/6.0/store-dashboard.md)
- [Store monthly checklist](store-monthly-checklist.md)
- [Measuring ROI](../value/measuring-roi.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
