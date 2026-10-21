---
title: Donation options at a glance
audience: non-profit, businesses, project-manager
category: business-solutions
order: 20
summary: A side-by-side comparison of the ways a non-profit can take donations with an Exponential site - an external donation platform, shop items with ezpaypal (redirect to PayPal) and shop items with ezauthorize (an on-site card form to Authorize.Net) - by setup effort, free amounts, recurring giving, card data, visibility, receipts and fees, with a decision table.
keywords: donation options, comparison, external platform, ezpaypal, ezauthorize, PayPal, Authorize.Net, redirect gateway, on-site card form, PCI, recurring giving, free amount, decision
type: help
---
# Donation options at a glance

This page compares the three practical ways to take donations with an Exponential site, so a director or board can
decide in one meeting. Exponential has no donation module; each option uses something that exists
([Donations](README.md)).

## Contents

1. [The three options](#the-three-options)
2. [Comparison](#comparison)
3. [Card data: redirect or on-site form](#card-data-redirect-or-on-site-form)
4. [Decision table](#decision-table)
5. [Questions to settle before you start](#questions-to-settle-before-you-start)

## The three options

1. **External platform.** A link or button to a donation page hosted by a payment provider or fundraising platform.
2. **Shop items with `ezpaypal`.** Fixed donation products in the built-in shop; at checkout the donor is sent to
   PayPal and PayPal calls back the site ([ezpaypal](../../../features/6.0/extensions/ezpaypal.md),
   [PayPal payments](../../businesses/stores/paypal-payments.md)).
3. **Shop items with `ezauthorize`.** The same products, but the donor types card details into a form on your site,
   which sends them to Authorize.Net ([Payment gateways](../../customers/commerce/payment-gateways.md)).

`ezpaypal` and `ezauthorize` are both required in `composer.json`; neither is active until it is set up.

## Comparison

| | External platform | Shop with `ezpaypal` | Shop with `ezauthorize` |
|---|---|---|---|
| Setup effort | Low: a page and a button | Medium: products, gateway, workflow, tests | Medium to high: as `ezpaypal`, plus card security care |
| Donor chooses any amount | As the platform offers | No: fixed items or defined options | No: fixed items or defined options |
| Recurring giving | As the platform offers | Not documented | Not documented |
| Card details typed on your site | No | No (on PayPal) | **Yes** |
| Gifts visible in the administration | No | Yes, as orders | Yes, as orders |
| Receipt | As the platform offers | Order receipt (not a tax receipt) | Order receipt (not a tax receipt) |
| Fees | The platform's | PayPal's | Authorize.Net's and your merchant account's |
| Who must test it after updates | The platform | You or your partner | You or your partner |

Sources: [Donations and fundraising pages](../../businesses/nonprofits/donations-and-fundraising-pages.md#comparing-the-options),
[Order receipts](../../../features/6.0/order-receipts.md),
[Invoices and receipts](../../customers/commerce/invoices-and-receipts.md).

## Card data: redirect or on-site form

With a redirect (external platform or `ezpaypal`), card numbers are typed on the provider's pages, not yours. With
an on-site card form (`ezauthorize`), card numbers pass through your site, which raises the security obligations of
your organization. Read [Card payments and PCI basics](../../businesses/stores/card-payments-and-pci-basics.md) and
[Card payments and PCI](../../customers/commerce/card-payments-and-pci.md) before you choose it; those pages are basics,
not compliance advice.

For a small non-profit, a redirect is usually the safer choice.

## Decision table

| If you ... | Choose |
|---|---|
| want the least work and monthly giving | External platform |
| already run the shop with PayPal | Shop with `ezpaypal` for one-off gifts |
| must see every gift in the administration | Shop with `ezpaypal` |
| have an Authorize.Net merchant account and a partner who maintains card security | Shop with `ezauthorize` |
| have no one to test a gateway after each update | External platform |

## Questions to settle before you start

- [ ] Who opens the account with the provider, and in whose name?
- [ ] What receipts must donors get in your country? (Ask your accountant.)
- [ ] Who checks gifts against the provider's statement, and how often?
- [ ] Where is the provider named in your privacy notice?
- [ ] What happens to a canceled payment? (With the shop: the order stays unpaid.)
- [ ] Who tests the donation path after each update?

Then follow [External donation platform, step by step](external-donation-platform-step-by-step.md) or
[Shop donation items, step by step](shop-donation-items-step-by-step.md).

## Related pages

- [Donations](README.md)
- [Payment gateways for businesses](../../businesses/stores/payment-gateways-for-businesses.md)
- [Payment gateways for website creators](../../creators/website-creators/payment-gateways.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
