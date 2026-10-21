---
title: Building a podcast feed
audience: podcaster
category: podcasting-streaming
order: 17
summary: Step by step - create an RSS export in the format Apple Podcasts (RSS 2.0 + iTunes), fill in the channel fields, add the show folder as a source with the episode class, map title, description and enclosure, make it active and open the feed address.
keywords: build podcast feed, create RSS export, Apple Podcasts, iTunes, Setup RSS, export form, source, enclosure mapping, access URL, active, feed address, rss/feed
type: help
---
# Building a podcast feed

This page is for producers and administrators who create the feed a show is published with. The feed is an **RSS
export** under **Setup > RSS** (`/rss/list`); you need the `rss/edit` policy, and the editor siteaccess does not
offer the module ([RSS feeds](../../guides/rss-feeds.md)). What the finished feed contains is explained on
[The podcast feed explained](the-podcast-feed-explained.md).

## Contents

1. [Before you start](#before-you-start)
2. [Create the export](#create-the-export)
3. [The feed part](#the-feed-part)
4. [What the feed lists](#what-the-feed-lists)
5. [The Apple Podcasts part](#the-apple-podcasts-part)
6. [Save and open](#save-and-open)

## Before you start

You need:

- the show folder with at least one published episode that has an audio file
  ([Publishing an episode](publishing-an-episode.md));
- the show's name, description, author, owner name and owner e-mail address;
- the full address of the cover art ([Artwork and episode images](artwork-and-episode-images.md));
- the category from Apple's list ([Channel fields reference](channel-fields-reference.md)).

## Create the export

1. Open **Setup > RSS**.
2. Press **New export**. The form opens as a draft; readers see nothing until you press **OK**.

## The feed part

| Field | Enter |
|---|---|
| **Name** | The show's name. Feeds also carry it as their title |
| **Description** | One paragraph about the show; written as the channel `description` |
| **Feed format** | **Apple Podcasts (RSS 2.0 + iTunes)**. The **Apple Podcasts** section appears |
| **Access URL** | The last part of the feed address, for example `example-show`; the feed answers at `/rss/feed/example-show` |
| **Active** | Tick it when the feed should answer |
| **Site URL** | Usually empty: the public address of the export's siteaccess is used. Fill it in when the feed must point to a specific host |

The **Image** of this part is used only by RSS 2.0 exports, not by the podcast format; podcast artwork goes in
**Artwork address** below.

## What the feed lists

1. **Number of objects**: how many episodes the feed holds, newest first. Choose enough for your back catalog (up to
   1000).
2. **Main node only**: tick it if episodes have more than one location, so none is listed twice.
3. In the source card, press **Browse** and choose the **show folder**.
4. Tick **Subnodes** if episodes are in season folders below it.
5. Choose the episode **Class** and press **Set** to load its attributes.
6. Map the attributes:

| Mapping | Choose |
|---|---|
| **Title** | the title field |
| **Description** | the short description, or the show notes ([Writing show notes](writing-show-notes.md)) |
| **Category** | optional: a keyword or text field |
| **Enclosure (media)** | the media or file field holding the audio. Required: an episode without it is left out |

For several shows in one feed, or episodes of two classes, press **Add source** for each.

## The Apple Podcasts part

Fill in at least **Owner name**, **Owner email**, **Artwork address** and **Category**, which the feature description lists as
required ([RSS: Apple Podcasts feeds](../../features/6.0/rss-podcast-and-feed-list.md#channel-fields)). Then **Author**, **Language**, **Subtitle**, **Summary**, **Copyright**, **Show type** and
**Contains explicit content**. Leave **The show is finished**, **Keep the show out of the Apple Podcasts directory** and
**Moved to** empty for a new show. Every field: [Channel fields reference](channel-fields-reference.md).

## Save and open

1. Press **OK**. If something keeps the export from being saved (a class without the chosen attribute, an active
   export without an address), the form lists it at the top.
2. In the RSS list, the export's card shows the feed address and **Open feed**.
3. Open the feed. Your episodes should be there, newest first, each with an `enclosure`.
4. Run the checks on [Checking your feed](checking-your-feed.md) before you submit the address anywhere.

The address is `https://<your site>/rss/feed/<access URL>`. Choose the access URL carefully: it is what directories
and subscribers store. Changing it later needs the steps on [Moving, pausing or ending a show](moving-pausing-or-ending-a-show.md).

## Related pages

- [Episode mapping reference](episode-mapping-reference.md)
- [Submitting to podcast directories](submitting-to-podcast-directories.md)
- [RSS and podcast feeds](../customers/growth/rss-and-podcast-feeds.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
