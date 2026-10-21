---
title: Operations calendar
audience: ecommerce, customer, businesses
category: e-commerce
order: 80
summary: A year of recurring work for an Exponential store, by frequency and by role - what the daily, weekly and monthly routines leave out (quarterly, yearly, before peaks, after updates), with the screen, setting or page for each task.
keywords: operations calendar, store calendar, yearly tasks, quarterly tasks, exchange rates, VAT review, retention, archive, payment provider, certificates, peak season, updates, roles review, audit verify
type: help
---
# Operations calendar

This page is for merchants, store managers and the agencies that maintain stores. The daily, weekly and monthly
routines are written down in [Running your store](../../customers/commerce/README.md); this calendar adds the rarer
tasks that are easy to forget, and puts them all on one page by frequency.

## Contents

1. [Every day, week and month](#every-day-week-and-month)
2. [Every quarter](#every-quarter)
3. [Every year](#every-year)
4. [Before a peak](#before-a-peak)
5. [After every update](#after-every-update)
6. [Background jobs to watch](#background-jobs-to-watch)

## Every day, week and month

| Frequency | Routine |
|---|---|
| Daily | [Daily store routine](../../customers/commerce/daily-store-routine.md) |
| Weekly | [Store weekly checklist](../../customers/commerce/store-weekly-checklist.md) |
| Monthly | [Store monthly checklist](../../customers/commerce/store-monthly-checklist.md), [Store figures and exports](store-figures-and-exports.md) |

For multi-currency stores with automatic prices, add **Update autorates** and **Update autoprices** in
**Store > Currencies** at the frequency you decided ([Exchange rate handlers](../extending/exchange-rate-handlers.md#keeping-rates-and-prices-current)).

## Every quarter

| Task | Who | Where |
|---|---|---|
| Review roles and policies of shop staff; remove leavers | Merchant | [Status transitions and permissions](../orders/status-transitions-and-permissions.md) |
| Check VAT types and rules against current rates | Merchant with tax adviser | [VAT checklist](../../customers/commerce/vat-checklist.md) |
| Review discount groups and their members | Merchant | [Discounts](../../customers/commerce/discounts.md) |
| Place one real order per payment method | Store manager | [Testing a store](testing-a-store.md) |
| Read the dashboard's "Next steps" and act on each | Store manager | Store dashboard |
| Verify the commerce audit chain | Administrator | `./console exp:audit verify --channel=commerce` |

## Every year

| Task | Who | Where |
|---|---|---|
| Retention: review archived orders older than the agreed period | Merchant, data protection adviser | [Archive, remove and cleanup](../orders/archive-remove-and-cleanup.md#a-retention-practice) |
| Payment provider: contract, fees, credentials, callback address | Merchant, developer | [paymentgateways.ini walkthrough](../settings/paymentgateways-ini-walkthrough.md) |
| Card security self-assessment, if card data touches your site | Merchant | [Card payments and PCI basics](../../businesses/stores/card-payments-and-pci-basics.md) |
| Shipping rates and terms | Merchant | [Shipping and handling](../../customers/commerce/shipping-and-handling.md) |
| Store accessibility check | Developer, agency | [Store accessibility](store-accessibility.md) |
| Restore drill of the database with orders | Host or partner | [Restore drills](../../businesses/reliability/restore-drills.md) |
| Plan the year's campaigns and their landing pages | Merchant, editors | [Content commerce with Layouts](content-commerce-with-layouts.md) |

## Before a peak

Four to six weeks before a sale or a season: [Peak season preparation](../../customers/commerce/peak-season-preparation.md)
and [Store performance at peak times](../../businesses/stores/store-performance-at-peak-times.md). Add: landing pages
with time conditions, staff roles for extra helpers, and a test order on the day before.

## After every update

After an Exponential or extension update: one test order per payment method, the payment callback, the order mail,
and the dashboard's "How your shop works" compared with before ([Testing a store](testing-a-store.md#after-every-update)).

## Background jobs to watch

| Job | Cron part | If it stops |
|---|---|---|
| `basket_cleanup.php` | `infrequent` | Old baskets pile up |
| `workflow.php` | `frequent` | Deferred workflow processes never resume |
| `audit.php` | `frequent` | Audit records are not indexed, rotated or archived |

See [Cron jobs](../../../guides/cronjobs.md).

## Related pages

- [Operating a store](README.md)
- [Owner's year at a glance](../../customers/owners-year-at-a-glance.md)
- [Decision checklists](decision-checklists.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
