---
title: News and alerts
audience: government, website-creator, project-manager, admin
category: public-sector
order: 10
summary: Index of the guides for publishing news, press releases and urgent notices on a public-sector Exponential site, and for reaching people with RSS, notifications and newsletters, archiving old items and describing pages for sharing.
keywords: news, alerts, urgent notices, press releases, RSS, notifications, newsletters, archive, expiry, metadata, Open Graph, social sharing
type: help
---
# News and alerts

This folder is for website creators, project managers and administrators who set up the news side of a public-sector
site: news articles, press releases, urgent notices, and the channels that carry them to people (RSS, notification
mails, newsletters), plus what happens to items when they are old. Exponential has no dedicated alert banner or
broadcast system; urgent notices are built from content, templates and caches, as the pages explain.

## Contents

1. [The pages](#the-pages)
2. [Reading order](#reading-order)
3. [Choosing channels](#choosing-channels)

## The pages

| Page | The answer in one line |
|---|---|
| [News articles](news-articles.md) | The Article class, a news folder by year, lists on the front page, dates and images |
| [The urgent notices pattern](urgent-notices-pattern.md) | A notices folder the front page shows when it is not empty, with an end time and cache refresh |
| [RSS feeds](rss-feeds.md) | RSS exports of news and notices in **Setup > RSS**, formats, caching, imports |
| [Notifications and subscriptions](notifications-and-subscriptions.md) | People with an account follow folders with **Notify me**; at once or as a digest |
| [Newsletters](newsletters.md) | cjw_newsletter for a periodic news digest, with double opt-in and one-click unsubscribe |
| [Press releases](press-releases.md) | A press release class, embargo with a *Wait until date* event, press contacts as roles |
| [Archive and expiry](archive-and-expiry.md) | `hide.php` and `unpublish.php`, archives by year, what to keep |
| [Metadata and social sharing](metadata-and-social-sharing.md) | Titles, descriptions and Open Graph images with xrowmetadata, without third-party trackers |

## Reading order

1. **News articles** and **The urgent notices pattern**.
2. **RSS feeds**, **Notifications and subscriptions** and **Newsletters**.
3. **Press releases** if you have a press office.
4. **Archive and expiry** and **Metadata and social sharing**.

Up: [Building a public-sector site](../README.md), the index of all building guides, and [Governments: start here](../../government-start-here.md), the start of the Governments
section. Alerts for specific kinds of organizations are in
[Alerts in many languages and plain language](../../levels/public-health/alerts-multilingual-and-plain-language.md) and
[Alerts, availability, maintenance mode and static cache](../../levels/emergency-services/alerts-availability-maintenance-and-static-cache.md).

## Choosing channels

| Channel | Who gets it | Needs | Speed |
|---|---|---|---|
| The website | anyone who looks | nothing | at publication (through caches) |
| RSS | feed readers, other sites, media | an RSS export | when the reader polls |
| Notification mails | account holders who follow a folder | accounts, the notification cronjob | next notification run, or a digest |
| Newsletter | subscribers of a list | cjw_newsletter and its cronjobs | when an edition is sent |
| Social media, warning systems | their audiences | outside Exponential | their own |

None of these is an emergency broadcast system. For warnings to everyone, use the warning systems of your country.

## Related pages

- [Building a service catalog](../service-catalog/README.md)
- [RSS feeds guide](../../../../guides/rss-feeds.md)
- [Notifications for administrators](../../../../guides/notifications-administrator.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
