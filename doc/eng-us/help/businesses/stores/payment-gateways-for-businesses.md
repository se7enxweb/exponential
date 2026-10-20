---
title: Payment gateways for businesses
audience: businesses, project-manager, website-creator
category: business-solutions
order: 130
summary: How payments work in an Exponential store - gateways as a checkout workflow step, redirect and on-site card form gateways, the PayPal and Authorize.Net extensions, adding another provider, and how to choose.
keywords: payment gateway, payment provider, PayPal, ezpaypal, Authorize.Net, ezauthorize, redirect payment, card form, paymentgateways.ini, checkout workflow, choosing a payment provider
type: help
---
# Payment gateways for businesses

This page is for business owners who choose how their customers pay. It explains how a payment provider plugs into an
Exponential store, the two kinds of gateway, the providers the project offers extensions for, and the questions to ask
before you sign with a provider. The setup is described in
[Payment gateways](../../creators/website-creators/payment-gateways.md).

## Contents

1. [How payment fits in](#how-payment-fits-in)
2. [Two kinds of gateway](#two-kinds-of-gateway)
3. [The gateways the project offers](#the-gateways-the-project-offers)
4. [Other providers](#other-providers)
5. [Choosing a provider](#choosing-a-provider)
6. [Payment and order statuses](#payment-and-order-statuses)
7. [Testing payments](#testing-payments)

## How payment fits in

In Exponential a payment gateway is a **workflow event** on the checkout. When the customer checks out, the event sends
them to the provider or charges them directly, and marks the order paid when the provider confirms
([Payment gateways](../../creators/website-creators/payment-gateways.md)). In practice:

1. `settings/paymentgateways.ini` lists the active gateways.
2. A workflow with the payment gateway event (`ezpaymentgateway`, in `kernel/classes/workflowtypes/event/`) is connected
   to the `shop_checkout` trigger.
3. At checkout, the customer pays; the order is placed when the workflow finishes.

The kernel provides the building blocks: `eZPaymentGateway`, `eZRedirectGateway`, `eZPaymentObject` and
`eZPaymentCallbackChecker` in `kernel/shop/classes/`, documented in `doc/eng-us/shop/`.

If no payment step is on checkout, orders are placed without payment. The Store dashboard warns about that in its
"Next steps" ([Store dashboard and reports](store-dashboard-and-reports.md#next-steps)). Some businesses want exactly
that, for invoice or pay-on-pickup orders.

## Two kinds of gateway

| Kind | What the customer sees | What your site handles | Example |
|---|---|---|---|
| Redirect | Leaves your site to pay on the provider's page, then returns | No card data; a callback from the provider | `ezpaypal` |
| On-site card form | Types card details into a form on your site | Card data passes through your server to the provider | `ezauthorize` |

The difference matters for card security. With a redirect, card details are typed on the provider's page; with an
on-site form, they pass through your site. See [Card payments and PCI basics](card-payments-and-pci-basics.md).

## The gateways the project offers

| Extension | Provider | How it works | Status |
|---|---|---|---|
| `ezpaypal` 1.2.3 | PayPal | Redirect to PayPal; PayPal notifies `/paypal/notify_url` | Required by the root `composer.json` (`se7enxweb/ezpaypal ~1.2.3`) |
| `ezauthorize` | Authorize.Net | Card form on your site, sent to Authorize.Net's AIM interface | Suggested in `composer.json` (`se7enxweb/ezauthorize`) |

Sources: [ezpaypal](../../../features/6.0/extensions/ezpaypal.md), the root `composer.json`, and the extension's own
files (`extension/ezauthorize/doc/README`, `classes/ezauthorizeaim.php`). The `composer.json` suggestion describes
ezauthorize as a "transparent (no redirect away to paypal.com website distractions)" payment solution.

More detail: [PayPal payments](paypal-payments.md) and [Card payments and PCI basics](card-payments-and-pci-basics.md).

## Other providers

Many businesses already have a bank or payment provider. If the project has no extension for it, a gateway can be
written: a class extending `eZPaymentGateway` or, for redirect providers, `eZRedirectGateway`, registered in
`paymentgateways.ini` of an extension, plus a view for the provider's callback. The `doc/eng-us/shop/examples/paypal/` pages
walk through one ([Payment gateways](../../creators/website-creators/payment-gateways.md#a-gateway-of-your-own)).

This is application creator work ([Writing a workflow event type](../../creators/app-creators/writing-a-workflow-event-type.md)).
A redirect gateway is usually the simpler and safer project. If you commission one, ask that it is published as an
extension others can use ([Contribute extensions back](../../contribute-extensions-back.md)).

## Choosing a provider

- [ ] Does an Exponential extension exist for it, or must one be written?
- [ ] Redirect or card form on your site? (Redirect keeps card data off your server.)
- [ ] Which payment methods do your customers expect?
- [ ] Which currencies does it settle, and at what rates ([Prices and currencies](prices-and-currencies.md))?
- [ ] Does it have a test mode, and can you test the whole checkout there?
- [ ] How do refunds work, and who does them?
- [ ] What does it charge per transaction and per month? (Compare offers yourself; prices change.)
- [ ] What does your contract require from you on card security?

## Payment and order statuses

A gateway can set the order's status when payment is confirmed. The shipped statuses include **Awaiting payment**,
**Paid** and **Payment failed** ([Orders and order statuses](orders-and-order-statuses.md)). Align the gateway's
status with them, so the dashboard counts paid and failed orders correctly. For PayPal the setting is
`paypal.ini [OrderSettings] PaymentMadeOrderStatusID` ([PayPal payments](paypal-payments.md#order-status-after-payment)).

## Testing payments

From [Payment gateways](../../creators/website-creators/payment-gateways.md#testing):

- [ ] A payment in the provider's test mode completes and the order becomes paid.
- [ ] A canceled payment leaves the order unpaid and the customer can try again.
- [ ] The callback works over HTTPS and through any proxy.
- [ ] The Store dashboard no longer says there is no payment step on checkout.

## Related pages

- [Payment gateways](../../creators/website-creators/payment-gateways.md)
- [PayPal payments](paypal-payments.md)
- [Card payments and PCI basics](card-payments-and-pci-basics.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
