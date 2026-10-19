---
title: Store FAQ
audience: businesses, sales-marketing, project-manager
category: business-solutions
order: 240
summary: Short answers to the questions business owners ask most about selling with Exponential's built-in shop - features, payments, taxes, stock, coupons, downloads, costs, security and performance - each with a link to the full page.
keywords: store FAQ, webshop questions, e-commerce FAQ, shop features, payments, VAT, stock, coupons, digital downloads, cost, license
type: help
---
# Store FAQ

This page is for business owners and decision makers who want quick answers about selling online with Exponential.
Each answer is short and links to the page with the details and the sources.

## Contents

1. [The shop in general](#the-shop-in-general)
2. [Products and prices](#products-and-prices)
3. [Checkout and customers](#checkout-and-customers)
4. [Payments](#payments)
5. [Orders and daily work](#orders-and-daily-work)
6. [Missing features](#missing-features)
7. [Costs, security and reliability](#costs-security-and-reliability)

## The shop in general

**Is the shop an add-on I have to buy?**
No. The shop is part of the Exponential kernel (`kernel/shop/`), and the README lists "Advanced e-Commerce Stores
(Built in)" among the main features. Exponential is licensed GPL v2 or later.
See [What the built-in shop does](what-the-built-in-shop-does.md).

**Can my website and my store be the same site?**
Yes. Products are content pages with a price field, edited with the same tools as the rest of the site.
See [Product classes and catalog](product-classes-and-catalog.md).

**Can I run several stores, for example one per country or brand?**
Exponential runs several sites from one installation, each with its own settings, such as its country ranking.
See [Multi-brand planning](../multisite/multi-brand-planning.md) and [Country groups and regions](country-groups-and-regions.md).

## Products and prices

**Can I sell in several currencies?**
Yes, with the multi-currency price field, custom or automatic prices per currency, and exchange rates.
See [Prices and currencies](prices-and-currencies.md).

**Can products have sizes and colors?**
Yes, with option fields that can change the price. See [Product classes and catalog](product-classes-and-catalog.md#options-and-variants).

**Does the shop handle VAT for different countries?**
It charges what you configure: VAT types, and VAT rules by customer country and product category. Which rates are right
is a question for your tax adviser. See [VAT rules and VAT types](vat-rules-and-vat-types.md).

## Checkout and customers

**Must customers create an account?**
No. The shipped setting lets everyone check out with a form; you can require accounts instead.
See [Customer accounts and guest checkout](customer-accounts-and-guest-checkout.md).

**Can guests find their order later?**
Yes, if you turn on permanent receipts: a signed link that works for years on any device.
See [Order receipts and confirmation mail](order-receipts-and-confirmation-mail.md).

**What does shipping cost?**
Out of the box, one flat amount per order that you set. Rules by weight, zone or order value need a shipping handler.
See [Shipping and handling](shipping-and-handling.md).

## Payments

**Which payment providers work?**
PayPal through `ezpaypal`, which is installed with Exponential, and Authorize.Net through `ezauthorize`, which is
suggested. Other providers need a gateway extension. See [Payment gateways for businesses](payment-gateways-for-businesses.md).

**Do card numbers pass through my website?**
Not with a redirect gateway such as PayPal. With an on-site card form such as ezauthorize they do, and the code's card
data handling must be reviewed before launch. See [Card payments and PCI basics](card-payments-and-pci-basics.md).

**Can I take orders without online payment, for invoice or pickup?**
Yes: without a payment step, orders are placed unpaid and you handle payment yourself. The dashboard reminds you that no
payment step is configured. See [Payment gateways for businesses](payment-gateways-for-businesses.md#how-payment-fits-in).

## Orders and daily work

**How do I see what needs doing?**
The Store dashboard lists orders waiting for the shop, longest first, and suggests next steps.
See [Store dashboard and reports](store-dashboard-and-reports.md).

**Which order statuses are there?**
Seventeen, from Pending and Paid through Packed and Shipped to Completed, Cancelled and Refunded, plus your own.
See [Orders and order statuses](orders-and-order-statuses.md).

**Can I limit what a packing assistant may do?**
Yes. The permission to change statuses can be limited to certain from and to statuses.
See [Orders and order statuses](orders-and-order-statuses.md#who-may-change-a-status).

## Missing features

**Does the shop track stock?**
No. Stock management is among the things to add as an extension or integration.
See [What the built-in shop does](what-the-built-in-shop-does.md#what-is-not-included).

**Can customers enter a coupon code?**
No. Discounts follow customer groups. Coupon codes need an extension.
See [Discounts and wishlists](discounts-and-wishlists.md#what-is-not-included).

**Can I sell downloads that are delivered automatically after payment?**
Not out of the box. The parts exist (protected files, user groups, checkout workflows); connecting them is manual work
or a small extension. See [Digital products and downloads](digital-products-and-downloads.md).

**Can I export orders to my accounting software?**
Not from the shipped order list. Accounting integration is extension or partner work.
See [Store dashboard and reports](store-dashboard-and-reports.md#what-the-dashboard-does-not-do).

**Who can add what is missing?**
A developer or a partner. See [Choosing a partner](../choosing/choosing-a-partner.md) and
[Working with 7x and the project](../choosing/working-with-7x-and-the-project.md).

## Costs, security and reliability

**What does running a store cost?**
Mainly hosting, setup and ongoing care, since there is no license fee. Work it out with your own numbers.
See [Total cost of ownership](../costs/total-cost-of-ownership.md) and [Worked example: small store](../costs/worked-example-small-store.md).

**How do I keep the store secure?**
Personal accounts with two-step sign-in, limited shop permissions, HTTPS, updates and tested backups.
See [Store security checklist](store-security-checklist.md).

**Will the store stay fast during a sale?**
Public pages can be cached; basket and checkout cannot, so plan capacity for them and measure before the peak.
See [Store performance at peak times](store-performance-at-peak-times.md).

**Where do I report a security problem in Exponential?**
Privately, to the address in SECURITY.md, never in a public issue.

## Related pages

- [Online stores and small shops](README.md)
- Businesses FAQ
- [Website creator FAQ](../../creators/website-creators/website-creator-faq.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
