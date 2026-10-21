---
title: Hosting requirements for your offers
audience: reseller, hosting
category: reselling
order: 210
summary: The server requirements a reseller must check before selling an Exponential offer - PHP 8.0 to 8.5 and its extensions, php.ini values, the six databases in the official order, Composer, the engine, image conversion, writable directories - and the questions to ask a hosting supplier.
keywords: requirements, hosting requirements, PHP 8, extensions, php.ini, memory_limit, database, SQLite, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL, Composer, Velocity, ImageMagick, GD, supplier questions
type: help
---
# Hosting requirements for your offers

This page is for resellers who sell hosting they operate themselves or buy from a supplier. Before an offer goes on
your price list, check that every server it runs on meets these requirements. The full reference is
[Requirements at a glance](../../hosting/basics/requirements-at-a-glance.md).

## Contents

1. [The minimum](#the-minimum)
2. [Databases](#databases)
3. [What is not required](#what-is-not-required)
4. [Questions for a hosting supplier](#questions-for-a-hosting-supplier)
5. [Checking a server](#checking-a-server)

## The minimum

| Need | Minimum | Recommended | Checked by |
|---|---|---|---|
| PHP | 8.0 | The newest 8.x the system offers; 8.5 is the newest tested | `composer.json` and the setup wizard (`settings/setup.ini`, `[phpversion] MinimumVersion=8.0.0`) |
| PHP extensions | `dom`, `libxml`, `simplexml`, `xsl`, `mbstring`, `iconv`, `intl`, `zlib`, one database extension, `gd` or ImageMagick | Plus `curl`, `gd`, `openssl`, `pcntl`, `opcache` | Composer and the setup wizard's critical tests |
| `php.ini` | `memory_limit` 64M, `max_execution_time` 30, `date.timezone` set, `file_uploads` on, `allow_url_fopen` on | More memory for imports and image work | The setup wizard's critical tests |
| Composer | 2.x | A current 2.x | You |
| Web server | None (Exponential Velocity or PHP's built-in server) | Velocity, or Apache or nginx with PHP-FPM | You |
| Image conversion | GD or ImageMagick's `convert` | Both | Critical test `image_conversion` |
| 64-bit PHP | Recommended | 64-bit; `composer.json` suggests it "for support of more than 30 languages" | You |
| Command line | A shell with the same PHP as the web | Yes, for Composer, cron and the console | You |

Source: [Requirements at a glance](../../hosting/basics/requirements-at-a-glance.md). PHP 8.0 support is new in the
6.0.15 line; tags `v6.0.8` to `v6.0.14` require PHP 8.1.

## Databases

> **Supported databases (Exponential 6.0.x), in order of preference:** 1. SQLite 3 (the default) · 2. PostgreSQL ·
> 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL. Microsoft SQL Server is known to be possible with additional work
> at this time (no driver in 6.0.x).

For an offer this means:

- **SQLite 3** needs no database server and is a full production database for a site served from one machine: the
  cheapest plan to run ([Database support](../../database-support.md)).
- **PostgreSQL** first when the plan has several web nodes or a separate database server.
- Offer the databases your team can back up, restore and tune; see
  [Databases in the official order](../../hosting/basics/databases-in-the-official-order.md).

## What is not required

- A particular operating system: Exponential runs wherever PHP 8 runs ([Operating systems for hosts](../../hosting/basics/operating-systems-for-hosts.md)).
- A database server, when SQLite 3 is used.
- A separate web server, when Velocity serves the site.
- Solr, Valkey or Redis: optional, for search and caching.

These are good selling points for low-cost plans.

## Questions for a hosting supplier

If you resell someone else's servers, send these questions before you sign:

- [ ] Which PHP versions do you offer, and when do you retire each one?
- [ ] Are the web PHP and the command-line PHP the same version with the same extensions?
- [ ] Can we run cron jobs and a shell as the site's user?
- [ ] May a long-running process (Exponential Velocity) run, or only PHP-FPM?
- [ ] Which databases, and who backs them up?
- [ ] One system user and PHP-FPM pool per site, or shared? ([Per-customer users and pools](../../hosting/security/per-customer-users-and-pools.md))
- [ ] Where is the data stored, and who are your subprocessors? ([Data protection for resellers](../responsibilities/data-protection.md))
- [ ] How do we get a full copy of a site if we leave?

## Checking a server

```bash
php -v                          # 8.0 or later, for Velocity too
php -m                          # the extensions listed above
php -i | grep "Integer Size"    # 64 bits recommended
composer --version              # Composer 2, and the PHP it runs with
```

The setup wizard's **System check** page, the kickstarter and `exp:install` run the critical tests of
`settings/site.ini [SetupSettings] CriticalTests` and stop with the cause and the fix when one fails
([Requirements at a glance](../../hosting/basics/requirements-at-a-glance.md#what-the-installer-checks-for-you)).

## Related pages

- [Requirements at a glance](../../hosting/basics/requirements-at-a-glance.md)
- [New server checklist](../../hosting/checklists/new-server.md)
- [Hosting reseller](../models/hosting-reseller.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
