---
title: E-commerce FAQ
audience: ecommerce, customer, businesses
category: e-commerce
order: 90
summary: Short answers, each with its source, to the questions merchants, store managers, developers and agencies ask about selling with Exponential - what the shop includes and lacks, payments, VAT, shipping, orders, data, several sites, performance and extending.
keywords: FAQ, e-commerce questions, shop, payment providers, PayPal, Authorize.Net, VAT, shipping, coupons, stock, orders, export, receipts, multisite, headless, extending, migration
type: help
---
# E-commerce FAQ

Short answers to the questions people ask most about selling with Exponential. Each answer links to the page with the
details. For questions about running a store day to day, see also the [Store FAQ for owners](../customers/commerce/store-faq.md)
and the [Store FAQ](../businesses/stores/store-faq.md) for decision makers.

## Contents

1. [What the shop is](#what-the-shop-is)
2. [What the shop does not do](#what-the-shop-does-not-do)
3. [Payments](#payments)
4. [Tax and shipping](#tax-and-shipping)
5. [Orders and data](#orders-and-data)
6. [Sites, apps and growth](#sites-apps-and-growth)
7. [Building and extending](#building-and-extending)

## What the shop is

**Is the shop an extension I have to install?**
No. It is part of the kernel: the module `shop` with its views in `kernel/shop/module.php`, its classes in
`kernel/classes/`, its settings in `settings/shop.ini`. You switch on the parts you need
([What the built-in shop does](../businesses/stores/what-the-built-in-shop-does.md)).

**What makes content a product?**
A price field: any class with an `ezprice` or `ezmultiprice` attribute is a product class
([Catalog modeling](catalog/README.md)).

**Where do staff work?**
In the **Store** tab of the administration, which opens the Store dashboard
([Store manager path](roles/store-manager-path.md)).

## What the shop does not do

**What is not included?**
Stock management, coupon or voucher codes, invoices in an accounting system, shipping labels, marketplaces, automatic
delivery of files after payment, and shipping beyond a flat amount without a handler. Each can be added as an extension
([Decision checklists](operations/decision-checklists.md#beyond-the-kernel)).

**Can I export orders to a spreadsheet from the order list?**
No. Order data is available as JSON through the `expservices` order services, from which a script can write a
spreadsheet ([Store figures and exports](operations/store-figures-and-exports.md)).

## Payments

**Which payment providers are supported?**
The repository contains `ezpaypal` (PayPal, redirect) and `ezauthorize` (Authorize.Net, card form on your site). Other
providers need a gateway written for them ([Payment gateway development](extending/payment-gateway-development.md)).

**How is payment connected to the checkout?**
As the Payment Gateway workflow event on the before trigger of `shop_checkout`
([Payment gateway event](checkout/payment-gateway-event.md)).

**Can customers choose between several providers?**
Yes. Select several gateways in the event; the customer then picks one on `workflow/selectgateway.tpl`.

**A customer paid but there is no order. Why?**
The provider's callback did not reach the site, so the temporary order was never activated
([Troubleshooting checkout](operations/troubleshooting-checkout.md#customer-paid-but-no-order)).

## Tax and shipping

**Does the shop charge VAT by country?**
Yes, with VAT rules by country and product category, once `shop.ini [VATSettings] Handler=ezdefault` is set
([Product categories and VAT rules](catalog/product-categories-and-vat-rules.md)).

**Why do some customers get 0 percent VAT?**
No VAT rule matched their country and the product's category. Add a catch-all rule.

**How is shipping charged?**
A flat amount per order with the Simple shipping event, or anything else with a shipping handler
([Simple shipping event](checkout/simple-shipping-event.md), [Custom shipping handler](extending/custom-shipping-handler.md)).

## Orders and data

**Can packing staff be limited to their own steps?**
Yes, with `shop/setstatus` policies limited by `FromStatus` and `ToStatus`
([Status transitions and permissions](orders/status-transitions-and-permissions.md)).

**What is the difference between archiving and removing an order?**
Archiving hides a finished order from the list and keeps it; removing deletes it for good
([Archive, remove and cleanup](orders/archive-remove-and-cleanup.md)).

**Can guests find their order later?**
Yes, with permanent receipts: `shop.ini [OrderViewSettings] OrderLinkView=orderreceipt`
([Order mail and receipts for operators](orders/order-mail-and-receipts-for-operators.md)).

**Is card data stored?**
The kernel does not store card data, and the audit trail records payments without it
([Commerce audit trail](operations/commerce-audit-trail.md)). With a card form on your site, your gateway code must
not store it either.

## Sites, apps and growth

**Can I run several stores in one installation?**
Yes, with siteaccesses; they share orders, VAT, currencies and discounts in one database
([Multi-store and multisite](operations/multi-store-and-multisite.md)).

**Can an app use the shop?**
Yes, through the `expservices` commerce services (products, basket, orders and more) or the REST API for content
([Headless commerce with expservices](operations/headless-commerce-with-expservices.md)).

**Can marketing build sale pages without a developer?**
Yes, with the Layouts editor, including time-limited rules
([Content commerce with Layouts](operations/content-commerce-with-layouts.md)).

## Building and extending

**How do I import a catalog?**
With `bin/php/ezcsvimport.php` for simple catalogs, or with code for anything that must update existing products
([Product imports and migrations](operations/product-imports-and-migrations.md)).

**Where do my handlers and gateways go?**
In an extension of your own, selected by settings; never in `kernel/` ([Extending the shop](extending/README.md)).

**Is there a generator for handlers?**
Yes: the RAD handler wizard (`setup/handlerextension/<kind>`) and the workflow event wizard (`setup/workflowevent`)
([Developer path](roles/developer-path.md#generators-that-save-time)).

## Related pages

- [E-commerce: start here](e-commerce-start-here.md)
- [E-commerce glossary](e-commerce-glossary.md)
- [Businesses FAQ](../businesses/businesses-faq.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
