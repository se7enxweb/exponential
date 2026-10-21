---
title: Downloads and statistics
audience: podcaster
category: podcasting-streaming
order: 42
summary: What an Exponential site can tell a show about its audience - the download counter of file fields, page view counts, web server logs, analytics on episode pages - what it cannot count, such as plays in podcast apps, and how to read the numbers honestly.
keywords: statistics, downloads, download count, analytics, listeners, plays, view count, updateviewcount, server logs, web analytics, podcast statistics, audience, measurement
type: help
---
# Downloads and statistics

This page is for producers and sponsors' contacts who want to know how many people listen. An Exponential site counts
some things well and others not at all. Podcast audiences are hard to measure in any system; this page says what each
number really means.

## Contents

1. [What the site counts](#what-the-site-counts)
2. [The download counter](#the-download-counter)
3. [Page views and analytics](#page-views-and-analytics)
4. [Server logs](#server-logs)
5. [What cannot be counted here](#what-cannot-be-counted-here)
6. [Reporting numbers](#reporting-numbers)

## What the site counts

| Number | Source | Counts |
|---|---|---|
| Downloads of a **file** field | `download_count` of `ezbinaryfile` | requests for the file through `content/download` |
| Downloads of a **media** field | none | the media datatype has no counter |
| Page views | the `updateviewcount` cronjob part, from the access log | views of episode pages |
| Visitors, sources, devices | an analytics service on the pages, if you add one | browser visits to pages |
| Every request | the web server's access log | everything, including apps fetching files and the feed |

The page views and analytics options are on [Visitor statistics and analytics](../creators/website-creators/statistics-and-analytics.md).

## The download counter

Each download of a file field through `content/download` adds one to its `download_count`
(`kernel/classes/datatypes/ezbinaryfile/ezbinaryfiletype.php`). The counter is per field and version. The subitems
lists of the administration can show it as a column (`kernel/classes/subitems/columns/expsubitemsmediacolumn.php`).

What it means:

- every request is counted, including a `Range` request that fetches only a part, so one listen can count several
  times while another (served from a cache or a CDN) counts not at all;
- requests from bots and directory crawlers count too;
- an episode whose audio is in a **media** field is not counted at all.

Use it as a trend ("more than last month"), not as a number of listeners.

## Page views and analytics

Page views count people who opened the episode page in a browser. An analytics script on the page sees browser
visits only: it never sees an app downloading the file from the feed, and it needs the visitor's consent in many
jurisdictions ([Privacy and third-party players](privacy-and-third-party-players.md)). On installations with
`bcwebsitestatistics`, that extension adds a Google Analytics script; any other service can be added through the
design.

## Server logs

The web server's access log records every request to `/content/download/` and `/rss/feed/`. A host or administrator
can count, per episode file:

- requests and unique client addresses per day;
- the user agents, which often name the podcast app;
- requests for the feed, which show how many apps check it.

Ask your host for these figures; reading logs is server work, outside the administration. Treat addresses in logs as
personal data ([Log and data retention](../hosting/security/log-and-data-retention.md)).

## What cannot be counted here

- **Plays in podcast apps**: apps download files and play them offline; the site sees a download, not a listen.
- **Listeners in directories**: the directories have their own statistics for their apps; read them in their tools.
- **Downloads answered by a CDN** that never reach the site ([CDN and external media hosting](cdn-and-external-media-hosting.md)).

## Reporting numbers

- Say what was counted: "file requests in the server log, 1 to 30 September", not "listeners".
- Name the source and the period, and keep the method the same from month to month.
- Do not add numbers from different sources (site downloads plus directory plays) as if they were the same thing.
- When a sponsor asks for audience figures, give the directories' figures and the site's download trend separately,
  each with its definition.

## Related pages

- [Visitor statistics and analytics](../creators/website-creators/statistics-and-analytics.md)
- [Analytics and what to measure](../customers/day-to-day/analytics-and-what-to-measure.md)
- [Request logs](../hosting/operations/request-logs.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
