---
title: Feeds for new acquisitions
audience: businesses, editor, admin
category: business-solutions
order: 70
summary: How a library or archive tells readers and partner sites what is new - RSS 2.0 and Atom feeds of new records, a feed per subject or collection, letting browsers find them, podcasts of recorded talks, OPML lists, a recurring newsletter of new acquisitions, and new-acquisition lists on the site itself.
keywords: new acquisitions, RSS, Atom, feed, OPML, podcast, rss/feed, syndication, new books, recurring newsletter, article pool, library
type: help
---
# Feeds for new acquisitions

This page is for librarians and communication staff who want readers, teachers and partner sites to learn about new
records without visiting the catalog every day. It explains the feed exports of Exponential in plain terms, the setups
that suit a library, and two other channels that use the same content: a list on the site and a newsletter.

## Contents

1. [Three channels from the same records](#three-channels-from-the-same-records)
2. [What a feed export is](#what-a-feed-export-is)
3. [Feeds a library typically offers](#feeds-a-library-typically-offers)
4. [Setting up a new acquisitions feed](#setting-up-a-new-acquisitions-feed)
5. [Letting readers find the feeds](#letting-readers-find-the-feeds)
6. [Recorded talks as a podcast](#recorded-talks-as-a-podcast)
7. [A newsletter of new acquisitions](#a-newsletter-of-new-acquisitions)
8. [New acquisitions on the site](#new-acquisitions-on-the-site)
9. [Feed checklist](#feed-checklist)

## Three channels from the same records

| Channel | Reaches | Feature |
|---|---|---|
| A feed | Feed readers, partner sites, other systems | RSS exports (`/rss/feed/<address>`) |
| A newsletter | Subscribers' inboxes | `cjw_newsletter` recurring sends with an article pool |
| A list on the site | Visitors | A folder list, a tag page or a layouts block |

All three read the same published records, so catalogers do no extra work: publishing a record is enough.

## What a feed export is

"An **export** is a feed this site publishes at `/rss/feed/<address>`. It lists the newest objects below its
**sources** (locations, each with a class and optionally their subitems), as RSS 1.0, RSS 2.0, Atom, OPML or an Apple
Podcasts feed" ([RSS feeds, In short](../../../guides/rss-feeds.md#in-short)). Exports need no cronjob; imports of
other sites' feeds do (`rssimport.php`).

Feed addresses are resolved from the feed's own siteaccess, so feeds advertise the public site and not the
administration host ([Feed addresses use the public site](../../../features/6.0/rss-podcast-and-feed-list.md#feed-addresses-use-the-public-site)).

## Feeds a library typically offers

| Feed | Source location | Class | Subitems |
|---|---|---|---|
| All new records | The catalog root | Each record class (one source per class) | Yes |
| New in a collection | The collection | Record classes | Yes |
| News and events | The news folder | Article, event | No |
| A list of all feeds | An OPML export listing the other feeds | | |

A source names one location and one class; add a source per record class when a feed should include books and
recordings. The export card shows up to five sources "then 'and N more'"
([RSS feeds, An export card](../../../guides/rss-feeds.md#3-an-export-card)).

Feeds by subject: a feed source is a location in the content tree, not a tag. If readers want a feed per subject, the
subject needs a location (a folder) or a developer must build a feed on the tag; plan this with your website creator.

## Setting up a new acquisitions feed

1. **Setup > RSS > New export.**
2. Give it a name and an address, for example `new-acquisitions`; the feed is at `/rss/feed/new-acquisitions`.
3. Add the sources: the catalog root, the record class, with subitems; map the title and description to the record's
   fields (title, summary).
4. Choose the format (RSS 2.0 or Atom for most sites), the number of items and the siteaccess its links point to.
5. Activate it, press **Open feed** and check the result in a feed reader.

Source: [RSS feeds for your site, Publishing a feed](../../creators/website-creators/rss-feeds-for-your-site.md#publishing-a-feed).
Images in feeds use the `rss` image alias (88 x 31 in `settings/image.ini`); if covers matter, ask your designer about
the alias size.

> **Tip**
> A feed lists the last published objects (`kernel/classes/ezrssexportitem.php` sorts its sources by `published`,
> newest first), so a record that is edited later is not "new" again only because it changed. If
> you import an old collection, publish it before the feed is announced, or the first feed fills with old items.

## Letting readers find the feeds

- Add an autodiscovery link to the pagelayout's `<head>` so browsers and feed readers find the feed, and a visible
  link on the page the feed is about ([RSS feeds for your site](../../creators/website-creators/rss-feeds-for-your-site.md#letting-browsers-and-readers-find-it)).
- Make sure `robots.txt` does not block `/rss/feed/` if you want feeds found (same page, checklist).
- Tell partner institutions the address; they can import it with their own systems, or with an RSS import if they
  also run Exponential.

## Recorded talks as a podcast

An RSS export can be written as **Apple Podcasts (RSS 2.0 + iTunes)**, "so a show published here can be submitted to
Apple without a hand written feed". The source needs an audio file field mapped as the enclosure
([RSS: Apple Podcasts feeds](../../../features/6.0/rss-podcast-and-feed-list.md#apple-podcasts-as-a-feed-format)).
Author talks, readings or lectures recorded by the library can be published this way.

## A newsletter of new acquisitions

The `cjw_newsletter` extension (release 4.2.0) can send a newsletter on a schedule from an **article pool**:

- a **recurring send** in mode **copy** copies a template edition and, with **auto-fill**, fills it from the pool with
  content published since the schedule's last send;
- **skip if empty** (the default) sends nothing when nothing new was published;
- the pool's defaults are the content root, the class `cjw_newsletter_article`, 10 items and 31 days
  (`[ArticlePoolSettings]`); set the pool of the list to your catalog and record classes.

Source: [editorial.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/editorial.md). Only content the
anonymous user may read is taken, so restricted records never reach the newsletter. Consent, sending and the rest are
in the [Newsletters](../newsletters/README.md) section.

## New acquisitions on the site

- A folder's full view can list its newest children; a tag page lists records with a subject
  ([Tagging and subject headings](tagging-and-subject-headings.md#subject-pages-and-lists-by-subject)).
- A layouts block with a dynamic collection keeps itself current, for example "the latest five articles in this
  section" ([Landing pages with layouts](../../creators/website-creators/landing-pages-with-layouts.md),
  [Layouts reference](../../../bc/6.0/LAYOUTS.md)).
- A stored PDF of the month's acquisitions can be offered for download ([PDF and print exports](pdf-and-print-exports.md)).

## Feed checklist

- [ ] Each export has a source, an address and a format, and is active.
- [ ] Every record class that should appear has its own source.
- [ ] Title and description mapped to record fields that read well out of context.
- [ ] Links in the feed point to the public siteaccess; **Open feed** checked in a feed reader.
- [ ] The pagelayout links the feeds for autodiscovery; `robots.txt` does not block `/rss/feed/`.
- [ ] Old collections published before a feed is announced.
- [ ] A recurring newsletter planned, if readers prefer mail.

## Related pages

- [Libraries and catalogs](README.md)
- [RSS feeds (guide)](../../../guides/rss-feeds.md)
- [RSS feeds for your site](../../creators/website-creators/rss-feeds-for-your-site.md)
- [Feeds and syndication](../mobile-and-headless/feeds-and-syndication.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
