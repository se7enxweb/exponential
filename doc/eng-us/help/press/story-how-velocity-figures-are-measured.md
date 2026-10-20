---
title: "Story: how the Velocity figures are measured"
audience: press
category: press-media
order: 57
summary: The speed figures published for Exponential Velocity (0.36 ms per cached page, 8,941 requests per second) with the conditions they were measured under, the before-and-after table they come from, how to reproduce them with exp:benchmark, and how to print them honestly.
keywords: story, Velocity, benchmark, exp:benchmark, figures, 0.36 ms, 8941, requests per second, measurement, methodology, press, fact-checking
type: help
---
# Story: how the Velocity figures are measured

This page is for journalists, reviewers and fact-checkers who want to quote a speed figure for Exponential. Speed
numbers are only true together with their conditions. This page gives the published figures, where they come from,
what they mean, and how a reporter can measure their own.

## Contents

1. [The story in one paragraph](#the-story-in-one-paragraph)
2. [The published figures](#the-published-figures)
3. [Where they come from](#where-they-come-from)
4. [Measuring yourself](#measuring-yourself)
5. [What not to claim](#what-not-to-claim)
6. [Angles and headline ideas](#angles-and-headline-ideas)
7. [Who to ask](#who-to-ask)
8. [Related pages](#related-pages)

## The story in one paragraph

The README of Exponential publishes three figures for Velocity and states that they are "reproducible with scripts
that ship with the project — they are not marketing numbers, and the methodology is written down alongside them"
([README](https://github.com/se7enxweb/exponential/blob/main/README.md#how-exponential-is-served)). The same text adds the most important caveat itself: "Most of
the speed came from fixing Exponential and the cache, not from the server process model." A fair story reports both:
the figures and that the largest gains came from cache fixes that apply on Apache and Nginx too.

## The published figures

| Figure, as published | What the README says it means |
|---|---|
| "0.36 ms per cached page" | "the server's own work to answer a cached page — measured as a median over hundreds of requests, not a best case" |
| "8,941 requests per second sustained" | "measured on a 12-core machine serving a real page, with zero errors at every concurrency tried, up to 384 connections in flight" |
| "repeat visits never touch the network" | "refers to the optional navigation cache (a service worker)" in the visitor's browser |

Source for all three: [README, How Exponential is served](https://github.com/se7enxweb/exponential/blob/main/README.md#how-exponential-is-served).

## Where they come from

The before-and-after table of the response cache work lists the same numbers next to their starting points
([The response cache](../../bc/6.0/response-cache-and-navigation.md#what-this-adds-up-to)):

| Measure | Before | After |
|---|---|---|
| Cached page, server side | 0.36 ms | 0.36 ms |
| CPU per cached request | 3.80 ms | 0.54 ms |
| Peak throughput | 2,961 req/s | 8,941 req/s |
| p99 at 128 concurrent | 1,317 ms | 37.9 ms |
| Reload of a page the browser holds | 9.7 KB | 202 bytes |
| A cached 404 | 758 ms | 27 ms |

The same page gives the two numbers "everything sits between": a cache hit of 0.36 ms and a full render of 1382 ms
([The two numbers](../../bc/6.0/response-cache-and-navigation.md#the-two-numbers-everything-sits-between)). That is
the context a headline loses: a cached page and a rendered page are very different things.

## Measuring yourself

Exponential ships `exp:benchmark`, which "needs nothing but PHP with the curl extension"
([Benchmark](../../features/6.0/benchmark.md)). The guide's own sample output was made on a demonstration server with
"12 CPUs, shared with other work at a load of 5 to 11" ([Benchmarking guide](../../guides/benchmarking.md)).

```text
./console exp:benchmark --allow-root-user                       # front page, menu pages, admin login
./console exp:benchmark --compare=https://site,https://site:8080 --allow-root-user   # two servers back to back
./console exp:benchmark --cold --allow-root-user                # rendered pages instead of cache hits
```

Write down the machine, PHP version, engine, database, caches, pages and request counts with every result
([Talking points: speed](../advocates/talking-points-speed-and-velocity.md#numbers-measure-do-not-quote)). The guide
warns that "One run on a busy machine proves little"
([Benchmarking, In short](../../guides/benchmarking.md#in-short)).

## What not to claim

- "Exponential answers every page in 0.36 ms." It is the server's work for a **cached** page.
- "Velocity is N times faster than X." No published comparison with another product exists.
- "Independently verified." The sources describe the project's own measurements and scripts.
- A figure without its conditions (machine, cached or rendered, concurrency).

## Angles and headline ideas

```text
"0.36 milliseconds - for a cached page: reading Exponential's speed claims carefully"
"The speed came from the cache, not the server, says Exponential's own README"
"We ran Exponential's benchmark ourselves: here is our method"
```

## Who to ask

| Question | Ask | Where |
|---|---|---|
| The scripts and conditions behind a figure | 7x | info@se7enx.com, or <https://exponential.earth/about/contact-us> |
| Your own measurement in public | the community | <https://share.exponential.earth/forums> |

## Related pages

- [Story: Exponential Velocity](story-velocity.md)
- [Benchmarking Exponential](../../guides/benchmarking.md)
- [Talking points: speed and Velocity](../advocates/talking-points-speed-and-velocity.md)
- [Feature stories: index](feature-stories-index.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
