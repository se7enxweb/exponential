---
title: Checking your feed
audience: podcaster
category: podcasting-streaming
order: 21
summary: How to check a podcast feed of Exponential yourself - in the browser, in the RSS list, and on the command line with curl - covering the headers, the channel, every episode's enclosure, Range requests, caching and the most frequent mistakes.
keywords: check feed, validate feed, curl, enclosure, Range, Content-Type, application/rss+xml, Last-Modified, 304, cache, rss_cache, feed address, test, signed out
type: help
---
# Checking your feed

This page is for producers and administrators who want to be sure a podcast feed is right before a directory or a
listener finds out it is not. Run these checks after building the feed, after changing an export, and after any
change to the site's host name, HTTPS or proxy.

## Contents

1. [In the RSS list](#in-the-rss-list)
2. [In the browser](#in-the-browser)
3. [On the command line](#on-the-command-line)
4. [The episodes](#the-episodes)
5. [When a change does not show](#when-a-change-does-not-show)
6. [Checklist](#checklist)

## In the RSS list

**Setup > RSS** shows a card per export ([RSS feeds](../../guides/rss-feeds.md#3-an-export-card)). Check:

- **Active**, and the format *Apple Podcasts (RSS 2.0 + iTunes)*;
- **Feed address**: the public host, `https://`, then `/rss/feed/<access URL>`;
- **Sources**: the show folder, the episode class, no "(missing)";
- no **Needs attention** marker. It lists a missing address, no source, or a source that no longer exists.

## In the browser

Open the feed address in a private window, so you see it as a visitor does. Most browsers show the XML or offer to
download it; use "view source" if needed. Look for:

- `<rss version="2.0" ...>` with the `itunes` namespace;
- the channel's `title`, `description`, `itunes:image href="..."`, `itunes:category`, `itunes:owner`;
- one `<item>` per episode, newest first;
- in each item an `<enclosure url="..." length="..." type="..."/>`.

## On the command line

The feed is served with `Content-Type: application/rss+xml` and a `Last-Modified` header; a request with a matching
`If-Modified-Since` gets `304 Not Modified` (`kernel/private/classes/views/rss/feed.php`).

```bash
# headers of the feed
curl -sI https://www.example.com/rss/feed/example-show

# the first lines of the feed
curl -s https://www.example.com/rss/feed/example-show | head -40

# headers of an episode file: size, type, range support
curl -sI "https://www.example.com/content/download/123/456/example-show-042.mp3"

# a range request, as players and apps make it: expect 206 Partial Content
curl -s -o /dev/null -w '%{http_code}\n' -H 'Range: bytes=1024-2047' \
  "https://www.example.com/content/download/123/456/example-show-042.mp3"
```

Replace the host, access URL and download address with your own; copy the download address from the feed's
`enclosure url`. The file headers come from `eZFile::downloadHeaders()` (`lib/ezfile/classes/ezfile.php`):
`Content-Length`, `Accept-Ranges: bytes`, `Content-Disposition` and `Content-Type`.

## The episodes

For each new episode compare the feed with the file:

| In the feed | Must match |
|---|---|
| `enclosure length` | the `Content-Length` of a full request for the file |
| `enclosure type` | an audio or video type (`audio/mpeg`, `video/mp4`), never `application/octet-stream` |
| `enclosure url` | opens and plays signed out |
| `guid` | the same value as last time for an existing episode |
| `itunes:duration` | present for a CBR MP3; may be missing otherwise, which is allowed |
| `link` | opens the episode page on the public site |

## When a change does not show

The feed is cached for `settings/site.ini [RSSSettings] CacheTime` seconds, 1200 as shipped. The RSS card's **Last
written** says when the cached copy was written. Wait, or ask an administrator to clear the RSS cache:

```bash
php bin/php/ezcache.php --clear-id=rss_cache --allow-root-user
```

Podcast apps and directories also cache feeds on their side, on their own schedule; Exponential cannot make them
fetch sooner.

## Checklist

- [ ] The feed answers with `200` and `application/rss+xml`.
- [ ] Addresses use the public host and `https://`.
- [ ] Artwork address opens the image.
- [ ] Every item has an enclosure with a real type and length.
- [ ] Range requests answer `206`.
- [ ] Old episodes kept their `guid`.
- [ ] No test or draft episode is listed.

## Related pages

- [The podcast feed explained](the-podcast-feed-explained.md)
- [Troubleshooting feeds and media](troubleshooting-feeds-and-media.md)
- [Submitting to podcast directories](submitting-to-podcast-directories.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
