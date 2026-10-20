---
title: Payment gateways
audience: customer, admin, businesses
category: customer-success
order: 100
summary: Living with the payment step of an Exponential store - checks to do every day and month, what to watch when the provider changes something, signs that payments are not reaching orders, and what to ask your partner and provider.
keywords: payment gateway, payment provider, payment step, checkout workflow, payment failed, payment notification, paymentgateways.ini, provider changes, reconciliation
type: help
---
# Payment gateways

This page is for owners and store managers whose store takes payment online. It covers the running store: what to
check, what can break, and who to ask. How payment fits into the checkout and how to choose a provider is on
[Payment gateways for businesses](../../businesses/stores/payment-gateways-for-businesses.md).

## How it works, in one paragraph

In Exponential a payment gateway is a workflow event on the checkout. `settings/paymentgateways.ini` lists the active
gateways, and a workflow with the payment gateway event is connected to the `shop_checkout` trigger. At checkout the
customer pays, and the order is placed when the workflow finishes
([Payment gateways for website creators](../../creators/website-creators/payment-gateways.md)). The project offers
`ezpaypal` (a redirect to PayPal) and `ezauthorize` (a card form sent to Authorize.Net)
([Payment gateways for businesses](../../businesses/stores/payment-gateways-for-businesses.md)).

If no payment step is on checkout, orders are placed without payment, and the Store dashboard says so in its
"Next steps" ([Store dashboard and order statuses](../../../features/6.0/store-dashboard.md)).

## Who does what

| Task | Usually |
|---|---|
| The contract and fees with the provider | Owner |
| Refunds and disputes in the provider's account | Owner, or one named person |
| Daily comparison of payments and orders | Store manager |
| Gateway settings, test mode, keys | Partner or administrator, in override files |
| Updating the gateway extension with Exponential | Host or partner |

## Every day

- [ ] Orders in **Payment failed**: one is a customer; several in a row is the payment step
  ([Daily store routine](daily-store-routine.md)).
- [ ] Payments in the provider's account match paid orders.

## Every month

- [ ] The provider's statement matches the month's paid orders (**Store > Product statistics**, [Store reports](store-reports.md)).
- [ ] The provider's messages read: changes to their interface, security requirements, fees.
- [ ] Your partner knows about any change the provider announced.

## Signs that payments do not reach orders

| Sign | Likely cause | Ask |
|---|---|---|
| Customers paid, orders not marked paid | The provider's notification does not reach the site | Partner or host: is the notify address reachable without login? |
| All payments fail since a given day | A key, certificate or provider interface changed | Partner, and the provider's status page |
| Customers land on a test page | Test mode or a sandbox address still set | Partner, at once |
| Paid orders have a status nobody uses | The gateway's "paid" status does not match your path | [Order statuses](order-statuses.md) |

For PayPal, the checks the extension makes before marking an order paid, and the signs of each problem, are on
[PayPal](paypal.md).

> **Warning**
> A site behind a login, an IP allow list or maintenance mode can take payments that never get marked paid, because
> the provider's notification is refused ([PayPal payments for businesses](../../businesses/stores/paypal-payments.md)).
> Tell your host before switching on maintenance mode in shop hours.

## When the provider changes something

Providers change their interfaces, signatures and security rules. When they announce a change:

1. Forward it to your partner the same day.
2. Ask whether the gateway extension supports the change, and whether an update is needed.
3. Ask for a test in the provider's test mode before the provider's deadline.
4. Plan the switch outside your busy hours.

## Adding or changing a provider

Another provider needs a gateway extension: a class extending `eZPaymentGateway` or `eZRedirectGateway`, documented
in `doc/eng-us/shop/` and walked through in `doc/eng-us/shop/examples/paypal/`. That is partner work. Ask for a redirect-style
gateway where possible, so card data does not pass your site ([Card payments and PCI](card-payments-and-pci.md)).

## Questions for your partner

- Which gateway and which version runs on our store, and when was it last updated?
- Where are its settings and keys kept, and who can change them?
- Is the notify address reachable from outside, and is it monitored?
- How do we test a change without real payments?

## Related pages

- [PayPal](paypal.md)
- [Card payments and PCI](card-payments-and-pci.md)
- [Payment gateways for businesses](../../businesses/stores/payment-gateways-for-businesses.md)
- [Payment gateways for website creators](../../creators/website-creators/payment-gateways.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
