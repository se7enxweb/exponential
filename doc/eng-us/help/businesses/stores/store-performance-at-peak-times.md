---
title: Store performance at peak times
audience: businesses, hosting, project-manager
category: business-solutions
order: 200
summary: Keeping an Exponential store fast during a sale, a holiday season or a newsletter send - which pages can be cached and which cannot, the levers in order of effect, measuring before the peak, and a plan for the day.
keywords: performance, peak traffic, sale, holiday season, caching, Velocity, response cache, view cache, benchmark, load, slow site, checkout performance
type: help
---
# Store performance at peak times

This page is for owners and project managers who expect busy days: a sale, a holiday season, a newsletter that goes to
every customer at once. It explains why store pages behave differently from other pages under load, what to prepare,
and what to do if the site slows down. The technical details are in
[Performance and caching for websites](../../creators/website-creators/performance-and-caching.md).

## Contents

1. [Why stores are different](#why-stores-are-different)
2. [Pages that can be cached](#pages-that-can-be-cached)
3. [Pages that cannot](#pages-that-cannot)
4. [The levers, in order of effect](#the-levers-in-order-of-effect)
5. [Measure before the peak](#measure-before-the-peak)
6. [The week before](#the-week-before)
7. [On the day](#on-the-day)
8. [After the peak](#after-the-peak)

## Why stores are different

Most visitors of a content site read pages that are the same for everyone, which caches well. A store mixes two kinds
of page: product and category pages that are the same for everyone, and basket, checkout and account pages that are
personal. Peaks hit both, and the personal pages are where money is made.

## Pages that can be cached

Product pages, category pages, landing pages and the home page can be served from cache. Exponential has several cache
layers ([Performance and caching for websites](../../creators/website-creators/performance-and-caching.md#the-layers)):
the Velocity response cache for whole public pages, the content view cache for each page's main area, the template
block cache for menus and footers, compiled templates and OPcache.

The Velocity response cache answers public pages without running PHP, and skips requests that carry a session cookie or
an `Authorization` header, or responses that set a cookie
([Velocity response cache](../../../features/6.0/velocity-response-cache.md)).

## Pages that cannot

| Page | Why |
|---|---|
| Basket, checkout, confirmation | Personal; the visitor has a session |
| Account pages, wishlist, receipts | Personal |
| Pages with forms that carry a form token | The token is per session |
| Payment notifications from the provider | Must reach the application every time |

The CDN page lists the same: pages for logged-in users, baskets, checkout, account pages and forms with a form token
must not be cached by a CDN ([HTTP caching, proxies and CDNs](../../creators/website-creators/http-caching-and-cdns.md)).

So the capacity that matters at peak is: how many personal requests per second can the server handle, after cached
pages have been taken off its hands?

## The levers, in order of effect

From the website creators' performance page, which follows the installation book:

1. **Serve with Velocity** and its response cache; persistent workers keep the application warm
   ([Velocity for businesses](../reliability/velocity-for-businesses.md)).
2. **Keep the content view cache and template block cache on** (both are on in the shipped settings), and warm the
   site after a full clear.
3. **OPcache** for compiled PHP.
4. **APCu** for the response cache's memory tier and the shared query cache.
5. **The SQL query cache**, measured on your site.
6. **The database** itself.

Leave the static cache off; the response cache gives visitors the same benefit without slowing down publishing (same
page). Hosting-side tuning is in [Caching layers](../../hosting/performance/caching-layers.md) and
[PHP-FPM tuning](../../hosting/performance/php-fpm-tuning.md).

## Measure before the peak

Exponential ships a benchmark command that prints median, p90 and p99 response times per page and compares runs
against a baseline ([Performance and caching for websites](../../creators/website-creators/performance-and-caching.md#measure)).
Ask your host or developer to:

- [ ] run it on the live setup a few weeks before the peak and save the result as a baseline;
- [ ] include a product page, a category page and the basket;
- [ ] repeat after every change, on the same machine, back to back;
- [ ] test a few complete checkouts in the payment provider's test mode during a load test, if your host offers one.

Capacity planning in general is in [Peak traffic planning](../reliability/peak-traffic-planning.md).

## The week before

- [ ] No code or design changes in the last days before the peak (a change freeze).
- [ ] Campaign landing pages published, caches warmed.
- [ ] Prices and discount rules set and tested; a test order placed.
- [ ] Cron jobs run, so old baskets are cleaned up ([Cron jobs](../../../guides/cronjobs.md)).
- [ ] Backups verified ([Backups that restore](../reliability/backups-that-restore.md)).
- [ ] Payment provider's status page bookmarked; their support contact at hand.
- [ ] Who to call at the host, and who decides to switch on maintenance mode, written down.
- [ ] Newsletter sends spread out, rather than to every customer in the same minute, if your newsletter tool allows it.

## On the day

| Sign | First step |
|---|---|
| Pages slow for everyone | Follow the host's slow-site runbook ([Slow site](../../hosting/runbooks/slow-site.md)) |
| Site down | [Site down](../../hosting/runbooks/site-down.md) |
| Orders stop with "payment failed" | Check the provider's status; look at the dashboard's Payment failed count |
| Paid orders not marked paid | Notification address blocked? ([PayPal payments](paypal-payments.md#when-something-goes-wrong)) |

Avoid clearing all caches during the peak: every page then has to be rebuilt at the worst moment. Clear only what a
change needs ([Performance and caching for websites](../../creators/website-creators/performance-and-caching.md#clearing-caches-in-the-right-order)).

## After the peak

- Compare the dashboard's revenue per day and abandoned baskets with the weeks before.
- Look at the slowest pages in the request logs ([Request logs](../../hosting/operations/request-logs.md)).
- Write down what went wrong and what to prepare next time ([Incident response for small teams](../reliability/incident-response-for-small-teams.md)).

## Related pages

- [Performance and caching for websites](../../creators/website-creators/performance-and-caching.md)
- [Static cache and the Velocity response cache](../../creators/website-creators/static-cache-and-velocity.md)
- [Caching for availability](../reliability/caching-for-availability.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
