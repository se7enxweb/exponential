---
title: Order confirmation mail
audience: customer, admin, hosting
category: customer-success
order: 160
summary: Keeping the order confirmation mail of an Exponential store working and useful - who receives it, the settings behind it, a monthly delivery check, what to put in it and what not, and what to ask your host when mails go missing.
keywords: order confirmation, order mail, order email, AdminEmail, EmailSender, SendOrderEmail, orderemail.tpl, mail delivery, spam folder, essential mail
type: help
---
# Order confirmation mail

This page is for the owner and the store manager. The confirmation mail is often the only thing a customer keeps of
an order, and a mail that goes missing becomes a support call. This page gives the checks to keep it arriving and the
decisions about its content. What the mail contains and the receipt link it can carry are explained on
[Order receipts and confirmation mail for businesses](../../businesses/stores/order-receipts-and-confirmation-mail.md).

## How it is sent

When `site.ini [ShopSettings] SendOrderEmail` is `enabled` (the shipped value), the default confirm order handler
(`kernel/classes/confirmorderhandlers/ezdefaultconfirmorderhandler.php`) renders `shop/orderemail.tpl` and sends:

- one mail to the customer's e-mail address;
- one copy to `site.ini [MailSettings] AdminEmail`, the shop's own address;
- with `[MailSettings] EmailSender` as the sender, or `AdminEmail` when no sender is set.

The shipped `AdminEmail` is a placeholder (`nospam@example.com` in `settings/site.ini`) and `EmailSender` is empty.
Your partner sets both in an override.

> **Note**
> Order mail is essential mail: the e-mail preferences feature always sends it and it never carries advertising
> ([E-mail preferences](../../../features/6.0/mail-preferences.md)). Customers cannot switch it off, and it should
> not be used for offers.

## Who does what

| Task | Who |
|---|---|
| Reads the shop's copies every day | Store manager |
| Decides the mail's wording, contact details and terms | Owner |
| Changes the template `shop/orderemail.tpl` in the site design | Partner or designer |
| Sends mail from the server, sender domain records, delivery | Host ([Mail delivery](../../hosting/operations/mail-delivery.md)) |

## A monthly delivery check

- [ ] Place a test order (or use a real small one) with addresses at the mail providers your customers use most.
- [ ] The mail arrives in the inbox, not the spam folder.
- [ ] The sender is an address at your own domain, and replies reach a person.
- [ ] The shop's copy arrived at the `AdminEmail` address.
- [ ] Links in the mail open (the receipt link, if permanent receipts are on: [Invoices and receipts](invoices-and-receipts.md)).
- [ ] The mail reads well on a phone.

## What belongs in the mail

| Include | Keep out |
|---|---|
| Your business name and contact details | Advertising and newsletter offers |
| The order number and the receipt link | Anything the customer did not order |
| What happens next: when it ships, how to ask questions | Internal codes or staff notes |
| Your returns terms, or a link to them | Card details of any kind |

Wording changes are template changes, so collect them and ask your partner for them in one go.

## When mails go missing

| Sign | Likely cause | Ask |
|---|---|---|
| No copies at the `AdminEmail` address since a given day | Mail sending on the server, or the address changed | Host |
| Customers find the mail in spam | Sender domain records, or the sender address | Host ([Mail delivery](../../hosting/operations/mail-delivery.md)) |
| Only one mail provider's customers miss it | That provider rejects or filters the mail | Host, with an example order |
| No mails at all, customers and shop | `SendOrderEmail` switched off, or the mail transport | Partner |

Until it is fixed, send affected customers their receipt link or a short confirmation by hand from the shop's
mailbox.

## Related pages

- [Invoices and receipts](invoices-and-receipts.md)
- [Order receipts and confirmation mail for businesses](../../businesses/stores/order-receipts-and-confirmation-mail.md)
- [Mail delivery](../../hosting/operations/mail-delivery.md)
- [E-mail preferences](../../../features/6.0/mail-preferences.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
