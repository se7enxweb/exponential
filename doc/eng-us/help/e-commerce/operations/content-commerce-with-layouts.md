---
title: Content commerce with Layouts
audience: ecommerce, editor, businesses
category: e-commerce
order: 74
summary: Selling with content in Exponential - landing and campaign pages built with the Layouts editor that list products, time-limited rules for sales, editorial content next to products, and the line between layouts and the shop's own templates.
keywords: content commerce, Layouts, landing page, campaign page, product list block, collection, dynamic collection, layout rule, time condition, sale, editorial, explayouts, merchandising
type: help
---
# Content commerce with Layouts

This page is for merchants, marketers and editors who want pages that sell: a campaign page for a sale, a gift guide,
a category page with an editorial introduction. In Exponential products are content, so the Layouts editor can place
them on pages like any other content. This page shows how, and where the shop's own templates take over.

## Contents

1. [Why it works](#why-it-works)
2. [A sale landing page](#a-sale-landing-page)
3. [Patterns](#patterns)
4. [Where layouts stop](#where-layouts-stop)
5. [Checklist](#checklist)

## Why it works

The Layouts editor arranges **blocks** in **zones**; listing blocks show a **collection** of content, either picked by
hand (manual) or found by a query (dynamic); **rules** decide which page gets which layout, and can be limited in time
([Landing pages with the layouts editor](../../creators/website-creators/landing-pages-with-layouts.md#the-five-ideas)).
Products are content objects in folders, so a collection can be "children of the Sale folder" or six products picked
by hand. Clicking a product leads to its full view with the **Add to basket** button.

## A sale landing page

1. Create a landing page object, for example `/Spring-sale`.
2. In the editor, create a layout with a hero zone and a main zone.
3. Top: a hero block with the message and a button.
4. Main: a list block with a grid view type and a dynamic collection, "children of the Sale folder", limit 12.
5. Add a rich text block with the sale's terms: dates, shipping, returns.
6. In **Mappings**, a rule for the landing page with a time condition (from, to) so the layout applies only during
   the sale ([Layout rules](../../creators/website-creators/layout-rules.md)).
7. Preview, publish, and check the page logged out.

The sale price itself is not set by the layout: change the products' prices, or use discount groups for customer
groups ([Discounts](../../customers/commerce/discounts.md)). There are no coupon codes in the kernel.

## Patterns

| Pattern | Blocks and collections |
|---|---|
| Category page with an introduction | Rich text, then a list block with "children of this category folder" |
| Gift guide | Several titled list blocks with manual collections |
| New arrivals | Dynamic collection sorted by publication date |
| Bestsellers | A template block using `fetch( 'shop', 'best_sell_list', ... )` from the shop's order data (`kernel/shop/function_definition.php`) |
| "Also bought" on product pages | A template block with `fetch( 'shop', 'related_purchase', ... )` |
| Shared promotional header | A shared layout linked into many pages ([Shared header and footer](../../creators/website-creators/shared-header-and-footer.md)) |

Bestsellers and "also bought" need a template block written by a developer (a `tpl_block`), since they come from
order data rather than the content tree.

## Where layouts stop

| Layouts | Shop templates |
|---|---|
| Landing, campaign, category and front pages | Product full view with the add-to-basket form |
| Editorial content around products | Basket, customer data, confirmation, checkout |
| Time-limited arrangements | Order view, receipt, order mail |

The basket and checkout are shop views with their own templates; keep them out of layouts
([Landing pages with the layouts editor](../../creators/website-creators/landing-pages-with-layouts.md#when-to-use-layouts-and-when-templates)).

## Checklist

- [ ] Every product shown is published and has a price.
- [ ] The sale's terms are on the page, not only in the image.
- [ ] Time conditions start and end when the prices change.
- [ ] After a price change, the landing page's lists show the new prices, logged out; if not, check the caches
  ([Caching for availability](../../businesses/reliability/caching-for-availability.md)).
- [ ] The page passes the accessibility checks ([Store accessibility](store-accessibility.md)).

## Related pages

- [The layouts editor](../../layouts-editor.md)
- [Promoting your store](../../businesses/stores/promoting-your-store.md)
- [Layout types, zones and blocks](../../creators/website-creators/layout-types-zones-and-blocks.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
