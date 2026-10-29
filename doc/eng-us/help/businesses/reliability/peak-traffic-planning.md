---
title: Peak traffic planning
audience: businesses, admin, project-manager, sales-marketing
category: business-solutions
order: 130
summary: How to prepare an Exponential site for a sale, a campaign, a newsletter send or a press mention, from the calendar and a measurement of your own pages to caches, warming, a change freeze and a plan for the day itself.
keywords: peak traffic, traffic spike, sale, campaign, newsletter send, capacity, benchmark, exp:benchmark, preload, warm, change freeze, load
type: help
---
# Peak traffic planning

This page is for owners, marketing managers and operators who know a busy day is coming: a seasonal sale, a product
launch, a newsletter to all members, a television or press mention. It gives a plan that starts weeks ahead and ends
with a review afterwards. Shop owners will find the store-specific details on
[Store performance at peak times](../stores/store-performance-at-peak-times.md).

## Contents

1. [Know your peaks](#know-your-peaks)
2. [Measure before you guess](#measure-before-you-guess)
3. [What changes under load](#what-changes-under-load)
4. [Weeks before](#weeks-before)
5. [Days before](#days-before)
6. [On the day](#on-the-day)
7. [After the peak](#after-the-peak)
8. [Checklist](#checklist)

## Know your peaks

Many peaks are caused by the business itself, which means they can be planned:

| Peak | Who knows it first |
|---|---|
| A sale or a discount campaign | Sales and marketing |
| A newsletter or mailing to all members ([Newsletters](../newsletters/README.md)) | Whoever sends it; sending can be scheduled at a quiet hour |
| A product launch or an event registration opening | The project lead |
| A press or broadcast mention | Communications; often with little notice |
| Seasonal business (holidays, the start of a school year, tax deadlines) | Everyone, from last year |

Put these dates in one shared calendar that the operator reads. A peak nobody told the operator about is the most
common cause of a bad day.

## Measure before you guess

No general figure tells you what your site can carry; your pages, hardware and database decide it. Exponential ships
a measuring tool, `exp:benchmark` ([Benchmarking](../../../guides/benchmarking.md#in-short)):

- `php bin/php/console exp:benchmark --allow-root-user` requests the front page, menu pages, the search page and the
  administration login page and prints median, p90 and p99 times.
- `--cold` measures rendered pages instead of cache hits, which is closer to what signed-in customers get.
- `--save=run.json` keeps a run and `--baseline=run.json` compares with it, exiting 1 on a regression.

The guide warns: "One run on a busy machine proves little: compare runs made back to back, with the same counts, the
same pages and the same PHP set-up." Measure on a copy of the site or at a quiet hour, and record the result as your
baseline.

## What changes under load

| Visitors | What serves them | What to plan |
|---|---|---|
| Anonymous visitors on public pages | Caches: the response cache, the static cache, the view cache ([Caching for availability](caching-for-availability.md)) | Make sure the caches are on and warm |
| Signed-in customers, baskets, checkout, forms | Rendered pages; the response cache never serves signed-in visitors | Enough PHP workers and database capacity |
| Search | The search engine or the built-in search | Test search under load; it is often the slowest page |
| Editors publishing during the peak | Publish work, cache clears | Avoid large publishing runs during the peak |

On SQLite, writes queue for a single lock; "For many editors publishing at once use MySQL or PostgreSQL"
([Operating a site, know your database driver](../../../guides/operating-a-site.md#7-know-your-database-driver)). A
busy shop checkout also writes; see [Database choice for reliability](database-choice-for-reliability.md).

## Weeks before

- [ ] The peak is in the shared calendar, with an expected time and duration.
- [ ] A baseline measurement exists, and a measurement of the pages the campaign links to.
- [ ] **Setup > System information** shows no failed check, OPcache is on, and memory limits are not low
      ([System information, health checks](../../../guides/system-information.md#3-health-checks)).
- [ ] The landing pages are public pages that can be cached; personal content is kept off them.
- [ ] If the site is on Velocity, the worker count and response cache are reviewed with your operator
      ([Velocity for businesses](velocity-for-businesses.md)).
- [ ] If one machine is not enough, the hosting partner is asked early: adding servers takes time
      ([Clustering and high availability](clustering-and-high-availability.md)).
- [ ] Any upgrade is done well before, not in the last week ([Upgrades without surprises](upgrades-without-surprises.md)).

## Days before

- [ ] **Change freeze**: no deploys, upgrades or settings changes from an agreed date until after the peak.
- [ ] A fresh backup has been taken and the last restore drill was successful ([Restore drills](restore-drills.md)).
- [ ] Campaign content is published and checked through the preview
      ([Preview before you publish](preview-before-you-publish.md)).
- [ ] Heavy scheduled work (imports, index rebuilds, large content jobs) is moved away from the peak hours
      ([Cronjobs and background jobs](cronjobs-and-background-jobs.md)).
- [ ] The incident plan is printed and the people on duty are named
      ([Incident response for small teams](incident-response-for-small-teams.md)).

## On the day

1. **Warm the caches** before the peak starts, so the first visitors do not pay for the render
   ([Preloading caches](../../../guides/preloading-caches.md)):

   ```bash
   php bin/php/preload.php --apply --siteaccess=site --allow-root-user
   ```

   Use the name of your own public siteaccess.
2. **Watch**: the external monitor, the error digest, and on Velocity the dashboard at `/Q/dashboard`, which shows
   live requests, workers and status codes ([Velocity control panel](../../../features/6.0/velocity-control-panel.md)).
3. **Do not clear all caches** during the peak unless something is wrong; every full clear makes the next visitors
   wait for renders.
4. **Have a fallback message ready**: if the site cannot keep up, a short maintenance window with a clear message and
   an expected end is better than errors ([Maintenance mode and planned work](maintenance-mode-and-planned-work.md)).

## After the peak

- Compare what happened with the plan: response times from the monitor, errors in the digest, any customer complaints.
- Note the busiest hour and how the site behaved; this is next year's planning input.
- Lift the change freeze and catch up on postponed work.
- If the site struggled, discuss with your partner whether caching, workers, the database or more servers would help,
  and measure again after each change.

## Checklist

- [ ] Peak dates are known to the operator weeks ahead.
- [ ] A measured baseline exists; the campaign pages were measured.
- [ ] Caches are on and warmed before the peak.
- [ ] A change freeze and a fresh backup cover the peak.
- [ ] Someone watches during the peak, and knows the incident plan.
- [ ] A short review is written afterwards.

## Related pages

- [Caching for availability](caching-for-availability.md)
- [Store performance at peak times](../stores/store-performance-at-peak-times.md)
- [Slow site runbook](../../hosting/runbooks/slow-site.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
