---
title: SEO basics
audience: cms-developer
category: cms-development
order: 45
summary: For CMS developers, the search engine work that belongs in the build of an Exponential site. It covers clean addresses from the tree, canonical links for content in several places, titles and descriptions from content, the xrowmetadata datatype, XML sitemaps, robots.txt, redirects after moves and migrations, language versions, and speed.
keywords: SEO, search engine optimization, URL aliases, canonical, main node, title, meta description, MetaDataArray, xrowmetadata, sitemap, bcgooglesitemaps, robots.txt, redirects, URL wildcards, hreflang, language switcher, structured data, page speed
type: help
---
# SEO basics

This page is for developers who build sites that search engines must understand. Most of it is not tricks but
structure: stable addresses, one canonical address per piece of content, a real title and description per page, a
sitemap and redirects when things move. Exponential provides the parts; your templates and settings put them together.

## Contents

1. [Addresses](#addresses)
2. [One canonical address](#one-canonical-address)
3. [Titles and descriptions](#titles-and-descriptions)
4. [Metadata from content: xrowmetadata](#metadata-from-content-xrowmetadata)
5. [Sitemaps and robots.txt](#sitemaps-and-robotstxt)
6. [Redirects](#redirects)
7. [Language versions](#language-versions)
8. [Speed and markup](#speed-and-markup)
9. [Checklist](#checklist)

## Addresses

Every node gets a readable address from its path in the tree (`/news/opening-of-the-new-hall`), lower case and with
dashes as configured in `site.ini [URLTranslator]`. A tree designed like the site's navigation gives addresses that
explain themselves; avoid deep folder chains that exist only for editors
([URL aliases and routing](url-aliases-and-routing.md), [Designing a content model](designing-a-content-model.md#step-5-draw-the-tree)).
Keep view parameters (`/(offset)/10`) to paging and known filters.

## One canonical address

An object with several locations has several addresses. Print a canonical link to the main node in the pagelayout
or full view:

```smarty
<link rel="canonical" href={$node.object.main_node.url_alias|ezurl( 'double', 'full' )}>
```

`$module_result.content_info.main_node_url_alias` has the same in the pagelayout
([Objects, nodes and locations](objects-nodes-and-locations.md#the-main-node-and-extra-locations)).

## Titles and descriptions

- The `<title>` is built in the pagelayout, usually from the path of the module result (the node's name and the
  site name) ([Module result and content info](../designers/templates/module-result-and-content-info.md)).
- `site.ini [SiteSettings] MetaDataArray[]` holds the site-wide defaults (`description`, `keywords`, `author`,
  `copyright`); replace the shipped values in your siteaccess settings.
- Per-page descriptions come from content: an intro field or a metadata attribute, printed washed in the pagelayout.

## Metadata from content: xrowmetadata

The `xrowmetadata` extension of the default distribution adds an `xrowmetadata` datatype (title, keywords,
description) that editors fill per object, and generates XML sitemaps. Add the attribute to the classes that have
pages of their own and print it in the pagelayout, falling back to the name and intro when it is empty
([Mapping content: search engine metadata](../../install/migration/content-model-mapping.md#11-search-engine-metadata)).

## Sitemaps and robots.txt

- **Sitemaps**: `bcgooglesitemaps`, part of the default distribution, generates XML sitemaps through its cronjob
  parts; `xrowmetadata` can generate them too. Use one of them, not both.
- **robots.txt**: a new installation ships one at the web root with sensible rules; add your `Disallow:` lines and your
  sitemap address ([A useful robots.txt out of the box](../../features/6.0/robots-txt.md)).

## Redirects

Renamed and moved content keeps its old addresses as history entries that still lead to it. For addresses of an old
site, add custom URL aliases with redirect or URL wildcards (`old-blog/*` to `news/{1}`) before launch, and watch the
404 list after it ([Links, URL aliases and wildcards](../../guides/urls-and-aliases.md),
[Migration checklist](../../install/migration/checklist.md)).

## Language versions

Each language has its own addresses of the same node. Link the versions of a page to each other: `[RegionalSettings]
TranslationSA[]` names the translation siteaccesses, and the `language_switcher` operator with `switchlanguage/to` builds
the links; `hreflang` alternates are written by your pagelayout from the same data
([Translations and languages](translations-and-languages.md#one-siteaccess-per-language)).

## Speed and markup

Fast pages and clean markup help both visitors and search engines: semantic headings, landmarks, images with
alternative text, width and height ([Performance](performance.md), [Accessibility](accessibility.md)). Structured data
(JSON-LD) is written by your templates; keep it in a template file of your design so it follows the content.

## Checklist

- [ ] Addresses read well; no test or "copy of" pages left in the tree.
- [ ] A canonical link on every page with content in several places.
- [ ] Titles and descriptions per page; the shipped `MetaDataArray` values replaced.
- [ ] A sitemap generated on a schedule and named in `robots.txt`.
- [ ] Redirects for every old address that had traffic or links.
- [ ] Language versions linked to each other.

## Related pages

- [Project launch checklist](project-launch-checklist.md)
- [URL operators](../designers/templates/url-operators.md)
- [The pagelayout](../designers/theme-design/the-pagelayout.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
