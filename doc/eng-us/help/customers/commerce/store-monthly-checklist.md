---
title: Store monthly checklist
audience: customer, admin, businesses
category: customer-success
order: 250
summary: The month-end checklist for the owner of an Exponential store - figures for the bookkeeper, payments and refunds reconciled, shipping costs, customer data, security and updates with the host, and the plan for next month.
keywords: monthly checklist, month end, store checklist, bookkeeping, reconciliation, product statistics, updates, backups, cron jobs, store owner
type: help
---
# Store monthly checklist

This page is the checklist for the first working days of each month. The owner does most of it, with the store
manager and the host. It closes the month for the books and catches slow problems that a daily look does not show.
A general monthly checklist for any business site is in
[Monthly operations checklist](../../businesses/templates/monthly-operations-checklist.md); this one is for the store.

## The checklist

```text
STORE MONTHLY CHECKLIST - month: __________     Owner: __________

FIGURES
[ ] Product statistics for the month saved (Store > Product statistics, shop/statistics)
[ ] Store monthly report filled in (see Store reports)
[ ] Figures handed to the bookkeeper on: __________

RECONCILIATION
[ ] Payment provider statement matches the month's paid orders
[ ] Every refund in the provider's account is on an order as Refunded / Partially refunded
[ ] Orders still Pending or Awaiting payment from last month: followed up or canceled
[ ] Shipping charged compared with the carrier's invoices

CATALOG AND PRICES
[ ] Products without a price: none
[ ] Seasonal products hidden or shown for the coming month
[ ] With several currencies: auto rates and autoprices updated; a few prices compared
[ ] Unused images and files reviewed (content checks), if the catalog changed a lot

PROMOTIONS
[ ] Every discount that should have ended has ended
[ ] Promotion log complete for the month

CUSTOMERS AND DATA
[ ] Privacy requests of the month closed in the request log
[ ] Export files deleted
[ ] Staff who left: accounts disabled

WITH THE HOST OR PARTNER
[ ] Backups ran, and the last restore test date is known
[ ] Cron jobs run (including basket cleanup and the audit)
[ ] Exponential and extension updates: any due? Planned for: __________
[ ] Mail delivery: order mails arriving in inboxes
[ ] Anything unusual in the logs or the dashboard's Next steps

NEXT MONTH
[ ] Sales, holidays and campaigns: dates and preparation (see Peak season preparation)
[ ] Price, VAT or shipping changes coming: dates and owners
```

## Where each item is done

| Item | Where | Read |
|---|---|---|
| Month figures | **Store > Product statistics** | [Store reports](store-reports.md) |
| Reconciliation | The provider's account, the order list sorted by status | [Payment gateways](payment-gateways.md), [Returns and refunds](returns-and-refunds.md) |
| Shipping | The carrier's invoices | [Shipping and handling](shipping-and-handling.md) |
| Currencies | **Store > Currencies** | [Prices and currencies](prices-and-currencies.md) |
| Unused files | Content checks | [The content checks](../../../features/6.0/content-checks.md) |
| Privacy requests | Your request log | [GDPR requests](gdpr-requests.md), [GDPR request log template](gdpr-request-log-template.md) |
| Accounts | **Users > Check user accounts** | [Check user accounts](../../../features/6.0/user-hygiene.md) |
| Backups, cron jobs, updates | The host | [Cron jobs](../../../guides/cronjobs.md), [Upgrading](../../../guides/upgrading.md) |

## Questions for the host each month

- Did all backups run, and when was a restore last tested
  ([Restore drills](../../businesses/reliability/restore-drills.md))?
- Do the cron jobs run on schedule? **Setup > Cronjobs** shows the parts and whether they are scheduled
  ([Cron jobs](../../../guides/cronjobs.md)).
- Is a new Exponential 6.0.x release out, with security fixes? Fixes are published with the next 6.0.x release
  (`SECURITY.md`).
- Anything in the logs we should know about ([Log viewer](../../../features/6.0/log-viewer.md))?

## Related pages

- [Store weekly checklist](store-weekly-checklist.md)
- [Store reports](store-reports.md)
- [VAT checklist](vat-checklist.md)
- [Monthly checklist for the whole site](../value/checklists/monthly.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
