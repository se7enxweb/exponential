---
title: Payment gateways
audience: website-creator, developer
category: website-creation
order: 550
summary: Take payments in the shop - how gateways plug into the checkout workflow, the shipped PayPal and Authorize.Net extensions, paymentgateways.ini, test mode, callbacks and what a gateway of your own needs.
keywords: payment, payment gateway, PayPal, ezpaypal, ezauthorize, Authorize.Net, paymentgateways.ini, checkout workflow, callback, notify URL, test mode, website creator
type: help
---
# Payment gateways

This page is for website creators who connect a shop to a payment provider. In Exponential a payment gateway is a
**workflow event** on the checkout: when the customer checks out, the event sends them to the provider (or charges
directly) and marks the order paid when the provider confirms.

## Contents

1. [How payment fits into checkout](#how-payment-fits-into-checkout)
2. [The gateways you can use](#the-gateways-you-can-use)
3. [Setting up PayPal](#setting-up-paypal)
4. [Testing](#testing)
5. [A gateway of your own](#a-gateway-of-your-own)

## How payment fits into checkout

1. `settings/paymentgateways.ini [GatewaysSettings] AvailableGateways[]` lists the active gateways;
   `GatewaysDirectories[]` where their classes are (`kernel/shop/paymentgateways` and extensions).
2. A workflow with the payment gateway event is connected to the `shop_checkout` trigger (before)
   ([Workflows](../../../guides/workflows.md)).
3. At checkout the event runs. A redirect gateway sends the customer to the provider; the provider calls back the site
   to confirm; the order gets its paid status.

The kernel's payment classes are documented in `doc/eng-us/shop/` (gateway, redirect gateway, payment object, callback
checker, and a PayPal example).

## The gateways you can use

| Extension | Provider | Read |
|---|---|---|
| `ezpaypal` | PayPal (redirect, NVP API, notify URL `/paypal/notify_url`) | [ezpaypal](../../../features/6.0/extensions/ezpaypal.md) |
| `ezauthorize` | Authorize.Net | its own documentation in `extension/ezauthorize/` |

## Setting up PayPal

From [ezpaypal](../../../features/6.0/extensions/ezpaypal.md):

1. Activate the extension.
2. Create `settings/override/paypal.ini.append.php` and set at least `[PaypalSettings] Business` (your PayPal business
   account).
3. Test with PayPal's sandbox server in `[ServerSettings] ServerName` before going live.
4. Add the PayPal workflow event to the checkout workflow.
5. `[OrderSettings] PaymentMadeOrderStatusID` (`1000`) is the status set when the payment is made.

The notify URL must be reachable from the internet and must not require a login.

## Testing

- [ ] A payment in the provider's test mode completes and the order becomes paid.
- [ ] A cancelled payment leaves the order unpaid and the customer can try again.
- [ ] The callback works over HTTPS and through any proxy (check the trusted proxies of your setup).
- [ ] The Store dashboard no longer says there is no payment step on checkout.

## A gateway of your own

A gateway is a class extending the kernel's gateway classes (`eZPaymentGateway`, or `eZRedirectGateway` for redirect
providers), registered in `paymentgateways.ini` of an extension, plus a module view for the provider's callback. The
`doc/eng-us/shop/examples/paypal/` pages walk through one. This is application creator work: see
[Writing a workflow event type](../app-creators/writing-a-workflow-event-type.md).

**Setup > RAD** (`/setup/handlerextension/paymentgateway`, or `paymentgatewaydirect` for a gateway that takes the
payment without leaving the site) writes the skeleton. Check that the generated class is at
`extension/<name>/paymentgateways/<alias>/classes/<alias>gateway.php`: the kernel loads
`<GatewaysDirectories>/<gateway>/classes/<gateway>gateway.php` for each name in `AvailableGateways[]` and nothing
else. Earlier versions of the tool wrote `paymentgateways/<alias>gateway.php`, which is never loaded.

## Related pages

- [Running a shop: overview](shop-overview.md)
- [Basket, checkout and orders](basket-checkout-and-orders.md)
- [Security for applications](../app-creators/security-for-applications.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
