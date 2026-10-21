---
title: Shop donation items, step by step
audience: non-profit, businesses, website-creator, admin
category: non-profits
order: 40
summary: Building donation items in Exponential's built-in shop - fixed-amount products of the shipped product class, VAT as your accountant advises, the ezpaypal gateway (paymentgateways.ini, paypal.ini Business, sandbox, the checkout workflow) or ezauthorize, order receipts, the store dashboard check, the tests to run and the weekly routine - with the limits stated plainly.
keywords: shop donation, donation product, fixed amount, product class, ezprice, ezpaypal, ezauthorize, paymentgateways.ini, paypal.ini, sandbox, shop_checkout, workflow, order receipts, store dashboard, PaymentMadeOrderStatusID
type: help
---
# Shop donation items, step by step

This page is for the website creator or partner who builds route B: donations as fixed-amount products in the
built-in shop. Read [Donation options at a glance](donation-options-at-a-glance.md) first; this route has limits a
fundraiser must accept.

> **Note**
> The limits: donors choose from fixed amounts (or options you define), not any amount; recurring payments are not
> documented for the shop or its gateways; the shop's receipt is an order receipt, not a tax receipt; and donors go
> through a basket and checkout, which reads like a purchase.

## Contents

1. [Step 1: the donation products](#step-1-the-donation-products)
2. [Step 2: VAT](#step-2-vat)
3. [Step 3: the payment gateway](#step-3-the-payment-gateway)
4. [Step 4: receipts and mail](#step-4-receipts-and-mail)
5. [Step 5: test](#step-5-test)
6. [The weekly routine](#the-weekly-routine)
7. [Checklist](#checklist)

## Step 1: the donation products

1. Create a folder **Donate** for the items.
2. Create one object of the shipped `product` class per amount, for example "Gift of 10", "Gift of 25",
   "Gift of 50", each with its price (`ezprice`, or `ezmultiprice` for several currencies)
   ([Products, prices and VAT](../../creators/website-creators/products-prices-and-vat.md),
   [Adding a product](../../customers/commerce/adding-a-product.md)).
3. In the description, say what each amount does.
4. Options such as `ezoption` or `ezrangeoption` can change the price, but only to values you define; there is no
   free amount ([Donations and fundraising pages](../../businesses/nonprofits/donations-and-fundraising-pages.md#option-b-donation-items-in-the-built-in-shop)).
5. Link the folder from your [giving page](the-giving-page.md).

## Step 2: VAT

Donations are usually treated differently from sales for tax purposes, but the rules are your country's. Set the VAT
type of the donation items as your accountant advises ([VAT types and rules](../../customers/commerce/vat-types-and-rules.md),
[VAT checklist](../../customers/commerce/vat-checklist.md)).

## Step 3: the payment gateway

**With `ezpaypal` (redirect to PayPal):**

1. Activate the extension.
2. Create `settings/override/paypal.ini.append.php` and set at least `[PaypalSettings] Business`, your PayPal
   business account.
3. Point `[ServerSettings] ServerName` at PayPal's sandbox while you test.
4. Add the PayPal workflow event to the checkout workflow, connected to the `shop_checkout` trigger
   ([Workflows](../../../guides/workflows.md)).
5. The notify URL (`/paypal/notify_url`) "must be reachable from the internet and must not require a login".
6. `[OrderSettings] PaymentMadeOrderStatusID` (`1000`) is the status set when payment is made.

Source: [Payment gateways for website creators](../../creators/website-creators/payment-gateways.md#setting-up-paypal),
[ezpaypal](../../../features/6.0/extensions/ezpaypal.md).

**With `ezauthorize` (card form to Authorize.Net):** the extension's own documentation is in
`extension/ezauthorize/`. Card details are typed on your site; read
[Card payments and PCI basics](../../businesses/stores/card-payments-and-pci-basics.md) first.

The active gateways are listed in `settings/paymentgateways.ini [GatewaysSettings] AvailableGateways[]`.

## Step 4: receipts and mail

- Switch on order receipts so donors can reach their receipt at any time ([Order receipts](../../../features/6.0/order-receipts.md)).
- Check the order confirmation mail reads well for a gift, not a purchase ([Order confirmation mail](../../customers/commerce/order-confirmation-mail.md)).
- If your country requires a donation or tax receipt, issue it outside Exponential as your accountant advises.

## Step 5: test

From [Payment gateways, Testing](../../creators/website-creators/payment-gateways.md#testing):

- [ ] A payment in the provider's test mode completes and the order becomes paid.
- [ ] A canceled payment leaves the order unpaid and the donor can try again.
- [ ] The callback works over HTTPS and through any proxy.
- [ ] The Store dashboard no longer says there is no payment step on checkout ([Store dashboard](../../../features/6.0/store-dashboard.md)).

Then switch to the live server, make one small real gift and refund it.

## The weekly routine

| Task | Read |
|---|---|
| Compare paid orders with the provider's statement | [Orders day to day](../../customers/commerce/orders-day-to-day.md) |
| Look at orders in "payment failed" | [Payment gateways for owners](../../customers/commerce/payment-gateways.md) |
| Answer donors' receipt questions | [Invoices and receipts](../../customers/commerce/invoices-and-receipts.md) |
| Handle a refund request | [Returns and refunds](../../customers/commerce/returns-and-refunds.md) |

After each Exponential or extension update, repeat the test of step 5.

## Checklist

- [ ] Donation products created with clear descriptions.
- [ ] VAT set as advised.
- [ ] Gateway set up, sandbox tested, then live tested with a small gift.
- [ ] Order receipts on; confirmation mail reviewed.
- [ ] Tax receipts handled outside the site, if required.
- [ ] Weekly routine has a name next to it.

## Related pages

- [Donations](README.md)
- [Shop overview](../../creators/website-creators/shop-overview.md)
- [PayPal payments](../../businesses/stores/paypal-payments.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
