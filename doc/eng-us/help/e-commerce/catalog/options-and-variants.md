---
title: Options and variants
audience: ecommerce, website-creator, businesses
category: e-commerce
order: 23
summary: Letting customers choose size, color or extras in an Exponential store - the four option datatypes, how a choice is stored with the basket item and the order, the add-to-basket form, and when separate products are the better model.
keywords: options, variants, ezoption, ezmultioption, ezmultioption2, ezrangeoption, product options, additional price, basket item option, size, color, configurable product
type: help
---
# Options and variants

This page is for developers and merchants who sell products with choices. Exponential has four option datatypes; a
customer's choice is posted with the add-to-basket form, stored with the basket item and copied into the order. This
page explains which datatype fits which kind of choice and when a "variant" should be its own product.

## Contents

1. [The four option datatypes](#the-four-option-datatypes)
2. [How a choice travels](#how-a-choice-travels)
3. [Options or separate products](#options-or-separate-products)
4. [Template work](#template-work)
5. [Checklist](#checklist)

## The four option datatypes

| Datatype | Use for | Example |
|---|---|---|
| `ezoption` | One list of choices, each with an optional additional price | Gift wrapping: none, standard (+2.00), premium (+5.00) |
| `ezmultioption` | Several independent lists on one product | Size and color |
| `ezmultioption2` | Groups of option lists that can be nested, with rules between choices, for configurable products | A computer with base model, memory and extras |
| `ezrangeoption` | A numeric range with a start, stop and step value | A length from 1 to 10 meters in steps of 1 |

All four live in `kernel/classes/datatypes/`. Their editing is covered in
[Choosing datatypes for your fields](../../creators/website-creators/choosing-datatypes.md).

## How a choice travels

1. The product's full template posts the add-to-basket form with the chosen option values
   ([Basket, checkout and orders](../../creators/website-creators/basket-checkout-and-orders.md#add-to-basket)).
2. The `addtobasket` operation stores the item in the basket's product collection, and each chosen option as an item
   option with its name, value and price (table `ezproductcollection_item_opt`).
3. Item prices are calculated with the option prices included; the basket and confirmation show them.
4. At checkout the product collection becomes the order's, so the choice stays visible in the order view and the
   order mail even if the product changes later.

The workflow trigger `shop_addtobasket` receives the `option_list`, so an event on that trigger can check a choice
before the item is added ([Workflows on shop triggers](../checkout/workflows-on-shop-triggers.md)).

## Options or separate products

| Prefer options when | Prefer separate products when |
|---|---|
| The choice changes the price a little or not at all | Each variant has its own item number for stock or accounting |
| All variants share images and description | Variants have their own images or descriptions |
| You do not track stock per variant | You will connect a stock system later |
| Customers see one product page | Customers should find each variant by search |

Separate products can share a parent folder or relations so that the page still shows "other colors". A shipping or
stock integration usually needs an identifier per sellable unit, which favors separate products.

## Template work

The shipped themes' product templates post the add-to-basket form; the option fields must be rendered inside that
form so their values are posted. The standard design shows chosen options in the basket and order templates under
`design/standard/templates/shop/`. Check every template that lists items: basket, confirmation, order view, order
receipt and order mail.

## Checklist

- [ ] Each option list has a clear label for screen readers ([Store accessibility](../operations/store-accessibility.md)).
- [ ] Additional prices include or exclude VAT the same way as the product's price.
- [ ] A test order with options shows each choice in the basket, the order view and the mail.
- [ ] Changing an option's price on the product does not change existing orders.

## Related pages

- [Catalog modeling](README.md)
- [Catalog modeling in depth](catalog-modeling-in-depth.md)
- [Product classes and catalog](../../businesses/stores/product-classes-and-catalog.md)
- [Wishlists and baskets](../../customers/commerce/wishlists-and-baskets.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
