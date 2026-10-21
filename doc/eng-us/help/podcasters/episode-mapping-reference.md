---
title: Episode mapping reference
audience: podcaster
category: podcasting-streaming
order: 19
summary: How each element of a podcast feed item is produced from an episode - the Title, Description, Category and Enclosure mappings of an export source, which datatypes each accepts, and the elements that come from the item itself such as guid, pubDate, link, author and duration.
keywords: mapping, source, title, description, category, enclosure, guid, remote id, pubDate, link, itunes:author, itunes:duration, datatypes, podcastText, podcastItemList, reference
type: help
---
# Episode mapping reference

This page is for producers and administrators who map an episode class in a podcast export, and for anyone who wants
to know where a value in the feed comes from. The code is `eZRSSExport::podcastItemList()` and `podcastText()` in
`kernel/classes/ezrssexport.php`; the mapping columns are in `eZRSSExportItem` (`kernel/classes/ezrssexportitem.php`).

## Contents

1. [The four mappings](#the-four-mappings)
2. [Datatypes and what the feed gets](#datatypes-and-what-the-feed-gets)
3. [Elements from the item itself](#elements-from-the-item-itself)
4. [Which mapping applies](#which-mapping-applies)
5. [Common mistakes](#common-mistakes)

## The four mappings

Each source of an export has a class and four attribute choices:

| Mapping | Feed element | If empty or not mapped |
|---|---|---|
| **Title** | `title` and `itunes:title` | the item's name |
| **Description** | `description` | the element is left out |
| **Category** | `category` | the element is left out |
| **Enclosure (media)** | `enclosure` with `url`, `length` and `type` | **the episode is left out of the feed** |

## Datatypes and what the feed gets

For Title, Description and Category (`podcastText()`):

| Datatype of the mapped field | What is written |
|---|---|
| Rich text (`ezxmltext`) | the field's rendered output, as text (the markup escaped into the XML) |
| Keywords (`ezkeyword`) | the keywords as one string |
| Text line, text block, numbers and other fields with a plain value | that value |
| Anything else | nothing |

For the Enclosure:

| Datatype | Result |
|---|---|
| Media (`ezmedia`) | used: address, size and type of the stored file |
| File (`ezbinaryfile`) | used: the same |
| Image or any other | not an enclosure: the episode is left out of a podcast feed |

The form's help says an enclosure may be "a media, image or file attribute". The podcast writer accepts only media
and file fields, and the RSS 1.0, RSS 2.0 and Atom writers use no enclosure at all (`generateFeed()` in the same file
writes none).

## Elements from the item itself

| Element | Source |
|---|---|
| `link` | the episode page: the nice URL with URL translation on, else `/content/view/full/<node id>`, after the export's site address |
| `guid` (`isPermaLink="false"`) | the object's remote id, which survives moving, renaming and republishing |
| `pubDate` | the object's published date, as an RFC 2822 date |
| `enclosure url` | `<site>/content/download/<object id>/<attribute id>/<file name>` |
| `enclosure length` | the file size in bytes, as stored |
| `enclosure type` | the stored MIME type, decided by the file suffix through `settings/mime.ini` |
| `itunes:duration` | `HH:MM:SS`, worked out from the first MPEG-1 Layer III frame and the file size; missing for other files |
| `itunes:explicit` | the channel's **Contains explicit content** |
| `itunes:author` | the name of the user who created the item (the node's creator) |

## Which mapping applies

An item is matched to a mapping by its class **and** by the source location being in its path. With two sources for
two shows, each episode gets the mapping of the show it is in. An item that matches no mapping is skipped. Hidden
items are skipped unless the site shows hidden items.

## Common mistakes

| Mistake | Effect | Fix |
|---|---|---|
| Enclosure not mapped | the feed has a channel and no items | map the media or file field |
| Enclosure mapped to the image | no items | map the audio field |
| Wrong class on the source | no items | choose the episode class and press **Set** again |
| Description mapped to a very long field | apps cut the text | map a short field; keep long notes on the page |
| An M4A file without a `mime.ini` entry | `type="application/octet-stream"` | add the suffix ([Working with your administrator](working-with-your-administrator.md)) |
| Episodes created by a shared technical user | `itunes:author` shows that user's name | create episodes with personal accounts, or accept the name |

## Related pages

- [The podcast feed explained](the-podcast-feed-explained.md)
- [Channel fields reference](channel-fields-reference.md)
- [Troubleshooting feeds and media](troubleshooting-feeds-and-media.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
