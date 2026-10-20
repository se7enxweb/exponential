---
title: "Demo script: Velocity"
audience: press
category: press-media
order: 159
summary: An eight-minute video script for Exponential Velocity, the persistent-worker application server - install the engine package off camera, start and check it, read the cache headers, look at the built-in dashboard, and talk about speed only with the documented figures and their method. What never to show and what not to claim.
keywords: demo script, Velocity, Exponential Velocity, application server, persistent workers, exp:velocity, qbix, FrankenPHP, response cache, X-Cache, dashboard, benchmark, exp:benchmark, speed, video, creator
type: help
---
# Demo script: Velocity

This page is for creators who show **Exponential Velocity**, the application server that "boots once and keeps a pool
of workers alive" ([Velocity](../../features/6.0/velocity-persistent-worker-server.md)). Speed is the topic where
videos most often say more than the sources do. This script shows how Velocity works and quotes only the documented
figures, with their method. It runs about eight minutes, in a terminal and a browser.

Velocity arrived in the 6.0.15 line (`./console exp:velocity`), which is in development; the engine package has its own
version numbers (Exponential Velocity 0.0.4.x). Say both on camera.

## Contents

1. [Off camera: install and prepare](#off-camera-install-and-prepare)
2. [The script](#the-script)
3. [Talking about speed](#talking-about-speed)
4. [What never to show](#what-never-to-show)
5. [Recovering on camera](#recovering-on-camera)

## Off camera: install and prepare

The engine is suggested by Exponential's `composer.json` but not installed by default; it needs PHP 8.1 or later and
the `sockets` extension ([Serving the site, 8.3.1](../../install/08-serving-the-site.md#831-install-the-engine-package)):

```bash
composer require se7enxweb/exponential-velocity:"<the version the kernel's composer.json suggests>"
php bin/php/console exp:velocity ext check                         # missing PHP extensions, with the install command
php bin/php/console exp:velocity config set ServerSettings Engine qbix
```

Read the version from the `suggest` block of the kernel's `composer.json`: on 19 October 2026 it was `~0.0.4.52`, while
the installation book still shows `~0.0.4.44`. The tilde accepts every later 0.0.4.x.

The shipped default engine is `php` (PHP's built-in server, for development only); `qbix` is the recommended engine
and FrankenPHP the production-ready alternative. Shipped ports: `qbix` 8088 (HTTP) and 8080 (HTTPS), `frankenphp` 8089
and 8444, `php` 8087. Start it once off camera and set the control panel password from the command line, so the
password never appears on screen ([Velocity control panel](../../features/6.0/velocity-control-panel.md#first-sign-in)).

## The script

| Time | On screen | Do | Say |
|---|---|---|---|
| 0:00 | Title card | | Disclosure. "Exponential Velocity, the application server for Exponential, on my own test machine." |
| 0:20 | Terminal | | "Classic PHP hosting starts the application for every request and tears it down after. Velocity loads it once and keeps workers alive between requests." |
| 1:00 | Terminal | `./console exp:velocity start` | "One command starts the server and shows its status." |
| 1:30 | Terminal | `./console exp:velocity status` | "It lists every address to open, the HTTPS address beside the plain one, and whether the caches get shared memory." |
| 2:15 | Browser | Open the site's HTTPS address | "HTTPS from the first start: a self-signed certificate is made for me; my own certificates are used when I name them." Accept the browser warning off camera if you can |
| 3:00 | Browser developer tools, Network | Reload the front page twice | "The first answer is rendered; the second has `X-Cache: HIT`: answered from the server's response cache." ([Velocity response cache](../../features/6.0/velocity-response-cache.md#read-it-headers-and-health)) |
| 4:00 | Browser | Open `/Q/dashboard` (signed in to the panel) | "The server's own dashboard: live requests, workers, memory, status codes, top paths, without a refresh." |
| 5:00 | Terminal | `./console exp:velocity graceful` | "Workers reload one at a time; the port keeps answering." |
| 5:45 | Terminal | `./console exp:velocity cache clear` | "After a template or stylesheet change, this clears every cache." |
| 6:30 | Title card | | Speed, as below. Then: "The commands and the method are linked below." |

`./console` is a shortcut you create with `ln -s bin/php/console console`; without it, type `php bin/php/console`.

## Talking about speed

Say only what the README documents, and say how it was measured:

```text
"The README documents 0.36 milliseconds of server work per cached page, measured as a median over hundreds of
requests, and 8,941 requests per second sustained on a 12-core machine serving a real page, with zero errors up to
384 connections. It says these are reproducible with scripts that ship with the project. Most of the speed came from
fixing Exponential and its caches, which helps wherever it runs."
```

- To measure yourself: `./console exp:benchmark --allow-root-user`, or two servers back to back with
  `--compare=<first address>,<second address>` ([Benchmarking](../../guides/benchmarking.md)).
- The guide warns: "One run on a busy machine proves little." A measurement during a recording on a laptop is a
  demonstration, not a result. Say that on camera if you show one.
- Never turn the figures into "N times faster than <another product>": no published comparison supports it
  ([Comparisons done fairly](comparisons-done-fairly.md) explains why).

## What never to show

| Never on screen | Why |
|---|---|
| The control panel password, the documented default key, `qbixctl panel:password` output | It signs in to the server |
| A dashboard address with `?token=...` | The token opens the dashboard |
| Certificate private keys, `var/vc/` contents, `/etc/vc/` or `/etc/qbix/` files | Keys and configuration |
| `settings/override/` | Database credentials |
| Public IP addresses or host names of a real server | Use `localhost` on a demo machine |

## Recovering on camera

| What happens | What to do |
|---|---|
| The start refuses: no process isolation or no `openssl` | Run `exp:velocity ext check` off camera; it prints the install command |
| A change does not show | Workers keep the application in memory: `exp:velocity restart`, or `deploy` for a PHP change |
| `./console exp:vc` says the command was not found | Use `exp:velocity`; the documentation records that the alias is not found on this tree |
| The dashboard answers 403 | Sign in to the panel first, off camera |

## Related pages

- [Velocity](../../features/6.0/velocity-persistent-worker-server.md)
- [Talking points: speed and Velocity](../advocates/talking-points-speed-and-velocity.md)
- [Benchmarking](../../guides/benchmarking.md)
- [Demo script: install from zero](demo-script-install-from-zero.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
