---
title: Donations
audience: non-profit, businesses, customer
category: business-solutions
order: 10
summary: What a non-profit can do about online donations with Exponential - there is no dedicated donation module, so the practical routes are a link to an external donation platform or fixed donation items in the built-in shop with the ezpaypal or ezauthorize gateway - and the pages of this folder that walk through each route, the giving page and donor trust.
keywords: donations, donate, fundraising, giving page, no donation module, external donation platform, shop, ezpaypal, ezauthorize, PayPal, Authorize.Net, recurring giving, receipts, donor trust
type: help
---
# Donations

This folder is for fundraisers, directors and the person who builds the site. It starts with the most important fact
and then shows the two routes that actually work.

> **Important**
> Exponential has **no dedicated donation module**. There is no donation datatype, no "amount of your choice" field,
> no recurring giving, no gift-aid or tax-receipt feature. The repository was searched for donation features and
> only pages about donating to the Exponential project itself were found
> ([Donations and fundraising pages](../../businesses/nonprofits/donations-and-fundraising-pages.md#what-exists)).

## Contents

1. [The two routes](#the-two-routes)
2. [The pages of this folder](#the-pages-of-this-folder)
3. [Which route for which organization](#which-route-for-which-organization)
4. [What every route needs](#what-every-route-needs)

## The two routes

| Route | What it is | Built from |
|---|---|---|
| **A. External donation platform** | A clear Donate button that leads to a donation page hosted by a payment provider or fundraising platform | A page, a link or a Layouts button block |
| **B. Donation items in the shop** | Fixed amounts ("Donation 25") sold as products through basket and checkout | The shipped `product` class, the shop, and the `ezpaypal` or `ezauthorize` gateway from `composer.json` |

Both can run side by side. Neither adds a license fee; fees are the payment provider's.

## The pages of this folder

| Page | What it answers |
|---|---|
| [Donation options at a glance](donation-options-at-a-glance.md) | A side-by-side comparison and a decision table |
| [External donation platform, step by step](external-donation-platform-step-by-step.md) | Building route A in an afternoon |
| [Shop donation items, step by step](shop-donation-items-step-by-step.md) | Building route B, including gateway tests |
| [The giving page](the-giving-page.md) | What a good donation page says and shows |
| [Donor trust and thank-you](donor-trust-and-thank-you.md) | HTTPS, privacy notice, the thank-you page, receipts, follow-up |

The compact version of all this is [Donations and fundraising pages](../../businesses/nonprofits/donations-and-fundraising-pages.md)
in the Businesses section.

## Which route for which organization

| Your situation | Route |
|---|---|
| Small organization, no shop, wants monthly giving | A |
| Already sells shirts, tickets or publications in the shop | B for one-off gifts, A for monthly giving |
| Wants every gift visible in the site's administration | B |
| No one to test and maintain a payment gateway | A |
| Needs donors to choose any amount | A (the shop offers fixed prices and defined options only) |

## What every route needs

- [ ] A giving page with a clear purpose ([The giving page](the-giving-page.md)).
- [ ] The whole site on HTTPS ([Going live: hosts and HTTPS](../../creators/website-creators/going-live-hosts-https.md)).
- [ ] The payment provider named in the privacy notice.
- [ ] A thank-you page and a plan for receipts ([Donor trust and thank-you](donor-trust-and-thank-you.md)).
- [ ] A named person who checks donations weekly.
- [ ] Your accountant consulted on receipts and tax.

## Related pages

- [Non-profits: start here](../non-profits-start-here.md)
- [Campaigns and appeals](../communications/campaigns-and-appeals.md)
- [Stores](../../businesses/stores/README.md)
- [Commerce for store owners](../../customers/commerce/README.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
