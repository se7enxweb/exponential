---
title: Joomla and Exponential
audience: admin, developer, project-manager
category: install-upgrade
order: 42
summary: Moving a Joomla 4 or 5 site to Exponential 6 and back - the Web Services API, the database, mapping articles, categories, tags, menus, custom fields, media and users, SEF URLs and redirects, and the way back with Joomla's Redirects bulk import.
keywords: Joomla, Joomla 4, Joomla 5, Web Services API, api/index.php/v1, X-Joomla-Token, articles, categories, tags, menu items, custom fields, SEF URLs, redirects, migration
type: install
---
# Joomla and Exponential

[Migration hub](README.md) · [Checklist](checklist.md) · [Mapping](content-model-mapping.md) · [Tools](exponential-tools.md)

## Contents

- [1. What Joomla can export](#1-what-joomla-can-export)
- [2. Mapping Joomla to Exponential](#2-mapping-joomla-to-exponential)
- [3. From Joomla to Exponential, step by step](#3-from-joomla-to-exponential-step-by-step)
- [4. Addresses and redirects](#4-addresses-and-redirects)
- [5. From Exponential to Joomla](#5-from-exponential-to-joomla)
- [6. Risks and checklist](#6-risks-and-checklist)
- [Sources](#sources)

## 1. What Joomla can export

| Source | What it gives you | Notes |
|---|---|---|
| **Web Services API** (Joomla 4 and 5) | JSON at `/api/index.php/v1/...`, for example `/api/index.php/v1/content/articles`, media type `application/vnd.api+json`, methods GET, POST, PATCH, DELETE | a token in the `X-Joomla-Token` header (plugins "API Authentication - Web Services Joomla Token" and "User - Joomla API Token"); the user needs Super User, or `core.login.api` plus `core.login.site` |
| The database | articles (`#__content`), categories (`#__categories`), tags, menu items (`#__menu`), custom fields and their values, users | the table prefix is your installation's |
| `images/` and `media/` directories | the media files | copy them |

A core "export the site's content" function was not found in Joomla's documentation (not verified either way). The
pagination parameters of the Web Services API were not verified; read the `links` of each answer.

## 2. Mapping Joomla to Exponential

| Joomla | Exponential 6 |
|---|---|
| Article | `article` object |
| `introtext` / `fulltext` (split by the read-more line) | `intro` and `body` (`ezxmltext`), HTML converted |
| Category (nested) | folders; the article's category is its main location |
| Tags | `eztags` or `ezkeyword` |
| Custom fields (`com_fields`) | attributes of the class |
| Intro and full images (`images` JSON column) | `ezimage` attributes or relations to image objects |
| Menu items | the content tree and sort order; menu items that point to components need a decision each |
| Modules (side blocks) | layout blocks (Exponential Layouts) or template parts |
| Users and user groups, access levels | user objects in groups; sections and roles for access levels. Joomla's password hashes: test whether they are standard PHP `password_hash()` values (they then work as type 7, [mapping, 6](content-model-mapping.md#6-users-groups-roles-and-passwords)); otherwise plan resets |
| `metadesc`, `metakey`, page title | `xrowmetadata` |
| Multilingual associations | translations of the same object (link the associated articles before the import) |
| Extensions' content (K2, forms, shops) | each its own decision |

## 3. From Joomla to Exponential, step by step

1. **Inventory**: articles per category and language, custom fields, menus, modules, installed extensions that hold
   content, the SEF settings (Global Configuration > SEO).
2. **Create the classes** and the folder tree.
3. **Export**: through the Web Services API (an API user and token on a copy of the site), or SQL against a copy of
   the database; write JSON rows with remote ids `joomla-article-<id>`, the category path, the converted HTML and the
   old SEF address. Copy `images/`.
4. **Import** media, then categories as folders, then articles, with xrowextract's importer or the
   [script skeleton](exponential-tools.md#4-a-custom-import-script-the-skeleton).
5. **Redirects** ([4](#4-addresses-and-redirects)), SEO fields, search, test, rehearse, cut over.

## 4. Addresses and redirects

Joomla's addresses depend on the SEF settings and the menu: `/index.php/blog/12-my-article`,
`/blog/my-article` with URL rewriting, `/index.php?option=com_content&view=article&id=12`. Build the redirect table
from the live site (crawl it, and take the server log), not from the database alone, because the menu decides the
path. Query-string forms go to the web server; path forms become redirecting custom URL aliases. Joomla's own
**System > Redirects** list can be exported from its table and added to the same redirect table.

## 5. From Exponential to Joomla

Export with xrowextract ([leaving Exponential](leaving-exponential.md)), convert XmlText to HTML, and create the
articles through the Web Services API (`POST /api/index.php/v1/content/articles`) or Joomla's own import tooling of
your choice. Joomla's **System > Redirects** has a **Bulk Import** (default separator `|`; the new URL must be a full
URL), which takes the redirect table from the Exponential URL aliases.

## 6. Risks and checklist

| Risk | Counter-measure |
|---|---|
| Menu-driven addresses | crawl the live site for the address list |
| Read-more split | map `introtext`/`fulltext` explicitly |
| Third-party extensions | inventory and decide each one early |

- [ ] Articles, categories, fields, menus, extensions inventoried.
- [ ] API token or database copy ready; export complete; images copied.
- [ ] Import with remote ids; categories as folders; translations linked.
- [ ] Redirect table from the crawl; tested; SEO fields mapped.

## Sources

- Joomla Web Services: <https://manual.joomla.org/docs/general-concepts/webservices/>
- Redirects (Bulk Import): <https://docs.joomla.org/Help5.x:Redirects:_Links>

This page is part of the Exponential documentation and is free documentation under the GNU Free Documentation License
1.3 or any later version ([the license of this documentation](../../README-LICENSE.md)).
