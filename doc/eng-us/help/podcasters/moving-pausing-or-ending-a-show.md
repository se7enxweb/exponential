---
title: Moving, pausing or ending a show
audience: podcaster
category: podcasting-streaming
order: 22
summary: What to do with a podcast feed when the show takes a break, moves to a new address or a new site, ends for good, or must be withdrawn - the Moved to, The show is finished and Keep the show out fields, the Active switch, and what never to do.
keywords: move feed, new feed URL, itunes:new-feed-url, redirect, pause, hiatus, end show, itunes:complete, withdraw, itunes:block, inactive export, remove export, archive
type: help
---
# Moving, pausing or ending a show

This page is for producers whose show changes its state: a season break, a new address, the last episode, or a
withdrawal. Each case has a matching field in the export's **Apple Podcasts** section, or a setting of the export.
Fields are written into the feed only when set (`generateITunes()` in `kernel/classes/ezrssexport.php`).

## Contents

1. [Overview](#overview)
2. [A break between seasons](#a-break-between-seasons)
3. [Moving the feed](#moving-the-feed)
4. [Ending the show](#ending-the-show)
5. [Withdrawing the show](#withdrawing-the-show)
6. [Never do this](#never-do-this)

## Overview

| Situation | What to do | Feed element |
|---|---|---|
| A break | nothing; keep the feed active | none |
| New feed address | fill in **Moved to**, keep the old address answering | `itunes:new-feed-url` |
| The show has ended | tick **The show is finished - no further episodes** | `itunes:complete` `Yes` |
| Withdraw from the Apple directory | tick **Keep the show out of the Apple Podcasts directory** | `itunes:block` `Yes` |
| Stop the feed answering | clear **Active** | the address returns an error |

## A break between seasons

Nothing needs to change. The feed keeps listing the existing episodes. If you want listeners to know, publish a short
announcement episode, or say it on the show page. When the show returns, the new episodes appear at the top as usual.

## Moving the feed

You might move a feed because the site gets a new domain, the access URL must change, or the show moves to another
site.

1. Make the new feed work first and check it ([Checking your feed](checking-your-feed.md)).
2. In the **old** export, fill in **Moved to** with the full new feed address. It is checked like the artwork address:
   `http` or `https`, at most 255 characters.
3. Keep the old export **active** and listing the episodes, so apps that read it see the new address.
4. Tell the directories through their own tools, as they ask.
5. Remove the old export only when you are sure no one reads it any more. Removing it makes the address stop
   answering ([RSS feeds](../../guides/rss-feeds.md#5-creating-and-removing)).

If only the site's host name changes and the old host keeps forwarding to the new one, the web server's redirect does
the work; still fill in **Moved to** so apps store the new address.

## Ending the show

1. Publish the last episode.
2. Tick **The show is finished - no further episodes** in the export and press **OK**.
3. Keep the export **active** and the episodes published. A finished show can stay listened to for years.

Make sure **Number of objects** covers all episodes, so the archive stays complete in the feed
([How a show maps to content](how-a-show-maps-to-content.md)).

## Withdrawing the show

To take the show out of the Apple Podcasts directory while the feed keeps working for existing subscribers, tick
**Keep the show out of the Apple Podcasts directory**. To stop the feed altogether, clear **Active**: the address no
longer answers. The episodes stay on the site unless you hide or remove them
([Hiding and retiring episodes](hiding-and-retiring-episodes.md)).

Withdrawing for legal reasons (a rights dispute, a court order) usually also means removing or hiding specific
episodes; see [Rights and licensing](rights-and-licensing.md).

## Never do this

- **Change the access URL without Moved to.** Every subscriber loses the show.
- **Recreate the export to "start fresh".** The new export is a new feed; directories do not know it.
- **Recreate episodes.** New items get new remote ids and therefore new `guid` values; apps list them as new.
- **Delete the audio of old episodes.** Apps that list them get errors.

## Related pages

- [Channel fields reference](channel-fields-reference.md)
- [Redirects and moved content](../creators/website-creators/redirects-and-moved-content.md)
- [Submitting to podcast directories](submitting-to-podcast-directories.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
