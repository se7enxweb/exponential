---
title: Scheduling a release
audience: podcaster
category: podcasting-streaming
order: 27
summary: How to release an episode at a set date and time with Exponential's Wait until date workflow event - what the administrator sets up once, what the producer does per episode, when the episode really appears on the site and in the feed, and how to check it.
keywords: schedule episode, release date, publish later, wait until date, ezwaituntildate, workflow, cronjob, workflow.php, frequent, pending, published date, embargo, feed timing
type: help
---
# Scheduling a release

This page is for producers who want an episode to go out at a set time without anyone pressing a button. Exponential
does it with the **Wait until date** workflow event (`kernel/classes/workflowtypes/event/ezwaituntildate/ezwaituntildatetype.php`).
It is not active out of the box: an administrator sets it up once.

## Contents

1. [How it works](#how-it-works)
2. [One-time setup](#one-time-setup)
3. [Scheduling an episode](#scheduling-an-episode)
4. [When it really appears](#when-it-really-appears)
5. [Changing or cancelling](#changing-or-cancelling)
6. [Checks](#checks)

## How it works

1. The episode class has a date field, for example **Release date** (`ezdatetime`).
2. A workflow with the *Wait until date* event runs before publishing.
3. When an editor sends the episode for publishing and the date is in the future, the event records "Event delayed
   until" the date and the version waits.
4. The workflow cronjob (`workflow.php`, cronjob part `frequent`) checks waiting versions and publishes the episode at
   its first run after the date.
5. If the event's option to change the published date is on, the item's **published** date is set to the release
   date. That date orders the feed.

If the date field is empty or already past, the episode is published at once.

## One-time setup

For the administrator, following [the ezwaituntildate example](../../examples/workflow-events/ezwaituntildate.md):

1. Add the date field to the episode class ([Building an episode class](building-an-episode-class.md)).
2. **Setup > Workflows**: create a workflow and add the event **Wait until date**. In its settings, choose the
   episode class and its date field, and decide on the option that sets the published date.
3. **Setup > Triggers**: connect the workflow to `content / publish / before`.
4. Make sure the `frequent` cronjob part runs `workflow.php`, and know how often it runs
   ([Cronjobs](../../guides/cronjobs.md)).

The full picture of scheduled content, including hiding and removing at a date, is on
[Scheduled publishing](../customers/day-to-day/scheduled-publishing.md).

## Scheduling an episode

1. Prepare the episode completely: audio, show notes, guests ([Publishing an episode](publishing-an-episode.md)).
2. Fill in **Release date** with the date and time of release.
3. Press **Send for publishing**. The version is now **pending**: it is in **My pending items**, not on the site.

Do not leave a scheduled episode as a draft: a draft is never published by the cronjob.

## When it really appears

| Step | Delay |
|---|---|
| The release time passes | - |
| The next run of the `frequent` cronjob part | up to its interval, as set in the server's crontab |
| The episode is published on the site | at that run |
| The podcast feed lists it | when the feed cache runs out: up to `CacheTime`, 1200 seconds as shipped |
| Apps show it | on each app's own schedule |

So "release at 6:00" means "on the site at the first cron run after 6:00, in the feed up to 20 minutes later". For a
release that must be exact to the minute, schedule a few minutes early or have someone check.

The time you enter is read by the site in its configured time zone; agree in the team which zone is meant.

## Changing or cancelling

- **Move the date**: while the version waits, the waiting process is listed under **Setup > Workflow processes**.
  Ask your administrator how changes to waiting versions are handled on your site; the safe way is to cancel the
  waiting process and send a version with the new date.
- **Cancel**: an administrator can cancel the waiting workflow process on that page, after a confirmation; the
  episode stays unpublished ([Workflows](../../guides/workflows.md#7-cancelling-and-cleaning-up-processes-safely)).

## Checks

- [ ] The day before: the episode is in **My pending items**, and **Setup > Workflow processes** lists it as waiting.
- [ ] After the release time and the next cron run: the episode page opens signed out.
- [ ] Within the feed's cache time: the feed lists the episode at the top.
- [ ] The published date is the release date, if the option is on.

If nothing happens on the day, the cronjob most likely did not run
([Troubleshooting feeds and media](troubleshooting-feeds-and-media.md)).

## Related pages

- [Scheduled publishing](../customers/day-to-day/scheduled-publishing.md)
- [Workflows](../../guides/workflows.md)
- [A release calendar](a-release-calendar.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
