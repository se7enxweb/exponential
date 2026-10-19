---
title: PayPal payments
audience: businesses, admin, website-creator
category: business-solutions
order: 140
summary: Taking PayPal payments in an Exponential store with the ezpaypal extension - how the redirect and the payment notification work, what the extension checks before marking an order paid, the settings a business owner decides, and how to test.
keywords: PayPal, ezpaypal, paypal.ini, notify URL, payment notification, sandbox, redirect payment, PaymentMadeOrderStatusID, business account, payment status
type: help
---
# PayPal payments

This page is for business owners who want customers to pay with PayPal. It explains, from the business side, what the
`ezpaypal` extension does, what it checks before an order counts as paid, which settings you decide, and how to test
before going live. The setup reference is [ezpaypal](../../../features/6.0/extensions/ezpaypal.md).

## Contents

1. [Why PayPal](#why-paypal)
2. [How a PayPal payment works](#how-a-paypal-payment-works)
3. [What the extension checks](#what-the-extension-checks)
4. [What you decide](#what-you-decide)
5. [Order status after payment](#order-status-after-payment)
6. [Testing in the sandbox](#testing-in-the-sandbox)
7. [When something goes wrong](#when-something-goes-wrong)
8. [Version and license](#version-and-license)

## Why PayPal

For a small business, a redirect provider such as PayPal has two practical benefits:

- **Card details never pass through your site.** The customer pays on PayPal's page, so your server does not handle
  card numbers ([Card payments and PCI basics](card-payments-and-pci-basics.md)).
- **It ships with Exponential.** `se7enxweb/ezpaypal` is required by the root `composer.json` (`~1.2.3`), so it is
  installed with the system and only needs to be activated and configured.

## How a PayPal payment works

From [ezpaypal](../../../features/6.0/extensions/ezpaypal.md) and the extension's code
(`extension/ezpaypal/classes/ezpaypalgateway.php`):

1. The customer confirms the order and checks out.
2. The PayPal workflow event on the checkout sends the customer to PayPal, with your business account, a short order
   description, the order number, the amount and the currency.
3. The customer pays on PayPal's page.
4. PayPal sends a notification to your site's notify address, `/paypal/notify_url`.
5. The extension checks the notification (next section) and, if all is well, approves the payment and sets the order's
   status.

> **Warning**
> The notify address must be reachable from the internet and must not require a login
> ([Payment gateways](../../creators/website-creators/payment-gateways.md#setting-up-paypal)). A site behind HTTP
> basic authentication, an IP allow list or maintenance mode will take payments that never get marked as paid.

## What the extension checks

Before marking an order paid, the notify view (`extension/ezpaypal/modules/paypal/notify_url.php`) and the kernel's
callback checker (`kernel/shop/classes/ezpaymentcallbackchecker.php`) check that:

- PayPal confirms the notification is genuine (the checker posts it back to PayPal for validation);
- the payment status is **Completed**;
- the amount matches the order total including VAT;
- the currency matches the order's currency.

If any check fails, the order is not marked paid and the reason is written to the extension's log. This protects you
against forged notifications and against payments of the wrong amount.

## What you decide

All settings go in `settings/override/paypal.ini.append.php`, never in the shipped file
([ezpaypal](../../../features/6.0/extensions/ezpaypal.md#settings)):

| Setting | Decide |
|---|---|
| `[PaypalSettings] Business` | Your PayPal business account; required |
| `[ServerSettings] ServerName` | PayPal's sandbox for testing, the live server (shipped `https://www.paypal.com`) for real payments |
| `[PaypalSettings] MaxDescriptionLength` | How long the order description on PayPal may be (shipped 127) |
| `NoNote`, `NoteLabel`, `PageStyle`, `BackgroundColor`, `LogoURI` | How PayPal's page looks to your customer: your logo, a note field |
| `[OrderSettings] PaymentMadeOrderStatusID` | The order status set when payment is made (shipped 1000) |

## Order status after payment

The shipped value of `PaymentMadeOrderStatusID` is `1000`, which is the first number for **custom** statuses; the
seventeen shipped statuses use 1 to 17 ([Orders and order statuses](orders-and-order-statuses.md#custom-statuses)).
Before going live, decide one of these:

- point it at the shipped **Paid** status, 5, so the dashboard and the status path work with the standard statuses; or
- create a custom status with the number 1000 (for example "Paid with PayPal") and include it in your process.

Then place a test payment and check that the order shows the status you chose.

## Testing in the sandbox

- [ ] `ServerName` points at PayPal's sandbox server, with a sandbox business account in `Business`.
- [ ] A test payment completes and the order gets the paid status.
- [ ] A canceled payment leaves the order unpaid, and the customer can return to the basket.
- [ ] The notify address is reached over HTTPS from outside (no login, no IP block).
- [ ] An order in a second currency is accepted by PayPal and matched on return.
- [ ] `ServerName` and `Business` are switched to live values on launch day, and one small real payment is made and
  refunded.

PayPal's own account types, fees and integration rules are set by PayPal; check with them that your account supports
this kind of redirect payment and notification before you build on it.

## When something goes wrong

| Symptom | First thing to check |
|---|---|
| Customer paid, order not marked paid | Is the notify address reachable without login? Does the log show a failed check? |
| "Amount does not match" in the log | Rounding or a currency mismatch ([Prices and currencies](prices-and-currencies.md)) |
| Customers land on the sandbox after launch | `ServerName` still points at the sandbox |
| Status is set but not shown in your process | `PaymentMadeOrderStatusID` points at a status you do not use |

For errors on the site in general, see [When something goes wrong](../../when-something-goes-wrong.md).

## Version and license

The Exponential releases of ezpaypal (1.2.0 to 1.2.3) are GPL v2 or later; the extension's own README still describes
its original dual licensing, and payment behavior is unchanged in these releases
([ezpaypal](../../../features/6.0/extensions/ezpaypal.md#what-changed-in-the-exponential-6-releases)).

## Related pages

- [ezpaypal](../../../features/6.0/extensions/ezpaypal.md)
- [Payment gateways for businesses](payment-gateways-for-businesses.md)
- [Card payments and PCI basics](card-payments-and-pci-basics.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
