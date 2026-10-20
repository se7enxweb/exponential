---
title: "Talking points: speed and Velocity"
audience: advocate
category: advocacy
order: 19
summary: How to talk about Exponential's speed and Exponential Velocity without invented benchmarks, with sourced points on the persistent-worker engine, caches, HTTPS and HTTP/2, the engines side by side, and how to measure and report your own numbers honestly.
keywords: talking points, speed, performance, Velocity, persistent workers, FrankenPHP, PHP-FPM, HTTP cache, response cache, query cache, HTTP/2, HTTPS, benchmark, measurement
type: help
---
# Talking points: speed and Velocity

This page is for advocates who are asked "is it fast?" Speed is the topic where invented numbers do the most damage.
Describe **how** Exponential is made fast, and if you quote a number, say exactly how you measured it.

## Contents

1. [What Velocity is](#what-velocity-is)
2. [The points](#the-points)
3. [Numbers: measure, do not quote](#numbers-measure-do-not-quote)
4. [Do not say](#do-not-say)
5. [Questions you will hear](#questions-you-will-hear)

## What Velocity is

"A pure PHP web server. No nginx, no Apache, no php-fpm. One process serves static files, PHP scripts, WebSocket
connections, and a live dashboard" (README of `se7enxweb/exponential-velocity`, quoted on
[Exponential Velocity](../who-is-the-exponential-foundation.md#exponential-velocity)). It keeps the application loaded
between requests and "began as a fork of the Qbix web server engine". It is MIT licensed.

## The points

| Say | Source |
|---|---|
| "Velocity is the recommended engine for every stage, from development to production. FrankenPHP is the production-ready alternative." | [Velocity, Why you would use it](../../features/6.0/velocity-persistent-worker-server.md#why-you-would-use-it) |
| "It keeps the application loaded between requests, so each request does not start from zero." | [Exponential Velocity](../who-is-the-exponential-foundation.md#exponential-velocity) |
| "HTTPS from the first start, with a self-signed certificate made for you; HTTP/2 is built in." | [Velocity, Why you would use it](../../features/6.0/velocity-persistent-worker-server.md#why-you-would-use-it) |
| "Whole pages can be answered before the kernel starts, from the role-aware HTTP cache and the server's response cache." | same |
| "An SQL query cache reduces database queries per page." | same |
| "One command deploys a PHP change." | [Velocity, Deploy a PHP change in one command](../../features/6.0/velocity-persistent-worker-server.md#deploy-a-php-change-in-one-command) |
| "There is a benchmark command, so you can measure your own site." | [Benchmarking](../../guides/benchmarking.md) |
| "Velocity is young: first commits in July 2026, first release in September 2026." | [Exponential Velocity](../who-is-the-exponential-foundation.md#exponential-velocity) |

The last point is worth saying yourself: it shows you are honest, and it lets people decide how to roll it out.

## Numbers: measure, do not quote

If you want to talk about numbers:

1. Measure on a machine you control, with the [benchmark guide](../../guides/benchmarking.md).
2. Write down: the hardware, the PHP version, the engine, the database, the caches on or off, the page measured, the
   tool and the number of requests.
3. Publish the method with the result, and say "on my machine".
4. Never compare with another product's numbers that were measured differently
   ([Respecting other projects and companies](respecting-other-projects-and-companies.md)).

```text
MEASUREMENT NOTE                                   Date: <date>
Machine        <CPU, cores, memory>      OS: <distribution>
PHP            <version>                 Engine: <Velocity | FrankenPHP | PHP-FPM + web server>
Database       <engine, version>         Caches: <which on, which off>
Page           <path, logged in or not>
Tool           <name, options>           Requests: <n>, concurrency <n>
Result         <median, 95th percentile>
Caveats        <anything unusual>
```

## Do not say

- "N times faster than X." Unless you measured both yourself, the same way, and publish the method.
- "Velocity is production-proven at scale." The sources do not say that; it is young.
- "You do not need a cache." Caches are part of how it is fast.
- "It runs on any hosting." Velocity usually cannot run on shared hosting
  ([Containers, cloud and hosting panels](../os-support.md#containers-cloud-and-hosting-panels)).

## Questions you will hear

| Question | Answer |
|---|---|
| "Do I have to use Velocity?" | No. FrankenPHP, Apache or nginx with PHP-FPM also serve Exponential ([Engines side by side](../../features/6.0/velocity-persistent-worker-server.md#engines-side-by-side)). |
| "What is the default engine?" | The shipped default in `settings/velocity.ini` is PHP's built-in server, because it needs nothing installed; change it for any real site ([Velocity](../../features/6.0/velocity-persistent-worker-server.md#why-you-would-use-it)). |
| "Where do I report a Velocity bug?" | The Velocity tracker ([Which tracker for which report](which-tracker-for-which-report.md)). |
| "Are there limits?" | Yes, documented in [Limits and cautions](../../features/6.0/velocity-persistent-worker-server.md#limits-and-cautions). |

## Related pages

- [Talking points for administrators](talking-points-for-administrators.md)
- [Claims you must not make](claims-you-must-not-make.md)
- [Velocity](../../features/6.0/velocity-persistent-worker-server.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
