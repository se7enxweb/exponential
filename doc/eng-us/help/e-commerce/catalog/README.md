---
title: Catalog modeling
audience: ecommerce, website-creator, businesses
category: e-commerce
order: 20
summary: How an Exponential store's catalog is built from content classes, price fields, option fields, product categories and folders - an overview of the catalog pages, the products overview screen, and the questions to answer before modeling.
keywords: catalog, product class, product model, price field, ezprice, ezmultiprice, ezproductcategory, options, variants, folders, products overview, shop/productsoverview
type: help
---
# Catalog modeling

This part is for merchants and developers who design the product catalog of an Exponential store. In Exponential a
product is ordinary content: any content class becomes sellable when it has a price field. That gives you freedom,
and with it a few decisions that are hard to change later. These pages go deeper than the business overview in
[Product classes and catalog](../../businesses/stores/product-classes-and-catalog.md).

## Contents

1. [The building blocks](#the-building-blocks)
2. [Questions to answer before modeling](#questions-to-answer-before-modeling)
3. [The pages of this part](#the-pages-of-this-part)
4. [The products overview screen](#the-products-overview-screen)

## The building blocks

| Block | What it is | Where it is defined |
|---|---|---|
| Product class | A content class with a price field; the shop recognizes it by that field | Setup > Classes |
| Price field | `ezprice` (one price) or `ezmultiprice` (one price per currency) | `kernel/classes/datatypes/ezprice/`, `ezmultiprice/` |
| Option fields | `ezoption`, `ezmultioption`, `ezmultioption2`, `ezrangeoption`: customer choices stored with the basket item | `kernel/classes/datatypes/` |
| Product category | `ezproductcategory`: the tax class of a product, used by VAT rules | `kernel/classes/datatypes/ezproductcategory/`, **Store > Product categories** (`shop/productcategories`) |
| Folders and locations | Where products sit in the content tree; one product can have several locations | The content tree |

Which datatypes count as price fields is decided by `eZShopFunctions` (`kernel/shop/classes/ezshopfunctions.php`):
`ezprice` and `ezmultiprice`, or the list in `site.ini [ShopSettings] ProductDatatypeStringList[]` when that
setting is present ([Custom price datatypes](../extending/custom-price-datatypes.md)).

## Questions to answer before modeling

- [ ] Which kinds of product do you sell, and does each kind need different fields?
- [ ] One currency or several? Prices set by hand per currency, or converted?
- [ ] Do prices include VAT or exclude it, and is that the same for all products?
- [ ] Does VAT depend on the customer's country and the product's tax class?
- [ ] Which customer choices change the price (size, material, service level)?
- [ ] Which choices are really separate products (different stock, images, item number)?
- [ ] How will customers browse: by category folders, by tags, by search facets?
- [ ] Where do product data come from at launch, and later?

## The pages of this part

| Page | Answers |
|---|---|
| [Catalog modeling in depth](catalog-modeling-in-depth.md) | Class design, fields, folders, several locations, translations and import-friendly models |
| [Price fields compared](price-fields-compared.md) | `ezprice` against `ezmultiprice`, custom and automatic prices, string formats |
| [Options and variants](options-and-variants.md) | The four option datatypes, when to use separate products |
| [Product categories and VAT rules](product-categories-and-vat-rules.md) | Tax classes, VAT rules, the VAT handler and its priority logic |

## The products overview screen

**Store > Products overview** (`shop/productsoverview`, policy `shop/administrate`) lists the products of one product
class with their prices and lets you sort the list. Use it to check a catalog after an import or a price change:
missing prices show at a glance. The Store dashboard also counts products without a price
([Store dashboard and order statuses](../../../features/6.0/store-dashboard.md)).

## Related pages

- [Products, prices and VAT](../../creators/website-creators/products-prices-and-vat.md)
- [Designing your content model](../../creators/website-creators/designing-your-content-model.md)
- [Products and catalog (running your store)](../../customers/commerce/products-and-catalog.md)
- [Product imports and migrations](../operations/product-imports-and-migrations.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
