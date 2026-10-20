---
title: "FAQ: performance and Velocity"
audience: government, project-manager, hosting, admin
category: public-sector
order: 190
summary: Answers to the performance questions public bodies ask about Exponential - how fast it is, caching, traffic spikes, Exponential Velocity, other engines, CDNs and how to measure speed yourself - without unverified figures.
keywords: performance FAQ, speed, Velocity, caching, traffic spike, CDN, static cache, benchmark, load, scalability, engines, public sector
type: help
---
# FAQ: performance and Velocity

This page answers the performance questions public bodies ask about Exponential. It gives no speed figures of its own:
measure your own site. The answers come from [Velocity web server](../../../features/6.0/velocity-web-server.md),
[Caches](../../../guides/caches.md) and [Benchmarking](../../../guides/benchmarking.md).

## Contents

1. [How fast is Exponential?](#how-fast-is-exponential)
2. [What is Exponential Velocity?](#what-is-exponential-velocity)
3. [Do we have to use Velocity?](#do-we-have-to-use-velocity)
4. [How does caching work?](#how-does-caching-work)
5. [Can it take a traffic spike?](#can-it-take-a-traffic-spike)
6. [Can we put a CDN in front?](#can-we-put-a-cdn-in-front)
7. [How do we measure it ourselves?](#how-do-we-measure-it-ourselves)

## How fast is Exponential?

It depends on your design, content, caches, engine and hardware. Ask bidders to measure your site under your expected
load, and write the result into acceptance tests. How published figures are measured is explained for the press in
[How Velocity figures are measured](../../press/story-how-velocity-figures-are-measured.md).

## What is Exponential Velocity?

A web server written in PHP that runs Exponential from a pool of long-lived PHP processes. It speaks HTTP/1.1,
HTTP/2 over TLS and WebSockets, serves static files and caches pages in its own process; it needs no Apache, nginx or
PHP-FPM in front, though it works behind them ([Velocity web server](../../../features/6.0/velocity-web-server.md)).

## Do we have to use Velocity?

No. Apache or nginx with PHP-FPM and FrankenPHP are supported too ([8. Serving the site](../../../install/08-serving-the-site.md)).
The project recommends Velocity ([the Exponential glossary](../../../glossary.md#v)). Velocity runs on PHP 8.0 or later (from its release 0.0.4.54; until then 8.1).

## How does caching work?

Several caches work together: view cache for pages, template block caches, and optional static cache and Velocity's
response cache; **Setup > Cache management** lists them ([Caches](../../../guides/caches.md),
[Caching layers](../../hosting/performance/caching-layers.md)).

## Can it take a traffic spike?

Caches carry most of the load. Preload them before an announced event ([Preloading caches](../../../guides/preloading-caches.md)),
consider static cache or a reverse proxy, and run several web nodes for more capacity
([Several web nodes](../../hosting/operations/several-web-nodes.md)). See [Emergency alert readiness](../checklists/emergency-alert-readiness.md).

## Can we put a CDN in front?

Yes; HTTP cache headers and reverse proxies are documented for hosts ([CDN](../../hosting/performance/cdn.md),
[HTTP cache headers](../../hosting/performance/http-cache-headers.md)).

## How do we measure it ourselves?

With `exp:benchmark`: pages over HTTP, comparisons between engines and guards against regressions
([Benchmarking](../../../guides/benchmarking.md), [Benchmarking a hosted site](../../hosting/performance/benchmarking-a-hosted-site.md)).

## Related pages

- [Government FAQ](README.md)
- [Performance and caching](../../creators/website-creators/performance-and-caching.md)
- [Velocity for hosts](../../hosting/servers/velocity-for-hosts.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
