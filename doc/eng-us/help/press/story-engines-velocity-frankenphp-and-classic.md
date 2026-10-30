---
title: "Story: engines, Velocity, FrankenPHP and the classic servers"
audience: press
category: press-media
order: 58
summary: A feature story about the choice of web server for Exponential - the bundled Velocity, FrankenPHP as the production-ready alternative, PHP's built-in server for development, and Apache, Nginx and the other classic servers - with printable facts, sources and the claims to avoid.
keywords: story, engines, Velocity, Qbix, FrankenPHP, Caddy, PHP built-in server, Apache, Nginx, LiteSpeed, IIS, PHP-FPM, web server, press
type: help
---
# Story: engines, Velocity, FrankenPHP and the classic servers

This page is for journalists and reviewers who compare ways of running Exponential. It explains the three engines
that the `exp:velocity` commands drive, the classic web servers that still work, and when each is the right answer
according to the sources.

> **Warning**
> **`.htaccess` slows down every request.** When `.htaccess` files are enabled (`AllowOverride` anything but `None`), let alone
> used, Apache looks for them and reads and evaluates them on every request, in every directory of the path, and
> page loading times go up drastically ([measured](../../guides/setup-files-from-the-console.md#measured-htaccess-against-rules-in-the-virtual-host)). The only real fix is to switch the feature off for
> the virtual host: `AllowOverride None` in the HTTPS virtual host, and the rules in the virtual host's own
> configuration as mod_rewrite rules. That is far faster in production speed tests and well worth the time it takes
> to optimize a production configuration. Switch over right away: copy the rules of the
> [example HTTPS virtual host](../../examples/web-servers/apache-virtual-host.md) into your virtual host (with the static cache, also
> [`staticcache_offload.conf`](../../examples/staticcache_offload.conf)), set `AllowOverride None`, run `apachectl configtest`, reload Apache and
> test the site. The same rules for other servers: [nginx](../../examples/web-servers/nginx.md), [LiteSpeed](../../examples/web-servers/litespeed.md),
> [Caddy](../../examples/web-servers/caddy.md), [IIS](../../examples/web-servers/iis.md), [Velocity](../../examples/web-servers/velocity.md) (built in); where virtual host rules go in a control
> panel: [Plesk](../hosting/panels/plesk.md), [cPanel](../hosting/panels/cpanel.md), [DirectAdmin](../hosting/panels/directadmin.md), [ISPConfig](../hosting/panels/ispconfig.md).
> **On LiteSpeed or OpenLiteSpeed,** the server's own page cache can also answer anonymous visitors and crawlers without PHP: [Page caching with LiteSpeed and OpenLiteSpeed](../../guides/litespeed-page-cache.md).

## Contents

1. [The story in one paragraph](#the-story-in-one-paragraph)
2. [The engines side by side](#the-engines-side-by-side)
3. [Facts you can print](#facts-you-can-print)
4. [What not to claim](#what-not-to-claim)
5. [Angles and headline ideas](#angles-and-headline-ideas)
6. [Who to ask](#who-to-ask)
7. [Related pages](#related-pages)

## The story in one paragraph

Exponential does not require any particular web server: "All of these work. Exponential does not require any
particular one" ([README, The traditional options](https://github.com/se7enxweb/exponential/blob/main/README.md#the-traditional-options)). Since the 6.0.15
development line the same console commands also drive three engines of their own: Velocity's bundled Qbix-based
server, "the recommended engine for every stage, from development to production, and the fastest"; FrankenPHP,
"Caddy with PHP built in ... The production-ready alternative"; and PHP's built-in server, "For development, and the
shipped default because it always works" ([README, Engines](https://github.com/se7enxweb/exponential/blob/main/README.md#engines-frankenphp-phps-built-in-server-or-qbix)).
The story is about choice: the project recommends one engine and documents when a classic server is still right.

## The engines side by side

| Engine | Role in the sources | Default port | Source |
|---|---|---|---|
| Qbix (Velocity's own server) | Recommended for every stage; serves HTTP and HTTPS itself | 8088 (HTTPS 8080) | [Velocity, Engines side by side](../../features/6.0/velocity-persistent-worker-server.md#engines-side-by-side) |
| FrankenPHP | Production-ready alternative; one checksum-verified binary | 8089 (HTTPS 8444) | same |
| PHP built-in server | Development only; the shipped default | 8087 | same |
| Apache, Nginx, LiteSpeed, Caddy, IIS, lighttpd, H2O, Traefik | Classic servers with rewrite rules; all work | their own | [README, The traditional options](https://github.com/se7enxweb/exponential/blob/main/README.md#the-traditional-options) |

When a traditional server is still the right answer, according to the README: per-site operating-system isolation,
hosting that does not allow a long-lived process (shared hosting), or a tuned Nginx or Apache already in front
([README](https://github.com/se7enxweb/exponential/blob/main/README.md#when-a-traditional-server-is-still-the-right-answer)).

## Facts you can print

| Fact | Source |
|---|---|
| The shipped default in `settings/velocity.ini` is `Engine=php`, because it needs nothing installed; the documentation says to change it to `qbix` for any real site. | [Velocity, Why you would use it](../../features/6.0/velocity-persistent-worker-server.md#why-you-would-use-it) |
| All three engines can run at once, each with its own port, pid file and logs, for tests. | [Velocity, Engines side by side](../../features/6.0/velocity-persistent-worker-server.md#engines-side-by-side) |
| PHP's built-in server is "Single-threaded, no concurrency, not hardened. Never expose it to the internet." | [README, The traditional options](https://github.com/se7enxweb/exponential/blob/main/README.md#the-traditional-options) |
| PHP 8.0 runs Exponential under Apache, PHP-FPM or Velocity (Velocity from its release 0.0.4.54). | [README, PHP](https://github.com/se7enxweb/exponential/blob/main/README.md#what-version-of-php-is-required) |
| Shared hosting: Apache or LiteSpeed with `.htaccess`; Velocity usually cannot run there. | [Operating system support](../os-support.md#containers-cloud-and-hosting-panels) |
| Workers keep the application in memory, so a changed template, setting or class needs a restart. | [README, One thing to know](https://github.com/se7enxweb/exponential/blob/main/README.md#one-thing-to-know-before-you-rely-on-it) |

## What not to claim

- "You must use Velocity." Every classic server listed in the README works.
- "FrankenPHP is part of Exponential." It is a separate project that Exponential can download and drive.
- "Velocity is always the best choice." The README lists cases where a traditional server is the right answer.
- Comparisons of speed between engines without your own measurement and method
  ([Story: how the Velocity figures are measured](story-how-velocity-figures-are-measured.md)).

## Angles and headline ideas

```text
"One CMS, three engines, any web server: how Exponential lets you choose"
"When the old web server is still the right answer - according to the project itself"
"Exponential drives FrankenPHP, Velocity and PHP's own server with the same commands"
```

## Who to ask

| Question | Ask | Where |
|---|---|---|
| Why Velocity is recommended | 7x | info@se7enx.com, or <https://exponential.earth/about/contact-us> |
| FrankenPHP itself | the FrankenPHP project | its own channels |
| Experiences on real servers | the community | <https://share.exponential.earth/forums> |

## Related pages

- [Story: Exponential Velocity](story-velocity.md)
- [FrankenPHP and PHP's built-in server](../../bc/6.0/frankenphp.md)
- [Story: hosting freedom](story-hosting-freedom.md)
- [Feature stories: index](feature-stories-index.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
