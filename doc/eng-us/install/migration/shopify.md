---
title: Shopify and Exponential
audience: admin, developer, project-manager, sales-marketing
category: install-upgrade
order: 44
summary: Moving content between Shopify and Exponential 6 - the product CSV, the Admin GraphQL API, what Shopify does not export (blog posts and pages have no CSV, images only as URLs), the URL redirects CSV, mapping and step by step, in both directions.
keywords: Shopify, CSV, Admin GraphQL API, X-Shopify-Access-Token, products, blog posts, pages, URL redirects, migration
type: install
---
# Shopify and Exponential

[Migration hub](README.md) · [Checklist](checklist.md) · [Mapping](content-model-mapping.md) · [Tools](exponential-tools.md)

## 1. What Shopify can export

According to Shopify's help center and developer documentation:

| Source | What it gives you | Limits |
|---|---|---|
| **CSV exports** | products, customers, orders, gift card codes, discount codes, financial data; "A CSV file with UTF-8 encoding is the only file format" | product images are not in the file, only URLs pointing at the store (they work only while it stays public) |
| **Blog posts and pages** | no CSV: Shopify's own advice is to copy and paste them | read them through the Admin API instead |
| **Admin GraphQL API** | `POST https://{store}.myshopify.com/admin/api/<version>/graphql.json`, header `X-Shopify-Access-Token`; cursor pagination (`first`/`after`, `pageInfo { hasNextPage endCursor }`), at most 250 per request | at most 25,000 objects can be paginated; Shopify recommends bulk operations above that |
| **URL redirects** (Content > Menus > URL redirects) | CSV export and import with "Redirect from" and "Redirect to" columns | up to 100,000 redirects (20,000,000 on Plus) |
| Theme, settings, menus, apps | not exportable as content | rebuilt by hand |

## 2. Mapping to Exponential

| Shopify | Exponential 6 |
|---|---|
| Blog article (body HTML, tags, author, image, SEO) | `article` with `ezxmltext` body, `ezkeyword`/`eztags`, owner or `ezauthor`, image relation, `xrowmetadata` |
| Page | `article`/`folder` |
| Product (title, description HTML, images, options, variants) | a product class (kernel shop: `ezprice`/`ezmultiprice`, options), or content that links to the Shopify store |
| Collection | folder or `eztags` |
| Customers, orders | stay in Shopify or its archive (personal data) |
| Metafields | attributes |

## 3. Step by step (Shopify to Exponential)

1. Decide: content only (shop stays on Shopify, Exponential is the content site), or catalog too.
2. Export products as CSV; read blogs, articles and pages through the Admin GraphQL API (a custom app with read
   scopes, on the store); download every image by its URL **while the store is still public**.
3. Convert the HTML, import media, collections, products, articles and pages with remote ids
   (`shopify-<gid>`), using xrowextract's importer or the [script skeleton](exponential-tools.md#4-a-custom-import-script-the-skeleton).
4. Export Shopify's URL redirects and the paths of products (`/products/<handle>`), collections
   (`/collections/<handle>`), pages (`/pages/<handle>`) and articles (`/blogs/<blog>/<handle>`); redirect each to its
   new node ([checklist, 4](checklist.md#4-urls-and-redirects)).
5. Test, rehearse, cut over.

## 4. From Exponential to Shopify

Export with xrowextract ([leaving Exponential](leaving-exponential.md)). Products: Shopify's product CSV import.
Articles and pages: through the Admin GraphQL API (no CSV). Redirects: the URL redirects CSV import, from the
Exponential URL aliases.

## 5. Checklist

- [ ] Scope decided; Shopify store kept public until every image is downloaded.
- [ ] Products by CSV, articles and pages by API; counts checked.
- [ ] Remote ids; HTML converted; collections mapped.
- [ ] Redirect table for every handle path; tested.

## Sources

- URL redirects: <https://help.shopify.com/en/manual/online-store/menus-and-links/url-redirect>
- Exporting products: <https://help.shopify.com/en/manual/products/import-export/export-products>
- Duplicating a store (what is copied and what is not): <https://help.shopify.com/en/manual/shopify-admin/duplicate-store>
- GraphQL pagination: <https://shopify.dev/docs/api/usage/pagination-graphql>

This page is part of the Exponential documentation and is free documentation under the GNU Free Documentation License
1.3 or any later version ([the license of this documentation](../../README-LICENSE.md)).
