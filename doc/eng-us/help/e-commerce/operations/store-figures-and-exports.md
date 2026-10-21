---
title: Store figures and exports
audience: ecommerce, customer, businesses
category: e-commerce
order: 76
summary: Getting figures and data out of an Exponential store - the dashboard and order statistics, order data as JSON through the expservices order services, product data as CSV with ezcsvexport, the audit export, and what has to be built for an accounting export.
keywords: reporting, export, order export, accounting, statistics, shop/statistics, exporder, expservices, JSON, ezcsvexport, CSV, audit export, month end, figures
type: help
---
# Store figures and exports

This page is for merchants, the people who prepare figures for the accountant, and developers who need store data
outside the administration. The order list has no spreadsheet export, so this page lists what does exist, what each
produces, and what remains to be built.

## Contents

1. [What exists](#what-exists)
2. [Month-end figures](#month-end-figures)
3. [Order data as JSON](#order-data-as-json)
4. [Product data as CSV](#product-data-as-csv)
5. [Audit exports](#audit-exports)
6. [An accounting export](#an-accounting-export)

## What exists

| Need | Tool | Output |
|---|---|---|
| Sales per product and month | **Store > Statistics** (`shop/statistics`) | Screen |
| Trends, open orders, abandoned baskets | Store dashboard | Screen |
| Orders, items, customers, statistics | `expservices` order services (`exporder::list`, `view`, `items`, `statistics`, `customers` and more) | JSON |
| Products of a subtree | `bin/php/ezcsvexport.php` | CSV, one file per class |
| Who changed what in commerce | Audit export (`audit/export`) | CSV, JSON lines or JSON |

## Month-end figures

1. **Store > Statistics** for the month: products, quantities and totals per currency.
2. The dashboard's 30-day figures for trends and the share of failed, cancelled and refunded orders, which count as
   orders but not as revenue on the dashboard.
3. Write the figures into your monthly report as described in
   [Store reports](../../customers/commerce/store-reports.md#a-one-page-monthly-report).

The statistics count placed orders only, never temporary ones (`eZOrder::orderStatistics()` filters
`is_temporary = 0`); archived orders are included.

## Order data as JSON

The `expservices` extension, shipped in the repository's `extension/` folder, offers the shop's data as remote
services ([Headless commerce with expservices](headless-commerce-with-expservices.md)). For figures and exports:

| Service | Returns | Policy |
|---|---|---|
| `exporder::list` | Orders, filterable by status and archived, sorted by created, id, total or user name, paged | `shop/administrate` |
| `exporder::view`, `exporder::items`, `exporder::history` | One order, its lines, its status history | `shop/administrate` |
| `exporder::statistics` | Products sold in a year or month with totals per currency | `shop/administrate` |
| `exporder::customers`, `exporder::customerOrders` | Customers and their orders | `shop/administrate` |

A script with a personal API token of a dedicated user (with `shop/administrate` and nothing else) can fetch these
and write a spreadsheet. The extension was added as version 0.1.0 and its feature page calls it a first draft; test
the services you rely on after updates.

## Product data as CSV

```bash
php bin/php/ezcsvexport.php --storage-dir=export <node id>
```

writes one CSV file per content class for the subtree, prices in the text format of the price datatype
([Price fields compared](../catalog/price-fields-compared.md#text-formats-for-imports-and-exports)). Use it for a
price list, a catalog check, or as a template for an import.

## Audit exports

With `audit/export`, the **Audit** tab exports the console's filter as CSV, JSON lines or JSON, for example all
`commerce.*` events of a month ([Commerce audit trail](commerce-audit-trail.md)).

## An accounting export

An export to an accounting system (orders with VAT per rate, payments, refunds) is not in the kernel. Build it as:

- a script that reads `exporder::list` and `exporder::view`, or the order classes directly, filtering
  `is_temporary = 0`; or
- an event on the after trigger of `shop_checkout` that sends each placed order as it happens
  ([Workflows on shop triggers](../checkout/workflows-on-shop-triggers.md)).

Agree the fields with your accountant first. Leave customer data out unless the accounting system needs it.

## Related pages

- [Operating a store](README.md)
- [Store dashboard and reports](../../businesses/stores/store-dashboard-and-reports.md)
- [Analytics for stores](analytics-for-stores.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
