---
title: Decision checklists
audience: ecommerce, businesses, project-manager
category: e-commerce
order: 81
summary: Short checklists for the decisions an Exponential store faces - price field, VAT mode, account handler, shipping, payment gateway, receipts, basket behavior, multisite design, what to build beyond the kernel, and whether to remove or archive - each with the facts that decide it.
keywords: decision checklist, store decisions, price field, VAT handler, account handler, shipping, payment gateway, receipts, basket settings, multisite, build or buy, stock, coupons, accounting, archive or remove
type: help
---
# Decision checklists

This page is for merchants, project managers and agencies who must make store decisions and record why. Each
checklist is short; tick what applies and the answer follows. The facts behind each are on the linked page.

## Contents

1. [Price field](#price-field)
2. [VAT mode](#vat-mode)
3. [Customer data](#customer-data)
4. [Shipping](#shipping)
5. [Payment](#payment)
6. [Receipts and basket behavior](#receipts-and-basket-behavior)
7. [Several sites](#several-sites)
8. [Beyond the kernel](#beyond-the-kernel)
9. [Archive or remove](#archive-or-remove)

## Price field

- [ ] We sell in more than one currency.
- [ ] Prices per currency are set by market, not converted.

None ticked: `ezprice`. First ticked: `ezmultiprice`, with automatic or custom prices according to the second.
([Price fields compared](../catalog/price-fields-compared.md))

## VAT mode

- [ ] All products have the same rate everywhere we sell.
- [ ] Rates differ by product only.
- [ ] Rates differ by customer country and product category.
- [ ] Rates depend on something else (business customers, delivery address, an external service).

First: one VAT type. Second: several VAT types, chosen per product. Third: VAT rules plus `[VATSettings] Handler=ezdefault`.
Fourth: a VAT handler of your own. ([Product categories and VAT rules](../catalog/product-categories-and-vat-rules.md),
[Custom VAT handler](../extending/custom-vat-handler.md))

## Customer data

- [ ] Guests may buy.
- [ ] We need a full postal address.
- [ ] We need fields the shipped forms lack (company, VAT number, delivery address).

Guests and full address: `ezuser`. Short form: `ezsimple`. Accounts only: `ezdefault`. Extra fields: own handler.
([shopaccount.ini walkthrough](../settings/shopaccount-ini-walkthrough.md))

## Shipping

- [ ] One flat amount per order is acceptable.
- [ ] Shipping must carry VAT.
- [ ] Rates differ by country, weight or amount, or customers choose a method.

Only the first: Simple shipping event. Any other: a shipping handler.
([Simple shipping event](../checkout/simple-shipping-event.md), [Custom shipping handler](../extending/custom-shipping-handler.md))

## Payment

- [ ] Our provider is PayPal.
- [ ] Our provider is Authorize.Net and we accept card data passing through our server.
- [ ] Our provider offers a hosted payment page or redirect.

First: `ezpaypal`. Second: `ezauthorize`. Third: write a redirect gateway. Otherwise: a transparent gateway, with the
card security work that brings. ([Payment gateway development](../extending/payment-gateway-development.md))

## Receipts and basket behavior

- [ ] Guests buy, and should reach their order later: `OrderLinkView=orderreceipt`.
- [ ] Customers buy many items at once: `RedirectAfterAddToBasket=reload`.
- [ ] The payment provider's documentation requires an empty basket before payment: `ClearBasketOnCheckout=enabled`.
- [ ] Computers are shared: `ClearBasketOnLogout=enabled`.

([shop.ini walkthrough](../settings/shop-ini-walkthrough.md), [site.ini shop settings walkthrough](../settings/site-ini-shop-settings-walkthrough.md))

## Several sites

- [ ] One order list, one tax setup and one currency list for all sites is acceptable.

Ticked: siteaccesses on one installation. Not ticked: separate installations or databases.
([Multi-store and multisite](multi-store-and-multisite.md))

## Beyond the kernel

Not in the kernel, so each ticked item is a project:

- [ ] Stock management
- [ ] Coupon or voucher codes
- [ ] Invoices in an accounting system, accounting export
- [ ] Shipping labels, carrier integration
- [ ] Automatic delivery of files after payment
- [ ] Marketplaces

For each: an extension, a workflow event or a handler, built in house or by a partner
([What the built-in shop does](../../businesses/stores/what-the-built-in-shop-does.md#what-is-not-included)).

## Archive or remove

- [ ] The order is finished and must be kept for the records: archive.
- [ ] The order is a test order on the live store: remove.
- [ ] Data must be erased and nothing obliges us to keep it: remove, and record it.
- [ ] This is a test database before handover: the shop cleanup script.

([Archive, remove and cleanup](../orders/archive-remove-and-cleanup.md))

## Related pages

- [Operating a store](README.md)
- [Merchant path](../roles/merchant-path.md)
- [Store planning for small businesses](../../businesses/stores/store-planning-for-small-businesses.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
