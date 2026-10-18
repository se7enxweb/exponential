---
title: Wix, Squarespace, GoDaddy and Exponential
audience: admin, developer, project-manager, sales-marketing
category: install-upgrade
order: 45
summary: Moving a site from the hosted site builders Wix, Squarespace and GoDaddy Website Builder to Exponential 6, and from Exponential to them - what each builder can export according to its own help center, what has to be read off the live site, URL redirects on each side, and a crawl-based plan.
keywords: Wix, Squarespace, GoDaddy, Websites + Marketing, site builder, export, WordPress XML, CSV, CMS collections, URL Redirect Manager, URL mappings, crawl, migration
type: install
---
# Wix, Squarespace, GoDaddy and Exponential

Hosted site builders keep a site on their own platform. What you can take with you is limited, and differs a lot
between them; plan for reading part of the content off the live site.

[Migration hub](README.md) · [Checklist](checklist.md) · [Static HTML sites](static-html.md) (the crawl method) ·
[Tools](exponential-tools.md)

## Contents

- [1. What each builder can export](#1-what-each-builder-can-export)
- [2. The crawl method for what cannot be exported](#2-the-crawl-method-for-what-cannot-be-exported)
- [3. Mapping and import](#3-mapping-and-import)
- [4. Addresses and redirects](#4-addresses-and-redirects)
- [5. From Exponential to a site builder](#5-from-exponential-to-a-site-builder)
- [6. Checklist](#6-checklist)
- [Sources](#sources)

## 1. What each builder can export

### Wix

According to Wix's help center:

- **No site export.** A Wix site "needs to be hosted and operated on Wix's servers" because of its proprietary
  technology.
- **Blog**: "Currently, it is not possible to export blog posts to other platforms" (a feature request on Wix's
  side).
- **CMS collections**: *More Actions > Export to CSV* (all fields, the filtered items, or the structure only; UTF-8;
  times in UTC+0).
- **REST APIs** exist for some data, for example querying blog posts (`POST https://www.wixapis.com/v3/posts/query`,
  `cursorPaging` up to 100, `fieldsets` such as `RICH_CONTENT`, `URL`, `SEO`). The body comes as Wix's rich content
  node structure, not as HTML, and needs a converter of your own.
- Store products can be exported (Wix has an article on it; not opened for this page).

### Squarespace

According to Squarespace's help center, *Settings > Import/Export* exports to a **WordPress XML** file with the layout
pages, **one** blog page with its posts (up to 1000 comments per post), and text, image and gallery content. **Not
exported**: albums, covers, indexes, calendars, portfolios, store pages; page-specific headers, footers and sidebars;
further blog pages; dropdowns; audio, video and product blocks; drafts; style settings and custom CSS. Products have
a separate CSV export (up to 10,000; without additional info, reviews, featured images, alt text or variant images).

The WordPress XML is WXR: convert it as in the [WordPress guide](wordpress.md#3-from-wordpress-to-exponential-step-by-step).

### GoDaddy Website Builder (Websites + Marketing)

No official site or content export was found in GoDaddy's help center. Its built-in backup "only backs up your site's
sections, text and settings" and does not include blogs, products, appointments or images; the article describes
restoring a backup, not downloading one. Online-store products have a CSV export (help article exists; not opened).
An API and a redirect import were not verified. Plan to read the site off the live pages
([2](#2-the-crawl-method-for-what-cannot-be-exported)).

## 2. The crawl method for what cannot be exported

1. **List every address**: the builder's sitemap (`/sitemap.xml` on most of them), plus a crawl, plus the analytics
   top pages.
2. **Fetch each page** with a crawler you control, politely (rate-limited), and save the HTML.
3. **Extract the content area** per page type with CSS selectors: title, body, images, dates, author. Builders wrap
   content in generated markup; write one extractor per page template and test it on a sample.
4. **Download the images** in their largest size, with their alternative text.
5. **Write JSON rows** with the old address as remote id (`page-<hash of address>`), then import as for any source
   ([tools](exponential-tools.md#4-a-custom-import-script-the-skeleton)).

Check the builder's terms of service before crawling your own site in bulk, and do it while you still have the
account.

## 3. Mapping and import

| Builder content | Exponential 6 |
|---|---|
| Pages | `folder`/`article` objects in a tree that follows the site's navigation |
| Blog posts | `article` objects below a blog folder; tags to `ezkeyword` or `eztags` |
| CMS collection items (Wix CSV) | a class per collection; xrowextract's importer with `--map` reads the CSV |
| Galleries | a gallery folder with image objects |
| Products | the kernel shop's product class, or content that links to an external shop |
| Forms | information collection; past submissions archived |
| SEO title and description | `xrowmetadata` |

The design is never exported: plan the templates of the new site from the start.

## 4. Addresses and redirects

- Build the redirect table from the address list of [2](#2-the-crawl-method-for-what-cannot-be-exported).
- In Exponential: redirecting custom URL aliases, wildcards for regular patterns, the web server for query strings
  ([checklist, 4](checklist.md#4-urls-and-redirects)).
- Keep the builder site online until DNS has switched; afterwards only your redirects carry the old links.

## 5. From Exponential to a site builder

| Target | Way in | Redirects |
|---|---|---|
| Wix | CMS collections can import CSV (from xrowextract's export); pages and blog posts by hand or through the APIs | **SEO > URL Redirect Manager > Import redirects**: a two-column CSV, at most 500 redirects per file |
| Squarespace | its own import options (check them for your plan); otherwise by hand | **URL mappings**: lines of the form `/old -> /new 301`, a `[name]` wildcard, up to 400 KB (about 2,500 lines) |
| GoDaddy | by hand (no import was verified) | not verified |

Export with xrowextract ([leaving Exponential](leaving-exponential.md)) and keep the storage files for the images.

## 6. Checklist

- [ ] What the builder can export is exported (CSV, WordPress XML); everything else is on the crawl list.
- [ ] Address list complete (sitemap, crawl, analytics).
- [ ] Extractors written per page template and tested; images downloaded in full size with alt text.
- [ ] Imported with remote ids; templates built; redirects tested.
- [ ] Builder account kept until after the cut-over.

## Sources

- Wix, exporting a site: <https://support.wix.com/en/article/exporting-or-embedding-your-wix-site-elsewhere>
- Wix, exporting blog posts: <https://support.wix.com/en/article/wix-blog-request-exporting-blog-posts-to-other-platforms>
- Wix, query posts API: <https://dev.wix.com/docs/rest/business-solutions/blog/posts-stats/query-posts>
- Squarespace, exporting your site: <https://support.squarespace.com/hc/en-us/articles/206566687-Exporting-your-site>
- Squarespace, URL mappings: <https://support.squarespace.com/hc/en-us/articles/205815308-URL-mappings>
- Squarespace, export products: <https://support.squarespace.com/hc/en-us/articles/360000694948-Export-products-to-a-csv>
- GoDaddy, back up my website: <https://www.godaddy.com/help/back-up-my-website-32228>

This page is part of the Exponential documentation and is free documentation under the GNU Free Documentation License
1.3 or any later version ([the license of this documentation](../../README-LICENSE.md)).
