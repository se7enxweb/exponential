---
title: Adding a product
audience: customer, editor
category: customer-success
order: 30
summary: The checklist a store team uses for every new product in an Exponential store - what to prepare, the fields to fill, the price and VAT, options, a test in the basket, and who approves it before it goes live.
keywords: add product, new product, product checklist, product page, price, VAT type, options, product number, SKU, product images, publish product
type: help
---
# Adding a product

This page is for product editors and the store manager who signs new products off. It is the owner's checklist for
every new product: what to have ready, what to fill in, and what to test before customers can buy it. The clicks for
creating and publishing content are on [Creating and editing content](../../editing-content.md) and
[Publishing and versions](../../publishing-and-versions.md); this page does not repeat them.

## Before you start

- [ ] The product folder it belongs in exists (one folder per category is the usual structure,
  [Product classes and catalog](../../businesses/stores/product-classes-and-catalog.md)).
- [ ] You have permission to create content of the product class in that folder.
- [ ] Texts, images and the price are agreed. Prices and VAT are the owner's decision, not the editor's.
- [ ] The product number matches the one in your bookkeeping or warehouse list.

## The fields

The fields depend on how your product class was built. On the demonstration installation at
<https://alpha.se7enx.com> the Product class has a name, a product number, a short description, a description, a
price, images, additional options, tags, a star rating, metadata and a specifications table
([Product classes and catalog](../../businesses/stores/product-classes-and-catalog.md)). Your own class may differ.

| Field (typical) | Fill in | Check |
|---|---|---|
| Name | The name customers search for | Short, no internal codes |
| Product number | Your article number or SKU | Same as in your bookkeeping |
| Short description | One or two sentences for lists | Reads well on a phone |
| Description | The full text | Sizes, materials, what is included |
| Price | The amount, the VAT type, and whether it includes VAT | See the next section |
| Images | Related image objects | Alternative text filled in ([Media and uploads](../../media-and-uploads.md)) |
| Options | Sizes, colors, extras, with price differences | Tested in the basket |
| Tags or keywords | Words for search and filters | The words your customers use |
| Metadata | Title and description for search engines | Not a copy of the name |

## Price and VAT

With a single-currency price (`ezprice`) each price names one VAT type and says whether the amount includes VAT
([Prices and currencies](../../businesses/stores/prices-and-currencies.md)).

- [ ] The VAT type is the right one for this product, or "Determined by VAT charging rules" if your store uses VAT
  rules (the name comes from `settings/shop.ini [VATSettings] DynamicVatTypeName`).
- [ ] With VAT rules: the product category field is set, or the rules cannot tell which rate applies
  ([VAT types and rules](vat-types-and-rules.md)).
- [ ] "Price includes VAT" is set the same way as on your other products.
- [ ] With prices in several currencies (`ezmultiprice`): custom prices for your main markets, automatic prices for
  the rest ([Prices and currencies](prices-and-currencies.md)).

## Options

The kernel has four option fields: `ezoption`, `ezmultioption`, `ezmultioption2` and `ezrangeoption`
(`kernel/classes/datatypes/`). An option can change the price, and the choice is stored with the basket item.

- [ ] Every option a customer can choose is one you can actually deliver.
- [ ] Price additions are entered with the same VAT treatment as the base price.
- [ ] Nothing in the option names needs explaining by phone.

## Test before it goes live

Preview the draft first ([Publishing and versions](../../publishing-and-versions.md)). After publishing:

- [ ] The product appears in its folder, in the menu and in search.
- [ ] **Add to basket** works, with each option.
- [ ] The basket shows the right price; the confirmation page shows the right VAT and shipping.
- [ ] The Store dashboard does not count it among "products without a price"
  ([Store dashboard and order statuses](../../../features/6.0/store-dashboard.md)).

You do not need to place a real order for every product. A test order is worth it for a new kind of product, a new
VAT type, or a new option field ([Basket, checkout and orders](../../creators/website-creators/basket-checkout-and-orders.md)).

## Who signs off

| Product | Suggested sign-off |
|---|---|
| A new item in an existing range | The editor publishes; the store manager checks the price the same day |
| A new range or category | The owner approves price and VAT before publishing |
| A product with a new VAT rate or a new option field | Owner, and a test order |

If your site has an approval workflow on the product folders, the draft goes to the approver automatically when the
editor publishes ([Workflows](../../../guides/workflows.md)).

## Related pages

- [Products and catalog](products-and-catalog.md)
- [Creating and editing content](../../editing-content.md)
- [Product classes and catalog (for businesses)](../../businesses/stores/product-classes-and-catalog.md)
- [Products, prices and VAT](../../creators/website-creators/products-prices-and-vat.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
