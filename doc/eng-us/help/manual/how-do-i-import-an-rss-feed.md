---
title: How do I import an RSS feed from another site?
audience: admin
category: manual
order: 555
summary: In Setup > RSS, click New import, enter the Source URL and click Update, choose the destination folder, the class and which feed field fills which class field, tick Active and click OK; the RSS import cronjob then creates the objects.
keywords: RSS import, feed, import, source URL, destination path, cronjob, rssimport, syndication
type: help
---
# How do I import an RSS feed from another site?

An RSS import reads another site's feed regularly and creates an object for each new item, for example news from a
partner.

## Who can do this

Users whose role has `rss/edit`.

## Steps

1. Open **Setup** > **RSS** and click **New import** under **RSS imports**.
2. Enter a **Name** and the **Source URL** of the feed, then click **Update**.
   The page reads the feed and shows its fields.
3. Under **Destination path**, click **Browse** and choose the folder for the new objects.
4. Choose the **Class** of the new objects, and for each class field which feed field fills it (or **Ignore**).
5. Under **Imported objects will be owned by**, choose the owner (**Change user**).
6. Tick **Active** and click **OK**.
7. Wait for the RSS import cronjob, then check the folder. **Open the cronjob** in the list shows when it runs.

## What can go wrong

- **No objects arrive.** The cronjob does not run, or the feed address is wrong. Open the source address in a browser.
- **Duplicates or old items.** The import only creates items it has not seen. If the other site changes its item ids,
  items come again.
- **Rights.** Only import feeds you are allowed to republish.

## Related pages

- [How do I publish an RSS feed of my content?](how-do-i-publish-an-rss-feed.md)
- [RSS feeds](../../guides/rss-feeds.md)
- [User manual](README.md)

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
