# Exponential Velocity: history of the web server engine

Exponential Velocity (`vc`, driven from Exponential by `exp:velocity`) is the PHP application server that Exponential recommends for every stage from development to production. This section tells how it came to be, month by month, and where to find each feature, setting and upgrade note it produced. It is the chronicle of the repository `se7enxweb/exponential-velocity` (earlier name `se7enxweb/qbix-webserver`; the two ledgers are the same history under two names and are documented once here) and of the small installer and package repositories next to it.

How to use this section:

- **Want to know what happened and why?** Read the month pages in order. Each opens with a paragraph, a table of what a user could do afterwards that they could not before, and the story of the month; the last table lists *every* change of the period with its class.
- **Want to use a feature?** Go to the feature pages below; each has settings with defaults, commands that work and limits.
- **Upgrading?** Read the [upgrade notes](../../bc/6.0/velocity-engine-upgrade-notes.md) first, then the [changelog](../../changelogs/extensions/exponential-velocity.md).
- **Looking for one commit?** The complete, machine-made list is in the [ledger](../ledger/exponential-velocity.md) (and, under the old name, [the ledger of qbix-webserver](../ledger/qbix-webserver.md)).

## Month pages

| Page | Period | Releases | Theme |
|---|---|---|---|
| [July 2026](2026-07.md) | 20 to 30 July | none | The first release: a pure PHP web server |
| [August 2026](2026-08.md) | 4 to 31 August | none | App mode, octane workers, logging, binaries |
| [September, first part](2026-09a.md) | 7 to 21 September | `v0.0.1` | Compatibility with existing PHP applications, the benchmark |
| [22 September](2026-09b.md) | one day | `v0.0.2.1` to `v0.0.4.14` | HTTP/2 and the response cache |
| [23 September](2026-09c.md) | one day | `v0.0.4.15` to `v0.0.4.26` | Security fixes, platforms, the Exponential preset |
| [24 September](2026-09d.md) | one day | `v0.0.4.27` | HTTPS, dynamic pool, shell, configuration tree, packages |
| [25 to 30 September](2026-09e.md) | six days | `v0.0.4.28` to `v0.0.4.42` | Domains, panel, caches, non-root workers, `sbin/`, uwebserver |
| [October 2026](2026-10.md) | 1 October | closes `v0.0.4.42` | Release bookkeeping |
| [Installers and packages](installers-and-packages.md) | December 2023 to July 2026 | `exponential-legacy-installer` 2.2.1 to 2.2.3 | The legacy installer and the `ezwebin` site packages |

## Feature pages

| Page | What it covers |
|---|---|
| [Velocity web server](../../features/6.0/velocity-web-server.md) | What it is, how to start it, presets, WebSockets, what it serves |
| [Control panel and dashboard](../../features/6.0/velocity-control-panel.md) | Signing in, tabs, domains, SSL, cache, logs, passwords, two-factor authentication |
| [Response cache](../../features/6.0/velocity-response-cache.md) | Turning it on, lifetimes, stale-while-revalidate, the generation marker, the application's own cache |
| [HTTPS and certificates](../../features/6.0/velocity-https-certificates.md) | Self-signed, your own files, Let's Encrypt and other ACME CAs |
| [Q shell](../../features/6.0/velocity-q-shell.md) | The drop-down console on every server view |
| [Packages and binaries](../../features/6.0/velocity-packages-and-binaries.md) | deb, rpm, Docker, static binaries and the phar |
| [uwebserver](../../features/6.0/velocity-uwebserver.md) | The small C static file server |
| [Scheduler](../../features/6.0/velocity-scheduler.md) | Cron-like tasks inside the server |
| [Static files and images](../../features/6.0/velocity-static-files-and-images.md) | Caching lifetimes, compression, directory listings, resized and converted images |
| [WebSockets, events and rooms](../../features/6.0/velocity-websockets-and-events.md) | socket.io, rooms, Server-Sent Events |
| [Discovery, federation and deploy](../../features/6.0/velocity-discovery-federation-and-deploy.md) | `/.well-known/` documents, forwarding events, `--deploy`, the experimental mesh |

## Reference pages

| Page | What it covers |
|---|---|
| [Worker pool](../../specifications/6.0/velocity-worker-pool.md) | Workers, the zygote, memory, reset between requests, the compatibility layer |
| [HTTP/2 and security](../../specifications/6.0/velocity-http2-and-security.md) | What the server refuses and why, request limits, headers |
| [Engine settings](../../specifications/6.0/velocity-engine-settings.md) | Every setting by area, with default and scope: logging, brand, event loop, start-up pre-warm cache |
| [Velocity engines](../../bc/6.0/velocity-engines.md) | Choosing and running an engine from Exponential |
| [Velocity on-disk layout](../../bc/6.0/velocity-ondisk-layout.md) | The `/etc/vc` tree |
| [The engine archive](../../bc/6.0/phar.md) | Running Exponential from a phar |

## A note on names

The engine began as the Qbix web server. In the code you will still see `Q_WebServer`, `qbixserver.php`, `qbixctl`, the `Q.` settings prefix and the `/Q/` URL prefix: these are identifiers and stay as they are. The product is called Velocity; the Composer package is `se7enxweb/exponential-velocity`.
