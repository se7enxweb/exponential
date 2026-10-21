---
title: RSS, Atom and OPML feeds for integrations
audience: integrator, developer, admin
category: integration
order: 100
summary: Feeds as the simplest pull integration - exporting content as RSS 1.0, RSS 2.0, Atom, OPML or podcast feeds, importing other systems' RSS feeds by cronjob, how imported items are keyed, caching, cleanup, and the limits (no Atom import).
keywords: RSS, RSS 2.0, Atom, OPML, podcast, feed, export, import, rssimport, rss/feed, RSSImport_, remote id, CacheTime, cleanuprss, syndication by feed, pull integration
type: help
---
# RSS, Atom and OPML feeds for integrations

This page is for integrators who need a simple, standard way to move lists of content between systems. A feed is
a pull integration that needs no code: Exponential publishes a feed that any reader, aggregator or downstream system
can poll, and it can read the RSS feeds of other systems and turn their items into content objects. The
administration side is described in [RSS feeds](../../guides/rss-feeds.md).

## Contents

1. [When a feed is enough](#when-a-feed-is-enough)
2. [Exporting](#exporting)
3. [Importing](#importing)
4. [How imported items are keyed](#how-imported-items-are-keyed)
5. [Caching and freshness](#caching-and-freshness)
6. [Limits](#limits)
7. [Checklist](#checklist)

## When a feed is enough

| Good fit | Poor fit |
|---|---|
| A partner site shows your latest news | The other system needs every field of every object |
| An aggregator, newsletter tool or social media scheduler picks up new articles | Two-way exchange, edits and removals must travel |
| You take in a press agency's or a group company's RSS feed | The source only offers Atom (no Atom import) |
| A podcast directory reads your episodes | Real-time delivery within seconds |

## Exporting

An **export** lists the newest objects below one or more sources (a location, a class, and optionally the subitems)
at `/rss/feed/<address>`, on the public site of the export's siteaccess. Formats (`kernel/classes/ezrssexport.php`):

| Format | Stored value | Notes |
|---|---|---|
| RSS 1.0 | `1.0` | Written with the Zeta Components Feed library |
| RSS 2.0 | `2.0` | The usual choice; an image can be set |
| Atom 1.0 | `ATOM` | RFC 4287 |
| OPML 2.0 | `OPML` | A list of other feeds; `site.ini [RSSSettings] OPMLMaxOutlines` (default 5000) |
| Apple Podcasts | `ITUNES` | RSS 2.0 with the podcast elements |

Per source you choose which class attributes become the item's title, description, category and enclosure. Create
and edit exports at **Setup > RSS** (`/rss/list`, policy `rss/edit`). Exports are stored in the database
(`ezrss_export` and related tables), so set them up on each environment or include them in the database you copy.

Test an export from the receiving side:

```bash
curl -s https://example.com/rss/feed/<address> | head -20
```

## Importing

An **import** reads an RSS feed (RSS 0.91, 0.92, 1.0 and 2.0; `kernel/classes/ezrssimport.php`) and creates an object
below its destination for every item it has not seen before. The import form has four parts: the feed and its URL,
where the items go (destination and owner), what each item becomes (the class and the mapping of feed fields to
attributes), and whether it runs.

Nothing is read until the cronjob runs: `rssimport.php` is in the **global** part of `settings/cronjob.ini`
(`[CronjobSettings] Scripts[]`), which runs when `runcronjobs.php` is called without a part name:

```bash
php runcronjobs.php -s <siteaccess> --script=rssimport.php   # one run, by hand
php runcronjobs.php -s <siteaccess>                          # the global part, as the crontab runs it
```

The import refuses external entities and switches the network off while parsing (`LIBXML_NONET`), and fetches only
`http` and `https` addresses.

## How imported items are keyed

Every object an import creates gets a remote id that starts with `RSSImport_<import id>_`; the RSS overview counts
imported objects by that prefix. This is the same idea every integration should use: a stable, prefixed key per
source record ([Idempotent imports keyed by remote_id](idempotent-imports-keyed-by-remote-id.md)).

An import adds objects and never removes or updates them. To keep a busy feed from growing without limit, switch on
the cleanup (`content.ini [RSSImportCleanupSettings]`, command `php bin/php/cleanuprss.php`, cronjob part
`cleanuprss`), which keeps the newest *N* items of each active import ([RSS import cleanup](../../bc/6.0/cleanuprss.md)).

## Caching and freshness

| Setting | Effect |
|---|---|
| `site.ini [RSSSettings] CacheTime` | Seconds a written feed is served from cache; `0` writes it anew for every request |
| Cache id `rss_cache` | Clear it after a change that must show at once: `php bin/php/ezcache.php --clear-id=rss_cache --allow-root-user` |

Tell the consumers how often to poll; polling far more often than `CacheTime` gains nothing.

## Limits

- No Atom import: the importer reads the `version` attribute of an RSS root element only.
- `GET /api/ezp/<version>/content/node/<id>/listAtom` of the REST API answers `500`; use an RSS export instead.
- An export lists the newest items, not a full archive: it is not a substitute for a data export.
- Imports do not follow updates or deletions of items at the source.

## Checklist

- [ ] Export: format, address, sources, item count and attribute mapping agreed with the consumer.
- [ ] Export: `CacheTime` and the polling interval written into the interface specification.
- [ ] Import: destination, owner (a service account) and class mapping set; the feed URL uses `https`.
- [ ] Import: the cronjob that runs `rssimport.php` is in the crontab and runs for the right siteaccess.
- [ ] Import: cleanup configured if the feed is busy.

## Related pages

- [RSS feeds](../../guides/rss-feeds.md)
- [RSS: Apple Podcasts feeds, a paged feed list and safer exports](../../features/6.0/rss-podcast-and-feed-list.md)
- [OPML exports](../../bc/6.0/opml.md)
- [Syndication between installations](syndication-between-installations.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
