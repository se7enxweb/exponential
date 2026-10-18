---
title: WordPress and Exponential
audience: admin, developer, project-manager
category: install-upgrade
order: 40
summary: Moving a WordPress site to Exponential 6 and back - the WXR export, WP-CLI and the REST API, mapping posts, pages, media, categories, tags, authors, comments and SEO fields, HTML to XmlText, permalinks and redirects, cut-over and the way back through the WordPress Importer.
keywords: WordPress, WXR, WordPress eXtended RSS, wp export, WP-CLI, wp-json, REST API, Application Passwords, posts, pages, attachments, categories, tags, permalinks, redirects, WordPress Importer, migration
type: install
---
# WordPress and Exponential

[Migration hub](README.md) · [Checklist](checklist.md) · [Mapping](content-model-mapping.md) · [Tools](exponential-tools.md)

## Contents

- [1. What WordPress can export](#1-what-wordpress-can-export)
- [2. Mapping WordPress to Exponential](#2-mapping-wordpress-to-exponential)
- [3. From WordPress to Exponential, step by step](#3-from-wordpress-to-exponential-step-by-step)
- [4. Addresses and redirects](#4-addresses-and-redirects)
- [5. From Exponential to WordPress](#5-from-exponential-to-wordpress)
- [6. Risks and checklist](#6-risks-and-checklist)
- [Sources](#sources)

## 1. What WordPress can export

According to WordPress's own documentation:

| Tool | What it gives you | Limits |
|---|---|---|
| **Tools > Export** | a "WordPress eXtended RSS or WXR" XML file with posts, pages, custom post types, comments, custom fields, categories, tags, custom taxonomies and users; filters by status, author and date range | no site configuration, no files |
| **WP-CLI `wp export`** | the same WXR, split into files (`--dir`, `--max_file_size` in MB, default 15, `-1` for no limit) | the WP-CLI documentation: "WXR files do not include site configuration (options) or the attachment files themselves" |
| **REST API** (`/wp-json/wp/v2/...`) | JSON of posts, pages, media, categories, tags, users and more; `per_page` 1 to 100, totals in the `X-WP-Total` and `X-WP-TotalPages` headers | authentication with Application Passwords (since WordPress 5.6) or a cookie with a nonce |
| The `wp-content/uploads` directory | the media files themselves | copy it from the server |

Use WXR as the frozen source of the import (one file, all content, repeatable) and the uploads directory for the
files. Use the REST API when you cannot get a WXR file, or for a delta of the last days before the cut-over.

What WXR holds per item (the format's elements): the title, the permalink (`<link>`), `<wp:post_id>`,
`<wp:post_name>` (the slug), `<wp:post_type>`, `<wp:status>`, `<wp:post_parent>`, the dates, `<content:encoded>` (the
body HTML, block editor comments included), `<excerpt:encoded>`, the categories and tags, `<wp:postmeta>` (custom
fields, also those of SEO plugins) and `<wp:comment>`. Attachments are items of type `attachment` with their URL.

## 2. Mapping WordPress to Exponential

| WordPress | Exponential 6 |
|---|---|
| Post | `article` (or `blog_post`) object below a blog folder |
| Page (with `post_parent`) | `folder`/`article` objects whose tree follows the page hierarchy |
| Custom post type | a class per type, attributes per custom field |
| `post_title` | `title` (`ezstring`) |
| `post_content` (HTML, block markup) | `body` (`ezxmltext`), converted: strip the `<!-- wp:... -->` block comments, reduce to the simplified input, images to embeds |
| `post_excerpt` | `intro` (`ezxmltext`) or `eztext` |
| Featured image (`_thumbnail_id` post meta) | an `ezimage` attribute, or a relation to the image object |
| Attachment | `image` or `file` object below a media folder |
| Category (hierarchical) | folders (main category = main location) or `eztags` |
| Tag | `ezkeyword` or `eztags` |
| Author | a user object; the object's owner, or an `ezauthor` attribute |
| Comment | child objects of a comment class, or archived |
| Custom fields (ACF and others) | attributes; repeating groups become child objects or `ezmatrix` |
| Shortcodes | converted to custom tags or embeds, or rendered out to HTML before the import |
| SEO plugin title and description (post meta) | `xrowmetadata` attribute |
| Status `draft`, `private`, `future` | unpublished drafts, a members section, or a publish date |
| Menus | the content tree and its sort order; menus that point elsewhere become links |
| Users and roles | user objects in groups; roles rebuilt. WordPress stores passwords in its own hash formats, which Exponential does not read: plan resets ([mapping, 6](content-model-mapping.md#6-users-groups-roles-and-passwords)) |

## 3. From WordPress to Exponential, step by step

1. **Inventory**: post types and counts (`wp post list --post_type=<type> --format=count`), plugins that store content
   (page builders, custom fields, SEO, forms), the uploads size, the permalink structure (Settings > Permalinks).
2. **Create the classes** in Exponential and the folder tree (blog, pages, media).
3. **Export**: `wp export --dir=<dir> --max_file_size=-1` (or Tools > Export), and copy `wp-content/uploads`.
4. **Convert**: a script reads the WXR with an XML parser and writes one JSON/CSV row per item with the remote id
   (`wp-<post_id>`), parent, title, converted body, dates, author, categories, tags, SEO fields and the old permalink.
   Page builders that store layout in post meta or shortcodes need their own conversion, or a rendered-HTML fallback
   (fetch the published page and take the content area).
5. **Import media first** (attachments as image/file objects with `wp-<post_id>`; the uploads files by their path),
   then pages in parent order, then posts, then comments. Use xrowextract's importer or the
   [script skeleton](exponential-tools.md#4-a-custom-import-script-the-skeleton).
6. **Rewrite inner links**: links to `/wp-content/uploads/...` and to other posts become embeds or links to the new
   nodes (resolve by remote id in a second pass).
7. **Redirects** ([4](#4-addresses-and-redirects)), **SEO** fields, **search** index, **sitemap**.
8. **Delta**: before the cut-over, export the items changed since the main export (WXR date filter or the REST API
   with `modified_after`) and run the import again; remote ids make it an update.
9. **Cut over** and watch the 404 list ([checklist, 8](checklist.md#8-cut-over)).

## 4. Addresses and redirects

WordPress addresses follow the permalink structure (`/%year%/%monthnum%/%postname%/`, `/%category%/%postname%/`,
`/?p=123` on the default "plain" setting), plus category, tag, author, date and feed archives.

| Old address | In Exponential |
|---|---|
| post and page permalinks | a redirecting custom URL alias on the node, generated from WXR `<link>` |
| `/category/<slug>/`, `/tag/<slug>/` | aliases to the folder or tag view |
| `/?p=123`, `/?page_id=45` | the web server, from the redirect table (query strings never reach URL aliases) |
| `/feed/`, `/category/<slug>/feed/` | an RSS export of Exponential, by alias |
| `/wp-content/uploads/...` | redirect to the new media objects, or keep the files at that path |
| `/wp-admin/`, `/wp-login.php` | 410, or a redirect to the new login |

## 5. From Exponential to WordPress

1. Export with xrowextract (CSV/JSON per class, with URL alias, remote id and every attribute) and copy the storage
   directory ([leaving Exponential](leaving-exponential.md)).
2. Convert XmlText to HTML (render it with the site's templates, or transform the XML) and write a WXR file: one
   `<item>` per object with `<wp:post_type>`, `<wp:post_name>` from the alias, categories from the tree.
3. Import with the **WordPress Importer**: it imports posts, pages, custom post types, comments, meta, terms and
   authors, asks you to map each author to an existing or new user, and can "Download and import file attachments"
   (so keep the old media URLs reachable during the import).
4. Redirect the Exponential addresses to the WordPress permalinks (a redirect plugin or the web server).
5. Users: WordPress cannot verify Exponential's hashes; plan resets.

## 6. Risks and checklist

| Risk | Counter-measure |
|---|---|
| Page-builder content | test the conversion on the most complex pages first, or import rendered HTML |
| Shortcodes left in text | list them (`grep -o '\[[a-z_]*'`), convert or render each |
| Lost featured images | map `_thumbnail_id` explicitly |
| Permalinks with query strings | web server redirects |
| Users cannot sign in | resets announced in advance |

- [ ] WXR export and uploads copy frozen; counts per post type noted.
- [ ] Classes and tree created; mapping table written.
- [ ] Conversion tested on the 20 hardest items; shortcodes handled.
- [ ] Media, pages, posts, comments imported with `wp-<id>` remote ids; inner links rewritten.
- [ ] Redirect table generated and tested; feeds and uploads handled.
- [ ] SEO fields, sitemap, analytics; delta import before cut-over.

## Sources

- WP-CLI `wp export`: <https://developer.wordpress.org/cli/commands/export/>
- REST API pagination: <https://developer.wordpress.org/rest-api/using-the-rest-api/pagination/>
- Tools > Export screen: <https://wordpress.org/documentation/article/tools-export-screen/>

This page is part of the Exponential documentation and is free documentation under the GNU Free Documentation License
1.3 or any later version ([the license of this documentation](../../README-LICENSE.md)).
