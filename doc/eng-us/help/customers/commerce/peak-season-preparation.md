---
title: Peak season preparation
audience: customer, businesses, hosting
category: customer-success
order: 230
summary: A store owner's plan for a sale, a holiday season or a big newsletter send on Exponential - six weeks out, two weeks out, the week before, the day itself and the review afterwards - with who does what and what to ask your host.
keywords: peak season, holiday season, sale, campaign, Black Friday, peak traffic, change freeze, load test, benchmark, store preparation, capacity, caching
type: help
---
# Peak season preparation

This page is for the owner and the store manager who expect busy days. It is a timeline that spreads the
preparation over several weeks, so the last days are quiet. Why store pages behave differently under load, and the
technical levers, are on [Store performance at peak times](../../businesses/stores/store-performance-at-peak-times.md);
this page is the plan.

## Why it needs a plan

Product, category and landing pages are the same for everyone and can be served from cache. Basket, checkout,
account pages and payment notifications are personal and cannot
([Store performance at peak times](../../businesses/stores/store-performance-at-peak-times.md)). On a peak day both
kinds get busy, and the personal pages are where the money is made.

## Six weeks before

| Task | Who |
|---|---|
| Fix the dates of the sale or season, and the expected scale (a past peak, a newsletter's size) | Owner |
| Tell the host the dates and the expected scale | Owner |
| Ask the host to measure response times with `exp:benchmark` and keep the result as a baseline, including a product page, a category page and the basket ([Benchmark](../../../features/6.0/benchmark.md)) | Host |
| Decide prices, discounts and shipping terms for the period | Owner |
| Plan the landing pages and newsletters | Marketing |

## Two weeks before

- [ ] Landing pages built and checked as drafts ([Landing pages with layouts](../../creators/website-creators/landing-pages-with-layouts.md)).
- [ ] Discount rules prepared, not yet active ([Discounts](discounts.md)).
- [ ] Price changes prepared as drafts ([Prices and currencies](prices-and-currencies.md)).
- [ ] Any planned updates of Exponential or extensions done now, not later, and tested.
- [ ] Payment provider asked whether they expect maintenance on your dates.
- [ ] Extra hands for packing and customer service arranged.

## The week before

From [Store performance at peak times](../../businesses/stores/store-performance-at-peak-times.md), with names added:

| Task | Who |
|---|---|
| Change freeze: no code, design or settings changes until after the peak | Owner tells partner and host |
| Landing pages published; caches warmed by the host | Marketing, host |
| A test order end to end, with payment | Store manager |
| Cron jobs run, so old baskets are cleaned up ([Cron jobs](../../../guides/cronjobs.md)) | Host |
| Backups verified ([Backups that restore](../../businesses/reliability/backups-that-restore.md)) | Host |
| Payment provider's status page and support contact at hand | Store manager |
| Who decides on maintenance mode, and who calls the host, written down | Owner |
| Newsletter sends spread out, if your newsletter tool allows it | Marketing |

## On the day

| Time | Task | Who |
|---|---|---|
| Start | Discounts and prices go live; check one product in the basket | Store manager |
| Every hour | Dashboard: orders, Payment failed, open orders | Store manager |
| Every few hours | Payments in the provider's account match orders | Owner or store manager |
| If pages are slow | Call the host; they follow their slow-site runbook ([Slow site](../../hosting/runbooks/slow-site.md)) | Store manager |
| If the site is down | [Site down](../../hosting/runbooks/site-down.md) | Host |
| End | Discounts and sale prices switched back | Store manager |

> **Warning**
> Do not clear all caches during the peak: every page then has to be rebuilt at the worst moment. Clear only what a
> change needs, and leave that to the host ([Store performance at peak times](../../businesses/stores/store-performance-at-peak-times.md)).

## Afterwards

- [ ] Revenue per day and abandoned baskets compared with the weeks before ([Store reports](store-reports.md)).
- [ ] Orders of the peak all moved through their statuses; nothing stuck in Pending.
- [ ] The host's notes on the slowest pages and any errors.
- [ ] What went wrong, and what to prepare earlier next time, written down while it is fresh
  ([Incident response for small teams](../../businesses/reliability/incident-response-for-small-teams.md)).
- [ ] The change freeze lifted, and postponed changes scheduled.

## What to ask your host

- What capacity do we have for basket and checkout requests, after cached pages are taken off the server?
- Is the site served with Velocity and its response cache, and are the view and template block caches on
  ([Velocity for businesses](../../businesses/reliability/velocity-for-businesses.md))?
- Can you run a load test on a copy, with a few complete checkouts in the provider's test mode?
- Who is on call on our dates, and how do we reach them?

## Related pages

- [Store performance at peak times](../../businesses/stores/store-performance-at-peak-times.md)
- [Peak traffic planning](../../businesses/reliability/peak-traffic-planning.md)
- [Before a campaign](../value/checklists/before-a-campaign.md)
- [Store weekly checklist](store-weekly-checklist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
