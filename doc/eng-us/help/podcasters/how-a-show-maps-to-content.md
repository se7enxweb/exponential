---
title: How a show maps to content
audience: podcaster
category: podcasting-streaming
order: 4
summary: How a podcast, a radio program or a video series becomes content in Exponential - the show as a folder, seasons as subfolders, episodes as items with a media field - and how that structure decides what the podcast feed lists, in which order and how many.
keywords: content structure, content tree, show, season, series, episode, folder, location, feed source, subnodes, number of objects, order, newest first
type: help
---
# How a show maps to content

This page is for podcasters and producers who are about to set up a show and want the structure right from the
start. In Exponential everything is **content**: items of a **class** (Folder, Article, Video ...), placed at
**locations** in a tree. A show is no exception. The structure you choose decides what the site shows and what the
podcast feed lists.

## Contents

1. [The building blocks](#the-building-blocks)
2. [A structure that works](#a-structure-that-works)
3. [What the feed takes from the structure](#what-the-feed-takes-from-the-structure)
4. [Several shows on one site](#several-shows-on-one-site)
5. [Things to decide early](#things-to-decide-early)

## The building blocks

| Show concept | Exponential concept | Notes |
|---|---|---|
| The show or program | A **folder** (or a container class of your site) | Holds the description and artwork shown on the site |
| A season or a series | A **subfolder** of the show | Optional; useful for long-running shows |
| An episode | An **item** of an episode class with a media or file field | One item per episode; its versions keep the history |
| Show notes | A rich text field of the episode | Also the description in the feed when mapped |
| A guest | An item of a person class, related to the episode | See [Guests and credits](guests-and-credits.md) |
| The feed | An **RSS export** that names the show folder as its source | Set up under **Setup > RSS** |

Classes are described on [Choosing a class for episodes](choosing-a-class-for-episodes.md). An item can have more than
one location, for example in the show and in a "Best of" folder; the export option **Main node only** keeps such an
episode from appearing twice in the feed.

## A structure that works

```text
Podcasts                      (folder)
  The Example Show            (folder: description, artwork)
    Season 1                  (folder)
      Episode 1: Pilot        (episode item: audio, show notes)
      Episode 2: ...
    Season 2                  (folder)
      Episode 9: ...
  Guests                      (folder of person items)
```

The names are examples. Keep guests in their own folder so that one guest can be related to several episodes.
[Setting up the show structure](setting-up-the-show-structure.md) builds this step by step.

## What the feed takes from the structure

An export lists the objects below each **source**. A source is a location, a class and the **Subnodes** checkbox
(`eZRSSExportItem::fetchNodeList()` in `kernel/classes/ezrssexportitem.php`):

| Setting of the source | What is listed |
|---|---|
| Subnodes off | Only the direct children of the location (depth 1) of the chosen class |
| Subnodes on | Every object of the chosen class anywhere below the location |

So a show with season folders needs **Subnodes** on, with the show folder as the source. A show without seasons works
either way.

Two more rules come from the same function:

- **Order**: items are sorted by their **published** date, newest first. The **Show type** of a podcast export
  (*Episodic - newest first* or *Serial - oldest first*) writes `itunes:type` for the apps; it does not reorder the
  feed itself.
- **How many**: the export's **Number of objects** limits the list. The choices come from `settings/site.ini
  [RSSSettings] NumberOfObjectsList[]` (5 to 1000, default 20). Older episodes beyond the limit drop out of the feed;
  they stay on the site.

Pick a number large enough for your back catalog: a podcast app that subscribes today sees only what the feed lists.

## Several shows on one site

Give each show its own folder and its own export. Each export has its own address (`/rss/feed/<access URL>`), its own
channel fields and its own artwork. A network of shows can then publish an OPML list of all feeds
([Feeds for readers and networks](feeds-for-readers-and-networks.md)). For a show per language, see
[Multilingual shows](multilingual-shows.md).

## Things to decide early

- **The episode class.** Changing it later means moving content between classes.
- **Where files live.** Next to the episode or in the media library; both work ([Images, media and uploads](../media-and-uploads.md)).
- **Seasons or not.** Adding season folders later means moving episodes; the feed's **Subnodes** must match.
- **The published date.** It orders the feed. Scheduling with the *Wait until date* event can set it to the release
  date ([Scheduling a release](scheduling-a-release.md)).
- **Never recreate an episode to fix it.** The feed's `guid` is the object's remote id; a new object is a new episode
  for every app ([The podcast feed explained](the-podcast-feed-explained.md)).

## Related pages

- [Setting up the show structure](setting-up-the-show-structure.md)
- [Structuring the content tree](../creators/website-creators/structuring-the-content-tree.md)
- [RSS feeds](../../guides/rss-feeds.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
