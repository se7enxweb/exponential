---
title: Hosting basics
audience: hosting
category: hosting
order: 10
summary: The overview of the hosting basics section: what a server needs to run Exponential, the PHP versions, extensions and settings, the six supported databases in the official order, operating systems, Composer and Git, permissions, sizing, image processing and search.
keywords: hosting, basics, overview, requirements, PHP, extensions, php.ini, databases, SQLite, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL, SQL Server, operating systems, Composer, Git, permissions, sizing, images, search, Solr
type: help
---
# Hosting basics

This section is for everyone at a hosting company who needs to know what Exponential asks of a server before an
offer is designed, a server is built or a customer is helped: product managers, system administrators and support
staff. It answers one question per page: what does the software need, and what does that mean for the way you host
it. Every fact on these pages is taken from the repository (the installation book in `doc/eng-us/install/`, the help pages
on [database support](../../database-support.md) and [operating system support](../../os-support.md), and the
shipped settings in `settings/`), and each page links to its sources so you can check them.

Back to the start of the hosting help: [Hosting companies: start here](../hosting-companies-start-here.md).

## Contents

1. [How to read this section](#how-to-read-this-section)
2. [The pages](#the-pages)
3. [The short version](#the-short-version)
4. [Related pages](#related-pages)

## How to read this section

| If you are ... | Start with |
|---|---|
| a product manager designing an offer | [What hosting Exponential involves](what-hosting-exponential-involves.md), then [Requirements at a glance](requirements-at-a-glance.md) and [Databases in the official order](databases-in-the-official-order.md) |
| a system administrator building a server | [Requirements at a glance](requirements-at-a-glance.md), [PHP versions and extensions](php-versions-and-extensions.md), [PHP settings for Exponential](php-settings-for-exponential.md), [Writable directories and permissions](writable-directories-and-permissions.md) |
| support staff answering a customer | [Requirements at a glance](requirements-at-a-glance.md) and the page of the customer's database |
| planning capacity | [Sizing memory, CPU and disk](sizing-memory-cpu-and-disk.md) |

## The pages

| Page | What it answers |
|---|---|
| [What hosting Exponential involves](what-hosting-exponential-involves.md) | The parts a host provides (PHP, database, web server or Velocity, cron, mail, storage, backups) and who usually does what |
| [Requirements at a glance](requirements-at-a-glance.md) | One table of everything a server needs, with the minimum, the recommendation and what checks it |
| [PHP versions and extensions](php-versions-and-extensions.md) | PHP 7.4 to 8.5, the required and recommended extensions, and the package names per system family |
| [PHP settings for Exponential](php-settings-for-exponential.md) | `php.ini` values the installer checks, the values to give a pool, OPcache, and the functions not to disable |
| [Databases in the official order](databases-in-the-official-order.md) | The six supported databases, in order, side by side, and how to choose what to offer |
| [SQLite for hosts](sqlite-for-hosts.md) | The default database: one file, its permissions, backups and limits |
| [PostgreSQL for hosts](postgresql-for-hosts.md) | The recommended database server: owner, `pgcrypto`, backups |
| [MongoDB for hosts](mongodb-for-hosts.md) | The MongoDB adapter: requirements, the user, indexes and limits |
| [MariaDB for hosts](mariadb-for-hosts.md) | MariaDB on the `mysqli` driver: character set, grants, backups |
| [Oracle for hosts](oracle-for-hosts.md) | Oracle 19c and later through `ezoracle`: `oci8`, the user, DRCP |
| [MySQL for hosts](mysql-for-hosts.md) | MySQL on the `mysqli` driver: grants, InnoDB, replicas |
| [Microsoft SQL Server status](microsoft-sql-server-status.md) | Why there is no driver in 6.0.x and what it would take |
| [Operating systems for hosts](operating-systems-for-hosts.md) | The four tiers, the reference platform, panels and containers |
| [Composer and Git on servers](composer-and-git-on-servers.md) | How the code arrives, how extensions are packages, what not to run on a live site |
| [Writable directories and permissions](writable-directories-and-permissions.md) | What must be writable, one user or one group, the hardened file modes |
| [Sizing memory, CPU and disk](sizing-memory-cpu-and-disk.md) | The figures the repository states, and how to measure the rest |
| [Image processing and media](image-processing-and-media.md) | GD and ImageMagick, image aliases, accepted upload types |
| [Search backends](search-backends.md) | The built-in search, delayed indexing, and Solr through eZ Find |

## The short version

- **PHP 8.0 or later** for the kernel, and for Exponential Velocity, the recommended web server, with `sockets`
  (from Velocity 0.0.4.54; [chapter 2](../../../install/02-requirements.md#22-php)).
- **Databases, in the official order:** 1. SQLite 3 (default) · 2. PostgreSQL · 3. MongoDB · 4. MariaDB ·
  5. Oracle · 6. MySQL. Microsoft SQL Server is known to be possible with additional work at this time (no driver in
  6.0.x). See [Database support](../../database-support.md).
- **Writable:** `var/` and `settings/` always; `design/` and `extension/` while packages are installed from the
  administration ([chapter 8](../../../install/08-serving-the-site.md#87-file-permissions-and-ownership)).
- **Command line access** matters: Composer, cronjobs, cache clearing and the console all run with the command line
  PHP.
- **The installer checks the machine for you:** the setup wizard's system check, the kickstarter and `exp:install`
  run the same critical tests and print the fix for each failure.

## Related pages

- [Hosting companies: start here](../hosting-companies-start-here.md)
- [Hosting models](../models/README.md) and [Control panels](../panels/README.md)
- [Web servers and engines](../servers/README.md) and [Performance](../performance/README.md)
- [Operations](../operations/README.md), [Security](../security/README.md) and [Provisioning](../provisioning/README.md)
- [Chapter 2: Requirements](../../../install/02-requirements.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
