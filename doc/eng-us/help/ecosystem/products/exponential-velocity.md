---
title: Exponential Velocity in the ecosystem
audience: ecosystem, admin, hosting, developer
category: ecosystem
order: 12
summary: Exponential Velocity's place in the ecosystem - a web and application server written in PHP, part of Exponential since 6.0.15, its three engines, why it is faster, when a traditional server is still the right answer, and where it is developed.
keywords: Velocity, application server, web server, Qbix, FrankenPHP, PHP built-in server, persistent workers, response cache, HTTPS, exponential-velocity, engines
type: help
---
# Exponential Velocity in the ecosystem

**"Velocity is a web server written in PHP, bundled with Exponential, that keeps the application loaded between
requests"** (`README.md`, "Exponential Velocity — the fast option").

## Contents

1. [Its place in the ecosystem](#its-place-in-the-ecosystem)
2. [The three engines](#the-three-engines)
3. [Why it is faster](#why-it-is-faster)
4. [When a traditional server is still right](#when-a-traditional-server-is-still-right)
5. [Where it is developed](#where-it-is-developed)

## Its place in the ecosystem

- "As of 6.0.15 Velocity is part of Exponential itself. `composer.json` requires
  `se7enxweb/exponential-velocity ~0.0.4.55`, so `composer install` installs the server with the rest of the product"
  (`README.md`).
- It "is based on the Qbix server and is developed alongside Exponential, so the two are tuned against each other"
  (same).
- It is MIT licensed ([Exponential Velocity](../../who-is-the-exponential-foundation.md#exponential-velocity)).
- The whole story: [Velocity is part of Exponential 6.0.15](../../velocity-is-part-of-exponential-6-0-15.md).

## The three engines

| Engine | Role, as the README states it |
|---|---|
| Qbix | Velocity's own server: "The recommended engine for every stage, from development to production, and the fastest"; serves HTTP and HTTPS itself |
| FrankenPHP | Caddy with PHP built in, one checksum-verified binary: "The production-ready alternative" |
| PHP's built-in server | Nothing to install; for development, "and the shipped default because it always works" |

The engine is chosen with `[ServerSettings] Engine` in `velocity.ini`. Details:
[Velocity engines](../../../bc/6.0/velocity-engines.md).

## Why it is faster

Velocity "loads the framework once into a parent process and forks workers that share it", and adds "a response
cache that answers a repeat request without waking the application at all" (`README.md`). The README also says:
"Most of the speed came from fixing Exponential and the cache, not from the server process model", so 6.0.15 "is fast
under load on Apache or Nginx too".

## When a traditional server is still right

From `README.md`, "When a traditional server is still the right answer":

- you need per-site operating-system isolation (run one Velocity instance per site instead);
- your hosting will not let you run a long-lived process, as is usual on shared hosting;
- you already have a tuned Nginx or Apache in front (Velocity is happy behind a reverse proxy).

## Where it is developed

- Repository and issues: [se7enxweb/exponential-velocity](https://github.com/se7enxweb/exponential-velocity).
- History: [Velocity chronicle](../../../history/velocity/README.md).
- Feature pages: [Persistent-worker server](../../../features/6.0/velocity-persistent-worker-server.md),
  [Response cache](../../../features/6.0/velocity-response-cache.md),
  [HTTPS certificates](../../../features/6.0/velocity-https-certificates.md).

## Related pages

- [Products](README.md)
- [Web servers and engines](../php-stack/web-servers-and-engines.md)
- [Velocity for businesses](../../businesses/reliability/velocity-for-businesses.md)
- [Talking points: speed and Velocity](../../advocates/talking-points-speed-and-velocity.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
