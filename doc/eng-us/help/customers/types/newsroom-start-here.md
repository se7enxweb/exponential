---
title: Newsrooms: start here
audience: customer, editor
category: customer-success
order: 10
summary: For newsrooms and news desks that publish on Exponential every day: the features that carry the work, who on the desk does what, and the routines that keep a fast publishing rhythm safe.
keywords: newsroom, news desk, news site, breaking news, articles, approvals, scheduled publication, feeds, RSS, front page, layouts
type: help
---
# Newsrooms: start here

A newsroom publishes many items a day, often under time pressure, and every mistake is public within seconds.
What a news desk needs from its system is therefore speed for routine work and a safety net for the rest: drafts
that nobody else sees, an approval step where you want one, a way back to the previous version, and front pages
that can be rearranged without a developer.

This page is for the editor-in-chief, the managing editor and whoever owns the site on the publisher's side. The
clicks are explained in the editor pages that are linked here; this page is about how to organize the work.

## What Exponential gives a newsroom

| Need | What carries it | Where it is explained |
|---|---|---|
| Write and publish quickly | Browser-based editing, drafts and publishing | [Editing content](../../editing-content.md), [Publishing and versions](../../publishing-and-versions.md) |
| Nobody publishes by mistake | Content approval through a workflow | [Workflows](../../../guides/workflows.md) |
| A story that goes live at a set time | Scheduled publication (the *Wait until date* workflow event) | [Workflows](../../../guides/workflows.md), section 1 |
| Undo a wrong correction | Version control: every publish keeps the earlier version | [Publishing and versions](../../publishing-and-versions.md#go-back-to-an-earlier-version) |
| Front pages and section pages laid out by editors | Exponential Layouts, the visual page builder | [The Layouts editor](../../layouts-editor.md) |
| Readers and partners follow the news | RSS, ATOM, iTunes RSS and OPML feeds | [RSS feeds](../../../guides/rss-feeds.md) |
| Readers find older stories | The built in search engine | [Searching](../../searching.md) |
| Help with drafts, summaries and translations | Exponential Intelligence (drafts only, never publishes) | [Exponential Intelligence for owners](../growth/exponential-intelligence.md) |

All of these features are listed in the repository's `README.md` under "Main Exponential features". Which content
classes your site has (an article, a news item, a gallery) depends on how it was built: the demonstration
installation at <https://alpha.se7enx.com> carries, among others, the classes Article, News, Gallery, Video and
Event, but your own site may use different names.

## Who on the desk does what

A newsroom usually needs four kinds of access. The roles behind them are set up by your administrator
([Roles for your team](../day-to-day/roles-for-your-team.md)):

- **Reporters and contributors** create drafts and may submit them for approval, but do not publish on their own
  if your approval workflow says so.
- **Desk editors** approve, correct and publish, and arrange the front page with Layouts.
- **The managing editor** sees what waits for approval and keeps the content calendar.
- **The administrator** (often at your partner or host) manages accounts, roles, caches and upgrades.

Keep the number of people who can publish directly small and known. The approval workflow is the usual way to
let many people write while few publish ([Approvals and review](../day-to-day/approvals-and-review.md)).

## Routines that keep the rhythm safe

- **Every shift**: check *My pending items* and the approval queue, so nothing waits unseen
  ([Publishing and versions](../../publishing-and-versions.md#my-pending-items)).
- **Every day**: look at what was scheduled for the next day and at stories that should be hidden or archived.
- **Every week**: run the weekly owner routine ([Weekly owner routine](../day-to-day/weekly-owner-routine.md)),
  including broken links and images without alternative text.
- **Before a big news day** (an election, a game, a launch): ask your host whether the site is prepared for the
  peak, and agree who is on call ([Peak traffic planning](../../businesses/reliability/peak-traffic-planning.md)).

> **Tip**
> A correction is a new version, not an edit of history. If a published story has to be changed, publish the
> correction as a new version; the previous one stays available in the version list, which helps when someone
> later asks what the story said at a given time.

## When something goes wrong

A wrong story is withdrawn by hiding it or by going back to an earlier version
([Hide an item](../../publishing-and-versions.md#hide-an-item)). If the site itself misbehaves, follow
[When something goes wrong](../../when-something-goes-wrong.md) and [When the site is down](../healthy/when-the-site-is-down.md).

## Your next step

Follow the [newsroom reading path](newsroom-reading-path.md). If your newsroom publishes in several languages, add
the [multilingual reading path](multilingual-site-reading-path.md).

## Related pages

- [Newsroom reading path](newsroom-reading-path.md)
- [Magazines: start here](magazine-start-here.md)
- [Content calendar](../day-to-day/content-calendar.md)
- [News and announcements on an intranet](../../businesses/intranets/news-and-announcements.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
