---
title: Order receipts and confirmation mail
audience: businesses, admin, website-creator
category: business-solutions
order: 110
summary: What a customer receives after buying in an Exponential store - the order confirmation mail to customer and shop, the permanent receipt link that never expires, and what to check before launch.
keywords: order confirmation, order mail, order email, receipt, permanent receipt, orderreceipt, AdminEmail, EmailSender, SendOrderEmail, print receipt, mail delivery
type: help
---
# Order receipts and confirmation mail

This page is for business owners who want customers to get a clear confirmation of every order and a receipt they can
find again. It explains the order mail, the permanent receipt link, who can open a receipt, and the settings involved.
The reference pages are [Permanent order receipts](../../../features/6.0/order-receipts.md) and
[Basket, checkout and orders](../../creators/website-creators/basket-checkout-and-orders.md#orders).

## Contents

1. [What the customer gets](#what-the-customer-gets)
2. [The confirmation mail](#the-confirmation-mail)
3. [The permanent receipt](#the-permanent-receipt)
4. [Who can open a receipt](#who-can-open-a-receipt)
5. [Mail that arrives](#mail-that-arrives)
6. [Receipts are not invoices](#receipts-are-not-invoices)
7. [Checklist](#checklist)

## What the customer gets

1. After payment, a page with the placed order.
2. A confirmation mail.
3. If you turn it on, a **permanent receipt** link in both, which works on any device, for years, with or without an
   account.

## The confirmation mail

The shop sends order mail when `site.ini [ShopSettings] SendOrderEmail` is `enabled`, which is the shipped value. The
default confirm order handler (`kernel/classes/confirmorderhandlers/ezdefaultconfirmorderhandler.php`) then:

- renders the template `shop/orderemail.tpl`;
- sends it to the customer's e-mail address, as the shop account handler knows it;
- sends a second copy to `site.ini [MailSettings] AdminEmail`, the shop's own address;
- uses `[MailSettings] EmailSender` as the sender, or `AdminEmail` when no sender is set;
- marks the mail with the category `orders`.

The shipped `AdminEmail` is a placeholder (`nospam@example.com` in `settings/site.ini`); set your real order address in
an override before launch, or the shop's copies go nowhere.

Order mail is essential e-mail: the mail preferences feature always sends it and it never carries advertising
([Mail preferences](../../../features/6.0/mail-preferences.md)). Keep marketing out of the order mail template and
send offers through a newsletter with consent instead ([Newsletters](../newsletters/README.md)).

## The permanent receipt

With one setting, `shop.ini [OrderViewSettings] OrderLinkView=orderreceipt`, the page after checkout, the customer's
order lists and the confirmation mail link to `shop/orderreceipt/<token>` instead of `shop/orderview/<id>`
([Permanent order receipts](../../../features/6.0/order-receipts.md#turn-it-on)).

Why it matters for a business:

- The classic `shop/orderview/<id>` link stops working for a guest once the browser session expires; the receipt link
  does not.
- The receipt page offers **Print** (the receipt alone, without the site around it) and **Download** (a
  self-contained HTML file), so customers can keep it for their own records.
- The address is signed: it carries the order, its time and its customer, and nothing extra is stored in the database.
- A misspelled `orderreciept` address redirects to the right one.

The administration keeps linking orders to `shop/orderview`, where staff manage them.

## Who can open a receipt

| Visitor | Opens it? |
|---|---|
| Anyone holding the link | yes, so the link is as private as the receipt |
| The signed-in customer who placed the order | yes, without the link |
| Staff with `shop/administrate` | yes, any receipt |
| Anyone with a wrong token | no, they get the login form |

Source: [Permanent order receipts](../../../features/6.0/order-receipts.md#who-can-open-a-receipt). The receipt
footer tells the customer to keep the link private.

> **Warning**
> The receipt addresses are signed with a secret (`shop.ini [OrderReceiptSettings] Secret`). Changing the secret
> changes every receipt address, so links in old mails stop working. Leave it alone once the store is live, and keep
> it out of the shipped settings file (the feature page explains where it is kept).

## Mail that arrives

A confirmation that lands in spam is a support call. Mail delivery depends on the server's mail setup, the sender
domain and its records, which your host handles ([Mail delivery](../../hosting/operations/mail-delivery.md)).

- Send from an address at your own domain (`EmailSender`).
- Make sure replies to the order mail reach someone.
- Test with the mail providers your customers use most.

## Receipts are not invoices

The receipt shows the order as placed: items, quantities, VAT and totals. Whether it is enough as a tax invoice
depends on the law that applies to you; ask your tax adviser. Invoices in an accounting system are among the things
the shop leaves to an integration ([Running a shop: overview](../../creators/website-creators/shop-overview.md#what-the-shop-does-not-do)).

## Checklist

- [ ] `SendOrderEmail` is `enabled`.
- [ ] `AdminEmail` and `EmailSender` are your real addresses (in an override, not the shipped file).
- [ ] The order mail template carries your name, contact details and terms.
- [ ] `OrderLinkView=orderreceipt` is set if guests buy.
- [ ] A test order's mail arrives, in the inbox not the spam folder.
- [ ] The receipt link opens on a phone without signing in, and prints cleanly.
- [ ] The receipt secret is set once and recorded with your other secrets.

## Related pages

- [Permanent order receipts](../../../features/6.0/order-receipts.md)
- [Customer accounts and guest checkout](customer-accounts-and-guest-checkout.md)
- [Orders and order statuses](orders-and-order-statuses.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
