---
title: Shop settings walkthroughs
audience: ecommerce, website-creator, admin
category: e-commerce
order: 30
summary: Every settings file that shapes an Exponential store, with where overrides go, how to check the value that is really in effect, and a walkthrough page per file - shop.ini, site.ini ShopSettings, shopaccount.ini, workflow.ini and paymentgateways.ini.
keywords: shop settings, shop.ini, site.ini, ShopSettings, shopaccount.ini, workflow.ini, paymentgateways.ini, paypal.ini, ezauthorize.ini, override, siteaccess settings, INI cache, settings reference
type: help
---
# Shop settings walkthroughs

This part is for developers and administrators who configure an Exponential store. The shop reads its behavior from
five kernel settings files and from the settings of the payment extensions. Each page of this part walks through one
file block by block: the shipped value, what it does, and when to change it.

## Contents

1. [The files](#the-files)
2. [Where shop settings go](#where-shop-settings-go)
3. [Checking the value in effect](#checking-the-value-in-effect)
4. [After a change](#after-a-change)
5. [A minimal store configuration](#a-minimal-store-configuration)

## The files

| File | Decides | Walkthrough |
|---|---|---|
| `settings/shop.ini` | Currency, rounding, exchange rates, shipping, basket info and VAT handlers, basket name, receipts | [shop.ini walkthrough](shop-ini-walkthrough.md) |
| `settings/site.ini [ShopSettings]` and `[Session]` | Basket clearing, redirect after add, order mail, price datatypes, basket cleanup | [site.ini shop settings walkthrough](site-ini-shop-settings-walkthrough.md) |
| `settings/shopaccount.ini` | The customer data form and the order mail handler | [shopaccount.ini walkthrough](shopaccount-ini-walkthrough.md) |
| `settings/workflow.ini` | Which event types and shop triggers exist; the flat shipping amount | [workflow.ini for the shop](workflow-ini-for-the-shop.md) |
| `settings/paymentgateways.ini` | Which payment gateways are loaded and from where | [paymentgateways.ini walkthrough](paymentgateways-ini-walkthrough.md) |
| `paypal.ini`, `ezauthorize.ini` (extensions) | The provider settings | [paymentgateways.ini walkthrough](paymentgateways-ini-walkthrough.md#the-provider-files) |

## Where shop settings go

Never edit the shipped files in `settings/`; an update overwrites them. Put your values in one of these places, from
the most general to the most specific:

| Place | Applies to | Use it for |
|---|---|---|
| An extension's `settings/<file>.ini.append.php` | Every siteaccess where the extension is active | Handlers and gateways you ship with an extension |
| `settings/override/<file>.ini.append.php` | The whole installation | Store-wide decisions: currency, VAT handler, receipts |
| `settings/siteaccess/<siteaccess>/<file>.ini.append.php` | One siteaccess | Per-site differences, such as a currency per store front ([Multi-store and multisite](../operations/multi-store-and-multisite.md)) |

The `.php` ending with the content wrapped in a PHP comment keeps the file from being served as text. Secrets, such as
a gateway's API key or `[OrderReceiptSettings] Secret`, belong in `settings/override/` and never in a repository.

## Checking the value in effect

Overrides stack, so the value that counts is not always the one you last edited. Check it before you debug:

- **Setup > Ini settings** (`settings/view`) shows, for every setting, the value in effect, the file it comes from and
  every file that sets it, and can compare two siteaccesses ([The settings page](../../../guides/settings-page.md)).
- From a shell, `./console exp:ini where` and `./console exp:ini get` answer the same questions, for example
  `./console exp:ini get shop.ini/VATSettings/Handler`.
- The Store dashboard's "How your shop works" section describes the checkout as this installation is configured,
  naming the setting behind each step ([Store dashboard and order statuses](../../../features/6.0/store-dashboard.md)).

## After a change

```bash
php bin/php/ezcache.php --clear-tag=ini
```

Clear the template caches too when a setting changes what templates show (for example `OrderLinkView`). On servers
that keep PHP running between requests, reload them as your hosting documentation describes
([Caches](../../../guides/caches.md)).

## A minimal store configuration

A store in one currency, with flat shipping, PayPal and VAT by country, needs these overrides:

```ini
# settings/override/shop.ini.append.php
[CurrencySettings]
PreferredCurrency=EUR

[VATSettings]
Handler=ezdefault
ProductCategoryAttribute=tax_class
UserCountryAttribute=country

# settings/override/workflow.ini.append.php
[SimpleShippingWorkflow]
ShippingCost=6.50
ShippingDescription=Shipping

# settings/override/paypal.ini.append.php
[PaypalSettings]
Business=<your PayPal business account>
```

Then the workflows in the administration: Simple shipping on `before_shop_confirmorder` and the payment gateway event
on `shop_checkout` ([Workflows on shop triggers](../checkout/workflows-on-shop-triggers.md)).

## Related pages

- [Settings and ini files](../../creators/app-creators/settings-and-ini-files.md)
- [Running a shop: overview](../../creators/website-creators/shop-overview.md)
- [E-commerce: start here](../e-commerce-start-here.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
