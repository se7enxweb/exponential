---
title: "Story: Exponential Velocity"
audience: press
category: press-media
order: 56
summary: A feature story about Exponential Velocity, the PHP web and application server bundled with Exponential - what it is, where it came from, how young it is, what it changes for site owners - with printable facts, sources and the claims to avoid.
keywords: story, Velocity, Exponential Velocity, vc, web server, application server, persistent workers, Qbix, HTTPS, HTTP/2, MIT, press
type: help
---
# Story: Exponential Velocity

This page is for journalists and creators who write about how Exponential is served, and about Velocity, the web
server written in PHP that ships with it. For the published speed figures and their conditions, read
[Story: how the Velocity figures are measured](story-how-velocity-figures-are-measured.md) before you quote any number.

## Contents

1. [The story in one paragraph](#the-story-in-one-paragraph)
2. [What is new, and when](#what-is-new-and-when)
3. [Facts you can print](#facts-you-can-print)
4. [What not to claim](#what-not-to-claim)
5. [Angles and headline ideas](#angles-and-headline-ideas)
6. [Who to ask](#who-to-ask)
7. [Related pages](#related-pages)

## The story in one paragraph

A classic PHP site starts the application fresh for every request and throws it away afterwards. "Velocity is a web
server written in PHP, bundled with Exponential, that keeps the application loaded between requests"
([README, Exponential Velocity](https://github.com/se7enxweb/exponential/blob/main/README.md#how-exponential-is-served)). It "began as a fork of the Qbix web
server engine, and the two projects have since drifted far apart" (the package's README, quoted on
[Who is 7x?, Exponential Velocity](../who-is-7x.md#exponential-velocity)). It serves HTTPS from the first start, speaks
HTTP/2, has a response cache and a browser control panel, and is the engine Exponential recommends "for every stage,
from development to production" ([Velocity](../../features/6.0/velocity-persistent-worker-server.md#why-you-would-use-it)).
It is also very young, which is part of the story.

## What is new, and when

| What | When | Source |
|---|---|---|
| First commits of the server | 20 July 2026 | [Velocity chronicle](../../history/velocity/README.md) |
| First release, `v0.0.1` | September 2026 (7 to 21 September period) | same |
| Velocity arrives in Exponential and becomes the recommended engine within two days | 22 September 2026 | [Who is 7x?, timeline](../who-is-7x.md#nearly-thirty-years-the-timeline) |
| Workers kept between requests (`ForkPerRequest=disabled`) | 30 September 2026 | [Velocity, Settings to know first](../../features/6.0/velocity-persistent-worker-server.md#settings-to-know-first) |
| Streams, deploys without a needless reload, each worker started clean | 11 October 2026 | [6.0.15 changelog](../../../changelogs/6.0/6.0.15.md#11-october-2026-exponential-velocity-streams-deploys-without-a-needless-reload-and-starts-each-worker-clean) |
| A cut-off restart or deploy no longer leaves Velocity down | 17 October 2026 | [6.0.15 changelog](../../../changelogs/6.0/6.0.15.md#17-october-2026-a-restart-or-deploy-that-is-cut-off-no-longer-leaves-velocity-down) |
| Velocity becomes part of Exponential: `composer.json` requires it at `~0.0.4.54`, which runs on PHP 8.0 | 20 October 2026 | [6.0.15 changelog](../../../changelogs/6.0/6.0.15.md#20-october-2026-exponential-velocity-is-part-of-exponential) |

Velocity in Exponential belongs to the 6.0.15 development line, which is not released.

## Facts you can print

| Fact | Source |
|---|---|
| Velocity is a web server written in PHP that keeps the application loaded between requests. | [README](https://github.com/se7enxweb/exponential/blob/main/README.md#how-exponential-is-served) |
| It is MIT licensed (Exponential itself is GPL). | [Who is 7x?, Open source licenses](../who-is-7x.md#open-source-licenses) |
| It runs on PHP 8.0 or later (from 0.0.4.54; it needed 8.1 before). | [README, What version of PHP is required](https://github.com/se7enxweb/exponential/blob/main/README.md#what-version-of-php-is-required) |
| HTTPS from the first start with a self-signed certificate; your own certificates or Let's Encrypt are possible. | [Velocity](../../features/6.0/velocity-persistent-worker-server.md#why-you-would-use-it), [README](https://github.com/se7enxweb/exponential/blob/main/README.md#how-exponential-is-served) |
| HTTP/2 is built into the bundled server. | [Velocity](../../features/6.0/velocity-persistent-worker-server.md#why-you-would-use-it) |
| One command deploys a PHP change: `./console exp:velocity deploy`. | [Velocity, Deploy a PHP change](../../features/6.0/velocity-persistent-worker-server.md#deploy-a-php-change-in-one-command) |
| Keeping workers between requests cut signed-in admin pages from 256 to 284 ms to 73 to 80 ms "in the measurement that led to the change". | [Velocity, Settings to know first](../../features/6.0/velocity-persistent-worker-server.md#settings-to-know-first) |
| A dashboard (`/Q/dashboard`) and a password-protected control panel (`/Q/panel`) are built in. | [Velocity control panel](../../features/6.0/velocity-control-panel.md) |
| As of 6.0.15 Velocity is part of Exponential: `composer.json` requires `se7enxweb/exponential-velocity ~0.0.4.54`, so every installation has it. | [Velocity is part of Exponential 6.0.15](../velocity-is-part-of-exponential-6-0-15.md) |
| The project calls this a turning point, a cornerstone and a capstone: a new level of application system integration, with Exponential one working stack (server, RAD platform, CMS, digital experience, e-commerce) rather than just a framework. | [Velocity is part of Exponential 6.0.15](../velocity-is-part-of-exponential-6-0-15.md#why-it-is-a-turning-point) |
| Velocity is the recommended engine for every stage; FrankenPHP is the production-ready alternative. Neither is "experimental". | [Velocity engines](../../bc/6.0/velocity-engines.md) |

## What not to claim

- "Production-proven at scale." The sources describe a young server: first commits in July 2026.
- "N times faster than Apache." No published comparison supports a multiple; the README itself says "Most of the
  speed came from fixing Exponential and the cache, not from the server process model".
- "Velocity replaces every web server." Apache, Nginx, LiteSpeed, Caddy, IIS and others still serve Exponential.
- "It runs on shared hosting." It "usually" cannot ([README](https://github.com/se7enxweb/exponential/blob/main/README.md#how-exponential-is-served)).
- "One Velocity server isolates customers from each other." The README says a single process serving several sites
  "is a routing convenience, not a security boundary".

## Angles and headline ideas

- A content management system that ships its own server.
- Young and candid: a server that documents its own limits.
- The fix that helped everyone: cache work that also speeds up Apache and Nginx.

```text
"Exponential ships its own web server, written in PHP"
"From first commit to recommended engine in two months: the Velocity story"
"Why Exponential's speed work helps Apache users too"
```

## Who to ask

| Question | Ask | Where |
|---|---|---|
| History, design, roadmap | 7x | info@se7enx.com, or <https://exponential.earth/about/contact-us> |
| The server's own code and issues | its repository | <https://github.com/se7enxweb/exponential-velocity> |
| A security problem in Velocity | privately | security@se7enx.com ([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)) |

## Related pages

- [Talking points: speed and Velocity](../advocates/talking-points-speed-and-velocity.md)
- [Story: engines, Velocity, FrankenPHP and the classic servers](story-engines-velocity-frankenphp-and-classic.md)
- [Story: the Velocity control panel](story-velocity-control-panel.md)
- [Feature stories: index](feature-stories-index.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
