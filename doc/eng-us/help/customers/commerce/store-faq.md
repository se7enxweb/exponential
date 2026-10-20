---
title: Store FAQ for owners
audience: customer, businesses, admin
category: customer-success
order: 260
summary: Short answers to the questions owners of a running Exponential store ask most - orders, payments, prices, VAT, shipping, customers, data and reports - each with a link to the page that has the routine and the source.
keywords: store FAQ, owner questions, orders, payments, PayPal, VAT, shipping, refunds, receipts, customers, GDPR, reports, export
type: help
---
# Store FAQ for owners

This page answers the questions that owners and store managers ask once their store runs. Questions about whether
the shop fits a business before launch are answered on the [Store FAQ for businesses](../../businesses/stores/store-faq.md).

## Orders

**Where do I see what needs doing today?**
On the Store dashboard, under "Work to do", orders waiting for the shop longest first.
See [Daily store routine](daily-store-routine.md).

**Which statuses should my team use?**
Only the ones your process needs; write them on the status path worksheet. See [Order statuses](order-statuses.md).

**Can I stop the packing assistant from refunding orders?**
Yes. The permission to change statuses, `shop/setstatus`, can be limited with `FromStatus` and `ToStatus`.
See [Orders day to day](orders-day-to-day.md).

**Does setting "Refunded" give the customer their money back?**
No. A status change never moves money; refund in your payment provider's account, then set the status.
See [Returns and refunds](returns-and-refunds.md).

**Should I remove old orders?**
Usually archive them; remove only when your bookkeeping period has passed. See [Data export and erasure](data-export-and-erasure.md).

## Payments

**A customer paid with PayPal but the order is not marked paid. Why?**
Most often PayPal's notification does not reach the site, or the paid status points at a status you do not use.
See [PayPal](paypal.md).

**Do card numbers pass through our website?**
Not with a redirect gateway such as PayPal; with an on-site card form they do. See [Card payments and PCI](card-payments-and-pci.md).

**Can we add another payment provider?**
Yes, with a gateway extension written by a developer. See [Payment gateways](payment-gateways.md).

## Prices, VAT and discounts

**How do I change prices at midnight?**
Prepare the changes as drafts and publish at the moment, or use scheduled publication if your site has it.
See [Prices and currencies](prices-and-currencies.md).

**A VAT rate changes next month. What do I do?**
Change the percentage of the VAT type on the day, and place a test order. See [VAT types and rules](vat-types-and-rules.md).

**Do old orders change when I change a price or a VAT rate?**
No. Each order line keeps its own price, VAT value and discount. See [VAT types and rules](vat-types-and-rules.md).

**Can customers type a coupon code?**
No, not without an extension. Discounts follow customer groups. See [Discounts](discounts.md).

## Shipping and receipts

**How do I change the shipping cost?**
Ask your partner to change the flat cost in an override, and place a test order. See [Shipping and handling](shipping-and-handling.md).

**Where do I put tracking numbers?**
The shop has no field for them by default; agree on one place. See [Shipping and handling](shipping-and-handling.md).

**A customer lost their confirmation. What do I send?**
Their receipt link, if permanent receipts are on, or a forward of the shop's copy. See [Invoices and receipts](invoices-and-receipts.md).

**Does the shop create invoices?**
No. It gives receipts; invoices come from your accounting software or an integration. See [Invoices and receipts](invoices-and-receipts.md).

## Customers and data

**Must customers have an account?**
Not with the shipped setting; guests can buy. See [Customer accounts](customer-accounts.md).

**A customer asks us to delete their data. Where do I start?**
Log the request, then follow the routine. Orders may have to be kept for bookkeeping. See [GDPR requests](gdpr-requests.md).

**Can guests use the wishlist?**
No, the wishlist needs an account. See [Wishlists and baskets](wishlists-and-baskets.md).

## Reports

**Can I export orders to a spreadsheet?**
Not from the shipped order list. Copy the figures, or ask your partner for an export. See [Store reports](store-reports.md).

**Is the revenue on the dashboard what reached our bank?**
No. It is order value; your bookkeeping stays the reference. See [Store reports](store-reports.md).

## Getting help

**Who do I ask when something in the shop seems broken?**
First your partner or host. A bug in Exponential itself can be reported on
<https://github.com/se7enxweb/exponential/issues>; a security problem privately to security@se7enx.com (`SECURITY.md`).
The help's report page in the administration is at `/exphelp/report`.

## Related pages

- [Running your store](README.md)
- [Store FAQ for businesses](../../businesses/stores/store-faq.md)
- [When something goes wrong](../../when-something-goes-wrong.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
