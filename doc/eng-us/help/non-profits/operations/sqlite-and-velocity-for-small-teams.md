---
title: SQLite and Velocity for small teams
audience: non-profit, admin, hosting, businesses
category: non-profits
order: 30
summary: The two Exponential choices that make a non-profit's site cheap to run - SQLite, the default database that keeps the whole site in one file with no database server, and Velocity, the bundled web server that needs nothing beyond PHP - with what each saves, their limits (one writer at a time, usually not on shared hosting, no forked worker pool on Windows), backups, and when to choose something else.
keywords: SQLite, Velocity, small team, database server, one file, one writer, backup, sqlite3 .backup, web server, exp:velocity, shared hosting, VPS, PHP 8.0, limits, low cost
type: help
---
# SQLite and Velocity for small teams

This page is for the technical volunteer or partner, and for the director who wants to understand the choice. Fewer
moving parts means fewer things to pay for, patch, back up and fix. Exponential offers two choices that remove whole
services from a small site's setup.

## Contents

1. [SQLite: the site in one file](#sqlite-the-site-in-one-file)
2. [SQLite limits and good practice](#sqlite-limits-and-good-practice)
3. [Velocity: a web server that is PHP](#velocity-a-web-server-that-is-php)
4. [Velocity limits](#velocity-limits)
5. [Which combination for which non-profit](#which-combination-for-which-non-profit)
6. [Checklist](#checklist)

## SQLite: the site in one file

SQLite 3 is the first database in the official order and the default. "With SQLite, a complete Exponential site runs
from one file on disk, with no database server to install, configure or secure"
([SQLite database support](../../../features/6.0/sqlite-database.md)).

What it saves a non-profit:

| Without SQLite | With SQLite |
|---|---|
| A database server to install, patch and monitor | Nothing extra |
| Database credentials to manage | None to leak |
| A separate database backup | One file (plus the storage directory) |
| A larger hosting plan | Often the smallest one |

## SQLite limits and good practice

From [SQLite database support, Limits and good practice](../../../features/6.0/sqlite-database.md#limits-and-good-practice):

- "SQLite allows one writer at a time. Many editors publishing at the same moment queue behind each other ... for a
  busy editorial team choose MySQL, PostgreSQL or MongoDB." A non-profit with a handful of editors is usually well
  within this.
- Do not put the database file inside a directory the web server serves directly; check that it cannot be downloaded.
- Back up with `sqlite3 <file> ".backup <target>"`, "not by copying the file while the site is busy".
- Run `ANALYZE` once after a big import.

Moving to another database later is possible ([Database support](../../database-support.md),
[SQLite for hosts](../../hosting/basics/sqlite-for-hosts.md)).

## Velocity: a web server that is PHP

Exponential Velocity is a web server written in PHP that runs the installation from a pool of long-lived PHP
processes; "you do not need Apache, nginx or PHP-FPM in front of it, though it works behind them"
([Velocity web server](../../../features/6.0/velocity-web-server.md)). Since Exponential 6.0.15 it is required by the
root `composer.json`, so every installation has it ([Velocity is part of Exponential 6.0.15](../../velocity-is-part-of-exponential-6-0-15.md)).

What it gives a small team:

- **One thing to run** instead of a web server plus PHP-FPM: `./console exp:velocity start`, `restart`, `deploy` and
  `cache clear` are the daily commands (same page).
- **HTTPS, a response cache and a control panel** in the same server.
- **Speed** that makes a small server enough for traffic peaks such as an appeal day. The figures measured by the
  project are on the feature page; measure your own site rather than quoting them.

## Velocity limits

- It usually does **not** run on shared hosting (`README.md` comparison table: "Shared hosting ... usually not"). It
  suits a small virtual server.
- PHP 8.0 or later (PHP 7.4 usable without support).
- Windows has no `fork()`, so no pool of forked workers there.
- Someone must start it at boot and restart it after changes ([Velocity web server, Limits](../../../features/6.0/velocity-web-server.md#limits)).

You do not need Velocity: Apache, Nginx, LiteSpeed and other servers keep working (`README.md`, "Requirements").

## Which combination for which non-profit

| Situation | Database | Web server |
|---|---|---|
| Very small site on shared hosting | SQLite | The host's Apache or LiteSpeed |
| Small site on a virtual server with a technical volunteer | SQLite | Velocity |
| Many editors publishing at once, or a large shop | PostgreSQL, MariaDB or MySQL | Velocity, or Apache/Nginx |
| Managed hosting | What the host supports | What the host runs |

Hosting models: [Low-budget hosting](low-budget-hosting.md).

## Checklist

- [ ] Database chosen for the number of editors publishing at once.
- [ ] SQLite file not downloadable from the web; backup with `.backup`, restore tested.
- [ ] Velocity only on a host that allows a long-running process; starts at boot.
- [ ] The daily commands written down for whoever takes over.

## Related pages

- [Operations for non-profits](README.md)
- [Low-budget operations](../../businesses/nonprofits/low-budget-operations.md)
- [Velocity for businesses](../../businesses/reliability/velocity-for-businesses.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
