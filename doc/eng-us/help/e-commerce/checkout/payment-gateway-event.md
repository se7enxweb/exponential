---
title: Payment gateway event
audience: ecommerce, website-creator, app-creator
category: e-commerce
order: 44
summary: How the Payment Gateway workflow event of Exponential works - available, selected and current gateways, the customer's choice when several are selected, redirect and transparent gateways, the payment object, the provider's callback that continues the workflow, and its logs.
keywords: payment gateway event, ezpaymentgateway, eZPaymentGatewayType, selectgateway.tpl, eZRedirectGateway, eZPaymentGateway, eZPaymentObject, eZPaymentCallbackChecker, callback, notify URL, continueWorkflow, STATUS_REDIRECT_REPEAT, payment logs
type: help
---
# Payment gateway event

This page is for developers and administrators who set up payment in an Exponential store. Payment is a workflow
event, **Payment Gateway** (`ezpaymentgateway`), on the before trigger of `shop_checkout`. This page explains what the
event does with the gateways, how a redirect payment comes back, and where to look when it does not.

## Contents

1. [Available, selected and current](#available-selected-and-current)
2. [When several gateways are selected](#when-several-gateways-are-selected)
3. [Redirect gateways](#redirect-gateways)
4. [Transparent gateways](#transparent-gateways)
5. [The callback](#the-callback)
6. [Logs](#logs)
7. [Checklist](#checklist)

## Available, selected and current

The event type (`kernel/classes/workflowtypes/event/ezpaymentgateway/ezpaymentgatewaytype.php`) knows three sets:

| Set | Meaning |
|---|---|
| Available | Every gateway that was loaded and called `registerGateway()` ([paymentgateways.ini walkthrough](../settings/paymentgateways-ini-walkthrough.md)) |
| Selected | The gateways you tick when you edit the event in the workflow |
| Current | The gateway this payment goes through |

## When several gateways are selected

If one gateway is selected, it is used. If several are, the event shows the template `workflow/selectgateway.tpl`
and the customer picks one; the workflow then continues with that gateway. Style that template like the rest of the
checkout, and give each choice a clear label ([Store accessibility](../operations/store-accessibility.md)).

## Redirect gateways

A redirect gateway extends `eZRedirectGateway` (`kernel/shop/classes/ezredirectgateway.php`), as `ezpaypal` does:

1. First run: it creates a **payment object** (`eZPaymentObject`: workflow process id, order id, payment type, status
   "not approved"), stores it, builds the provider's address and returns `STATUS_REDIRECT_REPEAT`. The customer is sent
   to the provider; the workflow process waits.
2. The provider calls the site back (see below), which approves the payment object and continues the workflow.
3. Second run: the payment object is approved, so the gateway returns `STATUS_ACCEPTED`; otherwise
   `STATUS_REJECTED`.
4. The checkout operation goes on: activation, order mail, after trigger.

A payment the customer abandons leaves a payment object; the gateway's `cleanup()` removes it when the workflow
process is cleaned up.

## Transparent gateways

A transparent gateway extends `eZPaymentGateway` directly (`ezauthorize` does, through `eZCurlGateway`). It shows a
card form on your site, sends the charge from your server and answers accepted or rejected in the same request. It is
simpler to follow, but card data passes through your server; read
[Card payments and PCI basics](../../businesses/stores/card-payments-and-pci-basics.md) before choosing one.

## The callback

`eZPaymentCallbackChecker` (`kernel/shop/classes/ezpaymentcallbackchecker.php`) is the base for the provider's
callback. Its methods show what a careful callback does:

| Method | Checks |
|---|---|
| `requestValidation()` | Asks the provider to confirm the message is genuine |
| `checkServerIP()` | The request comes from the provider's servers |
| `checkAmount()`, `checkCurrency()` | The amount and currency match the order |
| `checkDataField()` | A field has the expected value |
| `setupOrderAndPaymentObject()` | Loads the order and its payment object |
| `approvePayment()` | Approves the payment object and continues the workflow (`eZPaymentObject::continueWorkflow()`) |

With `ezpaypal` the callback address is `paypal/notify_url`, exempt from policy checks so the provider can reach it
without signing in. The address must be reachable from the internet over HTTPS, and not blocked by a login, a
maintenance page or an IP filter. When the audit trail is active, approving a payment records `commerce.payment.approve`
([Commerce audit trail](../operations/commerce-audit-trail.md)).

## Logs

| File | Written by |
|---|---|
| `var/log/eZPaymentGatewayType.log` | The event: execute, gateway selection, failures |
| `var/log/eZRedirectGateway.log` | Redirect gateways: payment object, redirect, accepted or rejected |

Read them together with the workflow process list (**Setup > Workflow processes**), which shows a payment process that
is still waiting ([Troubleshooting checkout](../operations/troubleshooting-checkout.md)).

## Checklist

- [ ] The event is on `shop` `checkout` **before**.
- [ ] Only the gateways you want are selected in the event.
- [ ] The provider's test mode or sandbox is set while testing, and switched back before launch.
- [ ] The callback address answers from outside your network.
- [ ] The status set on payment exists in your status list ([PayPal payments](../../businesses/stores/paypal-payments.md)).
- [ ] A cancelled payment leaves no placed order and lets the customer try again.

## Related pages

- [Checkout and workflows](README.md)
- [Payment gateway development](../extending/payment-gateway-development.md)
- [Payment gateways (website creators)](../../creators/website-creators/payment-gateways.md)
- [Payment gateways for businesses](../../businesses/stores/payment-gateways-for-businesses.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
