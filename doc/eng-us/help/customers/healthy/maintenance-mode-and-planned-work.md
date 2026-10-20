---
title: Maintenance mode and planned work
audience: customer, admin, editor
category: customer-success
order: 13
summary: How an owner plans work that takes an Exponential site offline - what visitors and search engines see, what to write in the message, who still gets in, what to announce and what to check before and after.
keywords: maintenance mode, planned work, maintenance window, 503, Retry-After, announcement, message, allowed addresses
type: help
---
# Maintenance mode and planned work

Some work, such as an upgrade, a large import or a repair, is safer with the site offline for a short time.
Exponential has a maintenance mode for this. This page is the owner's part: deciding, announcing and checking. The
person doing the work follows [Maintenance mode](../../../guides/maintenance-mode.md), and the business view is in
[Maintenance mode and planned work for businesses](../../businesses/reliability/maintenance-mode-and-planned-work.md).

## What visitors and search engines see

While maintenance mode is on, every page request gets one maintenance page with status `503 Service Unavailable`,
`Retry-After` and `no-store`; images, styles and scripts are still served, and the administration under `/admin`
stays reachable ([Maintenance mode](../../../guides/maintenance-mode.md)). "A 503 with `Retry-After` is the answer
search engines understand as 'come back later': the pages keep their place in the index" (same guide).

The page shows an optional message and an optional expected end. Maintenance "never ends by itself when the expected
time passes": somebody has to bring the site back.

## What the owner decides

| Decision | Notes |
|---|---|
| Whether the work needs the site offline | Many changes do not; ask the person doing the work ([Graceful deploys](../../businesses/reliability/graceful-deploys.md)) |
| When | Outside your busiest hours; never during a campaign or a newsletter send |
| How long, honestly | The page can show the expected end |
| The message | In visitors' words, saying when the site is back |
| Who still sees the site | Your own testers can be allowed by their address |
| Who announces it | Editors, newsletter, social media, as fits your audience |

## A message that works

The guide's example: "We are upgrading the site. Back at 06:00 UTC." Keep it to one or two sentences, give a time
with a time zone, and do not promise more than the person doing the work gave you. For an emergency the guide's
example is: "We are fixing a problem. Back shortly." ([Maintenance mode](../../../guides/maintenance-mode.md)).

## Before the window

- [ ] The person doing the work has a backup and a way back in from the server's shell, should the administration
      be unreachable (the guide's planned upgrade workflow asks for both).
- [ ] Visitors and staff are told.
- [ ] Testers' addresses are given to the person doing the work, if they must see the site during the window.
- [ ] Scheduled sends (newsletters, campaigns) are moved out of the window.
- [ ] Your external monitor is paused, or told to accept the 503 ([Monitoring](monitoring.md)).

## After the window

- [ ] The site answers for everyone, through every server that serves it (the guide's safety checklist).
- [ ] Caches in front of the site were cleared if content changed.
- [ ] You ran your test list ([Upgrade day checklist](upgrade-day-checklist.md)).
- [ ] The monitor is on again.

## The record

"Every switch on and off is written to the audit (`system.maintenance.change`); the page lists the latest eight"
([Maintenance mode](../../../guides/maintenance-mode.md)). So the times the site was offline, and who switched it,
are on record for your monthly report.

> **Note**
> Maintenance mode concerns web requests only. Commands and cronjobs keep running
> ([Maintenance mode](../../../guides/maintenance-mode.md)); the person doing the work stops the ones that must not run.

## Related pages

- [Upgrade day checklist](upgrade-day-checklist.md)
- [When the site is down](when-the-site-is-down.md)
- [Maintenance mode (feature reference)](../../../features/6.0/maintenance-mode.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
