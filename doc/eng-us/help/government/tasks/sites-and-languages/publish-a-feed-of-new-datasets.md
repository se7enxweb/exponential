---
title: Publish a feed of new datasets
audience: government, admin
category: public-sector
order: 240
summary: Task for administrators: create an RSS 2.0 or Atom export of the data catalog in Setup > RSS, map the dataset fields, and link the feed from the catalog page.
keywords: RSS, Atom, feed, new datasets, Setup RSS, rss/feed, export, catalog, open data
type: help
---
# Publish a feed of new datasets

This task is for administrators with the policy `rss/edit`. Background:
[Feeds for new and changed datasets](../../open-data/feeds-for-new-and-changed-datasets.md) and
[RSS feeds](../../../../guides/rss-feeds.md).

## Steps

1. **Setup > RSS** (`/rss/list`), **New export**.
2. **The feed**: name "New datasets", a description, format **RSS 2.0** or **Atom 1.0**, access URL `datasets`, tick
   **Active**.
3. **What the feed lists**: number of items; in the source card **Browse** to the catalog folder, tick **Subnodes** if
   datasets are in theme folders, choose the class `dataset`, **Set**, map Title and Description.
4. **OK**.
5. Link the feed on the catalog page: a visible link and `<link rel="alternate" type="application/rss+xml"
   href="/rss/feed/datasets">` in the page head.

## Check

On the export card, **Open feed** shows the XML with the newest datasets; a feed reader signed out shows the same.

## Problems

| Problem | Fix |
|---|---|
| The card says **Needs attention** | no address, no source, or a missing source location |
| A new dataset is missing | feeds are cached `[RSSSettings] CacheTime` seconds; `php bin/php/ezcache.php --clear-id=rss_cache` |

## Related pages

- [RSS feeds for your site](../../../creators/website-creators/rss-feeds-for-your-site.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
