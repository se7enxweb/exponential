---
title: Payment gateway development
audience: ecommerce, app-creator, developer
category: e-commerce
order: 63
summary: Building a payment gateway for a provider Exponential does not support yet - redirect or transparent, the files of the extension, the gateway class, the payment object, the callback view with its checks, registration, logging, security rules and a test plan, modeled on the ezpaypal extension.
keywords: payment gateway development, custom gateway, eZRedirectGateway, eZPaymentGateway, eZPaymentObject, eZPaymentCallbackChecker, registerGateway, createPaymentObject, createRedirectionUrl, notify_url, PolicyOmitList, AllowedRedirectHosts, webhook, callback, provider integration
type: help
---
# Payment gateway development

This page is for developers who connect an Exponential store to a payment provider for which no extension exists. The
repository ships the base classes and two complete examples, `ezpaypal` (redirect) and `ezauthorize` (transparent).
This page describes how they are put together so you can build the next one.

## Contents

1. [Redirect or transparent](#redirect-or-transparent)
2. [The extension's files](#the-extensions-files)
3. [The gateway class](#the-gateway-class)
4. [The callback](#the-callback)
5. [Settings the extension adds](#settings-the-extension-adds)
6. [Security rules](#security-rules)
7. [Test plan](#test-plan)

## Redirect or transparent

| | Redirect gateway | Transparent gateway |
|---|---|---|
| Base class | `eZRedirectGateway` | `eZPaymentGateway` |
| Card data | Entered at the provider | Entered on your site, or turned into a token by the provider's script |
| Visits | Two: out to the provider, back through the callback | One request |
| State between visits | `eZPaymentObject` | none needed |
| Example | `ezpaypal` | `ezauthorize` (through `eZCurlGateway`) |
| Card security duties | Lower | Higher ([Card payments and PCI basics](../../businesses/stores/card-payments-and-pci-basics.md)) |

Prefer a redirect or a provider-hosted payment page when the provider offers one.

## The extension's files

Modeled on `extension/ezpaypal/`:

```text
extension/mypay/
  classes/mypaygateway.php        the gateway; loaded automatically for an active extension
  classes/mypaychecker.php        the callback checker (extends eZPaymentCallbackChecker)
  modules/mypay/module.php        a module with one view for the provider's callback
  modules/mypay/notify.php
  settings/module.ini.append      ModuleList[]=mypay, ExtensionRepositories[]=mypay
  settings/site.ini.append        AllowedRedirectHosts[], PolicyOmitList[]=mypay/notify
  settings/mypay.ini              provider settings without secrets
```

The gateway file name must be `classes/<extension>gateway.php` to be loaded automatically
([paymentgateways.ini walkthrough](../settings/paymentgateways-ini-walkthrough.md#how-gateways-are-loaded)).

## The gateway class

For a redirect gateway, extend `eZRedirectGateway` (`kernel/shop/classes/ezredirectgateway.php`) and implement:

| Method | Does |
|---|---|
| `createPaymentObject( $processID, $orderID )` | Returns an `eZPaymentObject::createNew( $processID, $orderID, '<type>' )`; it survives the trip to the provider |
| `createRedirectionUrl( $process )` | Builds the provider's address with the amount, currency, order reference, and your return and callback addresses |

The base class does the rest: on the first run it stores the payment object and redirects; on the second it accepts if
the payment object is approved and rejects otherwise; `cleanup()` removes abandoned payment objects. At the foot of
the file:

```php
eZPaymentGatewayType::registerGateway( 'mypay', 'mypaygateway', 'My provider' );
```

For a transparent gateway, extend `eZPaymentGateway` and implement `execute( $process, $event )`, returning
`eZWorkflowType::STATUS_ACCEPTED` or `STATUS_REJECTED`, or a template to show a form
(`STATUS_FETCH_TEMPLATE_REPEAT`). `createShortDescription( $order, $maxDescLen )` of the base class gives an order
description cut to the provider's limit.

The RAD handler wizard has recipes for both kinds and writes a skeleton with comments on every method
([Developer path](../roles/developer-path.md#generators-that-save-time)); compare its output with the loading paths
above before relying on it.

## The callback

The callback view receives the provider's message and, with a checker extending `eZPaymentCallbackChecker`
(`kernel/shop/classes/ezpaymentcallbackchecker.php`), should:

1. Read the message (`createDataFromPOST()` or `createDataFromGET()`).
2. Validate it with the provider (`requestValidation()`) and, where the provider documents its addresses,
   `checkServerIP()`.
3. Load the order and payment object (`setupOrderAndPaymentObject()`).
4. Check amount and currency against the order (`checkAmount()`, `checkCurrency()`).
5. Approve and continue the workflow (`approvePayment()`), which runs the rest of the checkout: activation and the
   order mail.
6. Optionally set an order status, as `ezpaypal` does with `PaymentMadeOrderStatusID`.

Answer the provider quickly; the customer's browser is meanwhile waiting on `shop/checkout`, which polls for up to 25
seconds ([Checkout operations step by step](../checkout/checkout-operations-step-by-step.md#step-6-back-from-the-provider)).

## Settings the extension adds

| Setting | Why |
|---|---|
| `site.ini [SiteSettings] AllowedRedirectHosts[]` | The provider's host, so the redirect is allowed |
| `site.ini [RoleSettings] PolicyOmitList[]` | The callback view, so the provider can call it without signing in |
| `module.ini [ModuleSettings] ModuleList[]`, `ExtensionRepositories[]` | The callback module |
| Your provider file | Test mode switch, endpoint, merchant id; credentials only in `settings/override/` |

## Security rules

- Never trust the customer's return to your site as proof of payment; only the validated callback approves.
- Check amount and currency on every callback; refuse a mismatch.
- Never write card numbers or security codes to logs, the database or the audit trail. The kernel's audit records
  payments without card data, and your code must do the same.
- Keep the test mode switch and make it obvious in the administration which mode is active.
- See [Security for applications](../../creators/app-creators/security-for-applications.md) and
  [Forms, CSRF and HTTP input](../../creators/app-creators/forms-csrf-and-http-input.md).

## Test plan

- [ ] The gateway appears in the Payment Gateway event's list.
- [ ] A successful payment in test mode: order placed, number given, mail sent, status set.
- [ ] A cancelled payment: no placed order, basket intact, the customer can retry.
- [ ] A callback with a wrong amount: refused, logged without card data.
- [ ] A repeated callback: no second activation.
- [ ] A late callback (after the 25 seconds): the order is still activated, the customer finds it in the order list.
- [ ] The kernel's walkthrough in `doc/eng-us/shop/examples/paypal/` compared with your code.

## Related pages

- [Extending the shop](README.md)
- [Payment gateway event](../checkout/payment-gateway-event.md)
- [Writing a workflow event type](../../creators/app-creators/writing-a-workflow-event-type.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
