---
title: Store search and navigation
audience: businesses, website-creator, editor
category: business-solutions
order: 180
summary: Helping customers find products in an Exponential store - categories and menus from the content tree, several locations per product, tags, the built-in search, Solr with facets through ezfind, and ratings.
keywords: product search, store navigation, categories, menus, breadcrumbs, tags, eztags, ezfind, Solr, facets, filters, site search, star rating
type: help
---
# Store search and navigation

This page is for business owners and editors who want customers to find the right product quickly. It explains the
navigation tools of an Exponential store, when the built-in search is enough and when Solr pays off, and how to keep
the catalog easy to browse as it grows.

## Contents

1. [Two ways customers find products](#two-ways-customers-find-products)
2. [Categories and menus](#categories-and-menus)
3. [One product in several places](#one-product-in-several-places)
4. [Tags as a second path](#tags-as-a-second-path)
5. [Built-in search](#built-in-search)
6. [Solr search with facets](#solr-search-with-facets)
7. [Ratings and specifications](#ratings-and-specifications)
8. [Checklist](#checklist)

## Two ways customers find products

Some customers browse: they click through categories. Others search: they type what they want. A good store serves
both, and in Exponential both come from the same content, so a product added once appears in menus and search results
without extra work.

## Categories and menus

Menus come from the content tree: each category is a folder, and the menu shows the folders below a starting point
([Menus and navigation](../../creators/website-creators/menus-and-navigation.md)). For a store:

- keep the top level to the handful of categories customers think in;
- order categories by priority, not alphabetically;
- show breadcrumbs, so customers know where they are and can go one level up;
- hide empty or seasonal categories rather than deleting them.

Which classes appear in menus is a setting (`menu.ini`), so product pages need not crowd the menu. The tree itself is
planned in [Structuring the content tree](../../creators/website-creators/structuring-the-content-tree.md).

## One product in several places

A content object can have more than one location. A product can sit in its own category and in "New arrivals" or
"Gifts under 50" without being copied, so a price change is made once. Remove the extra location when the campaign ends.

## Tags as a second path

Tags cut across categories: brand, material, occasion, color. With `eztags`, tags are managed centrally, can be
translated and can be shown as tag pages and clouds
([Tagging with eztags](../../creators/website-creators/tagging-with-eztags.md)). Agree on a small tag vocabulary
before editors start, or the tag list will grow faster than the catalog.

## Built-in search

The built-in search engine keeps its index in the database and is enough for most sites
([Site search](../../creators/website-creators/site-search.md)). It searches the searchable fields of products (on alpha
the product class marks name, product number, descriptions and specifications as searchable; the price is not), and
records search statistics, so you can see what customers looked for and did not find.

> **Tip**
> Check the search statistics every month. A search term without results is a product you do not have, or a word your
> product pages do not use yet.

## Solr search with facets

Large catalogs, and customers who want to filter, call for Solr through the `ezfind` extension
([Search with ezfind and Solr](../../creators/website-creators/search-with-ezfind-and-solr.md)). It adds:

| With Solr | Benefit for a store |
|---|---|
| Facets (filter by class, tag, date) | "Show only this brand" |
| Relevance tuning | Best matches first |
| File contents | Manuals and data sheets found by their text |
| Tag clouds | A visual way into the catalog |

`ezfind` is suggested in the root `composer.json` (`se7enxweb/ezfind`) and needs a Solr server, which your host runs
([Search backends](../../hosting/basics/search-backends.md)). Facet templates are design work.

## Ratings and specifications

- The `ezstarrating` extension adds a star rating field, and lets templates sort and fetch by rating
  ([ezstarrating](../../../features/6.0/extensions/ezstarrating.md)); on alpha the product class has one.
- A specifications table (`ezmatrix` on alpha's product class) lets customers compare products on the page.

## Checklist

- [ ] Top-level categories agreed and ordered by priority.
- [ ] Breadcrumbs on product and category pages.
- [ ] Campaign pages use extra locations, not copies.
- [ ] A short tag vocabulary is written down.
- [ ] Search finds products by name and product number; tested with typical customer words.
- [ ] Search statistics reviewed monthly.
- [ ] Solr considered once the catalog or the filtering needs grow.

## Related pages

- [Product classes and catalog](product-classes-and-catalog.md)
- [Promoting your store](promoting-your-store.md)
- [Catalog search](../libraries/catalog-search.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
