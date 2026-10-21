---
title: Order mail and receipts for operators
audience: ecommerce, customer, admin
category: e-commerce
order: 54
summary: The order mail and receipt links of an Exponential store from the operator's side - when the mail is sent, to whom and from which address, the template, the mail category, permanent receipt links in customer service, and what to check when a mail does not arrive.
keywords: order mail, order confirmation, SendOrderEmail, AdminEmail, EmailSender, orderemail.tpl, confirm order handler, receipt link, orderreceipt, OrderLinkView, customer service, mail not received
type: help
---
# Order mail and receipts for operators

This page is for store managers and administrators who answer "I did not get my confirmation" and "can you send me my
receipt again". It explains when and how the order mail is sent, and how permanent receipt links make customer service
easier. The customer's view is in [Order receipts and confirmation mail](../../businesses/stores/order-receipts-and-confirmation-mail.md)
and the wording of the mail in [Order confirmation mail](../../customers/commerce/order-confirmation-mail.md).

## Contents

1. [When the mail is sent](#when-the-mail-is-sent)
2. [To whom and from where](#to-whom-and-from-where)
3. [What is in it](#what-is-in-it)
4. [Receipt links in customer service](#receipt-links-in-customer-service)
5. [When a mail does not arrive](#when-a-mail-does-not-arrive)

## When the mail is sent

The `send-order-email` method of the checkout operation runs right after the order is activated
([Checkout operations step by step](../checkout/checkout-operations-step-by-step.md#step-5-check-out-and-pay)). It
asks the confirm order handler (`shopaccount.ini [ConfirmOrderSettings] Handler`, shipped `ezdefault`) to send it, and
that handler only sends when `site.ini [ShopSettings] SendOrderEmail=enabled` (shipped). So the mail goes out once,
when the order is placed, and not for status changes later. Mails for later statuses need an event or handler of your
own.

## To whom and from where

| Copy | Receiver | Sender |
|---|---|---|
| Customer | The e-mail address of the order (from the account handler) | `[MailSettings] EmailSender`, else `AdminEmail` |
| Shop | `site.ini [MailSettings] AdminEmail` | same |

Both copies have the same subject and body, and the mail category `orders`
(`kernel/classes/confirmorderhandlers/ezdefaultconfirmorderhandler.php`). If the site administrator's address is not
the right inbox for orders, change the handler rather than `AdminEmail`, which is used for other system mail too
([Basket info and confirm order handlers](../extending/basket-info-and-confirm-order-handlers.md)).

## What is in it

The body and the subject come from the template `shop/orderemail.tpl`; the subject is a template variable set in it.
The template can set `content_type` for an HTML mail. With `shop.ini [OrderViewSettings] OrderLinkView=orderreceipt`
the mail links to the permanent receipt ([shop.ini walkthrough](../settings/shop-ini-walkthrough.md#orderviewsettings)).

## Receipt links in customer service

With permanent receipts switched on:

- the customer can open, print and download the receipt from any device without signing in;
- staff with `shop/administrate` can open any receipt, so you can look at exactly what the customer sees;
- a lost mail is not a problem: the customer's receipt link still works, and signed-in customers find it in their
  order list.

Treat a receipt link like the receipt itself: send it only to the customer's own address
([Permanent order receipts](../../../features/6.0/order-receipts.md)).

## When a mail does not arrive

| Check | How |
|---|---|
| Was the order placed at all? | It has an order number in the order list; otherwise see [Troubleshooting checkout](../operations/troubleshooting-checkout.md) |
| Is `SendOrderEmail` enabled? | **Setup > Ini settings**, `site.ini`, `ShopSettings` |
| Is the address on the order right? | Order view, customer data |
| Did the shop copy arrive at `AdminEmail`? | If yes, the problem is the customer's address or spam filter |
| Does the site send mail at all? | [Notifications and mail](../../creators/app-creators/notifications-and-mail.md), the mail transport settings |
| Is the sender domain allowed to send? | Ask your host about the sender address and the domain's mail records |

## Related pages

- [Order operations](README.md)
- [Invoices and receipts](../../customers/commerce/invoices-and-receipts.md)
- [site.ini shop settings walkthrough](../settings/site-ini-shop-settings-walkthrough.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
