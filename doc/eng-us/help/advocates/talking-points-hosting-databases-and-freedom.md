---
title: "Talking points: hosting, databases and freedom"
audience: advocate
category: advocacy
order: 22
summary: Sourced talking points about the freedom to choose where Exponential runs - six databases, operating systems in four tiers, web servers and engines, containers and hosting panels, moving between databases - and the honest limits.
keywords: talking points, hosting, databases, SQLite, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL, operating systems, Linux, BSD, macOS, Windows, containers, shared hosting, lock-in, freedom, migration
type: help
---
# Talking points: hosting, databases and freedom

This page is for advocates who meet the question "can we run it on what we have?" The answer is often yes, and the
details matter: Exponential documents its support in tiers and lists, not in slogans.

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

1. [The points](#the-points)
2. [The databases in one line each](#the-databases-in-one-line-each)
3. [The operating system tiers](#the-operating-system-tiers)
4. [The honest limits](#the-honest-limits)
5. [Questions you will hear](#questions-you-will-hear)

## The points

| Say | Source |
|---|---|
| "Six databases are supported, in this official order: SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL." | [Database support](../database-support.md#the-supported-databases-in-order) |
| "SQLite is the default: a complete site with no database server." | [Database support, SQLite 3](../database-support.md#1-sqlite-3-the-default), [Install in one command](../../features/6.0/install-in-one-command.md) |
| "Operating systems are documented in four tiers, from tested to theoretical." | [Operating system support, The four tiers](../os-support.md#the-four-tiers) |
| "PHP 8.0 or later (PHP 7.4 usable without support) on almost any operating system, with many web servers." | [Operating system support](../os-support.md#web-servers) |
| "Containers, cloud machines and hosting panels are covered." | [Containers, cloud and hosting panels](../os-support.md#containers-cloud-and-hosting-panels) |
| "You can move a site to another database with documented tools." | [Moving a site to another database](../database-support.md#moving-a-site-to-another-database) |
| "Backups are standard dumps for each database." | [Backup and restore](../database-support.md#backup-and-restore) |
| "Anyone may host it; you are not tied to one provider." | [Handling objections, Hosting and lock-in](../business/objection-handling.md#hosting-and-lock-in) |
| "You can check a machine in two minutes." | [Check a machine in two minutes](../os-support.md#check-a-machine-in-two-minutes) |

## The databases in one line each

Read the page before you go deeper; each database has its own section and limits.

| Database | Say |
|---|---|
| SQLite 3 | "The default; no server to run." |
| PostgreSQL | "The recommended database server." |
| MongoDB | "Supported, with documented limits." ([MongoDB limits](../database-support.md#mongodb-limits)) |
| MariaDB and MySQL | "Supported; they share one driver." |
| Oracle | "Supported through the `ezoracle` extension." |
| Microsoft SQL Server | "Possible with additional work; not one of the six." ([section](../database-support.md#microsoft-sql-server-possible-with-additional-work)) |

## The operating system tiers

| Tier | Meaning |
|---|---|
| Tier 1 | Tested here |
| Tier 2 | Known to work |
| Tier 3 | Expected to work |
| Tier 4 | Not possible today (theoretical) |

Which system is in which tier: [Operating system support](../os-support.md). Always name the tier, not just "it
runs on X".

## The honest limits

- **Shared hosting**: Apache or LiteSpeed with `.htaccess` can serve it, and the setup wizard installs it without a
  shell; Velocity usually cannot run there.
- **MongoDB** has documented limits; read them before recommending it.
- **Microsoft SQL Server** is not one of the six supported databases.
- **Tier 3 and 4** systems are not tested; say so.

## Questions you will hear

| Question | Answer |
|---|---|
| "Which database should a small site use?" | SQLite is the default and needs no server. For a database server, PostgreSQL is recommended. |
| "Can we start on SQLite and move later?" | Yes, the moves between engines are documented. |
| "Windows?" | Yes; read the platform notes for Windows on [Operating system support](../os-support.md#windows). |
| "ARM?" | Read [Processor architectures](../os-support.md#processor-architectures). |
| "Who hosts it?" | Anyone. 7x offers managed hosting among others; you choose ([Services and hosting](../who-is-7x.md#services-and-hosting)). |

## Related pages

- [Talking points for administrators](talking-points-for-administrators.md)
- [Choosing hosting, database and OS](../leadership/choosing-hosting-database-and-os.md)
- [Comparing Exponential factually, No lock-in](../business/comparing-exponential-factually.md#no-lock-in-point-by-point)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
