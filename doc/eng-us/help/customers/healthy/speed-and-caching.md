---
title: Speed and caching
audience: customer, editor, businesses
category: customer-success
order: 14
summary: What an owner needs to know about the caches of an Exponential site - why a change sometimes does not show at once, what to report when the site is slow, what is on by default, and what to ask your host before a busy day.
keywords: speed, slow site, cache, caching, stale page, response cache, Velocity, static cache, preload, benchmark
type: help
---
# Speed and caching

Caches make the site fast and keep it standing under load. They also explain the two most common owner
questions: "why is the site slow?" and "why does my change not show?". This page answers both from the owner's
side. The technical pages are [Caches](../../../guides/caches.md) and
[Caching for availability](../../businesses/reliability/caching-for-availability.md).

## The layers in one table

From [Caching for availability](../../businesses/reliability/caching-for-availability.md):

| Layer | In plain words | Shipped |
|---|---|---|
| Browser cache | The visitor's browser keeps images, styles and scripts | Always |
| Velocity response cache | Velocity answers repeat requests for public pages itself | On when Velocity serves the site |
| Content view cache | The rendered content of each page, per language and role | On |
| Template-block cache | Parts of the page layout, such as menus | On |
| Static cache | Public pages stored as plain files | Off |
| Role-aware HTTP cache | Whole pages per permission context | Off until switched on |

A content delivery network or caching proxy in front of the site is optional and not part of Exponential.

## "My change does not show"

- **Wait a few seconds and reload.** Velocity answers pages "from its own response cache for a few seconds"
  ([Caches](../../../guides/caches.md)).
- **Check the version was published**, not left as a draft ([Publishing and versions](../../publishing-and-versions.md)).
- **Try a private window**, which ignores your browser's cache.
- If it still does not show, tell your administrator the page address and the time you published. Clearing caches is
  their job: "Clear the smallest thing that fixes the problem", because clearing everything "costs the next visitors a
  slow page each" ([Caching for availability](../../businesses/reliability/caching-for-availability.md)).

## "The site is slow"

Report it in a way that can be measured:

| Tell them | Why |
|---|---|
| Which pages, or all of them | One slow page is a different problem from a slow site |
| Since when | A change around that time is the first suspect |
| Signed in or not | Signed-in pages are cached differently |
| From where | Your office network, a phone, another country |
| How slow, roughly | "Ten seconds for the front page" can be compared later |

The hosting runbook for a slow site starts with "measure first", separating cold from warm pages, before any change
([Slow site](../../hosting/runbooks/slow-site.md)). After a clear or a deploy "the first visitor pays for the render";
the administrator can warm the pages with **Preload Sites** ([Preloading caches](../../../guides/preloading-caches.md)).

## Before a busy day

A sale, a campaign, a newsletter or a press mention can multiply traffic. Ask your host, weeks ahead
([Peak traffic planning](../../businesses/reliability/peak-traffic-planning.md)):

- [ ] Have you measured our main pages under load ([Benchmarking](../../../guides/benchmarking.md))?
- [ ] Are the caches on that should be on? **Setup > System information** warns when view caching or template caching
      is off ([System information](../../../guides/system-information.md)).
- [ ] Will you avoid clearing caches and deploying during the peak?
- [ ] Who is on call that day?

## What not to ask for

- "Clear all caches" as a routine fix. It makes the next pages slow and rarely fixes the real problem.
- Turning a cache off to see changes faster. Editors preview their work instead
  ([Preview before you publish](../../businesses/reliability/preview-before-you-publish.md)).

## Related pages

- [Static and response cache](../../businesses/reliability/static-and-response-cache.md)
- [Velocity for businesses](../../businesses/reliability/velocity-for-businesses.md)
- [Caching layers (for hosts)](../../hosting/performance/caching-layers.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
