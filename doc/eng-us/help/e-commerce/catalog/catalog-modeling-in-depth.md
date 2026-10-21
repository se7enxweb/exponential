---
title: Catalog modeling in depth
audience: ecommerce, website-creator, businesses
category: e-commerce
order: 21
summary: Designing product classes for an Exponential store - one class or several, the fields a product needs, item numbers, relations, folders and several locations, translations, and models that import and report well.
keywords: catalog modeling, product class, content class design, SKU, item number, ezstring, ezisbn, relations, images, folders, locations, translations, multilingual catalog, import friendly
type: help
---
# Catalog modeling in depth

This page is for developers and merchants who design the content classes of a store catalog. It goes through the
decisions in the order they are usually made and explains the trade-offs of each in Exponential's content model.

## Contents

1. [One product class or several](#one-product-class-or-several)
2. [The fields of a product](#the-fields-of-a-product)
3. [Item numbers and identifiers](#item-numbers-and-identifiers)
4. [Images and related products](#images-and-related-products)
5. [Folders and several locations](#folders-and-several-locations)
6. [Translations](#translations)
7. [Models that import and report well](#models-that-import-and-report-well)
8. [A worked example](#a-worked-example)

## One product class or several

A class is a set of fields. Products that need different fields belong in different classes; products that differ
only in values belong in one.

| Situation | Model |
|---|---|
| All products are described the same way | One class, for example `product` |
| Books and clothing: different fields | Two classes, each with a price field |
| Physical products and services | Two classes; services need no weight or dimensions |
| Products and gift cards | Separate classes; a gift card's delivery is custom work |

Every class with a price field is a product class. **Store > Products overview** shows one class at a time, and the
dashboard lists the product classes with their price attribute, so a few clearly named classes are easier to run than
many.

## The fields of a product

| Field | Datatype | Note |
|---|---|---|
| Name | `ezstring` | Shown in the basket and the order; keep it short and unique enough for staff |
| Item number | `ezstring` (or `ezisbn` for books) | See below |
| Short description | `ezxmltext` or `eztext` | Used in lists and landing page blocks |
| Description | `ezxmltext` | The full view |
| Price | `ezprice` or `ezmultiprice` | Exactly one per class ([Price fields compared](price-fields-compared.md)) |
| Tax class | `ezproductcategory` | Only when VAT depends on product category ([Product categories and VAT rules](product-categories-and-vat-rules.md)) |
| Options | `ezoption`, `ezmultioption`, `ezmultioption2`, `ezrangeoption` | Only for choices that change the price ([Options and variants](options-and-variants.md)) |
| Images | `ezimage` or relations to image objects | See below |
| Weight, dimensions | `ezfloat`, `ezinteger` | Only if a shipping handler will read them |

How to choose datatypes in general is in
[Choosing datatypes for your fields](../../creators/website-creators/choosing-datatypes.md).

## Item numbers and identifiers

The kernel has no item number (SKU) field of its own. Add one as `ezstring` and make it required. It is what staff
search for, what imports match on, and what an accounting or stock system will expect. The order keeps the product's
name and price in its product collection, so an item number in the name or a separate field visible in order
templates helps staff identify products in old orders.

## Images and related products

- One `ezimage` field is simplest for a small catalog.
- An object relation list to image objects lets several products share images from the media library
  ([Relations and reuse](../../creators/website-creators/relations-and-reuse.md)).
- An object relation list to other products gives "related products" chosen by hand. The shop also has a fetch
  function, `fetch( 'shop', 'related_purchase', hash( 'contentobject_id', ..., 'limit', ... ) )`, that lists products
  bought together with a product, from real orders (`kernel/shop/function_definition.php`).

## Folders and several locations

Products live in folders like any content. One product can have several locations, so it can appear in "Shoes" and in
"Sale" without a copy. Keep one main location for its address and breadcrumb. Sections can group products for
permissions and for discount rules, which can target sections
([Discounts](../../customers/commerce/discounts.md)).

## Translations

A product translated into several languages is still one product: the price field and options are fields of the
translation, so check prices in every language you publish. Multilingual sites are described in
[Multilingual sites](../../creators/website-creators/multilingual-sites.md).

## Models that import and report well

- Keep field order stable: the CSV import reads columns in the order of the class's fields
  ([Product imports and migrations](../operations/product-imports-and-migrations.md)).
- Use plain datatypes for data that comes from outside (strings, numbers, prices) and rich text only for descriptions.
- Give every class and field a clear identifier; templates, imports and remote services use identifiers.
- Avoid putting prices in option text: options have their own price fields.

## A worked example

A small store sells printed books and gift sets, in one currency, with a reduced VAT rate for books:

| Class | Fields |
|---|---|
| `book` | title (`ezstring`), ISBN (`ezisbn`), author (`ezstring`), description (`ezxmltext`), price (`ezprice`), tax class (`ezproductcategory`), cover (`ezimage`) |
| `gift_set` | name (`ezstring`), item number (`ezstring`), description (`ezxmltext`), price (`ezprice`), tax class (`ezproductcategory`), image (`ezimage`), wrapping (`ezoption`) |

VAT: two VAT types (standard and reduced), two product categories ("Books", "Other"), VAT rules per country, and the
VAT handler switched on ([Product categories and VAT rules](product-categories-and-vat-rules.md)).

## Related pages

- [Catalog modeling](README.md)
- [Content classes for websites](../../creators/website-creators/content-classes-for-websites.md)
- [Adding a product](../../customers/commerce/adding-a-product.md)
- [Store search and navigation](../../businesses/stores/store-search-and-navigation.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
