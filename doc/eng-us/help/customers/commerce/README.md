---
title: Running your store
audience: customer, businesses, admin
category: customer-success
order: 1
summary: The store owner's section - routines, checklists and decisions for running the shop that is built into Exponential, after launch, with links to the reference pages for every screen and setting.
keywords: store owner, shop owner, running a store, e-commerce, webshop, orders, payments, VAT, shipping, store routine, store checklists
type: help
---
# Running your store

This section is for organizations that already sell with the shop built into Exponential: the owner, the store
manager, the person who packs and ships, and whoever talks to the host or partner. It is about the running store:
what to do every day, week and month, who on the team does it, what to check, and what to ask your provider.

The shop is part of Exponential itself. The repository's `README.md` lists "Advanced e-Commerce Stores (Built in)"
among the main features, and all shop screens are views of one module, `kernel/shop/module.php`. What the shop can
and cannot do is described for decision makers in [What the built-in shop does](../../businesses/stores/what-the-built-in-shop-does.md).
These pages do not repeat that; they tell you how to run it.

## Start here

| If you want to | Read |
|---|---|
| Know what to look at every morning | [Daily store routine](daily-store-routine.md) |
| Work through a quiet week | [Store weekly checklist](store-weekly-checklist.md) |
| Close a month | [Store monthly checklist](store-monthly-checklist.md) |
| Get ready for a sale or the holiday season | [Peak season preparation](peak-season-preparation.md) |
| Find a quick answer | [Store FAQ for owners](store-faq.md) |

## Catalog and prices

- [Products and catalog](products-and-catalog.md): keeping the catalog tidy, who maintains what.
- [Adding a product](adding-a-product.md): the owner's checklist for every new product.
- [Prices and currencies](prices-and-currencies.md): changing prices, currencies and exchange rates safely.
- [VAT types and rules](vat-types-and-rules.md) and the [VAT checklist](vat-checklist.md).
- [Discounts](discounts.md): running group discounts and sales by hand.

## Orders and payments

- [Orders day to day](orders-day-to-day.md) and [Order statuses](order-statuses.md).
- [Payment gateways](payment-gateways.md), [PayPal](paypal.md), [Card payments and PCI](card-payments-and-pci.md).
- [Shipping and handling](shipping-and-handling.md).
- [Returns and refunds](returns-and-refunds.md).
- [Invoices and receipts](invoices-and-receipts.md) and [Order confirmation mail](order-confirmation-mail.md).

## Customers and their data

- [Customer accounts](customer-accounts.md) and [Wishlists and baskets](wishlists-and-baskets.md).
- [Store reports](store-reports.md): the dashboard and statistics in a routine.
- [GDPR requests](gdpr-requests.md), the [GDPR request log template](gdpr-request-log-template.md) and
  [Data export and erasure](data-export-and-erasure.md).

## Who usually does what

| Role on your team | Typical store work | Permission it needs (`kernel/shop/module.php`) |
|---|---|---|
| Owner | Decisions on prices, VAT, payment, discounts; monthly figures | `shop/setup`, `shop/administrate` |
| Store manager | Orders, customers, the dashboard, customer questions | `shop/administrate`, `shop/setstatus` |
| Packing and shipping | Moves orders through the fulfillment statuses | `shop/setstatus`, limited by `FromStatus` and `ToStatus` |
| Product editors | Product pages, images, prices as fields of the product | content edit in the product folders |
| Host or partner | Settings, payment gateway, cron jobs, backups, updates | server and settings access |

The roles themselves are built by your administrator ([Roles and policies](../../../guides/roles-and-policies.md)).

> **Note**
> The shop module has five functions: `buy`, `administrate`, `setup`, `edit_status` and `setstatus`
> (`kernel/shop/module.php`). Give each person the smallest set that covers their work.

## Related pages

- [What the built-in shop does](../../businesses/stores/what-the-built-in-shop-does.md)
- [Running a store day to day (for businesses)](../../businesses/stores/running-a-store-day-to-day.md)
- [Running a shop: overview](../../creators/website-creators/shop-overview.md)
- [Store dashboard and order statuses](../../../features/6.0/store-dashboard.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
