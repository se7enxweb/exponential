---
title: RSS feeds for your site
audience: website-creator, admin
category: website-creation
order: 410
summary: Publish RSS 2.0, Atom, OPML or podcast feeds of your content with RSS exports, link them from the pagelayout so browsers and readers find them, and import other sites' feeds with the rssimport cronjob.
keywords: RSS, Atom, feed, podcast, OPML, RSS export, RSS import, rss/feed, rssimport, cronjob, feed autodiscovery, website creator
type: help
---
# RSS feeds for your site

This page is for website creators who offer feeds of news, blog posts or podcasts, or who show other sites' feeds.
Feeds are managed in **Setup > RSS** ([RSS feeds](../../../guides/rss-feeds.md)).

## Contents

1. [Exports and imports](#exports-and-imports)
2. [Publishing a feed](#publishing-a-feed)
3. [Letting browsers and readers find it](#letting-browsers-and-readers-find-it)
4. [Podcasts](#podcasts)
5. [Importing a feed](#importing-a-feed)
6. [Checklist](#checklist)

## Exports and imports

| | Export | Import |
|---|---|---|
| What | a feed this site publishes | another site's feed read into this site |
| Address | `/rss/feed/<address>` | none; it creates objects |
| Formats | RSS 1.0, RSS 2.0, Atom, OPML, Apple Podcasts | what the other site offers |
| Needs a cronjob | no | yes: `rssimport.php` |

## Publishing a feed

1. **Setup > RSS > New export.**
2. Give it a name and an address (for example `news`): the feed is at `/rss/feed/news`.
3. Add **sources**: a location (the News folder), the class to list (`article`) and whether subitems count; map the
   title and description to the class's fields (title, intro).
4. Choose the format (RSS 2.0 or Atom for most sites), the number of items and the siteaccess its links point to.
5. Activate it, press **Open feed** and check the result in a feed reader.

The export card shows when the cached copy was last written and marks exports that need attention (no address, a
missing source). Images in feeds use the `rss` image alias (88 x 31 in `settings/image.ini`).

## Letting browsers and readers find it

Add an autodiscovery link to the pagelayout's `<head>`:

```text
<link rel="alternate" type="application/rss+xml" title="{'News'|i18n( 'design/mysite' )}"
      href={'rss/feed/news'|ezurl( , 'full' )}>
```

and a visible link on the News page for visitors.

## Podcasts

The **Apple Podcasts** format writes RSS 2.0 with the iTunes tags. Sources need an audio file field; the feature page
[RSS, podcast and feed list](../../../features/6.0/rss-podcast-and-feed-list.md) lists what each format needs.

## Importing a feed

1. **Setup > RSS > New import**: the feed's address, a destination folder and a class to create (with field mapping).
2. Activate it and make sure the `rssimport` cronjob runs ([Cronjobs](../../../guides/cronjobs.md)); nothing is read
   without it.
3. Imported items are ordinary objects; removing the import keeps them.

Only import feeds you are allowed to republish.

## Checklist

- [ ] Each export has a source, an address and a format, and is active.
- [ ] Links in the feed point to the public siteaccess.
- [ ] The pagelayout links the feed for autodiscovery.
- [ ] `robots.txt` does not block `/rss/feed/` if you want feeds found.
- [ ] Imports run by cron, into a folder editors check.

## Related pages

- [RSS feeds](../../../guides/rss-feeds.md)
- [SEO basics for Exponential sites](seo-basics.md)
- [Clean up RSS](../../../bc/6.0/cleanuprss.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
