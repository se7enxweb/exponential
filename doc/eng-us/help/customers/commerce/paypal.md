---
title: PayPal
audience: customer, admin, businesses
category: customer-success
order: 110
summary: Running PayPal payments in an Exponential store with the ezpaypal extension - the daily and monthly checks, keeping PayPal's status in line with your order statuses, what to watch on launch and after changes, and what to do when a paid order is not marked paid.
keywords: PayPal, ezpaypal, paypal.ini, notify URL, PaymentMadeOrderStatusID, sandbox, payment not marked paid, refunds, PayPal account
type: help
---
# PayPal

This page is for owners and store managers whose store takes PayPal through the `ezpaypal` extension. It is about
running it: the checks, the one setting most stores get wrong, and what to do when something does not match. How the
extension works and what it checks before it marks an order paid is on
[PayPal payments for businesses](../../businesses/stores/paypal-payments.md); the settings reference is
[ezpaypal](../../../features/6.0/extensions/ezpaypal.md).

## What runs on your store

`se7enxweb/ezpaypal` is required by the root `composer.json`, so it is installed with Exponential. When it is
activated and on the checkout, the customer is sent to PayPal to pay, and PayPal notifies your site at
`/paypal/notify_url`. Before the order is marked paid, the extension checks with PayPal that the notification is
genuine, that the payment status is Completed, and that amount and currency match the order
([PayPal payments for businesses](../../businesses/stores/paypal-payments.md)).

Card details are typed on PayPal's page, never on yours ([Card payments and PCI](card-payments-and-pci.md)).

## The setting to check first

`paypal.ini [OrderSettings] PaymentMadeOrderStatusID` is the order status set when PayPal confirms a payment. Its
shipped value is `1000`, which is the first number for custom statuses, not the shipped **Paid** status (5)
([ezpaypal](../../../features/6.0/extensions/ezpaypal.md)). Ask your partner which value your store uses, and make
sure it is a status on your path ([Order statuses](order-statuses.md)):

| Your choice | Then |
|---|---|
| Point it at Paid (5) | The dashboard's paid figures and your status path use the standard status |
| Keep 1000 | A custom status with the number 1000 must exist, for example "Paid with PayPal" |

## Who does what

| Task | Who |
|---|---|
| The PayPal business account, fees, disputes | Owner |
| Refunds in the PayPal account | Owner, or one named person |
| `paypal.ini` overrides, the notify address, updates | Partner or administrator (settings go in `settings/override/paypal.ini.append.php`) |
| Daily comparison of PayPal payments and orders | Store manager |

## Every day

- [ ] Every PayPal payment received has an order with your paid status.
- [ ] No order with your paid status lacks a PayPal payment.
- [ ] Orders in Payment failed or still Pending after a PayPal payment are looked at the same day.

## Every month

- [ ] PayPal's monthly statement matches the paid orders of the month ([Store reports](store-reports.md)).
- [ ] Refunds made in PayPal are reflected as Refunded or Partially refunded on the orders.
- [ ] PayPal's messages about account or interface changes are forwarded to your partner.

## When a paid order is not marked paid

Work through the table from [PayPal payments for businesses](../../businesses/stores/paypal-payments.md):

| Symptom | First thing to check, or ask your partner |
|---|---|
| Customer paid, order not marked paid | Is `/paypal/notify_url` reachable from the internet without a login? Does the extension's log show a failed check? |
| "Amount does not match" in the log | Rounding or currency differences ([Prices and currencies](prices-and-currencies.md)) |
| Customers land on the sandbox | `[ServerSettings] ServerName` still points at the sandbox |
| A status is set but nobody sees it | `PaymentMadeOrderStatusID` points at a status you do not use |

Until it is fixed, handle the affected orders by hand: confirm the payment in PayPal, then set the order to your
paid status yourself, and note why in your log.

> **Warning**
> Maintenance mode, a login in front of the site, or an IP allow list blocks PayPal's notification too. Plan work
> that does this outside shop hours, and check the orders placed just before and after.

## After any change

Changes to the site, the server, HTTPS or the PayPal extension can affect the notification. After each one:

- [ ] One small real payment, then a refund in PayPal, and the order statuses checked.
- [ ] Or, if your partner has a sandbox setup on a copy, a full test there first.

## Related pages

- [Payment gateways](payment-gateways.md)
- [PayPal payments for businesses](../../businesses/stores/paypal-payments.md)
- [ezpaypal](../../../features/6.0/extensions/ezpaypal.md)
- [Order statuses](order-statuses.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
