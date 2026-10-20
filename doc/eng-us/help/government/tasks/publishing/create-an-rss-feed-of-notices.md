---
title: Create an RSS feed of notices
audience: government, editor, admin
category: public-sector
order: 220
summary: For editors with the rss/edit policy - create an RSS export of the public notices folder in Setup > RSS, choose format, items and attributes, open the feed, and link it from the notices page.
keywords: RSS, feed, notices, RSS export, Setup RSS, New export, access URL, rss/feed, sources, subnodes, Main node only, CacheTime
type: help
---
# Create an RSS feed of notices

This page is for editors or administrators who give media, partner sites and residents a feed of public notices. A
feed is an **RSS export** in **Setup > RSS** (`/rss/list`); it needs the `rss/edit` policy, and the editor siteaccess
does not offer the RSS module ([RSS feeds](../../../../guides/rss-feeds.md)).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check the result](#check-the-result)
4. [Pitfalls](#pitfalls)

## Before you start

- You can open **Setup > RSS** in the administration.
- You know the Notices folder and the notice class identifier.
- Choose a short, stable access URL, for example `notices`.

## Steps

1. **Setup > RSS**, **New export**.
2. **The feed**: name "Public notices", a description, **Feed format** *RSS 2.0*, access URL `notices`, tick **Active**.
3. **What the feed lists**: number of items (for example 20), tick **Main node only**.
4. **Add source**: **Browse** to the Notices folder, select it; tick **Subnodes** if notices are in year folders; choose
   the notice class and press **Set**; choose the title attribute (the headline) and the description attribute (the
   text).
5. **OK**. The card shows the feed address (`/rss/feed/notices`) and **Open feed**.
6. Link the feed from the Notices page: edit the folder's description and add a link "Notices as RSS feed".

## Check the result

- [ ] **Open feed** shows the newest notices.
- [ ] A feed reader subscribes to the address and shows items.
- [ ] A new notice appears in the feed after the feed cache time (`site.ini [RSSSettings] CacheTime`); for an urgent
  check, an administrator clears the RSS cache (`php bin/php/ezcache.php --clear-id=rss_cache`).

## Pitfalls

| Pitfall | Avoid it |
|---|---|
| The card says **Needs attention** | no address, no source, or a source that no longer exists; edit and fix |
| Changing the access URL later | subscribers lose the feed; choose it once |
| Hidden notices | open the feed signed out to confirm it lists only what visitors may see |
| Removing the export | the address stops answering; deactivate first (untick **Active**) and tell subscribers |

## Related pages

- [Publishing tasks](README.md)
- [RSS feeds (building)](../../building/news-and-alerts/rss-feeds.md)
- [Follow changes with notifications](follow-changes-with-notifications.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
