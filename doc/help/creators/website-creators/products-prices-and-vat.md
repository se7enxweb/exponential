---
title: Products, prices and VAT
audience: website-creator, admin
category: website-creation
order: 530
summary: Model products for the shop - the price datatypes (ezprice, ezmultiprice), options and variants, VAT types and VAT rules by country and product category, currencies, rounding and discounts.
keywords: products, price, ezprice, ezmultiprice, VAT, VAT types, VAT rules, product category, currency, exchange rates, rounding, discounts, options, variants, website creator
type: help
---
# Products, prices and VAT

This page is for website creators who set up the products of a shop: how prices are stored, how VAT is charged, and
which settings decide currencies and rounding.

## Contents

1. [The product class](#the-product-class)
2. [Price datatypes](#price-datatypes)
3. [Options and variants](#options-and-variants)
4. [VAT](#vat)
5. [Currencies and rounding](#currencies-and-rounding)
6. [Discounts](#discounts)

## The product class

A product is content of any class with a price field. The shipped `product` class is a good start; add fields for
what your products need (SKU as `ezstring`, images as relations, a category). Products live in folders like other
content and are found by search and menus.

## Price datatypes

| Datatype | Use |
|---|---|
| `ezprice` | one price, with a VAT type and "price includes VAT" or not |
| `ezmultiprice` | one price per currency |
| `ezproductcategory` | the product category used by VAT rules |

## Options and variants

`ezoption`, `ezmultioption`, `ezmultioption2` and `ezrangeoption` let visitors choose options (size, color) that can
change the price; the choice is stored with the basket item. For products that differ in stock or images, separate
products are usually clearer.

## VAT

| Tool | Where | Meaning |
|---|---|---|
| VAT types | **Store > VAT types** (`shop/vattype`) | named percentages: standard, reduced, zero |
| VAT rules | `shop/vatrules`, `shop/editvatrule` | which VAT type applies by customer country and product category |
| Settings | `shop.ini [VATSettings]` | `ProductCategoryAttribute`, `UserCountryAttribute`, `RequireUserCountry=true`, `DynamicVatTypeName` |

With VAT rules, choose the VAT type "Determined by VAT charging rules" (`DynamicVatTypeName`) on the price, set which
attribute holds the product category and which user attribute the country, and the rule for the customer's country
and the product's category applies. The Store dashboard warns when a 0 percent VAT type is in use.

> **Note**
> VAT law differs by country. The shop calculates what you configure; whether that is correct for your business is a
> question for your tax adviser.

## Currencies and rounding

| Setting (`shop.ini`) | Shipped | Meaning |
|---|---|---|
| `[CurrencySettings] PreferredCurrency` | `USD` | the shop's currency |
| `[ExchangeRatesSettings] BaseCurrency`, `ExchangeRatesUpdateHandler` | `EUR`, `eZECB` | exchange rates for `ezmultiprice` |
| `[MathSettings] RoundingType`, `RoundingPrecision` | `round`, `2` | rounding of prices |

Currencies are managed in `shop/currencylist`; visitors can choose theirs with `shop/setpreferredcurrency`.

## Discounts

Discount groups (`shop/discountgroup`) give user groups a percentage off products or classes, with rules per group
(`shop/discountruleedit`).

## Related pages

- [Running a shop: overview](shop-overview.md)
- [Basket, checkout and orders](basket-checkout-and-orders.md)
- [Choosing datatypes for your fields](choosing-datatypes.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
