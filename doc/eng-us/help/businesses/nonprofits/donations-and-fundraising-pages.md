---
title: Donations and fundraising pages
audience: businesses, project-manager, website-creator, sales-marketing
category: business-solutions
order: 20
summary: The honest options for taking donations on an Exponential site - there is no dedicated donation module; link to an external donation platform, or sell fixed donation items through the built-in shop with the ezpaypal gateway - with the trade-offs, what a good giving page contains, and what to check with your accountant.
keywords: donations, fundraising, donate button, giving page, ezpaypal, PayPal, shop, external donation platform, recurring donations, receipts, charity, nonprofit
type: help
---
# Donations and fundraising pages

This page is for directors, fundraisers and project leads of non-profits. It says plainly what Exponential offers for
taking donations and what it does not, compares the two practical options, and describes a good giving page. Read it
before promising online donations to your board.

## Contents

1. [What exists](#what-exists)
2. [Option A: link to an external donation platform](#option-a-link-to-an-external-donation-platform)
3. [Option B: donation items in the built-in shop](#option-b-donation-items-in-the-built-in-shop)
4. [Comparing the options](#comparing-the-options)
5. [What a good giving page contains](#what-a-good-giving-page-contains)
6. [Receipts, tax and accounting](#receipts-tax-and-accounting)
7. [Security and trust](#security-and-trust)
8. [Donation checklist](#donation-checklist)

## What exists

- **There is no dedicated donation module** in the repository: no donation datatype, no "amount of your choice" field,
  no gift-aid or tax-receipt feature. A search of the code and documentation for donation features finds only the
  pages about donating to the Exponential project itself.
- **The built-in shop** sells products with a price set by editors (`ezprice` or `ezmultiprice`), through basket,
  checkout and a payment step ([Running a shop: overview](../../creators/website-creators/shop-overview.md)).
- **`ezpaypal`** is a payment gateway for the shop, required by `composer.json`: the customer is sent to PayPal to pay
  and PayPal calls back the site ([ezpaypal](../../../features/6.0/extensions/ezpaypal.md)).
- **Recurring payments** are not documented for the shop or for `ezpaypal`.

## Option A: link to an external donation platform

Most small non-profits do this: a clear **Donate** button on the site that leads to a donation page hosted by a payment
provider or a fundraising platform you already use.

How to build it in Exponential:

1. Create a "Donate" page that explains why to give and what a gift does.
2. Put the link to the platform on it, and a **Donate** button in the header or on the home page. In Exponential
   Layouts the standard block `button` ("Button / Link") does this without a developer
   ([Layouts reference](../../../bc/6.0/LAYOUTS.md)); a link field (`ezurl`) on a page class works too
   ([Choosing datatypes](../../creators/website-creators/choosing-datatypes.md)).
3. For a campaign, put the button on the campaign page ([Campaigns and landing pages](campaigns-and-landing-pages.md)).
4. Tell donors before they click that they leave your site, and name the platform in your privacy notice.

What you gain: the platform handles card data, recurring giving and receipts as it offers them; your site handles no
payment data. What you give up: the donation itself is not recorded in Exponential.

## Option B: donation items in the built-in shop

If you already sell items (shirts, tickets, publications) through the shop, you can add fixed donation items:

1. A product of the shipped `product` class, for example "Donation 10", "Donation 25", "Donation 50", each with its
   price ([Products, prices and VAT](../../creators/website-creators/products-prices-and-vat.md)).
   Options such as `ezoption` or `ezrangeoption` "let visitors choose options ... that can change the price"; they
   offer choices you define, not a free amount.
2. `ezpaypal` set up: `Business` in `settings/override/paypal.ini.append.php`, tested against PayPal's sandbox, the
   PayPal event added to the checkout workflow ([Payment gateways](../../creators/website-creators/payment-gateways.md#setting-up-paypal)).
3. VAT types set for these items as your accountant advises ([VAT](../../creators/website-creators/products-prices-and-vat.md#vat)).
4. Order receipts switched on so donors can reach their receipt at any time
   ([Order receipts](../../../features/6.0/order-receipts.md)).
5. The store dashboard checked: it says when a payment step is missing
   ([Store dashboard](../../../features/6.0/store-dashboard.md)).

What you gain: donations appear as orders in the administration, with their payment status
(`PaymentMadeOrderStatusID`, `1000` by default). What you give up: donors go through a shop basket and checkout, which
reads like a purchase; there is no free amount and no recurring giving.

For the shop in depth, see the [Stores](../stores/README.md) section and
[PayPal payments](../stores/paypal-payments.md).

## Comparing the options

| | A: external platform | B: shop items with `ezpaypal` |
|---|---|---|
| Setup effort | Low: a page and a button | Medium: products, gateway, workflow, tests |
| Free amount | As the platform offers | No, fixed items or defined options only |
| Recurring giving | As the platform offers | Not documented |
| Payment data on your site | None | None: payment happens at PayPal; order data is on your site |
| Donations visible in Exponential | No | Yes, as orders |
| Receipts | As the platform offers | Order receipts (a purchase receipt, not a tax receipt) |
| Fees | The platform's | PayPal's |

Neither option adds a license fee; fees are those of the payment provider. Compare their published terms yourself.

## What a good giving page contains

- [ ] One sentence on what a gift achieves, with a real example from your work.
- [ ] The donate button above the fold, repeated at the end.
- [ ] Who you are: legal name, registration details your law requires, a contact.
- [ ] Where the money goes, with a link to your latest annual report (a file download).
- [ ] Other ways to give (bank transfer, in kind, volunteering) for people who prefer them.
- [ ] A privacy note covering the payment provider.
- [ ] A thank-you page for donors returning from the provider.
- [ ] Accessible: labels, contrast, keyboard use ([Accessibility for non-profits](accessibility-for-nonprofits.md)).

## Receipts, tax and accounting

Exponential's order receipts are receipts for an order. Whether you must issue donation or tax receipts, and in what
form, depends on your country; Exponential has no feature for that. Ask your accountant, and keep the provider's
records as the source for your books.

## Security and trust

- Serve the whole site over HTTPS ([Going live: hosts and HTTPS](../../creators/website-creators/going-live-hosts-https.md)).
- With the shop, the PayPal notify URL "must be reachable from the internet and must not require a login"
  ([Payment gateways](../../creators/website-creators/payment-gateways.md#setting-up-paypal)).
- Keep administrator accounts few and protected by two-factor login ([Two-factor login](../../../guides/two-factor-login.md)).
- For card handling in general, read [Card payments and PCI basics](../stores/card-payments-and-pci-basics.md).

## Donation checklist

- [ ] Option chosen (A, B, or both) and written down with the reason.
- [ ] Giving page complete (list above) and tested on a phone.
- [ ] Option A: link tested; donors told they leave the site; provider in the privacy notice.
- [ ] Option B: sandbox payment completed and order became paid; canceled payment leaves the order unpaid.
- [ ] Receipts and tax handling agreed with your accountant.
- [ ] A named person checks donations or orders weekly.

## Related pages

- [Non-profits](README.md)
- [Payment gateways](../../creators/website-creators/payment-gateways.md)
- [Payment gateways for businesses](../stores/payment-gateways-for-businesses.md)
- [How businesses sell online](../how-businesses-sell-online.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
