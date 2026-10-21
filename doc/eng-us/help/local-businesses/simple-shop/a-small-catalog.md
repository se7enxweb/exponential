---
title: A small catalog
audience: local-business, businesses, customer, editor
category: local-business
order: 42
summary: Setting up and keeping a small product catalog for a local business with the shipped product class - products, vouchers and gift cards, options such as sizes, photos and descriptions, sold-out and seasonal items, and a product checklist.
keywords: catalog, products, product class, vouchers, gift cards, options, price, photos, sold out, seasonal products, ezprice, ezoption, local business
type: help
---
# A small catalog

This page is for the owner or editor of a local business shop with a handful to a few hundred products. In
Exponential a product is ordinary content: an object of a class with a price field, placed in a folder. The `ezwebin`
site packages ship a `product` class (`extension/ezwebin/packages/product-1.0-1.ezpkg`), and builders adapt it
([Product classes and catalog](../../businesses/stores/product-classes-and-catalog.md)).

## Contents

1. [What a product is made of](#what-a-product-is-made-of)
2. [Vouchers and gift cards](#vouchers-and-gift-cards)
3. [Options: sizes, flavors, colors](#options-sizes-flavors-colors)
4. [Folders and menus](#folders-and-menus)
5. [Sold out and seasonal products](#sold-out-and-seasonal-products)
6. [Adding a product, step by step](#adding-a-product-step-by-step)

## What a product is made of

| Part | Datatype | Tip for a local business |
|---|---|---|
| Name | Text line | what people would search for: "Sourdough rye loaf, 1 kg" |
| Short description | Rich text | one or two sentences, the main facts |
| Description | Rich text | ingredients, materials, care, allergens where relevant |
| Price | Price (`ezprice`) or Multi-price (`ezmultiprice`) | the price, with the VAT type chosen ([Prices and currencies](../../businesses/stores/prices-and-currencies.md)) |
| Image | Image | your own photo on a plain background ([Photos that sell](../marketing/photos-that-sell.md)) |
| Product number | Text line | optional, for your own records |

The fields of your product class may differ; your builder decides them with you.

## Vouchers and gift cards

A voucher is a product too: name "Gift voucher 25", price, a photo of the printed card. What the shop does **not** do
is generate and check voucher codes: there is no voucher code or coupon system built in
([Discounts and wishlists](../../businesses/stores/discounts-and-wishlists.md)). A common small-business way:

1. The customer buys the voucher product.
2. Staff see the order, write a numbered voucher card by hand or from a template, and send or hand it over.
3. The numbers are kept in a simple list; a redeemed voucher is crossed off at the till.

For several values, use one product with an option, or one product per value.

## Options: sizes, flavors, colors

Products can carry options that the customer picks before adding to the basket (option datatypes such as
`ezoption`, `ezmultioption2` and `ezrangeoption` are in `kernel/classes/datatypes/`). An option can change the price.
Keep options few; a long option list on a phone is tiring. Details:
[Product classes and catalog](../../businesses/stores/product-classes-and-catalog.md).

## Folders and menus

Put products in a few folders ("Bread", "Cakes", "Gifts"). Folders become the shop's menu and addresses
(`/Shop/Gifts`). A product can appear in two folders through a second location without being copied
([Store search and navigation](../../businesses/stores/store-search-and-navigation.md)).

## Sold out and seasonal products

The built-in shop has **no stock counting** (listed among the things it does not do:
[What the built-in shop does](../../businesses/stores/what-the-built-in-shop-does.md)). For a small catalog:

| Situation | Do |
|---|---|
| Temporarily sold out | hide the product, or add "Sold out" to the name and remove it from the basket button (template, ask the builder) |
| Seasonal (Christmas cookies) | hide after the season, unhide next year; or schedule hiding at a date ([Scheduled publishing](../../customers/day-to-day/scheduled-publishing.md)) |
| Discontinued | move to the trash after open orders are done |

Hiding keeps the product and its address; unhiding brings it back as it was.

## Adding a product, step by step

1. Prepare: name, short text, description, price, VAT type, one or two photos.
2. In the administration or editor, open the right folder and create a **Product**.
3. Fill in the fields, upload the photo, write an alternative text.
4. Publish, open the product page, add it to the basket and check the price and VAT on the confirmation page.
5. Remove the test basket.

The full routine is in [Adding a product](../../customers/commerce/adding-a-product.md).

## Related pages

- [Do you need a shop?](do-you-need-a-shop.md)
- [Products and catalog](../../customers/commerce/products-and-catalog.md)
- [Running a store day to day](../../businesses/stores/running-a-store-day-to-day.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
