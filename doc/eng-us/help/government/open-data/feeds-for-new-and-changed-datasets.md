---
title: Feeds for new and changed datasets
audience: government, admin, editor
category: public-sector
order: 100
summary: Set up an RSS 2.0 or Atom feed that lists the newest datasets of the catalog in Setup > RSS, choose which fields become title and description, and check what re-users receive.
keywords: RSS, Atom, feed, new datasets, rss export, Setup RSS, rss/feed, subscription, CacheTime, open data, notifications
type: help
---
# Feeds for new and changed datasets

This page is for administrators who give re-users and journalists a feed of new datasets. Exponential publishes RSS
1.0, RSS 2.0, Atom, OPML and Apple Podcasts feeds from **Setup > RSS** (`/rss/list`); each export lists the newest
objects below its sources. The facts and labels come from the [RSS feeds guide](../../../guides/rss-feeds.md).

## Contents

1. [What the feed contains](#what-the-feed-contains)
2. [Create the export](#create-the-export)
3. [Check the feed](#check-the-feed)
4. [Changed datasets](#changed-datasets)
5. [Other ways to tell people](#other-ways-to-tell-people)
6. [Problems](#problems)

## What the feed contains

An export lists the newest objects of the chosen class below each source location, up to the number of items you
set, with the attributes you chose as title, description, category and enclosure. Its address is the public site
of the export's siteaccess followed by `/rss/feed/<address>`.

## Create the export

You need the policy `rss/edit`. The editor siteaccess does not offer the RSS module; use the administration.

1. Open **Setup > RSS** and press **New export**.
2. **The feed**: name it ("New datasets"), write a description, choose **RSS 2.0** or **Atom 1.0**, and give the
   access URL, for example `datasets`. Tick **Active**.
3. **What the feed lists**: set the number of items, then in the source card press **Browse** and choose the catalog
   folder. Tick **Subnodes** if datasets sit in theme folders below it. Choose the class `dataset` and press
   **Set**; map **Title** to the title and **Description** to the description; map **Category** to the theme if you
   have one.
4. Press **OK**. The card now shows **Active**, the feed address and the source.

For a feed of new files instead of new dataset pages, add a second source with the class of your distributions.

## Check the feed

1. On the export card press **Open feed**; the browser shows the XML.
2. Check each item: title, link to the dataset page, description, date.
3. Validate it in a feed reader you use, signed out, so you see what the public sees.

Feeds are cached for `site.ini [RSSSettings] CacheTime` seconds; the card's "Last written" says when the cached copy
was made. A new dataset appears after that time, or at once after `php bin/php/ezcache.php --clear-id=rss_cache`.

## Changed datasets

An RSS export lists the newest objects below its sources; it is a "what is new" feed. To tell
people that an existing dataset changed:

- add a short "Change notice" object below the dataset (for example a dated article) when the data changes, so the
  feed lists it; or
- publish a separate news item for important updates.

Exponential does not produce a change log of the data inside a file; it records versions of the page and its files
([Content history](../../../guides/content-history.md)).

## Other ways to tell people

| Way | Tool |
|---|---|
| E-mail when a part of the site changes | Notifications: users follow a part of the site ([Notifications](../../../guides/notifications-administrator.md)) |
| A mailing list for data announcements | The `cjw_newsletter` extension ([Newsletters with cjw_newsletter](../../creators/website-creators/newsletters-with-cjw-newsletter.md)) |
| Machines polling for changes | The REST API with a child list sorted by modification ([REST API v1 and v2](../../../guides/rest-api-v1-and-v2.md)) |

## Problems

| Problem | Fix |
|---|---|
| A reader gets an error | The export is Inactive or has no address: edit, set them, tick Active |
| The feed is empty | The source location is gone ("missing"), or the class is wrong |
| A new dataset is missing | Wait for CacheTime or clear the RSS cache |

## Related pages

- [RSS feeds](../../../guides/rss-feeds.md)
- [RSS feeds for your site](../../creators/website-creators/rss-feeds-for-your-site.md)
- [RSS: Apple Podcasts feeds, a paged feed list and safer exports](../../../features/6.0/rss-podcast-and-feed-list.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
