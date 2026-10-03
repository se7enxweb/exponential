# Store dashboard and order statuses

The **Store** tab of the administration now opens a dashboard
(`shop/dashboard`) that shows on one page what used to need a guide to piece
together: how the shop is doing, which orders need attention and how checkout
is configured. The same release adds an order status for every stage of an
order's life.

## Open it

1. Sign in to the administration.
2. Click **Store** (the tab was called Webshop before 30 September 2026).
3. The dashboard is the first entry of the shop menu. The order list stays one
   click away in the same menu and is linked from the dashboard wherever orders
   are shown.

It needs the `shop / administrate` policy, like the order list and the
statistics. Titles and texts are translated (German included).

## What is on the page

| Section | What it tells you |
|---|---|
| At a glance | Orders today, in 7 and 30 days with the trend against the 30 days before; revenue and average order per currency; customers (new, returning, all time); open orders and how many wait too long; baskets in progress and abandoned; products and products without a price. |
| Charts and rankings | Revenue per day, order status breakdown, best selling products. |
| Work to do | Orders waiting for the shop, longest first; the latest orders, baskets and unconfirmed checkouts; the product classes with their price attribute. |
| How your shop works | The checkout step by step as this installation is configured: add to basket, basket, the shop account handler, confirmation page with shipping and VAT, payment (the workflows on the shop triggers and the available gateways), the order e-mails and the receipt, and processing through the statuses. Each step names the setting or admin page that changes it. Order statuses, VAT types, rules and the countries orders came from, currencies and discount groups follow. |
| Next steps | Suggestions worked out from your data and settings, most urgent first: pending orders waiting more than two days, no payment step on checkout, a 0 percent VAT type in use, basket cleanup that would delete baskets kept in PHP sessions, and so on. |

Every figure comes from a fixed set of about twenty aggregate statements
whatever the size of the shop, written without `LIMIT` or engine-specific
functions, so it runs the same on Oracle, MySQL, PostgreSQL and SQLite. Totals
use the arithmetic of the order list, so the numbers agree with it.

## Order statuses for the whole lifecycle

An installation used to ship three statuses (Pending, Processing, Delivered),
so a shop that takes payment, ships parcels or handles returns had to invent
its own. Fourteen more now come with every new installation and with the 6.0.15
database update. They are internal statuses (identifier below 1000), so they
cannot be removed by mistake. Custom statuses start at 1000.

| Id | Constant in `eZOrderStatus` | Name | Finished | No revenue | Waits for customer |
|---|---|---|---|---|---|
| 1 | `PENDING` | Pending | | | yes |
| 2 | `PROCESSING` | Processing | | | |
| 3 | `DELIVERED` | Delivered | yes | | |
| 4 | `AWAITING_PAYMENT` | Awaiting payment | | | yes |
| 5 | `PAID` | Paid | | | |
| 6 | `PAYMENT_FAILED` | Payment failed | | yes | yes |
| 7 | `ON_HOLD` | On hold | | | |
| 8 | `BACKORDERED` | Backordered | | | |
| 9 | `PACKED` | Packed | | | |
| 10 | `SHIPPED` | Shipped | | | |
| 11 | `READY_FOR_PICKUP` | Ready for pickup | | | yes |
| 12 | `COMPLETED` | Completed | yes | | |
| 13 | `CANCELLED` | Cancelled | yes | yes | |
| 14 | `RETURN_REQUESTED` | Return requested | | | |
| 15 | `RETURNED` | Returned | | | |
| 16 | `PARTIALLY_REFUNDED` | Partially refunded | yes | | |
| 17 | `REFUNDED` | Refunded | yes | yes | |

Grouped by stage: payment (Awaiting payment, Paid, Payment failed),
fulfilment (On hold, Backordered, Packed, Shipped, Ready for pickup), the end
of an order (Completed, Cancelled) and after it (Return requested, Returned,
Partially refunded, Refunded).

The three helper methods tell code and the dashboard what each status means:

```php
eZOrderStatus::finishedStatusIDs();          // no longer an open order
eZOrderStatus::noRevenueStatusIDs();         // brought in no money
eZOrderStatus::waitingForCustomerStatusIDs();// the shorter "waiting too long" limit applies
```

On the dashboard an order is **open** until its status is finished (not only
until Delivered), so a cancelled or refunded order no longer waits in the list
of orders to handle. Failed, cancelled and refunded orders count as orders but
not as revenue in the totals, the average, the daily chart and the top
products. Orders waiting for the customer are highlighted after the shorter
limit. The status table lists the statuses in lifecycle order, says what each
means and colours each by what it stands for: new, waiting for the customer,
in progress, done, stopped or custom.

### Upgrading an existing shop

The seed data of every engine carries the new statuses (MySQL, PostgreSQL,
SQLite clean data and `share/db_data.dba`). The 6.0.15 database update scripts
(`update/database/<mysql|postgresql|sqlite>/6.0/dbupdate-6.0.0-6.0.15.sql`) add each status only when no status already
has that number and leave the row id to the database, so a custom status you
created keeps its row. If you already used one of the numbers 4 to 17 for a
custom status, check the shop's order status list after the update.

## Related pages

- [Order receipts](order-receipts.md)
- [Shop basket view name](../../bc/6.0/shop-basket-view-name.md)
- [Order list sorting](order-list-sorting.md)
- [Chronicle, 16 to 30 September 2026](../../history/2026/2026-09b.md)
- [Installer logs and seed data](../../specifications/6.0/installer-logs-and-seed-data.md)
- [Behaviour changes, 16 to 30 September 2026](../../bc/6.0/behaviour-changes-2026-09b.md)
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)
