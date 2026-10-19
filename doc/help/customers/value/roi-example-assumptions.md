---
title: ROI example assumptions
audience: customer, businesses, project-manager
category: customer-success
order: 20
summary: A worked one-year ROI calculation for a running store on Exponential, with every input clearly labeled as an example assumption, the arithmetic step by step, a sensitivity table, and how to replace each figure with your own.
keywords: ROI example, example assumptions, worked example, ROI calculation, sensitivity, store ROI, one year, costs, benefits, payment fees, staff time
type: help
---
# ROI example assumptions

This page shows the ROI arithmetic of [Measuring ROI](measuring-roi.md) on one worked example, a small store in its
second year. It exists so you can see how the pieces fit, then replace every figure with your own in the
[ROI worksheet](roi-worksheet.md).

> **Warning**
> **Example assumptions: replace with your own.** Every amount on this page is an invented, round figure in a neutral
> "currency unit". None is a price, a quote, a market rate or a measurement of any real site. The only facts are the
> ones with a source, such as the license fee of zero.

## The example

A small store that has run on Exponential for a year, with the built-in shop, PayPal payments and a newsletter. The
question for its second year: does the online store pay for itself?

## Example assumptions: replace with your own

| Input | Example value | Where your own figure comes from |
|---|---|---|
| License fee for Exponential | 0 (fact) | `README.md`, "License": free of charge under the GNU GPL |
| Hosting | 100 currency units a month | Your host's invoices |
| Support from a partner | 1,500 a year | Your contract |
| One planned update and maintenance job | 2,000 a year | Your partner's quote ([Cost of ownership for owners](cost-of-ownership-for-owners.md)) |
| Staff time for products and orders | 5 hours a week, 48 weeks, at 30 an hour | Your time records |
| Online revenue (order value) | 60,000 a year | Store dashboard and product statistics ([Store reports](../commerce/store-reports.md)) |
| Payment provider fees | 3 % of online revenue | Your provider's statements |
| Share of online revenue that is new (would not have come otherwise) | 50 % | Your own judgment, written down with its reason |
| Gross margin on sales | 40 % | Your bookkeeping |
| Staff time saved on phone and mail orders | 3 hours a week, 48 weeks, at 30 an hour | Your time records |
| Realization of that time saved | 50 % | Your own judgment |

## Step 1: the costs for the year

1. Hosting: 100 x 12 = **1,200**.
2. Support: **1,500**.
3. Update and maintenance: **2,000**.
4. Staff time: 5 x 48 x 30 = **7,200**.
5. Payment fees: 3 % of 60,000 = **1,800**.
6. License: **0**.
7. Total costs: 1,200 + 1,500 + 2,000 + 7,200 + 1,800 + 0 = **13,700**.

## Step 2: the benefits for the year

1. New revenue: 50 % of 60,000 = 30,000.
2. Gross profit on new revenue: 40 % of 30,000 = **12,000**.
3. Staff time saved: 3 x 48 x 30 = 4,320; realized at 50 % = **2,160**.
4. Total benefits: 12,000 + 2,160 = **14,160**.

## Step 3: the result

1. Net benefit: 14,160 - 13,700 = **460**.
2. ROI: 460 / 13,700 x 100 % = about **3.4 %**.

In this example the store just pays for itself. The result depends most on one judgment: how much of the online
revenue is really new.

## Sensitivity: what moves the result

Change one input at a time and recalculate. All values are example assumptions.

| Change | Benefits | Costs | Net | ROI |
|---|---|---|---|---|
| As above | 14,160 | 13,700 | 460 | about 3.4 % |
| New share 70 % instead of 50 % (gross profit 40 % of 42,000 = 16,800) | 18,960 | 13,700 | 5,260 | about 38.4 % |
| Staff time 3 hours a week instead of 5 (cost 4,320) | 14,160 | 10,820 | 3,340 | about 30.9 % |
| No planned update this year (cost 0 instead of 2,000) | 14,160 | 11,700 | 2,460 | about 21.0 % |

Arithmetic for the rows: 16,800 + 2,160 = 18,960 and 18,960 - 13,700 = 5,260, 5,260 / 13,700 = 0.384;
13,700 - 7,200 + 4,320 = 10,820 and 14,160 - 10,820 = 3,340, 3,340 / 10,820 = 0.309;
13,700 - 2,000 = 11,700 and 14,160 - 11,700 = 2,460, 2,460 / 11,700 = 0.210.

> **Tip**
> Leaving out the update to improve the figure is a false saving: an unmaintained site costs more later. Keep a
> yearly line for updates, as [Budgeting for upgrades and maintenance](../../businesses/costs/budgeting-for-upgrades-and-maintenance.md)
> plans for in its maintenance calendar.

## What this example leaves out

Things that are real but hard to put a number on, to describe in words beside the figures:

- the value of customers who come back, and of the newsletter list;
- less risk from running a maintained release line (`SECURITY.md`);
- the cost of an outage avoided ([Cost of downtime worksheet](../../businesses/costs/cost-of-downtime-worksheet.md)).

## Replacing the figures

1. Copy the [ROI worksheet](roi-worksheet.md).
2. Fill each line from the source in the third column of the assumptions table, never from this page.
3. Write the reason next to every judgment (new share, realization).
4. Ask someone outside the project to redo the arithmetic.

## Related pages

- [Measuring ROI](measuring-roi.md)
- [ROI worksheet](roi-worksheet.md)
- [Worked example: a small store](../../businesses/costs/worked-example-small-store.md)
- [Return on investment of stability](../../businesses/costs/roi-of-stability.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
