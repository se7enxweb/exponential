---
title: RSS feeds
audience: government, website-creator, admin
category: public-sector
order: 40
summary: How a public-sector site publishes news and notices as RSS feeds with Exponential's RSS exports in Setup > RSS - sources, formats, items, the feed address, caching - and when to read other bodies' feeds with RSS imports.
keywords: RSS, Atom, feed, RSS export, RSS import, Setup RSS, rss/feed, CacheTime, rss_cache, sources, subnodes, OPML, podcast, rssimport cronjob
type: help
---
# RSS feeds

This page is for website creators and administrators who give journalists, partner sites and feed readers a feed of
news, press releases or notices. In Exponential a feed is an **RSS export**, set up in **Setup > RSS** (`/rss/list`),
which needs the `rss/edit` policy. Everything below is documented in [RSS feeds](../../../../guides/rss-feeds.md).

## Contents

1. [Feeds a public body typically offers](#feeds-a-public-body-typically-offers)
2. [Creating an export](#creating-an-export)
3. [Checking and caching](#checking-and-caching)
4. [Reading other feeds: imports](#reading-other-feeds-imports)
5. [Pitfalls](#pitfalls)

## Feeds a public body typically offers

| Feed | Source | Note |
|---|---|---|
| News | the News folder, with subnodes, class Article | the most used |
| Press releases | the press release folder | for media |
| Notices or alerts | the Notices folder | short-lived items; readers see them until they are hidden |
| Consultations | the Consultations folder | new consultations |
| Jobs | the jobs folder, class Job position | |

## Creating an export

1. **Setup > RSS**, **New export**.
2. **The feed**: name, description, **Feed format** (RSS 2.0 is the most widely read; Atom 1.0, RSS 1.0, OPML and
   Apple Podcasts exist), **access URL** (the feed is then at `/rss/feed/<access URL>`), tick **Active**, Site URL.
3. **What the feed lists**: the number of items; **Main node only** so items with two locations appear once;
   **Add source**: **Browse** to the folder, tick **Subnodes** to include the year folders below it, choose the class,
   press **Set** and choose which attributes become the title (`title`), description (`intro`), category and enclosure.
4. **OK**. The card in the list shows the feed address and **Open feed**.

Feeds can have up to several sources (a card per source), for example News and Press releases in one combined feed.

## Checking and caching

| Check | Where |
|---|---|
| The feed opens | **Open feed** on the card |
| The card says **Needs attention** | no address, no source, or a source that no longer exists |
| A change does not show | the feed is cached for `site.ini [RSSSettings] CacheTime` seconds; wait, or clear the RSS cache: `php bin/php/ezcache.php --clear-id=rss_cache` |

For urgent notices, a short `CacheTime` for the notices feed's siteaccess, or clearing the RSS cache in your
procedure, keeps readers current. Feed readers poll on their own schedule; RSS is not instant.

Link the feeds from the pages they belong to, with a visible link ("News as RSS feed") and a `<link rel="alternate"
type="application/rss+xml">` in the page head so readers find them ([RSS feeds for your site](../../../creators/website-creators/rss-feeds-for-your-site.md)).

## Reading other feeds: imports

An **RSS import** reads another site's feed and creates an object below a destination for every new item, when the
`rssimport` cronjob runs ([Cronjobs](../../../../guides/cronjobs.md)). Useful for showing a partner agency's news or
a national body's warnings on your site.

| Before importing | Why |
|---|---|
| The source is official and stable | you republish what it says |
| The destination is a folder only for imports | imported objects are content in your tree |
| The class maps title, text and link | the import form's **3. What each item becomes** |
| Someone reviews what comes in | mistakes in the source appear on your site |

Removing an import stops reading; the objects it created stay.

## Pitfalls

| Pitfall | Avoid it |
|---|---|
| A source folder that holds restricted or hidden content | open the feed logged out and check it lists only what visitors may see; choose sources whose content is all public |
| An export with the wrong siteaccess | links in the feed point to the wrong host; check **Links point to** on the card |
| Removing an export that media rely on | readers get an error; deactivate first, tell them, then remove |

## Related pages

- [News and alerts](README.md)
- [RSS: Apple Podcasts feeds and the feed list](../../../../features/6.0/rss-podcast-and-feed-list.md)
- [Notifications and subscriptions](notifications-and-subscriptions.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
