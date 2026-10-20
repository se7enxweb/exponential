---
title: Online stores: start here
audience: customer, businesses
category: customer-success
order: 50
summary: For shops that already sell on Exponential's built-in store: the Store dashboard, the daily order routine, products, prices and VAT, payments and shipping, and who on the team owns each part.
keywords: online store, shop, e-commerce, store dashboard, orders, products, prices, VAT, payment gateway, shipping, returns, invoices, customer accounts
type: help
---
# Online stores: start here

An online store is the one type of site where an unnoticed problem costs money by the hour. Orders wait for
someone to handle them, payments must match orders, prices and VAT must be right in every country you sell to, and
customers expect an answer when they write. The work is a routine, and the routine is what this page and its reading
path are about.

This page is for the store owner, the store manager and whoever handles orders and customer questions. Choosing
and planning a store is covered in [Online stores for businesses](../../businesses/stores/README.md); building one
is covered for builders in [Shop overview](../../creators/website-creators/shop-overview.md).

## Where the store lives in the administration

Sign in to the administration and open the **Store** tab (it was called Webshop before 30 September 2026). Its
first entry is the **Store dashboard** (`shop/dashboard`, `doc/eng-us/features/6.0/store-dashboard.md`), which shows on
one page:

- **At a glance**: orders today, in 7 and in 30 days with the trend; revenue and average order per currency; new
  and returning customers; open orders and how many wait too long; baskets in progress and abandoned; products and
  products without a price.
- **Charts and rankings**: revenue per day, the breakdown of order statuses, best selling products.
- **Work to do**: orders waiting for the shop, longest first; the latest orders, baskets and unconfirmed checkouts.

The dashboard needs the `shop / administrate` policy, as do the order list and the statistics. Give it to the
people who run the store, not to every editor ([Roles for your team](../day-to-day/roles-for-your-team.md)).

## Who owns what

| Part | Usually owned by | The routine |
|---|---|---|
| Orders and order statuses | Store manager | [Orders day to day](../commerce/orders-day-to-day.md), [Order statuses](../commerce/order-statuses.md) |
| Products and the catalog | Product or content editor | [Products and catalog](../commerce/products-and-catalog.md), [Adding a product](../commerce/adding-a-product.md) |
| Prices, currencies and discounts | Store owner | [Prices and currencies](../commerce/prices-and-currencies.md), [Discounts](../commerce/discounts.md) |
| VAT | Store owner with the accountant | [VAT types and rules](../commerce/vat-types-and-rules.md), [VAT checklist](../commerce/vat-checklist.md) |
| Payments | Store owner with the partner | [Payment gateways](../commerce/payment-gateways.md) |
| Shipping and returns | Store manager | [Shipping and handling](../commerce/shipping-and-handling.md), [Returns and refunds](../commerce/returns-and-refunds.md) |
| Receipts, invoices and order mail | Store manager | [Invoices and receipts](../commerce/invoices-and-receipts.md), [Order confirmation mail](../commerce/order-confirmation-mail.md) |
| Customer accounts and data requests | Store owner | [Customer accounts](../commerce/customer-accounts.md), [GDPR requests](../commerce/gdpr-requests.md) |
| The site being up and fast | Host or partner | [Keeping the site healthy](../healthy/README.md) |

> **Note**
> VAT, invoicing and data protection rules depend on where you and your customers are. These pages describe what the
> software does and what to check; they are not legal or tax advice. Ask your accountant or adviser what applies to
> you.

## The store's rhythm

- **Every morning**: open the Store dashboard, handle the orders waiting longest first, and compare the payments
  your payment provider reports with the paid orders ([Daily store routine](../commerce/daily-store-routine.md)).
- **Every week**: products without a price, abandoned baskets, open returns
  ([Store weekly checklist](../commerce/store-weekly-checklist.md)).
- **Every month**: revenue per currency, best sellers, VAT figures for the accountant, and whether the payment
  gateway and the site are up to date ([Store monthly checklist](../commerce/store-monthly-checklist.md)).
- **Before a sale or the holiday season**: [Peak season preparation](../commerce/peak-season-preparation.md).

## When something goes wrong in the store

A payment that did not arrive, an order that stays in a status, a checkout that fails: start with
[Store FAQ](../commerce/store-faq.md), then [When something goes wrong](../../when-something-goes-wrong.md). Note the
order number and the error reference the site shows; your partner needs both
([Reporting problems](../healthy/reporting-problems.md)).

## Your next step

Follow the [online store reading path](online-store-reading-path.md).

## Related pages

- [Online store reading path](online-store-reading-path.md)
- [Running a store day to day (businesses)](../../businesses/stores/running-a-store-day-to-day.md)
- [Store dashboard and order statuses](../../../features/6.0/store-dashboard.md)
- [Store security checklist](../../businesses/stores/store-security-checklist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
