---
title: Velocity for businesses
audience: businesses, project-manager, hosting, admin
category: business-solutions
order: 90
summary: What Exponential Velocity, the application server bundled with Exponential, gives a business in plain words, the documented figures with their context, what it needs from your hosting, and when a traditional web server is still the right choice.
keywords: Velocity, Exponential Velocity, application server, persistent workers, response cache, performance, graceful restart, watchdog, Qbix, FrankenPHP, hosting requirements
type: help
---
# Velocity for businesses

This page is for owners and managers who hear "Velocity" from their developer or hosting partner and want to know
what it means for the business. It explains what Velocity is, the figures the project documents and how to read
them, what it does for availability, what it needs from your hosting, and when a traditional web server remains the
better answer. The technical entry point is
[Velocity: running Exponential in a persistent-worker web server](../../../features/6.0/velocity-persistent-worker-server.md).

## Contents

1. [What Velocity is](#what-velocity-is)
2. [The documented figures, read honestly](#the-documented-figures-read-honestly)
3. [Speed without Velocity](#speed-without-velocity)
4. [What it does for availability](#what-it-does-for-availability)
5. [What it needs from your hosting](#what-it-needs-from-your-hosting)
6. [When a traditional server is the right answer](#when-a-traditional-server-is-the-right-answer)
7. [Questions to ask your developer or host](#questions-to-ask-your-developer-or-host)

## What Velocity is

From the [README, "How Exponential is served"](https://github.com/se7enxweb/exponential/blob/main/README.md#exponential-velocity--the-fast-option):
"Velocity is a web server written in PHP, bundled with Exponential, that keeps the application loaded between
requests." It is based on the Qbix server and developed alongside Exponential.

In plain words: a traditional setup starts the application afresh for every page request and throws the work away
afterwards. Velocity loads the application once and keeps workers ready, and it adds a response cache that answers a
repeat request without waking the application at all (README, "Why it is faster").

Velocity can run three engines, chosen with one setting
([README, "Engines"](https://github.com/se7enxweb/exponential/blob/main/README.md#engines-frankenphp-phps-built-in-server-or-qbix)):

| Engine | Role, as the README states it |
|---|---|
| Qbix | "The recommended engine for every stage, from development to production, and the fastest" |
| FrankenPHP | "The production-ready alternative" |
| PHP's built-in server | "For development, and the shipped default because it always works" |

## The documented figures, read honestly

The README states: "**0.36 ms per cached page, 8,941 requests per second sustained, and repeat visits never touch
the network.**" It then explains each figure, and the explanations matter as much as the numbers
([README, "What that measures as"](https://github.com/se7enxweb/exponential/blob/main/README.md#what-that-measures-as)):

| Figure | What it measures, per the README |
|---|---|
| 0.36 ms | The server's own work to answer a **cached** page, as a median over hundreds of requests, not a best case |
| 8,941 requests per second | Sustained, measured on a 12-core machine serving a real page, with zero errors at every concurrency tried, up to 384 connections in flight |
| Repeat visits never touch the network | Refers to the **optional** navigation cache (a service worker) in the visitor's browser |

The README adds that these are reproducible with scripts that ship with the project, with the methodology written
down. What the figures do **not** say:

- They are not a promise for your server, your pages or your traffic. Hardware, page weight, database and how much
  of your traffic is cacheable all change the result.
- A signed-in customer (a shop customer with a basket, a member, an editor) is never served from the response cache
  ([settings/velocity.ini](../../../../../settings/velocity.ini), `[CacheSettings]`: "Signed-in visitors are never served
  from the cache"). Their pages are rendered, though by workers that keep the application loaded.

To measure your own site, your operator can use `exp:benchmark`, which compares two servers back to back
([Benchmarking](../../../guides/benchmarking.md)).

## Speed without Velocity

The README is explicit that "Most of the speed came from fixing Exponential and the cache, not from the server
process model", and that "Exponential 6.0.15+ is fast under load on Apache or Nginx too"
([README, "The part that matters even if you never run Velocity"](https://github.com/se7enxweb/exponential/blob/main/README.md#the-part-that-matters-even-if-you-never-run-velocity)).
Among those fixes: a returning visitor's reload answered with 202 bytes instead of the whole page, and an expired
page rebuilt by one request while everyone else is served the existing copy. See
[Caching for availability](caching-for-availability.md).

So the choice of Velocity is about raising the ceiling, not about whether the site is usable at all.

## What it does for availability

| Feature | What it means for the business | Source |
|---|---|---|
| Graceful reload | `graceful` "re-executes the server without releasing the listening socket, so a deploy does not drop requests" | [README](https://github.com/se7enxweb/exponential/blob/main/README.md#using-it) |
| Status for monitoring | "`status --json` is there for monitoring"; `status` says `running` only when the server answers | [README](https://github.com/se7enxweb/exponential/blob/main/README.md#using-it), [Velocity engines](../../../bc/6.0/velocity-engines.md#stop-restart-and-status-of-the-qbix-engine) |
| Watchdog | A systemd timer starts the server again after a crash or out-of-memory kill | [Health checks and uptime](../../hosting/operations/health-checks-and-uptime.md#velocity-health-endpoint-and-watchdog) |
| Restart that finishes on its own | A restart whose caller is killed between stop and start no longer leaves the server down | [Velocity engines](../../../bc/6.0/velocity-engines.md#a-restart-or-deploy-that-is-cut-off) |
| One-command deploy | `exp:velocity deploy` runs every step of shipping a PHP change in the right order | [Graceful deploys](graceful-deploys.md) |
| Its own HTTPS | Serves HTTPS itself and swaps a renewed certificate in without a restart | [README](https://github.com/se7enxweb/exponential/blob/main/README.md#when-a-traditional-server-is-still-the-right-answer) |
| Control panel and dashboard | Views of the server that keep working when the application is broken | [Velocity control panel](../../../features/6.0/velocity-control-panel.md) |

## What it needs from your hosting

- **PHP 8.1 or later** ([README, "What version of PHP is required"](https://github.com/se7enxweb/exponential/blob/main/README.md#what-version-of-php-is-required)).
- **A long-lived process.** "Shared hosting usually will not" allow one
  ([README](https://github.com/se7enxweb/exponential/blob/main/README.md#when-a-traditional-server-is-still-the-right-answer)). A VPS, a dedicated server,
  a container or managed hosting that supports it is needed
  ([Choosing a hosting model](../../hosting/models/choosing-a-hosting-model.md)).
- **A restart after code changes.** "Workers keep the application in memory between requests. After changing a
  template, a setting or a class, restart the server — clearing caches alone will not dislodge what a worker already
  holds" ([README, "One thing to know before you rely on it"](https://github.com/se7enxweb/exponential/blob/main/README.md#one-thing-to-know-before-you-rely-on-it)).
  The deploy command does this for you.

## When a traditional server is the right answer

From the README ([When a traditional server is still the right answer](https://github.com/se7enxweb/exponential/blob/main/README.md#when-a-traditional-server-is-still-the-right-answer)):

- **You need per-site operating-system isolation.** "A single server process serving several sites is a routing
  convenience, not a security boundary." Run one Velocity instance per site, each as its own user, or a traditional
  server.
- **Your hosting will not let you run a long-lived process.** Apache or LiteSpeed with `.htaccess` is the answer
  there.
- **You already have a tuned Nginx or Apache in front.** Keep it; Velocity runs happily behind a reverse proxy.

Exponential "does not require any particular" web server (README); Apache, Nginx, LiteSpeed, Caddy, IIS and others
all work. Choosing a traditional server is not a step down in reliability; it is a different set of trade-offs.

## Questions to ask your developer or host

- [ ] Which engine serves our site, and why that one?
- [ ] Is the watchdog installed, and who reads its log?
- [ ] Do deploys use `exp:velocity deploy` or `graceful`, so visitors are not dropped?
- [ ] Is the response cache on, and how long may a page be cached (`[CacheSettings] DefaultTtl`, shipped 30 seconds)?
- [ ] If several sites share one server, does each run as its own operating-system user?
- [ ] Have we measured our own pages, before and after, with `exp:benchmark`?

## Related pages

- [Graceful deploys](graceful-deploys.md)
- [Static and response cache](static-and-response-cache.md)
- [Velocity for hosts](../../hosting/servers/velocity-for-hosts.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
