---
title: Velocity is part of Exponential 6.0.15
audience: admin, developer, editor, user, press
category: getting-started
order: 2
summary: As of 6.0.15 the Velocity Internet and web application server is part of Exponential itself, installed with every copy and driven by one console command. Why that is a turning point, what it brings out of the box, how it fits next to Apache, nginx and FrankenPHP, and why Exponential is now one working stack for content management, digital experience and e-commerce.
keywords: Velocity, Exponential Velocity, application server, web server, Internet server, 6.0.15, turning point, cornerstone, capstone, integration, one stack, RAD, rapid application development, CMS, DXP, e-commerce, composer, exp:velocity, FrankenPHP, Apache, nginx, PHP-FPM, PHP 8.0, Red Hat Enterprise Linux
featured: true
type: help
---
# Velocity is part of Exponential 6.0.15

This page is for everyone who wants to know what changed when Exponential 6.0.15 took its own Internet and web
application server, **Exponential Velocity**, into the core product: site owners, administrators, hosting companies,
developers, decision makers and the press. It needs no technical knowledge to read; the commands are there for those
who want to try them.

## Contents

1. [In one paragraph](#in-one-paragraph)
2. [What changed in 6.0.15](#what-changed-in-6015)
3. [Why it is a turning point](#why-it-is-a-turning-point)
4. [One stack, not just a framework](#one-stack-not-just-a-framework)
5. [What Velocity brings out of the box](#what-velocity-brings-out-of-the-box)
6. [Velocity next to Apache, nginx and FrankenPHP](#velocity-next-to-apache-nginx-and-frankenphp)
7. [The commands](#the-commands)
8. [The documented figures](#the-documented-figures)
9. [What it means for you](#what-it-means-for-you)
10. [Questions and answers](#questions-and-answers)
11. [Read more](#read-more)

## In one paragraph

As of Exponential 6.0.15, the Velocity Internet / Web Application Server is part of Exponential. The root
`composer.json` **requires** the package `se7enxweb/exponential-velocity` at `~0.0.4.57`; it is no longer only a
suggestion. Every installation made with Composer therefore has the server, on every PHP version Exponential supports
(PHP 8.0 and later, including the stock PHP 8.0 of Red Hat Enterprise Linux 9). It is driven by the same console as
the rest of Exponential: `./console exp:velocity deploy`, `start`, `restart` and `cache clear`. This marks a turning
point for the project, a cornerstone and a capstone at once: a new level of application system integration, in which
the core product provides even greater features out of the box, by default.

## What changed in 6.0.15

| Before 6.0.15 | As of 6.0.15 |
|---|---|
| Exponential was a PHP application that needed a web server of someone else's choosing in front of it. | Exponential ships its own Internet and web application server, and still runs behind any other one. |
| Velocity was a separate package, listed in `composer.json` under `suggest`, to be added by hand. | `composer.json` requires `se7enxweb/exponential-velocity ~0.0.4.55` (through `se7enxweb/exponential-php-floor`, on PHP 8.0 and later); `composer install` installs it. |
| Velocity needed PHP 8.1, so installations on PHP 8.0 (Red Hat Enterprise Linux 9) had to leave it out. | Velocity 0.0.4.54 runs on PHP 8.0 to 8.5, the same range as the Exponential kernel. |
| Serving, caching and deploying were separate jobs with separate tools. | `./console exp:velocity deploy` refreshes autoloads, settings, caches, the PHP processes and the server in one ordered run. |

Nothing an existing site does stops working. A site served by Apache with PHP-FPM, by nginx or by FrankenPHP keeps
being served the way it was; the server is simply there, ready, the day you want it. The shipped
`settings/velocity.ini` still starts PHP's built-in server by default (`[ServerSettings] Engine=php`) because that
always works; one setting, `Engine=qbix`, switches to Velocity's own server.

## Why it is a turning point

For almost all of its history, the software now called Exponential stopped at the edge of PHP. What happened between
the visitor and the application (the web server, its process manager, the TLS certificate, the response cache, the
reload after a deploy) was assembled around it by each team on its own, and each team learned the same lessons again.

Exponential 6.0.15 closes that gap:

- **A cornerstone.** The server is now part of the foundation the rest of the product is built on. Kernel features
  are written and tested against it: persistent workers that boot once, a response cache that answers before a worker
  is chosen, a deploy command that knows which caches and processes a change touches.
- **A capstone.** It completes what the 6.0 line built up: current PHP from 8.0 to 8.5, SQLite as the default database
  so no database server is needed, six database engines on one kernel, a one-command installer, the console, the
  audit trail and the help center. With the server included, the whole path from a request to a rendered page belongs
  to one product, documented in one place.
- **A new level of application system integration.** The application and the server are developed together and tuned
  against each other rather than merely made compatible. The result is a product that provides even greater features
  out of the box, in the core, by default.

## One stack, not just a framework

Exponential is a whole working unit, not just a framework you have to complete before it does anything. One
`composer install` delivers a rapid application development (RAD) stack for Internet applications that is at the same
time a content management system, a digital experience platform and an e-commerce system:

| Layer | What is in the box | Read more |
|---|---|---|
| Server | Exponential Velocity: HTTP/1.1, HTTP/2 over TLS, WebSockets, its own HTTPS certificates, a response cache, static files and images | [Velocity web server](../features/6.0/velocity-web-server.md) |
| Application platform | The Exponential kernel: content classes and objects, versions, roles and policies, multisite, more than 360 locales, REST, cronjobs, the console | [README](https://github.com/se7enxweb/exponential/blob/main/README.md) |
| Rapid development | The RAD tools: a catalogue of extension points, wizards that write a working extension, a survey and a health check | [RAD tools](../features/6.0/rad-tools.md) |
| Content management | The administration (Admin 4), the online editor, publishing and versions, the trash, the audit trail | [Help for editors and users](README.md) |
| Digital experience | Exponential Layouts (a visual page builder with zones, blocks and rules), multisite and multilingual sites, the Exponential Intelligence writing assistant that writes drafts only | [The layouts editor](layouts-editor.md) |
| E-commerce | The shop: basket, orders, payment gateways, order receipts and the store dashboard | [Store dashboard](../features/6.0/store-dashboard.md) |
| Data | SQLite by default; PostgreSQL, MongoDB, MariaDB, Oracle and MySQL on the same kernel | [Database support](database-support.md) |

The Exponential family also includes Exponential Platform DXP, the Symfony-only member, for teams that want the
Symfony digital experience platform line; it is a separate product with its own feature set
([Who is the Exponential Foundation?](who-is-the-exponential-foundation.md)).

Because every one of those layers comes from one source, with one installer, one console and one set of
documentation, adopting Exponential anywhere in the world comes without the usual integration work: there is no
server to choose before the first page, no second vendor's documentation to reconcile, and no PHP version where half
of the stack is missing. Exponential runs on almost any operating system that has PHP 8.0 or later (PHP 7.4 usable without support)
([Operating system support](os-support.md)).

## What Velocity brings out of the box

Each of these is a documented, shipped feature of Velocity 0.0.4.x:

| Feature | What it does | Page |
|---|---|---|
| Persistent workers | The application boots once into a parent process; a pool of workers keeps it loaded between requests. | [Persistent-worker server](../features/6.0/velocity-persistent-worker-server.md) |
| Response cache | Public pages are answered inside the server process before a worker is chosen, with `304`, `ETag` and stale-while-revalidate. | [Response cache](../features/6.0/velocity-response-cache.md) |
| HTTPS that looks after itself | A self-signed certificate when none is configured; your own files, a PKCS#12 bundle, Let's Encrypt or another ACME CA when one is; renewed certificates swapped in without a restart. | [HTTPS and certificates](../features/6.0/velocity-https-certificates.md) |
| Static files and images | Files served without PHP, validated, compressed, sent in ranges, and images converted or resized on request. | [Static files and images](../features/6.0/velocity-static-files-and-images.md) |
| Real time | WebSockets, Server-Sent Events and rooms, built into the server. | [WebSockets and events](../features/6.0/velocity-websockets-and-events.md) |
| Dashboard and control panel | Live requests, workers and memory at `/Q/dashboard`; a password-protected control panel at `/Q/panel`. | [Control panel and dashboard](../features/6.0/velocity-control-panel.md) |
| Q shell | A drop-down console on every server view for status, workers, cache, sites, certificates and logs. | [Q shell](../features/6.0/velocity-q-shell.md) |
| Scheduler | A small cron inside the server for periodic work, without a second process manager. | [Scheduler](../features/6.0/velocity-scheduler.md) |
| Packages and binaries | System packages, Docker images and single binaries for use outside an Exponential installation. | [Packages and binaries](../features/6.0/velocity-packages-and-binaries.md) |
| Familiar configuration | An on-disk layout in the style of Debian's `/etc/apache2`: `sites-available`, `conf-available`, `mods-available`, enabled with `exp:velocity site\|conf\|mod enable`. | [On-disk layout](../bc/6.0/velocity-ondisk-layout.md) |

## Velocity next to Apache, nginx and FrankenPHP

Velocity does not replace the choice of web server; it adds the best one to the box. Exponential is served by any of
these, and `exp:velocity` drives the last two:

| Engine | Role |
|---|---|
| Apache with PHP-FPM, nginx with PHP-FPM, LiteSpeed and the others | Traditional servers. Fully supported, and the answer where a host does not allow a long-running process. |
| **Velocity's own server** (`Engine=qbix`) | **The recommended engine for every stage**, from development to production, and the fastest. Serves HTTP and HTTPS itself. |
| **FrankenPHP** (`Engine=frankenphp`) | **The production-ready alternative**: Caddy with PHP built in, downloaded as one checksum-verified binary by `exp:velocity install`. |
| PHP's built-in server (`Engine=php`) | Development only; the shipped default because it always works. |

Velocity also runs happily behind Apache or nginx as a reverse proxy, so a host with a tuned front server keeps it.
Where several customers share one machine and must not read each other's files, run one Velocity instance per site,
each as its own operating-system user. The details are in [Velocity engines](../bc/6.0/velocity-engines.md).

## The commands

```bash
./console exp:velocity deploy --allow-root-user       # after a change: autoloads, settings, caches, PHP-FPM, Velocity
./console exp:velocity start --allow-root-user        # start the configured engine
./console exp:velocity restart --allow-root-user      # restart it, so workers load changed classes
./console exp:velocity cache clear --apply --allow-root-user  # empty Velocity's response cache
```

`deploy --dry-run` lists what it would do and does nothing else; `deploy --kernel` also rebuilds the kernel autoloads
after a kernel class was added or renamed. Workers keep the application in memory between requests, so after a class
or settings change the server needs `deploy` (or `restart`), not only a cache clear.

## The documented figures

These are the figures already documented for this stack, with their method, in the
[README](https://github.com/se7enxweb/exponential/blob/main/README.md#exponential-velocity--the-fast-option): **0.36 ms**
of server work per cached page (a median over hundreds of requests), **8,941 requests per second** sustained on a
12-core machine serving a real page with zero errors up to 384 connections in flight, and a returning visitor's
reload answered with **202 bytes** instead of the whole page. The README is also clear that most of the speed came
from fixing Exponential and its cache, which helps on Apache and nginx too; Velocity raises the ceiling further.
How the figures are measured: [How Velocity figures are measured](press/story-how-velocity-figures-are-measured.md).

## What it means for you

- **Site owners and decision makers:** one product to buy into, one place to read, one supplier relationship. The
  server is not an extra project.
- **Hosting companies:** Velocity is installed on every Exponential site and runs on the PHP your machines already
  have, 8.0 included. Offer it where long-running processes are allowed; keep Apache or nginx with PHP-FPM where they
  are not. Start at [Hosting companies start here](hosting/hosting-companies-start-here.md).
- **Developers:** develop against the same engine production runs, with the RAD tools for extensions and WebSockets,
  the scheduler and the response cache available without adding anything.
- **Administrators:** one command deploys a change in the right order and stops at the first failure.

## Questions and answers

**Do I have to use Velocity?**
No. It is installed with every copy, and it is the recommended engine, but Exponential still runs under Apache, nginx,
LiteSpeed and the others exactly as before.

**Does it work on Red Hat Enterprise Linux 9 with its stock PHP 8.0?**
Yes. Velocity 0.0.4.54 runs on PHP 8.0 to 8.5. It needs the `sockets` extension; `pcntl` and `openssl` are
recommended for the worker pool and HTTPS.

**Is Velocity or FrankenPHP experimental?**
No. Velocity is the recommended engine for every stage, and FrankenPHP is a production-ready alternative.

**Do I need to change anything when I upgrade to 6.0.15?**
Run `composer install` as usual; it installs the server. Nothing switches engines on its own.

## Read more

- [Velocity web server](../features/6.0/velocity-web-server.md) and
  [running Exponential in a persistent-worker web server](../features/6.0/velocity-persistent-worker-server.md)
- [Velocity engines: Qbix, FrankenPHP, PHP's built-in server](../bc/6.0/velocity-engines.md)
- [Installation chapter 8: Serving the site](../install/08-serving-the-site.md)
- [The 6.0.15 changelog](../../changelogs/6.0/6.0.15.md)
- [Operating system support](os-support.md)
