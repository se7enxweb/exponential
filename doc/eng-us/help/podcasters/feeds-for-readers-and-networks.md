---
title: Feeds for readers and networks
audience: podcaster
category: podcasting-streaming
order: 23
summary: The feeds a show or a podcast network can publish besides the podcast feed - RSS 2.0 or Atom of episode pages for news readers, an OPML list of all shows of a network - and importing a partner's feed into the site, with what each format carries and what it does not.
keywords: RSS 2.0, Atom, RSS 1.0, OPML, feed list, network, news reader, syndication, import, rssimport, partner feed, show notes feed, OPMLMaxOutlines
type: help
---
# Feeds for readers and networks

This page is for producers and network editors who want more than one podcast feed: a feed of new episode pages for
news readers, a list of every show for a network, or a partner's episodes shown on the site. All of these are RSS
exports or imports under **Setup > RSS** ([RSS feeds](../../guides/rss-feeds.md)).

## Contents

1. [Which format for what](#which-format-for-what)
2. [A feed of episode pages](#a-feed-of-episode-pages)
3. [A list of all shows (OPML)](#a-list-of-all-shows-opml)
4. [Showing a partner's episodes](#showing-a-partners-episodes)
5. [Keeping several feeds in order](#keeping-several-feeds-in-order)

## Which format for what

| Format on the form | Stored value | Carries | Use it for |
|---|---|---|---|
| Apple Podcasts (RSS 2.0 + iTunes) | `ITUNES` | episodes with audio enclosures, iTunes channel fields | podcast apps and directories |
| RSS 2.0 | `2.0` | items with title, link, description, category; an image of the channel | news readers, other sites |
| Atom 1.0 | `ATOM` | the same, as Atom | readers that prefer Atom |
| RSS 1.0 (RDF) | `1.0` | the same, as RDF | older systems |
| OPML 2.0 (list of feeds) | `OPML` | outlines naming other feeds, nodes or addresses | a network's list of shows |

The labels come from `eZRSSExport::formatLabels()` (`kernel/classes/ezrssexport.php`).

> **Note**
> RSS 1.0, RSS 2.0 and Atom exports write **no enclosure**: their writer (`generateFeed()`) has none. A podcast app
> subscribing to them gets text only. For audio, use the Apple Podcasts format.

## A feed of episode pages

Some listeners follow a show in a news reader, and partner sites show the newest episodes. For them:

1. **New export**, format **RSS 2.0** (or Atom), a name such as "Example Show: new episodes".
2. Source: the show folder, **Subnodes** as in the podcast feed, the episode class.
3. Map **Title** to the title and **Description** to the short description.
4. Make it **Active** and give it its own access URL.

Readers then get a link to each new episode page, where the player and the full notes are. The RSS 2.0 export may
carry an **Image**, chosen from the site's images.

## A list of all shows (OPML)

A network with several shows can publish one OPML document that lists all their feeds; readers import it and
subscribe to everything at once.

1. **New export**, **Feed format** `OPML 2.0 (list of feeds)`. The page shows the OPML controls in place of content
   sources ([OPML exports](../../bc/6.0/opml.md#turn-an-export-into-an-opml-document)).
2. Add an outline for each show's podcast export. The address of each outline is worked out when the document is
   written, "so renaming a feed cannot leave a dead entry behind".
3. Fill in the head fields (owner name and e-mail), make it active and open it.

The document is served as `text/x-opml` (`kernel/private/classes/views/rss/feed.php`). It holds at most
`settings/site.ini [RSSSettings] OPMLMaxOutlines` outlines, 5000 as shipped and never more than 50000.

## Showing a partner's episodes

An **RSS import** reads another site's feed and creates an item below a destination for each entry it has not seen
before. The `rssimport.php` cronjob runs active imports; nothing is read without it. Imports read `http` and `https`
addresses only. Imported items keep what the mapping fills in; an import does not download the partner's audio files
([RSS feeds](../../guides/rss-feeds.md#4-an-import-card-and-the-cronjob)). Imported objects are never removed by the
import itself; a cleanup script exists ([RSS import cleanup](../../bc/6.0/cleanuprss.md)).

Ask the partner first. Their episodes, notes and artwork are their content
([Rights and licensing](rights-and-licensing.md)).

## Keeping several feeds in order

- Name exports so the list sorts well: "Example Show: podcast", "Example Show: new episodes".
- Use the RSS list's filter **Need attention** after any change to the content tree.
- Write down which partner or directory uses which address; removing an export makes its address stop answering.

## Related pages

- [Building a podcast feed](building-a-podcast-feed.md)
- [RSS feeds for your site](../creators/website-creators/rss-feeds-for-your-site.md)
- [RSS and podcast feeds](../customers/growth/rss-and-podcast-feeds.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
