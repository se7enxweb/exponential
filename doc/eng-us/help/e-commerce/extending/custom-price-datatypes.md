---
title: Custom price datatypes
audience: ecommerce, app-creator, developer
category: e-commerce
order: 67
summary: Making a datatype of your own count as a price in the Exponential shop - the ProductDatatypeStringList setting, what the shop reads from the datatype's content, building on eZSimplePrice, and when a custom price datatype is worth it.
keywords: custom price datatype, ProductDatatypeStringList, isProductDatatype, eZShopFunctions, eZSimplePrice, eZPrice, eZMultiPrice, price object, VATType, VATPercent, is_vat_included, currency, custom datatype
type: help
---
# Custom price datatypes

This page is for developers who need a price field that behaves differently from `ezprice` and `ezmultiprice`, for
example a price that depends on customer group or on a date. The shop decides which datatypes hold prices from a
setting, so a datatype of your own can take part in the basket and checkout.

## Contents

1. [How the shop finds prices](#how-the-shop-finds-prices)
2. [What your datatype must provide](#what-your-datatype-must-provide)
3. [Registering it](#registering-it)
4. [Is it worth it](#is-it-worth-it)

## How the shop finds prices

`eZShopFunctions::isProductDatatype()` (`kernel/shop/classes/ezshopfunctions.php`) checks a datatype against
`productDatatypeStringList()`: the list in `site.ini [ShopSettings] ProductDatatypeStringList[]` when that setting
exists, otherwise `ezprice` and `ezmultiprice`. A class with a field of a listed datatype is a product class. Adding to
the basket, verifying the basket and dynamic VAT all look for such a field (`kernel/shop/ezshopoperationcollection.php`,
`kernel/classes/ezproductcollectionitem.php`).

## What your datatype must provide

The shop reads the field's content object. The kernel's price contents, `eZPrice` and `eZMultiPrice`, both extend
`eZSimplePrice` (`kernel/shop/classes/ezsimpleprice.php`), and that is the easiest base for yours. The shop uses at
least:

| From the content | Used for |
|---|---|
| `attribute( 'price' )` | The amount put into the basket and compared when the basket is verified |
| `attribute( 'currency' )` | The basket's currency |
| `attribute( 'is_vat_included' )`, `attribute( 'vat_percent' )` | Basket verification and totals |
| `VATType()`, `VATPercent( $object, $country )` | Dynamic VAT updates |

Writing the datatype itself (storage, edit template, validation, `toString()`/`fromString()` for imports) is covered in
[Custom datatypes](../../creators/app-creators/custom-datatypes.md) and
[Datatype lifecycle](../../creators/app-creators/datatype-lifecycle.md).

## Registering it

Setting the list replaces the default, so name the shipped datatypes too:

```ini
# extension/mystore_shop/settings/site.ini.append.php
[ShopSettings]
ProductDatatypeStringList[]
ProductDatatypeStringList[]=ezprice
ProductDatatypeStringList[]=ezmultiprice
ProductDatatypeStringList[]=mystoreprice
```

## Is it worth it

| Need | Simpler alternative |
|---|---|
| A percentage off for some customers | Discount groups and rules ([Discounts](../../customers/commerce/discounts.md)) |
| Prices per currency | `ezmultiprice` |
| Surcharges for choices | Option datatypes ([Options and variants](../catalog/options-and-variants.md)) |
| Prices that change on a date | An editor's scheduled change, or a script that updates `ezprice` values |
| Prices computed from an external system | A custom price datatype, or an import that writes `ezprice` |

A custom price datatype touches the basket, verification, VAT, templates, imports and reports. Choose it only when the
alternatives cannot express the price.

## Related pages

- [Extending the shop](README.md)
- [Price fields compared](../catalog/price-fields-compared.md)
- [site.ini shop settings walkthrough](../settings/site-ini-shop-settings-walkthrough.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
