---
title: A release calendar
audience: podcaster
category: podcasting-streaming
order: 29
summary: How a show plans and tracks its releases with what Exponential offers - drafts and pending versions, scheduled publishing, object states as production stages, lists of what is coming - and a weekly routine and calendar template for a team.
keywords: release calendar, editorial calendar, content calendar, planning, production schedule, drafts, pending items, scheduled release, object states, weekly routine, team
type: help
---
# A release calendar

This page is for producers and show teams who release on a schedule. Exponential has no calendar view of planned
episodes; the calendar lives in your planning tool or a shared document. What the site gives you is the state of
every episode: draft, waiting, published, and, if you set them up, your own production stages. This page puts the two
together.

## Contents

1. [What the site tells you](#what-the-site-tells-you)
2. [A calendar template](#a-calendar-template)
3. [A weekly routine](#a-weekly-routine)
4. [Seasons and breaks](#seasons-and-breaks)
5. [When a date slips](#when-a-date-slips)

## What the site tells you

| Question | Where to look |
|---|---|
| What is being written? | **My drafts** of each editor ([Publishing, versions and the trash](../publishing-and-versions.md#my-drafts)) |
| What waits for approval or a date? | **My pending items**, and **Setup > Workflow processes** for administrators ([Workflows](../../guides/workflows.md)) |
| What stage is each episode in? | the object state, if your site uses states ([Production stages with object states](production-stages-with-object-states.md)) |
| What went out last? | the show folder sorted by published date, or the feed |

The broader planning practice for a site is on [Content calendar](../customers/day-to-day/content-calendar.md), with a
template on [Content calendar template](../customers/day-to-day/content-calendar-template.md).

## A calendar template

Copy this into your planning document. One row per episode:

```text
| No. | Working title | Guests | Recording | Edit done | Notes done | Release (date, time, zone) | Owner | Status on the site |
|-----|---------------|--------|-----------|-----------|------------|----------------------------|-------|--------------------|
| 42  | <title>       | <name> | <date>    | <date>    | <date>     | <date> 06:00 <zone>        | <name>| draft / pending / published |
```

The last column is checked against the site, not typed from memory.

## A weekly routine

**Monday**

- Compare the calendar with the site: every episode due this week exists, at least as a draft.
- Check that last week's episodes are in the feed ([Checking your feed](checking-your-feed.md)).

**Two days before each release**

- The episode is complete: audio, show notes, guests, transcript ([Podcaster checklists](podcaster-checklists.md)).
- It is sent for publishing with its release date and is **pending** ([Scheduling a release](scheduling-a-release.md)).
- If approval is needed, the approver knows ([Approvals before release](approvals-before-release.md)).

**Release day**

- After the first cron run past the release time: the episode is on the site, signed out.
- Within the feed's cache time: it is in the feed.
- Announce it on your channels with the episode's address.

**Friday**

- Remove test items and stale drafts.
- Note in the calendar what slipped and why.

## Seasons and breaks

- Create the next season's folder before its first episode ([Setting up the show structure](setting-up-the-show-structure.md)).
- During a break, keep the feed active; nothing needs to change ([Moving, pausing or ending a show](moving-pausing-or-ending-a-show.md)).
- At the end of a season, check that **Number of objects** of the feed still covers the whole back catalog.

## When a date slips

- If the episode is not ready, do not send it for publishing; keep it as a draft.
- If it is already waiting with an old date and must not go out, ask the administrator to cancel the waiting process
  before the date ([Scheduling a release](scheduling-a-release.md#changing-or-cancelling)).
- If it went out too early, hiding it takes it off the site and out of the feed at the next cache refresh, but apps
  that fetched it in between keep it ([Hiding and retiring episodes](hiding-and-retiring-episodes.md)).

## Related pages

- [Scheduling a release](scheduling-a-release.md)
- [Content calendar](../customers/day-to-day/content-calendar.md)
- [Podcaster checklists](podcaster-checklists.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
