---
title: RSS feeds for your blog
audience: individual, website-creator, customer
category: website-creation
order: 1625
summary: How one person gives a blog an RSS or Atom feed with the built-in RSS exports under Setup > RSS - sources, field mapping, format, the feed address, autodiscovery, podcasts, the optional autorss extension, and why the syndication extension is something else.
keywords: RSS, Atom, feed, RSS export, Setup RSS, rss/feed, autodiscovery, feed reader, podcast, autorss, syndication, blog, subscribe
type: help
---
# RSS feeds for your blog

This page is for individuals who want readers to follow their blog in a feed reader. A feed is a list of your
newest posts that feed readers check for you, so readers do not have to remember to visit. Feeds are built into
Exponential; nothing needs to be installed.

## Contents

1. [What ships](#what-ships)
2. [Creating the feed](#creating-the-feed)
3. [Letting readers find it](#letting-readers-find-it)
4. [Checking the feed](#checking-the-feed)
5. [Podcasts](#podcasts)
6. [Optional: autorss](#optional-autorss)
7. [Not a feed tool: syndication](#not-a-feed-tool-syndication)
8. [Checklist](#checklist)

## What ships

The kernel's `rss` module manages **RSS exports** (feeds your site publishes) and **RSS imports** (other sites' feeds
read into yours) under **Setup > RSS** ([RSS feeds](../../../guides/rss-feeds.md)).

| | Export |
|---|---|
| Address | `/rss/feed/<name>` |
| Formats | RSS 1.0, RSS 2.0, Atom, OPML, Apple Podcasts |
| Needs cron | No |

Source: [RSS feeds for your site](../../creators/website-creators/rss-feeds-for-your-site.md) and
[RSS, podcast and feed list](../../../features/6.0/rss-podcast-and-feed-list.md).

## Creating the feed

1. **Setup > RSS > New export.**
2. **Name and address**: for example `notes`; the feed is then at `/rss/feed/notes`.
3. **Source**: your *Blog* as the location, *Blog post* as the class.
4. **Field mapping**: title to `title`, description to `body` (or to a summary field if your class has one).
5. **Format**: RSS 2.0 or Atom; both are read by every common feed reader.
6. **Number of items**: ten to twenty is usual.
7. **Siteaccess**: the public site, so links in the feed point to the public pages.
8. **Activate** and press **Open feed**.

Images in feeds use the `rss` image variation (88 x 31 in `settings/image.ini`).

## Letting readers find it

- **A visible link** on the blog page: "Follow by RSS" pointing to `/rss/feed/notes`.
- **Autodiscovery**: a `<link rel="alternate" type="application/rss+xml" ...>` line in the pagelayout's `<head>`, so
  browsers and feed readers find the feed from your blog's address. This is a template change; the builder's page
  has the exact line ([Letting browsers and readers find it](../../creators/website-creators/rss-feeds-for-your-site.md#letting-browsers-and-readers-find-it)).
- **`robots.txt`** must not block `/rss/feed/` ([robots.txt](../../creators/website-creators/robots-txt.md)).

## Checking the feed

- Open the feed address in a browser: you should see XML, not an error page.
- Add it to a feed reader and publish a test post; it should appear after the reader's next check.
- Check that links in the feed open the public post, not the administration.
- The export card in **Setup > RSS** marks exports that need attention (no address, a missing source).

## Podcasts

If you publish audio, the **Apple Podcasts** format writes RSS 2.0 with the iTunes tags. The source class needs an
audio file field ([RSS and podcast feeds](../../customers/growth/rss-and-podcast-feeds.md)). The audio files are
served from your server, so check your hosting's storage and traffic allowance.

## Optional: autorss

`autorss` is a separate extension (composer package `se7enxweb/autorss`, version 1.1.1 on the demonstration
installation) that adds a workflow event creating RSS exports automatically when content is published. It is **not**
in the root `composer.json` `require` list, so a new installation does not have it, and its own description calls it
a tool "for advanced developers". For one blog, a single export made by hand is simpler.

## Not a feed tool: syndication

The installed `syndication` extension sounds like feeds but is not: it synchronizes content between separate
Exponential installations over SOAP ([syndication](../../../features/6.0/extensions/syndication.md)). You do not
need it for a blog feed.

## Checklist

- [ ] One RSS export per blog, active, with source, mapping and format.
- [ ] Links in the feed point to the public siteaccess.
- [ ] A visible feed link on the blog page.
- [ ] Autodiscovery line in the pagelayout, if you can edit templates.
- [ ] Tested in a feed reader.

## Related pages

- [Blogs](README.md)
- [RSS feeds for your site](../../creators/website-creators/rss-feeds-for-your-site.md)
- [RSS and podcast feeds](../../customers/growth/rss-and-podcast-feeds.md)
- [RSS feeds (guide)](../../../guides/rss-feeds.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
