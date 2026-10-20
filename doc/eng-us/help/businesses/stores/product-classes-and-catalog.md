---
title: Product classes and catalog
audience: businesses, editor, website-creator
category: business-solutions
order: 30
summary: How products are modeled in an Exponential store - the product class, price and option fields, folders and categories - and what a business should decide about its catalog.
keywords: product class, catalog, product catalog, product fields, options, variants, product number, SKU, product category, folders, images
type: help
---
# Product classes and catalog

This page is for business owners and editors who will fill the store with products. It explains what a product is in
Exponential, which fields a product can have, how options and variants work, and how to organize the catalog so that
customers and staff find things. The setup itself is described in
[Products, prices and VAT](../../creators/website-creators/products-prices-and-vat.md).

## Contents

1. [A product is content](#a-product-is-content)
2. [The product class as shipped](#the-product-class-as-shipped)
3. [Fields worth adding](#fields-worth-adding)
4. [Options and variants](#options-and-variants)
5. [Organizing the catalog](#organizing-the-catalog)
6. [Who maintains products](#who-maintains-products)
7. [Catalog checklist](#catalog-checklist)

## A product is content

In Exponential every page is a content object of a **class**, a template that says which fields it has. A product is
simply content of a class that has a price field (`ezprice` or `ezmultiprice`)
([Running a shop: overview](../../creators/website-creators/shop-overview.md)). This has practical benefits for a
business:

- Editors write product pages with the same editor, versions and preview as any other page
  ([Publishing and versions](../../publishing-and-versions.md)).
- Products can be translated like other content ([Multilingual sites](../../creators/website-creators/multilingual-sites.md)).
- Products appear in menus, search and feeds without extra work.
- Products can be related to articles, guides and other products
  ([Relations and reuse](../../creators/website-creators/relations-and-reuse.md)).

## The product class as shipped

On the alpha installation (https://alpha.se7enx.com), the **Product** class has these fields, in this order:

| Field | Datatype | Use |
|---|---|---|
| Name | `ezstring` | The product name |
| Product number | `ezstring` | Your article number or SKU |
| Short description | `ezxmltext` | Teaser for lists |
| Description | `ezxmltext` | The full text |
| Price | `ezprice` | The price with its VAT type |
| Image | `ezobjectrelationlist` | Images, as related image objects |
| Caption (Image) | `ezxmltext` | Image caption |
| Additional options | `ezmultioption` | Choices that can change the price |
| Tags | `ezkeyword` | Keywords |
| Star Rating | `ezsrrating` | Customer ratings ([ezstarrating](../../../features/6.0/extensions/ezstarrating.md)) |
| Meta Data | `xrowmetadata` | Search engine metadata ([xrowmetadata](../../../features/6.0/extensions/xrowmetadata.md)) |
| Specifications | `ezmatrix` | A table of technical data |

The class is a container, so a product can hold sub-pages such as manuals or accessories. Your installation's class
may differ: it depends on the site package that was installed
([Site packages and shipped themes](../../creators/website-creators/site-packages-and-shipped-themes.md)).

## Fields worth adding

Classes can be changed at any time in **Setup > Classes** ([Content classes for websites](../../creators/website-creators/content-classes-for-websites.md)).
Fields businesses often add:

| Need | Datatype | Note |
|---|---|---|
| Product category for VAT rules | `ezproductcategory` | Needed only when VAT differs by product type ([VAT rules and VAT types](vat-rules-and-vat-types.md)) |
| Prices in several currencies | `ezmultiprice` instead of `ezprice` | ([Prices and currencies](prices-and-currencies.md)) |
| Downloadable manual | `ezbinaryfile` | ([Files and downloads](../../creators/website-creators/files-and-downloads.md)) |
| Brand, material, color as filters | `eztags` or a selection | ([Tagging with eztags](../../creators/website-creators/tagging-with-eztags.md)) |
| Weight, for shipping rules | `ezfloat` or `ezinteger` | Only useful with a custom shipping handler ([Shipping and handling](shipping-and-handling.md)) |

Datatypes are compared in [Choosing datatypes for your fields](../../creators/website-creators/choosing-datatypes.md).

## Options and variants

The kernel ships four option datatypes: `ezoption`, `ezmultioption`, `ezmultioption2` and `ezrangeoption`
(`kernel/classes/datatypes/`). An option lets the customer choose, for example, a size or color, and can change the
price; the choice is stored with the basket item
([Products, prices and VAT](../../creators/website-creators/products-prices-and-vat.md#options-and-variants)).

When should a variant be a separate product instead?

| Use an option when | Use a separate product when |
|---|---|
| The variants share images and text | Each variant has its own images or description |
| Price differences are simple additions | Each variant needs its own product number for your warehouse |
| You do not track stock per variant | The variants are searched for separately |

> **Note**
> The shop has no stock management ([What the built-in shop does](what-the-built-in-shop-does.md#what-is-not-included)).
> If stock per variant matters, plan an extension or an integration with your inventory system.

## Organizing the catalog

- **Folders** make the main categories and the menu: a folder per category, products inside
  ([Structuring the content tree](../../creators/website-creators/structuring-the-content-tree.md)).
- **One product, several categories**: a content object can have several locations, so a product can sit in "New"
  and in its own category without being copied.
- **Tags** give a second way through the catalog (brand, material, occasion).
- **Sections** separate a wholesale catalog from the public one, with permissions per section
  ([Sections and object states](../../creators/website-creators/sections-and-object-states.md)).
- **Landing pages** for campaigns and seasons ([Landing pages with layouts](../../creators/website-creators/landing-pages-with-layouts.md)).

## Who maintains products

| Task | Who | Permission |
|---|---|---|
| Write and update product pages | Editors | content create and edit in the product folders |
| Change prices | Editors or the shop owner | content edit; prices are fields of the product |
| Create VAT types, product categories, currencies | Shop owner | `shop/setup` |
| Check that every product has a price | Shop owner | the dashboard counts products without a price |

The Store dashboard reports the number of products and of products without a price, and lists the product classes
with their price attribute ([Store dashboard and order statuses](../../../features/6.0/store-dashboard.md)).

## Catalog checklist

- [ ] The product class has every field your product pages need.
- [ ] Each product has a product number that matches your bookkeeping.
- [ ] Prices are set and the VAT type on each price is right.
- [ ] Options are tested in the basket with their price changes.
- [ ] Categories, menus and tags are agreed before mass entry starts.
- [ ] Image sizes are defined once ([Images and image variations](../../creators/website-creators/images-and-image-variations.md)).

## Related pages

- [Products, prices and VAT](../../creators/website-creators/products-prices-and-vat.md)
- [Store search and navigation](store-search-and-navigation.md)
- [Designing your content model](../../creators/website-creators/designing-your-content-model.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
