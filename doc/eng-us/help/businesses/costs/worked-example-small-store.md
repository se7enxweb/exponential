---
title: "Worked example: a small store"
audience: businesses, project-manager, sales-marketing
category: business-solutions
order: 40
summary: A step-by-step cost and return calculation for a small online store on Exponential's built-in shop, using only labeled example assumptions, with the facts about the shop that shape each cost line.
keywords: small store, online shop, e-commerce, worked example, TCO, ROI, gross margin, payment fees, hosting, break-even, budget
type: help
---
# Worked example: a small store

This page is for owners of a small business who want to sell online and need to know whether a store on
Exponential pays for itself. It walks through a first-year and a five-year calculation for an invented store, one
step at a time, so you can repeat it with your own figures.

> **Note**
> The store, its sales and every cost below are **Example assumptions**: round numbers in a neutral currency unit,
> chosen to make the arithmetic easy to follow. They are not market data, not prices of any provider and not the
> results of any real shop. Replace each one with your own figures.

## Contents

1. [What the store uses](#what-the-store-uses)
2. [The inputs](#the-inputs)
3. [Step 1: the costs](#step-1-the-costs)
4. [Step 2: the gross profit from online sales](#step-2-the-gross-profit-from-online-sales)
5. [Step 3: ROI and break-even](#step-3-roi-and-break-even)
6. [Step 4: what downtime would cost](#step-4-what-downtime-would-cost)
7. [What to change for your store](#what-to-change-for-your-store)

## What the store uses

The facts that shape the cost lines, each from the repository:

| Need | What Exponential provides | Source |
|---|---|---|
| Catalog, basket, checkout, orders | "Advanced e-Commerce Stores (Built in)" | `README.md`, "Main Exponential features"; [Shop overview](../../creators/website-creators/shop-overview.md) |
| VAT and prices | VAT types and rules, currencies | [Products, prices and VAT](../../creators/website-creators/products-prices-and-vat.md) |
| Payments | `ezpaypal` is required by `composer.json`; other gateways are extensions | [Payment gateways](../../creators/website-creators/payment-gateways.md) |
| Order overview | Store dashboard and order statuses | [Store dashboard](../../../features/6.0/store-dashboard.md) |
| Receipts | Permanent order receipts | [Order receipts](../../../features/6.0/order-receipts.md) |
| Database | SQLite 3, no database server, enough for one machine | [Database support](../../database-support.md) |

So the example has no line for a shop add-on or a shop license. Payment provider fees remain; they are set by the
provider you choose.

## The inputs

| Input | Value |
|---|---|
| Example assumption: setup by a partner (design, product classes, payment, launch) | 8,000 currency units, once |
| Example assumption: hosting plan | 50 currency units a month |
| Example assumption: maintenance and updates | 10 hours a year at 60 currency units an hour |
| Example assumption: online orders | 100 a month |
| Example assumption: average order value | 40 currency units |
| Example assumption: gross margin on goods | 30 % |
| Example assumption: payment provider fees | 3 % of turnover |
| License fee | 0 (fact: `README.md`, "License") |

## Step 1: the costs

First year:

1. Setup: **8,000**.
2. Hosting: 50 x 12 = **600**.
3. Maintenance: 10 x 60 = **600**.
4. License: **0**.
5. First-year cost (without payment fees): 8,000 + 600 + 600 + 0 = **9,200**.

Each later year: 600 + 600 = **1,200**.

Five years: 9,200 + (1,200 x 4) = 9,200 + 4,800 = **14,000**.

## Step 2: the gross profit from online sales

1. Turnover per month: 100 orders x 40 = **4,000**.
2. Turnover per year: 4,000 x 12 = **48,000**.
3. Gross margin per year: 48,000 x 30 % = **14,400**.
4. Payment fees per year: 48,000 x 3 % = **1,440**.
5. Gross profit after payment fees: 14,400 - 1,440 = **12,960**.

## Step 3: ROI and break-even

First year:

1. Net benefit: 12,960 - 9,200 = **3,760**.
2. ROI: 3,760 / 9,200 x 100 % = about **41 %**.

Five years:

1. Gross profit after fees: 12,960 x 5 = **64,800**.
2. Costs: **14,000**.
3. Net benefit: 64,800 - 14,000 = **50,800**.
4. ROI: 50,800 / 14,000 x 100 % = about **363 %**.

Break-even on the setup: monthly gross profit after fees is 12,960 / 12 = 1,080; monthly running costs are
1,200 / 12 = 100; so each month adds 1,080 - 100 = 980 towards the setup. 8,000 / 980 = about **8.2 months**.

> **Warning**
> This assumes the online orders are new business. If some would have come in by phone or in the shop anyway, count
> only the extra orders. That is the most common mistake in a store business case.

## Step 4: what downtime would cost

1. Example assumption: the store is offline for 4 hours on a busy day.
2. Gross profit after fees per hour, averaged over the year: 12,960 / (365 x 24) = about **1.5** currency units.
3. On a busy day orders cluster; Example assumption: busy hours bring ten times the average: about **15** an hour.
4. Lost gross profit: 4 x 15 = **60**, plus the staff time to answer customers and the orders that never come back.

For a small store the direct loss of a short outage is modest; the lost trust is not. The
[Cost of downtime worksheet](cost-of-downtime-worksheet.md) adds the other lines, and
[Store performance at peak times](../stores/store-performance-at-peak-times.md) explains how to stay up on the busy
days.

## What to change for your store

- [ ] Replace every Example assumption with your figures and write the source beside each.
- [ ] Add lines for product photography, content writing and shipping materials if you buy them.
- [ ] Add your payment provider's real fee structure from its contract.
- [ ] Count only the orders the store adds, not those it moves from another channel.
- [ ] Check VAT duties for the countries you sell to ([VAT rules and VAT types](../stores/vat-rules-and-vat-types.md)).
- [ ] Plan the launch with the [Store launch checklist](../stores/store-launch-checklist.md).

## Related pages

- [Stores](../stores/README.md)
- [Store planning for small businesses](../stores/store-planning-for-small-businesses.md)
- [Total cost of ownership](total-cost-of-ownership.md)
- [ROI worksheet template](../templates/roi-worksheet-template.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
