---
title: Magento, Adobe Commerce and Exponential
audience: admin, developer, project-manager
category: install-upgrade
order: 43
summary: Moving content between Magento Open Source or Adobe Commerce and Exponential 6 - the Data Transfer CSV export, REST and GraphQL, what is not in the export (CMS pages, blocks, URL rewrites), mapping catalog and CMS content, URL rewrites and redirects, and when to keep the shop and move only the content.
keywords: Magento, Adobe Commerce, Data Transfer, export, import, CSV, REST, GraphQL, cmsPage, URL rewrites, catalog, products, categories, CMS pages, shop, migration
type: install
---
# Magento, Adobe Commerce and Exponential

Magento Open Source and Adobe Commerce are shops first. The usual migration is **not** "the shop into Exponential":
it is moving the **content** (CMS pages, blog, guides, landing pages) into Exponential and keeping the shop, or moving
a small catalog into Exponential's kernel shop. Decide which before you start.

[Migration hub](README.md) · [Checklist](checklist.md) · [Mapping](content-model-mapping.md) · [Tools](exponential-tools.md)

## Contents

- [1. What Magento can export](#1-what-magento-can-export)
- [2. Mapping to Exponential](#2-mapping-to-exponential)
- [3. Step by step](#3-step-by-step)
- [4. Addresses and redirects](#4-addresses-and-redirects)
- [5. From Exponential to Magento](#5-from-exponential-to-magento)
- [6. Risks and checklist](#6-risks-and-checklist)
- [Sources](#sources)

## 1. What Magento can export

According to Adobe's documentation:

| Tool | What it gives you | Notes |
|---|---|---|
| **System > Data Transfer > Export** | CSV of Products, Advanced Pricing, Customers Main File, Customer Addresses, Customer Finances, Stock Sources | **CMS pages, CMS blocks and URL rewrites are not export entities** |
| **REST API** | e.g. `GET /rest/<store_code>/V1/...` with `searchCriteria[pageSize]` and `searchCriteria[currentPage]` | authentication by integration (OAuth), admin or customer bearer tokens |
| **GraphQL** | `cmsPage(identifier: ...)` returns one page (with `url_key`, `title`, `content`, `meta_*` fields) | it does not list pages; you need the identifiers |
| **Marketing > SEO & Search > URL Rewrites** | the list of rewrites (product, category, CMS page, custom; 301 or 302) | a CSV export of rewrites in core was not verified |
| The database | everything, including CMS pages (`cms_page`), blocks (`cms_block`) and rewrites (`url_rewrite`) | the practical source for CMS content |

## 2. Mapping to Exponential

| Magento | Exponential 6 |
|---|---|
| CMS page | `article`/`folder` objects; `content` HTML converted to XmlText; Page Builder markup needs its own conversion |
| CMS block | a layout block or an object included by templates |
| Category (with description) | folder with description |
| Product (content view: name, description, images, attributes) | a product class (`ezprice` or `ezmultiprice` for the kernel shop), or a content page that links to the shop |
| Product images (`pub/media/catalog/product`) | image objects |
| `meta_title`, `meta_description` | `xrowmetadata` |
| Customers, orders | stay in the shop or its archive; customers are personal data, move only with a reason and a legal basis |
| URL rewrites | redirecting URL aliases or web-server rules |

## 3. Step by step

1. **Decide the scope**: content only (shop stays), or content and catalog (Exponential's kernel shop or an external
   shop linked in).
2. **Export** CMS pages and blocks from the database (or GraphQL per identifier), the catalog from Data Transfer,
   the URL rewrites from `url_rewrite`, the media from `pub/media`.
3. **Convert** HTML (Page Builder content included) to the simplified input; widgets and directives (`{{widget ...}}`,
   `{{media url=...}}`) become embeds, links or are rendered.
4. **Import** media, categories, products, pages, with remote ids (`magento-cms-<id>`, `magento-product-<sku>`).
5. **Link** shop and content when both stay: product links from content pages go to the shop's addresses.
6. **Redirects** for every rewrite that leaves Magento; test; rehearse; cut over.

## 4. Addresses and redirects

Magento's `url_rewrite` table holds every public path (`request_path`) and what it points to (`target_path`,
`entity_type`, `redirect_type`). For each row that moves to Exponential, create a redirecting custom URL alias on the
new node; for rows that stay in the shop, nothing changes. GET parameters are removed by Magento's rewrites
(per Adobe's documentation), so they need no rule.

## 5. From Exponential to Magento

Export content with xrowextract ([leaving Exponential](leaving-exponential.md)). Products go through **System > Data Transfer > Import** as CSV (behaviors Add/Update, Replace, Delete); CMS pages and blocks through the REST API or by hand, since
they are not import entities. Redirects are entered as URL rewrites (custom type, 301).

## 6. Risks and checklist

| Risk | Counter-measure |
|---|---|
| CMS content not in Data Transfer | read it from the database |
| Widgets and directives in HTML | list them and convert each kind |
| Customer data moved without need | keep it in the shop |

- [ ] Scope decided: content, or content and catalog.
- [ ] CMS pages, blocks, catalog, media and URL rewrites exported.
- [ ] Widgets and directives converted; remote ids set.
- [ ] Redirects for every moved rewrite; links between content and shop checked.

## Sources

- Data export: <https://experienceleague.adobe.com/en/docs/commerce-admin/systems/data-transfer/data-export>
- Web API authentication: <https://developer.adobe.com/commerce/webapi/get-started/authentication/>
- GraphQL `cmsPage`: <https://developer.adobe.com/commerce/webapi/graphql/schema/store/queries/cms-page>

This page is part of the Exponential documentation and is free documentation under the GNU Free Documentation License
1.3 or any later version ([the license of this documentation](../../README-LICENSE.md)).
