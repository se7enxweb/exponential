---
title: Price fields compared
audience: ecommerce, website-creator, businesses
category: e-commerce
order: 22
summary: The two price datatypes of the Exponential shop side by side - ezprice and ezmultiprice - with VAT included or excluded, the class defaults, custom and automatic prices per currency, the update buttons of the currency list, and the text formats used by imports and exports.
keywords: ezprice, ezmultiprice, price field, multi-currency, custom price, automatic price, autoprices, exchange rates, VAT included, VAT excluded, default currency, CSV price format, fromString
type: help
---
# Price fields compared

This page is for developers and merchants who choose the price field of a product class. Exponential ships two price
datatypes. The choice decides how prices are entered, how several currencies work and how prices look in an import
file, so it is worth making on purpose.

## Contents

1. [Side by side](#side-by-side)
2. [VAT included or excluded](#vat-included-or-excluded)
3. [How ezmultiprice handles currencies](#how-ezmultiprice-handles-currencies)
4. [Text formats for imports and exports](#text-formats-for-imports-and-exports)
5. [Which one to choose](#which-one-to-choose)
6. [Changing later](#changing-later)

## Side by side

| | `ezprice` | `ezmultiprice` |
|---|---|---|
| Prices per product | One amount | One amount per currency |
| Currency | The shop's `[CurrencySettings] PreferredCurrency` (`shop.ini`) | Each price carries its currency code; the class sets a default currency |
| VAT type | Chosen per product, default from the class | Same |
| Price includes VAT | Per product, default from the class | Same |
| Converted prices | No | Yes: "automatic" prices calculated from exchange rates |
| Admin screens involved | Content edit | Content edit and **Store > Currencies** (`shop/currencylist`) |
| Source | `kernel/classes/datatypes/ezprice/ezpricetype.php` | `kernel/classes/datatypes/ezmultiprice/ezmultipricetype.php`, `kernel/shop/classes/ezmultipricedata.php` |

## VAT included or excluded

Both datatypes store whether the entered amount includes VAT (`INCLUDED_VAT = 1`) or excludes it
(`EXCLUDED_VAT = 2`), and which VAT type applies. The class definition sets the defaults that a new product starts
with. Choose one convention for the whole store and tell editors; a mix of "including" and "excluding" products is the
most common source of wrong totals. The VAT type can be "Determined by VAT charging rules" when VAT rules are used
([Product categories and VAT rules](product-categories-and-vat-rules.md)).

## How ezmultiprice handles currencies

Each price row of an `ezmultiprice` field has a currency code, a value and a type (`kernel/shop/classes/ezmultipricedata.php`):

| Type | Constant | Meaning |
|---|---|---|
| Custom | `VALUE_TYPE_CUSTOM = 1` | Entered by hand; never changed by the system |
| Automatic | `VALUE_TYPE_AUTO = 2` | Calculated from another price with the currency's exchange rate |

The currency list (`shop/currencylist`, policy `shop/setup`) has the buttons that drive this:

| Button | What it does |
|---|---|
| New currency | Adds a currency; automatic prices are created for existing products |
| Update autorates | Fetches exchange rates with the configured handler (shipped: `eZECB`, the European Central Bank) |
| Update autoprices | Recalculates every automatic price from the current rates |
| Apply changes | Saves edited rates and settings of the listed currencies |

Rates are not updated by a cron job in the shipped settings; someone presses the buttons, or a developer schedules it.
Rounding follows `shop.ini [MathSettings]` ([shop.ini walkthrough](../settings/shop-ini-walkthrough.md#mathsettings)).
Daily currency work is described for owners in [Prices and currencies](../../customers/commerce/prices-and-currencies.md).

## Text formats for imports and exports

The CSV import and export and other tools that use a datatype's text form read and write these formats
(`fromString()` and `toString()` of each datatype):

| Datatype | Format | Example |
|---|---|---|
| `ezprice` | `price|vat_type_id|vat_included` where the last part is `1` (included) or `0` (excluded) | `19.90|1|1` |
| `ezmultiprice` | `vat_type_id|vat_included|CUR|value|TYPE|CUR|value|TYPE...` with `TYPE` one of `CUSTOM`, `AUTO` | `1|1|EUR|19.90|CUSTOM|USD|21.50|AUTO` |

An `ezprice` value that is not a number is refused rather than stored as zero, and an incomplete trailing currency
triple of an `ezmultiprice` value is left out. Check the VAT type ids in **Store > VAT types** before preparing a file.

## Which one to choose

| Your store | Choose |
|---|---|
| Sells in one currency | `ezprice` |
| Shows prices in several currencies but charges in one | `ezprice`, and explain the conversion on the page |
| Sells in several currencies, prices set per market | `ezmultiprice` with custom prices |
| Sells in several currencies, prices follow exchange rates | `ezmultiprice` with automatic prices |

## Changing later

There is a script for the most common move: `bin/php/convertprice2multiprice.php` converts the `ezprice` attributes
of content objects to `ezmultiprice`, keeping the existing prices, and creates the shop's currency when it does not
exist yet (`kernel/private/classes/commands/convertprice2multiprice.php`). Before running it:

- [ ] Take a backup of the database ([Backups and restores](../../creators/website-creators/backups-and-restores.md)).
- [ ] Run it on a copy of the site first and check prices in **Store > Products overview**.
- [ ] Test every template that shows a price, the basket and a full order afterwards.

The opposite direction has no script. Decide before the catalog grows.

## Related pages

- [Catalog modeling](README.md)
- [Prices and currencies (businesses)](../../businesses/stores/prices-and-currencies.md)
- [Exchange rate handlers](../extending/exchange-rate-handlers.md)
- [Product imports and migrations](../operations/product-imports-and-migrations.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
