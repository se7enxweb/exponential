---
title: Channel fields reference
audience: podcaster
category: podcasting-streaming
order: 18
summary: Every field of the Apple Podcasts section of an RSS export - what to enter, the element it becomes, how Exponential checks the value when the export is saved, and the category list it accepts.
keywords: channel fields, Apple Podcasts, itunes:author, itunes:owner, itunes:image, itunes:category, itunes:explicit, itunes:type, itunes:block, itunes:complete, itunes:new-feed-url, podcast_head, categories, reference
type: help
---
# Channel fields reference

This page is for producers and administrators filling in the **Apple Podcasts** section of a podcast export. The
fields are stored as JSON in the column `ezrss_export.podcast_head` and read by `eZRSSExport::podcastHead()`; their
values are checked by `setPodcastHead()` when the export is saved (`kernel/classes/ezrssexport.php`).

## Contents

1. [The fields](#the-fields)
2. [How values are checked](#how-values-are-checked)
3. [Categories](#categories)
4. [Fields from the rest of the form](#fields-from-the-rest-of-the-form)

## The fields

| Field on the form | Element in the feed | Required for Apple (per the feature description) | Enter |
|---|---|---|---|
| **Author** | `itunes:author` | no | who makes the show, as listeners should see it |
| **Owner name** | `itunes:owner/itunes:name` | yes | the person or organization responsible for the feed |
| **Owner email** | `itunes:owner/itunes:email` | yes | an address the directory may write to; it is public in the feed |
| **Artwork address** | `itunes:image href` | yes | the full address of the cover image ([Artwork and episode images](artwork-and-episode-images.md)) |
| **Category** | `itunes:category text` | yes | chosen from a list (below) |
| **Subcategory** | nested `itunes:category` | no | one of the chosen category's subcategories |
| **Show type** | `itunes:type` | no | *Episodic - newest first* (default) or *Serial - oldest first* |
| **Language** | `language` | no | a two-letter code such as `en`, or a regional one such as `en-us`; empty uses the siteaccess language |
| **Copyright** | `copyright` | no | for example "Copyright 2026 Example Media" |
| **Subtitle** | `itunes:subtitle` | no | one line |
| **Summary** | `itunes:summary` | no | a longer description |
| **Contains explicit content** | `itunes:explicit` | no | tick if any episode needs it; applies to every episode |
| **The show is finished - no further episodes** | `itunes:complete` | no | only when the show has ended |
| **Keep the show out of the Apple Podcasts directory** | `itunes:block` | no | only to withdraw the show |
| **Moved to** | `itunes:new-feed-url` | no | only when the feed moves to a new address |

The owner e-mail address appears in the public feed. Use a role address of the show (for example
`podcast@example.com`), not a personal one.

## How values are checked

| Field | Rule (`setPodcastHead()`) |
|---|---|
| Artwork address, Moved to | `safeURL()`: white space removed, only `http` and `https` (or a relative address), at most 255 characters |
| Contains explicit content | stored as `true` or `false`; `true`, `yes` and `1` count as true; empty is `false` |
| The show is finished, Keep out of the directory | `true`, `false` or empty; written only when `true`, as `Yes` |
| Show type | `serial` stays `serial`; anything else becomes `episodic` |
| Category | kept only if Apple lists it, spelled exactly; otherwise dropped |
| Subcategory | kept only if it belongs to the chosen category |
| All other fields | `safeText()`: control characters removed, trimmed, at most 1024 characters |

A dropped value is not an error message; the field is simply empty when you open the export again. Check the form
after saving.

**Show type** only tells apps how to present the show. The feed itself always lists items newest first
([How a show maps to content](how-a-show-maps-to-content.md)).

## Categories

The accepted list is `eZRSSExport::podcastCategories()`, described in the code as "the list Apple published with the
2019 revision". Top-level categories:

Arts, Business, Comedy, Education, Fiction, Government, History, Health & Fitness, Kids & Family, Leisure, Music,
News, Religion & Spirituality, Science, Society & Culture, Sports, Technology, True Crime, TV & Film.

Examples of subcategories: *News* has Business News, Daily News, Entertainment News, News Commentary, Politics, Sports
News and Tech News; *Society & Culture* has Documentary, Personal Journals, Philosophy, Places & Travel and
Relationships; *Technology*, *History*, *Government* and *True Crime* have none. The full list is in the code. If Apple
revises its list, that function is "the one place to change".

## Fields from the rest of the form

| Field | Element |
|---|---|
| **Name** | `title`, `itunes:title` |
| **Description** | `description` |
| **Access URL** | the feed's own address, in `atom:link rel="self"` |
| **Site URL** | the base of `link` and of every episode address |

## Related pages

- [Building a podcast feed](building-a-podcast-feed.md)
- [Episode mapping reference](episode-mapping-reference.md)
- [Moving, pausing or ending a show](moving-pausing-or-ending-a-show.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
