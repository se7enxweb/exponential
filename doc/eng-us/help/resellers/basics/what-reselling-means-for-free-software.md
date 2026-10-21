---
title: What reselling means for free software
audience: reseller, partner
category: partnership
order: 10
summary: Why you can charge money for offers built on Exponential although the software is free, what the GNU GPL v2 or later allows a reseller, what it asks when you distribute copies, and where the documentation license fits. Not legal advice.
keywords: GPL, GPL v2 or later, free software, reselling, charge, distribute, source code, copyright notices, license, GFDL, legal advice, no license fee
type: help
---
# What reselling means for free software

This page is for owners and sales staff of reseller companies who need to explain, to themselves and to customers,
what is being sold when the software itself is free. It is a summary of the licenses as they affect a reseller's
business. It is **not legal advice**: the license texts decide, and your own adviser interprets them for your offer.

## Contents

1. [The short version](#the-short-version)
2. [Free means freedom, not price](#free-means-freedom-not-price)
3. [What you may do](#what-you-may-do)
4. [What you must do when you distribute](#what-you-must-do-when-you-distribute)
5. [Running a site for a customer is not distributing](#running-a-site-for-a-customer-is-not-distributing)
6. [The documentation license](#the-documentation-license)
7. [Phrases to use with customers](#phrases-to-use-with-customers)

## The short version

| Question | Answer | Source |
|---|---|---|
| Under which license is Exponential? | GNU General Public License, version 2 or any later version | `composer.json` (`"license": "GPL-2.0-or-later"`), `COPYRIGHT.md`, `LICENSE` |
| Does using it cost a license fee? | No: "Using Exponential under the terms of the GNU GPL is free of charge" | `README.md`, section "License" |
| Is the old commercial license still sold? | No: "The Professional License is no longer available" | `README.md` |
| May a reseller charge money? | Yes, for services, products, your own work and for distributing copies | `LICENSE`, preamble and section 1 |
| Must a reseller give anything back? | When you distribute copies: the license, the notices and the source | `LICENSE`, sections 1 to 3 |
| Under which license is this help? | GNU Free Documentation License 1.3 or later | [The license of this documentation](../../../README-LICENSE.md) |

## Free means freedom, not price

The preamble of the GPL says: "When we speak of free software, we are referring to freedom, not price." The license
explicitly allows charging: "You may charge a fee for the physical act of transferring a copy, and you may at your
option offer warranty protection in exchange for a fee" (section 1).

For a reseller this means three things:

1. **You never sell the right to use Exponential.** Everybody already has it. An invoice line "Exponential license"
   would be wrong ([Licensing explained for buyers](../../business/licensing-for-buyers.md)).
2. **You sell what costs work**: servers, setup, support, updates, design, extensions, training, warranty and your
   availability.
3. **Your customer keeps the freedoms.** They may take their site to another provider at any time. Build that into
   your offer instead of fighting it ([Renewals and churn](../customers/renewals-and-churn.md)).

## What you may do

- Install, run and change Exponential for any number of customers, for any purpose, without asking anyone.
- Charge for hosting, support, development, training, consulting and design.
- Charge for distributing copies, for example a preinstalled image or a packaged product.
- Offer a warranty for a fee (GPL section 1), with terms you define.
- Sell your own extensions, themes and services next to Exponential, within the license of each part
  ([What you can and cannot sell](what-you-can-and-cannot-sell.md)).
- Copy, adapt and rebrand this documentation for your customers under the GNU FDL
  ([Documentation you can give your customers](../customers/documentation-for-your-customers.md)).

## What you must do when you distribute

When you give Exponential, or software based on it, to someone else (a download, an image, an appliance, files that
become the customer's property), the GPL conditions apply:

- [ ] pass on the license text and keep every copyright notice (`LICENSE`, `LICENSE.md`, `COPYRIGHT.md`);
- [ ] provide the source code, including your changes; Exponential is PHP, so the files are the source, but changed
      files must be marked with a notice and the date of the change (GPL section 2a);
- [ ] add no restriction that takes the recipient's GPL rights away.

The complete list with the extension and component licenses is on
[Distribution obligations checklist](../responsibilities/distribution-obligations-checklist.md).

## Running a site for a customer is not distributing

Section 0 of the GPL: "The act of running the Program is not restricted". When you host a site on your own servers
and the customer only uses it through a browser, you run the software; you do not distribute it. The GPL v2 has no
clause about software used over a network, and Exponential itself is not under the AGPL
([Licensing explained for buyers](../../business/licensing-for-buyers.md#running-a-website-is-not-distributing)).

| Situation | Distribution? |
|---|---|
| You host the site, the customer edits it in the browser | No |
| You hand the customer the files at the end of the project | Yes: the customer receives the GPL rights and the source |
| You sell a downloadable image or product | Yes |
| You give a customer's developer a copy of your changed version | Yes |

Whether a specific arrangement counts as distribution is a question for your legal adviser.

## The documentation license

The help is under the GNU FDL 1.3 or later ([Free documentation](../../free-documentation.md)). You may copy, sell,
translate and adapt it, as long as copies keep the notices, carry the license and, for changed versions, stay under
the same license and say what was changed.

## Phrases to use with customers

```text
Exponential is free software under the GNU General Public License, version 2 or later.
You pay us for <hosting / support / development / training>, not for a license.
You may take your site to another provider at any time; we will hand over everything you need.
```

## Related pages

- [What you can and cannot sell](what-you-can-and-cannot-sell.md)
- [Licensing for partners: GPL code, GFDL documentation](../../partners/licensing-for-partners.md)
- [Licensing for hosts](../../hosting/business/licensing-for-hosts.md)
- [Licensing questions](../../project/licensing-faq.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
