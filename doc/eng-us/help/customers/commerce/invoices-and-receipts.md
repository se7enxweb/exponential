---
title: Invoices and receipts
audience: customer, admin, businesses
category: customer-success
order: 150
summary: What an Exponential store gives customers as a record of their order - the permanent receipt with print and download - why it is not an invoice system, and how a store owner handles invoices and receipt requests. Not tax advice.
keywords: receipt, invoice, permanent receipt, orderreceipt, print receipt, download receipt, receipt link, invoice numbers, accounting, bookkeeping, not tax advice
type: help
---
# Invoices and receipts

This page is for owners and whoever does the bookkeeping. It explains what the shop gives your customers as a record
of an order, what it does not do, and how to handle invoice requests in a running store. The receipt feature is
described on [Permanent order receipts](../../../features/6.0/order-receipts.md).

> **Note**
> Not tax advice. Whether a receipt is enough, or what an invoice must contain, depends on the law that applies to
> you. Ask your tax adviser.

## What the shop gives your customers

| Record | Where | Lasts |
|---|---|---|
| The order page after checkout | The browser | Until the visitor leaves |
| The order confirmation mail | The customer's inbox | As long as they keep it ([Order confirmation mail](order-confirmation-mail.md)) |
| The order view, `shop/orderview/<id>` | The site | For guests, only until their browser session expires |
| The permanent receipt, `shop/orderreceipt/<token>` | The site | Without end, on any device, with or without an account |

The permanent receipt is switched on with `settings/shop.ini [OrderViewSettings] OrderLinkView=orderreceipt` (shipped
value `orderview`). It offers **Print**, which prints the receipt alone, and **Download**, a self-contained
`receipt-<number>.html` file ([Permanent order receipts](../../../features/6.0/order-receipts.md)).

> **Tip**
> If guests can buy in your store, ask your partner to switch the permanent receipt on. It removes most "I lost my
> order" calls.

## What the shop does not do

Invoices in an accounting system are among the things the shop leaves to an extension or an integration
([Running a shop: overview](../../creators/website-creators/shop-overview.md)). The views of the shop module
(`kernel/shop/module.php`) include no invoice view or invoice
numbering, no credit notes and no export of orders to a spreadsheet file
([What the built-in shop does](../../businesses/stores/what-the-built-in-shop-does.md)).

## Handling invoices in practice

| Your situation | A common approach |
|---|---|
| Your adviser says the receipt is enough | Keep the receipt link on; nothing else to do |
| You need invoices for some customers (businesses) | Create them in your accounting software from the order view, on request |
| You need an invoice for every order | Ask your partner for an integration with your accounting software |
| You need monthly figures for the bookkeeper | **Store > Product statistics** per month ([Store reports](store-reports.md)) |

Whatever you choose, write down who creates invoices, from which data, and how fast you answer a request.

## Receipt requests from customers

| Request | Answer |
|---|---|
| "Send me my receipt again" | Copy the receipt link from the order and send it, or forward the order mail from the shop's copy |
| "My receipt shows the wrong address" | Agree how corrections are made; the receipt shows the order as it was placed |
| "I need an invoice with our VAT number" | Follow your invoice routine above |
| "Who can see my receipt?" | Anyone holding the link; the footer says to keep it private ([Permanent order receipts](../../../features/6.0/order-receipts.md)) |

## The receipt secret

Receipt addresses are signed with `shop.ini [OrderReceiptSettings] Secret`. Changing the secret changes every receipt
address, so links in old mails stop working ([Order receipts and confirmation mail for businesses](../../businesses/stores/order-receipts-and-confirmation-mail.md)).

- [ ] Your partner has set it once, outside the shipped settings file, and recorded it with your other secrets.
- [ ] Nobody changes it as part of a routine update.

## Related pages

- [Order confirmation mail](order-confirmation-mail.md)
- [Permanent order receipts](../../../features/6.0/order-receipts.md)
- [Order receipts and confirmation mail for businesses](../../businesses/stores/order-receipts-and-confirmation-mail.md)
- [Store reports](store-reports.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
