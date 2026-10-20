---
title: Products and catalog
audience: customer, editor, businesses
category: customer-success
order: 20
summary: Keeping the product catalog of an Exponential store in good shape after launch - who maintains what, the regular catalog checks, sold-out and seasonal products, and what to ask your partner when the product class needs to change.
keywords: product catalog, catalog upkeep, products, product class, sold out, seasonal products, product folders, catalog checks, product editors
type: help
---
# Products and catalog

This page is for owners and store managers who are responsible for the catalog once the store runs. It is about
upkeep: who looks after products, which checks to do regularly, and how to handle products that sell out, come back or
change. How products are modeled in Exponential is explained for decision makers in
[Product classes and catalog](../../businesses/stores/product-classes-and-catalog.md).

## A product is a page

In Exponential a product is content of a class with a price field (`ezprice` or `ezmultiprice`)
([Running a shop: overview](../../creators/website-creators/shop-overview.md)). For the team this means:

- product editors use the same editor, drafts, versions and preview as for every other page
  ([Creating and editing content](../../editing-content.md), [Publishing and versions](../../publishing-and-versions.md));
- a wrong change can be undone by going back to an earlier version;
- products can live in several places of the menu at once (one object, several locations), so "New" and "Sale"
  folders do not need copies.

## Who maintains what

| Task | Who | What they need |
|---|---|---|
| Product texts, images, options | Product editors | content create and edit in the product folders ([Roles and policies](../../../guides/roles-and-policies.md)) |
| Prices on products | Product editors or the owner | content edit; the price is a field of the product |
| VAT types, product categories, currencies | Owner | `shop/setup` (`kernel/shop/module.php`) |
| New fields in the product class | Partner or administrator | class editing in **Setup > Classes** |
| Catalog checks | Store manager | `shop/administrate` for the dashboard |

Decide whether price changes go through an approval step. If they should, your administrator can put an approval
workflow on the product folders ([Workflows](../../../guides/workflows.md)).

## Regular catalog checks

| How often | Check | Where |
|---|---|---|
| Weekly | Products without a price | The Store dashboard counts them ([Store dashboard and order statuses](../../../features/6.0/store-dashboard.md)) |
| Weekly | Sold-out items hidden or marked | The product folders |
| Weekly | Prices and VAT types of new products | **Store > Products overview** (`shop/productsoverview`) lists products with their prices |
| Monthly | Best sellers still easy to find | Dashboard ranking, home page, menus |
| Monthly | Images and files nobody uses any more | [The content checks](../../../features/6.0/content-checks.md) |
| Quarterly | Search terms without results | [Site search](../../creators/website-creators/site-search.md) |
| Yearly | Categories, tags and menus still fit the range | Content structure |

## Sold-out and seasonal products

The shop has no stock management ([What the built-in shop does](../../businesses/stores/what-the-built-in-shop-does.md)),
so "sold out" is something your team does by hand. Agree on one way and use it every time:

| Situation | A common approach | How |
|---|---|---|
| Out of stock for a few days | Leave the page, add a note in the text | Edit the product ([Creating and editing content](../../editing-content.md)) |
| Out of stock for longer | Hide the product, so it disappears from lists and search | [Hide an item](../../publishing-and-versions.md#hide-an-item) |
| Seasonal range | Hide at the end of the season, reveal next year | Hide and reveal; prices and texts stay |
| Discontinued for good | Hide, or move to an archive folder; remove only when you are sure | [Remove and restore](../../publishing-and-versions.md#remove-and-restore) |

> **Note**
> Before a product is removed or hidden, the administration lists the pages that point to it (articles that link to
> it, related products). That is the shipped setting, `content.ini [RemoveSettings] ReverseRelationCheck=warn`
> ([Removing and hiding: the check of reverse relations](../../../features/6.0/reverse-relation-check.md)).

Hiding is usually better than removing for a product that was sold: old orders keep their order lines either way,
but a hidden product keeps its address and history in case it comes back.

## When the product class must change

New fields (a second image, a size chart, a category for VAT rules) are changes to the product class. That is work
for your administrator or partner, not for editors. Ask them:

- [ ] Will existing products need to be edited to fill the new field?
- [ ] Does the product template on the site show the new field?
- [ ] Is the change tested on a copy before the live store?
- [ ] Does the change affect VAT rules, prices or options in the basket?

Changing the price field from one currency (`ezprice`) to several (`ezmultiprice`) means editing every product, so
plan it like a small project ([Prices and currencies](prices-and-currencies.md)).

## Many products at once

Moving, copying or removing a large number of products can run in the background as a content job, which works in
batches and resumes after an interruption ([Content jobs](../../../features/6.0/content-jobs.md)). Several selected
sub-items can be copied in one step ([Copy selected sub-items](../../../features/6.0/subitems-copy-selected.md)).

## Related pages

- [Adding a product](adding-a-product.md)
- [Product classes and catalog (for businesses)](../../businesses/stores/product-classes-and-catalog.md)
- [Store search and navigation](../../businesses/stores/store-search-and-navigation.md)
- [Products, prices and VAT](../../creators/website-creators/products-prices-and-vat.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
