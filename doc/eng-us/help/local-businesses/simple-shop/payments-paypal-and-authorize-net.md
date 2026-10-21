---
title: "Payments: PayPal and Authorize.Net"
audience: local-business, businesses, customer
category: local-business
order: 43
summary: How a local business takes payments in the Exponential shop - PayPal through ezpaypal (in the standard distribution, a redirect to PayPal) and card payments through ezauthorize (Authorize.Net, an on-site card form, a suggested package) - what each means for card security, what the business arranges with the provider, testing, and other options.
keywords: payment, PayPal, ezpaypal, Authorize.Net, ezauthorize, card payments, redirect, PCI, payment provider, fees, test mode, sandbox, local business
type: help
---
# Payments: PayPal and Authorize.Net

This page is for the owner of a local business shop who chooses how customers pay. Exponential does not process
money itself: a **payment gateway** extension connects the checkout to a payment provider, and the provider moves the
money ([Payment gateways](../../creators/website-creators/payment-gateways.md)).

## Contents

1. [The two gateways](#the-two-gateways)
2. [PayPal with ezpaypal](#paypal-with-ezpaypal)
3. [Card payments with ezauthorize](#card-payments-with-ezauthorize)
4. [What you arrange with the provider](#what-you-arrange-with-the-provider)
5. [Paying on collection](#paying-on-collection)
6. [Another provider](#another-provider)
7. [Testing before launch](#testing-before-launch)

## The two gateways

| | `ezpaypal` | `ezauthorize` |
|---|---|---|
| Provider | PayPal | Authorize.Net |
| In `composer.json` | `require`: part of the standard distribution | `suggest`: install when needed |
| Where the customer types card details | on PayPal's pages (a redirect) | in a form on your site |
| Card security work for you | smaller: card data never touches your server | larger: card data passes through your server |
| Read | [PayPal payments](../../businesses/stores/paypal-payments.md) | [Card payments and PCI basics](../../businesses/stores/card-payments-and-pci-basics.md) |

For most small businesses, a redirect to the provider (PayPal) is the simpler and safer start.

## PayPal with ezpaypal

From [ezpaypal](../../../features/6.0/extensions/ezpaypal.md) and [Payment gateways](../../creators/website-creators/payment-gateways.md#setting-up-paypal):

1. The business opens a PayPal business account (outside Exponential).
2. The builder activates `ezpaypal`, sets the business account in `paypal.ini`, and adds the PayPal event to the
   checkout workflow.
3. At checkout the customer is sent to PayPal, pays, and returns. PayPal notifies the site, which marks the order
   paid (status set by `PaymentMadeOrderStatusID`).
4. The notify address (`/paypal/notify_url`) must be reachable from the internet without a login.

Daily and monthly checks: [PayPal](../../customers/commerce/paypal.md).

## Card payments with ezauthorize

`ezauthorize` puts a card form on your own checkout and sends the card to Authorize.Net. The `composer.json` suggest
entry describes it as a transparent payment solution with no redirect. Because the card number passes through your
web server, the card security rules (PCI DSS) apply to your site more strongly. Read
[Card payments and PCI basics](../../businesses/stores/card-payments-and-pci-basics.md) with your provider before
choosing it; it is not compliance advice, and neither is this page.

## What you arrange with the provider

| Item | Who | Note |
|---|---|---|
| Merchant or business account | the business | in the business's name |
| Fees | set by the provider | this guide quotes no fees; read the provider's current terms |
| Payout to your bank account | the business | check how many days it takes |
| Refunds | the business, in the provider's account | and the order status in Exponential ([Returns and refunds](../../customers/commerce/returns-and-refunds.md)) |
| Disputes and chargebacks | the business | keep order records |

## Paying on collection

If customers collect in the shop, some businesses take payment at the till. The shop can run without a payment step:
the order is placed and staff take payment on collection. Ask your builder to leave out the gateway, or to offer it
as a choice; the Store dashboard warns when there is no payment step, which is expected in that case
([Payment gateways for businesses](../../businesses/stores/payment-gateways-for-businesses.md)). Agree what happens
when an order is not collected.

## Another provider

A gateway for another provider is a class plus a callback view in an extension, written by a developer
([A gateway of your own](../../creators/website-creators/payment-gateways.md#a-gateway-of-your-own)). Budget it as
custom work; no other provider's gateway is in the standard `composer.json`.

## Testing before launch

- [ ] A payment in the provider's test mode (PayPal sandbox) completes; the order becomes paid.
- [ ] A cancelled payment leaves the order unpaid.
- [ ] The confirmation mail reaches the customer and the shop.
- [ ] A refund done in the provider's account is reflected in the order status by staff.
- [ ] Switched to live mode, a real small purchase works and is refunded.

## Related pages

- [Payment gateways for businesses](../../businesses/stores/payment-gateways-for-businesses.md)
- [Payment gateways (store owners)](../../customers/commerce/payment-gateways.md)
- [Card payments and PCI](../../customers/commerce/card-payments-and-pci.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
