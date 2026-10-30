---
title: Running on a small budget
audience: individual, website-creator, customer
category: personal-sites
order: 1633
summary: How one person keeps the cost and the moving parts of an Exponential site low - no license fee, SQLite instead of a database server, one small machine, Velocity or a common web server, caching, few extensions, and a worksheet for your own yearly costs without invented prices.
keywords: budget, cost, cheap, low cost, free software, no license fee, SQLite, Velocity, shared hosting, VPS, small server, caching, extensions, yearly cost, worksheet, individual
type: help
---
# Running on a small budget

This page is for individuals who pay for their site out of their own pocket. Exponential itself costs nothing; what
costs money is where it runs, its address, and your time. This page shows where the savings are. It quotes no prices:
fill in your own in the worksheet at the end.

> **Warning**
> **`.htaccess` slows down every request.** When `.htaccess` files are enabled (`AllowOverride` anything but `None`), let alone
> used, Apache looks for them and reads and evaluates them on every request, in every directory of the path, and
> page loading times go up drastically ([measured](../../../guides/setup-files-from-the-console.md#measured-htaccess-against-rules-in-the-virtual-host)). The only real fix is to switch the feature off for
> the virtual host: `AllowOverride None` in the HTTPS virtual host, and the rules in the virtual host's own
> configuration as mod_rewrite rules. That is far faster in production speed tests and well worth the time it takes
> to optimize a production configuration. Switch over right away: copy the rules of the
> [example HTTPS virtual host](../../../examples/web-servers/apache-virtual-host.md) into your virtual host (with the static cache, also
> [`staticcache_offload.conf`](../../../examples/staticcache_offload.conf)), set `AllowOverride None`, run `apachectl configtest`, reload Apache and
> test the site. The same rules for other servers: [nginx](../../../examples/web-servers/nginx.md), [LiteSpeed](../../../examples/web-servers/litespeed.md),
> [Caddy](../../../examples/web-servers/caddy.md), [IIS](../../../examples/web-servers/iis.md), [Velocity](../../../examples/web-servers/velocity.md) (built in); where virtual host rules go in a control
> panel: [Plesk](../../hosting/panels/plesk.md), [cPanel](../../hosting/panels/cpanel.md), [DirectAdmin](../../hosting/panels/directadmin.md), [ISPConfig](../../hosting/panels/ispconfig.md).
> **On LiteSpeed or OpenLiteSpeed,** the server's own page cache can also answer anonymous visitors and crawlers without PHP: [Page caching with LiteSpeed and OpenLiteSpeed](../../../guides/litespeed-page-cache.md).

## Contents

1. [What you never pay for](#what-you-never-pay-for)
2. [What you do pay for](#what-you-do-pay-for)
3. [Fewer moving parts](#fewer-moving-parts)
4. [SQLite: no database server](#sqlite-no-database-server)
5. [Velocity or a common web server](#velocity-or-a-common-web-server)
6. [Keeping it light](#keeping-it-light)
7. [Your yearly cost worksheet](#your-yearly-cost-worksheet)

## What you never pay for

- **A license fee.** Exponential is free software under the GNU GPL, version 2 or later
  ([Licensing for buyers](../../business/licensing-for-buyers.md)).
- **Extensions that ship.** Everything in the root `composer.json` comes with it.
- **The documentation**, which is free under the GNU FDL ([Free documentation](../../free-documentation.md)).

See also [What you do not pay for](../../businesses/costs/what-you-do-not-pay-for.md).

## What you do pay for

| Item | Paid to | Can it be zero? |
|---|---|---|
| Hosting or a server | A hosting company | Only on a machine you already run, with a connection you control |
| Domain name | A registrar, yearly | No, if you want your own name |
| Certificate for HTTPS | Often free (Let's Encrypt), sometimes included by the host | Usually yes |
| Mail delivery | Your host or a mail provider | Often included in hosting |
| Off-site backup storage | A storage provider, or a disk you keep | Yes, with a disk at home |
| AI provider, if you use the writing assistant | The provider | Yes, by not using it, or with a local model |
| Help | A partner or freelancer | Yes, if you do it yourself |

## Fewer moving parts

Every extra service is something to pay for, update and back up. The smallest working setup
([Requirements at a glance](../../hosting/basics/requirements-at-a-glance.md)):

| Part | Minimum |
|---|---|
| PHP | 8.0 or later, with the required extensions |
| Database | SQLite 3, no server |
| Web server | None needed with Velocity; otherwise Apache or nginx with PHP-FPM, or the host's server |
| Search | The built-in search; no Solr |
| Cache server | None; the file caches are enough for a small site |

## SQLite: no database server

SQLite 3 is the default database and first in the official order of preference. The whole database is one file under
`var/storage/sqlite3/`: no server to run, no account to create, backups are a copy made with SQLite's own backup
command ([SQLite for hosts](../../hosting/basics/sqlite-for-hosts.md), [SQLite database](../../../features/6.0/sqlite-database.md)).
It suits a site served from one machine, which a personal, family or club site is. If your site grows a lot, it can
move to PostgreSQL later ([Database support](../../database-support.md)).

## Velocity or a common web server

**Exponential Velocity** is part of Exponential since 6.0.15 and serves the site itself, with no Apache or nginx; it
can obtain and renew Let's Encrypt certificates on its own
([Velocity is part of Exponential 6.0.15](../../velocity-is-part-of-exponential-6-0-15.md),
[Velocity HTTPS and certificates](../../../features/6.0/velocity-https-certificates.md)). It needs a long-running
process, which means a small server of your own, not shared hosting.

On shared hosting, Apache or LiteSpeed with `.htaccess` does the job; the cache fixes of the 6.0.15 line make the
site fast there too ([Shared hosting or a small server](shared-hosting-or-a-small-server.md)).

## Keeping it light

- **Images**: upload sensible sizes; let image variations do the rest
  ([Image variations for portfolios](../portfolios/image-variations-for-portfolios.md)).
- **Caching**: leave the shipped caches on ([Performance and caching](../../creators/website-creators/performance-and-caching.md)).
- **Extensions**: activate only what you use; each one is code to keep current.
- **Outside services**: each script you embed (analytics, video, maps) costs privacy and sometimes money.
- **Spam**: block bots early, so they do not fill your database and mailbox
  ([Spam and security basics](../running-it/spam-and-security-basics.md)).

## Your yearly cost worksheet

Fill in your own numbers; this page gives none.

| Item | Provider | Per year | Renewal date |
|---|---|---|---|
| Hosting or server | | | |
| Domain name | | | |
| Certificate (if not free) | | | |
| Mail (if separate) | | | |
| Backup storage | | | |
| AI provider (optional) | | | |
| Help, hours per year x rate (optional) | | | |
| **Total** | | | |
| Your time: hours per month for upkeep | | | |

Write the renewal dates into your calendar: an expired domain takes the site and your mail offline.

## Related pages

- [Setup for individuals](README.md)
- [Shared hosting or a small server](shared-hosting-or-a-small-server.md)
- [Total cost of ownership](../../businesses/costs/total-cost-of-ownership.md)
- [Requirements at a glance](../../hosting/basics/requirements-at-a-glance.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
