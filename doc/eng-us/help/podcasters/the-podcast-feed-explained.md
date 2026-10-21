---
title: The podcast feed explained
audience: podcaster
category: podcasting-streaming
order: 6
summary: What the Apple Podcasts export of Exponential writes, element by element - the channel from the export's podcast fields, one item per episode with its enclosure, guid, date and duration - and the rules that decide which episodes appear, with the code that does it.
keywords: podcast feed, Apple Podcasts, iTunes namespace, RSS 2.0, channel, item, enclosure, guid, remote id, pubDate, itunes:duration, feed cache, CacheTime, generateITunes
type: help
---
# The podcast feed explained

This page is for producers who want to understand the feed their show is published with, before they submit it to a
directory or answer a question from one. Everything here is what `eZRSSExport::generateITunes()` and
`podcastItemList()` in `kernel/classes/ezrssexport.php` do. The setup steps are on
[Building a podcast feed](building-a-podcast-feed.md).

## Contents

1. [The format](#the-format)
2. [The channel](#the-channel)
3. [The episodes](#the-episodes)
4. [Which episodes appear](#which-episodes-appear)
5. [Addresses in the feed](#addresses-in-the-feed)
6. [When changes show](#when-changes-show)

## The format

The export format **Apple Podcasts (RSS 2.0 + iTunes)** (stored value `ITUNES`, listed in `settings/site.ini
[RSSSettings] AvailableVersionList[]`) is RSS 2.0 with three namespaces: `itunes`
(`http://www.itunes.com/dtds/podcast-1.0.dtd`), `content` and `atom`. Any podcast app that reads RSS can subscribe
to it; Apple's elements are simply extra information for apps that understand them.

## The channel

The channel is written from the export's own fields and its **Apple Podcasts** section:

| Element | From |
|---|---|
| `title`, `itunes:title` | the export's **Name** |
| `link` | the site address (see [Addresses in the feed](#addresses-in-the-feed)) |
| `description` | the export's **Description** |
| `language` | **Language**, or the site locale's HTTP code when empty |
| `itunes:image href=...` | **Artwork address** |
| `itunes:category` (with a nested subcategory) | **Category**, **Subcategory** |
| `itunes:explicit` | **Contains explicit content**, `true` or `false` |
| `itunes:author`, `itunes:subtitle`, `itunes:summary`, `copyright` | **Author**, **Subtitle**, **Summary**, **Copyright** |
| `itunes:type` | **Show type**: `episodic` or `serial` |
| `itunes:owner` with `itunes:name`, `itunes:email` | **Owner name**, **Owner email** |
| `itunes:block`, `itunes:complete`, `itunes:new-feed-url` | only when set ([Moving, pausing or ending a show](moving-pausing-or-ending-a-show.md)) |
| `atom:link rel="self"` | the feed's own address |
| `lastBuildDate`, `generator` | the time of writing, the product edition |

An element whose value is empty is left out (`eZRSSExport::element()`). Every field is described on
[Channel fields reference](channel-fields-reference.md).

## The episodes

Each episode becomes one `item`:

| Element | From |
|---|---|
| `title`, `itunes:title` | the field mapped as **Title**, or the item's name when that is empty |
| `link` | the episode's page on the site |
| `description` | the field mapped as **Description**, as text |
| `pubDate` | the item's **published** date |
| `enclosure url length type` | the field mapped as **Enclosure (media)**: its download address, its size in bytes and its MIME type, read from the stored file |
| `guid isPermaLink="false"` | the object's **remote id** |
| `itunes:duration` | worked out from the file for a constant bitrate MP3, else left out |
| `itunes:explicit` | the channel's value |
| `itunes:author` | the name of the user who created the item |
| `category` | the field mapped as **Category** |

The `guid` is the important one. It never changes while the object exists, even when the episode is renamed, moved
or republished, so apps do not show an old episode as new. Fixing an episode means editing it, never deleting and
recreating it. Details per mapping: [Episode mapping reference](episode-mapping-reference.md).

## Which episodes appear

1. The export's sources are read: each source's location, class and **Subnodes** setting
   ([How a show maps to content](how-a-show-maps-to-content.md)).
2. Items are sorted by published date, newest first, and cut at **Number of objects**.
3. A hidden item is skipped, unless the site is set to show hidden items.
4. An item whose class has no mapping for that source is skipped.
5. An item whose enclosure field is empty, or is not a media or file field, is skipped: "An episode with nothing to
   play is not an episode" (code comment).

Drafts and items waiting for a scheduled date are not published, so they are not in the feed either.

## Addresses in the feed

Links and enclosure addresses are built from the export's **Site URL**. When it is empty, the public address of the
export's siteaccess is used (`publicSiteURL()`), not the address of the administration you happen to be using. With
URL translation on, `link` is the episode's nice URL; otherwise `/content/view/full/<node id>`. The enclosure is
always `/content/download/<object id>/<attribute id>/<file name>`.

## When changes show

The feed is cached for `settings/site.ini [RSSSettings] CacheTime` seconds, 1200 (20 minutes) as shipped; 0 writes it
for every request. A new episode therefore appears in the feed at most 20 minutes after publishing, and podcast apps
then fetch it on their own schedule. An administrator can clear the feed cache at once
(`php bin/php/ezcache.php --clear-id=rss_cache --allow-root-user`, from [RSS feeds](../../guides/rss-feeds.md#7-troubleshooting)).

If writing the feed fails, the address still answers with a valid, empty feed with the show's title
(`emptyPodcast()`), and the error goes to the debug log.

## Related pages

- [Building a podcast feed](building-a-podcast-feed.md)
- [Checking your feed](checking-your-feed.md)
- [RSS: Apple Podcasts feeds, a paged feed list and safer exports](../../features/6.0/rss-podcast-and-feed-list.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
