---
title: PHP-FPM tuning
audience: hosting, admin
category: hosting
order: 20
summary: The PHP-FPM settings that matter for Exponential on a hosting server: php.ini values per pool, timeouts and the settings that must stay below them, APCu sizing and sharing, sessions, reloading the right master without stalling other sites, and how to size a pool by measuring.
keywords: PHP-FPM, FPM, pool, php_admin_value, memory_limit, max_execution_time, request_terminate_timeout, process_control_timeout, reload, PhpFpmReload, PhpFpmService, APCu, apc.shm_size, ApcuReserve, sessions, session.save_path, OPcache, sizing
type: help
---
# PHP-FPM tuning

This page is for system administrators at hosting companies who run Exponential under PHP-FPM, behind Apache, nginx
or another web server. It answers: which PHP values to set per pool, which timeouts interact, how APCu is shared,
how to reload without hurting other customers, and how to size a pool. Pool syntax itself is PHP's; see PHP's
[FPM configuration](https://www.php.net/manual/en/install.fpm.configuration.php) reference for the keys.

## Contents

1. [One pool per site](#one-pool-per-site)
2. [PHP values per pool](#php-values-per-pool)
3. [Timeouts that must agree](#timeouts-that-must-agree)
4. [APCu](#apcu)
5. [Sessions](#sessions)
6. [Reloading without stalling other sites](#reloading-without-stalling-other-sites)
7. [Sizing a pool](#sizing-a-pool)
8. [Checklist](#checklist)

## One pool per site

A pool runs PHP as the site's user ([Chapter 8, PHP-FPM](../../../install/08-serving-the-site.md#852-php-fpm)). For a
host this is the unit of isolation and of tuning: one pool per customer site, running as that customer's user, with
its own PHP values. Panels such as Plesk create one pool per domain
(`/opt/plesk/php/<version>/etc/php-fpm.d/<domain>.conf`). See
[Per-customer users and pools](../security/per-customer-users-and-pools.md).

## PHP values per pool

From [Chapter 2, php.ini settings](../../../install/02-requirements.md#25-phpini-settings) and chapter 8; set them in
the pool's `php_admin_value` lines or the pool's `php.ini`:

| Setting | Value | Source |
|---|---|---|
| `memory_limit` | at least 256M in production (the setup wizard's floor is 64M); more for large imports | chapters 2 and 8 |
| `max_execution_time` | at least 30 (wizard); 60 in chapter 2's production example | chapter 2 |
| `date.timezone` | the site's time zone | wizard critical test |
| `file_uploads` | `On` | wizard critical test |
| `upload_max_filesize`, `post_max_size` | the customer's largest upload | chapter 2 |
| `allow_url_fopen` | `On` | wizard critical test |
| `variables_order` | contains `E`, for example `EGPCS` | wizard optional test |
| `open_basedir` | empty | wizard optional test |
| OPcache | on, timestamps checked | [OPcache](opcache.md) |

The example from chapter 2:

```ini
memory_limit = 256M
max_execution_time = 60
date.timezone = Europe/Berlin
file_uploads = On
upload_max_filesize = 64M
post_max_size = 64M
allow_url_fopen = On
variables_order = "EGPCS"
```

"The values above the minimums are suggestions, not requirements." The web PHP and the command-line PHP read
different files; a change that "does not work" is most often a change to the file the other PHP reads
([Chapter 2](../../../install/02-requirements.md#25-phpini-settings)).

## Timeouts that must agree

| Limit | Where | Notes from the sources |
|---|---|---|
| `max_execution_time` | PHP | the script's own timer |
| `request_terminate_timeout` | the FPM pool | enforced by the FPM master whatever the script does; the real wall-clock limit under FPM |
| `SQLiteTransactionWait` | `site.ini [DatabaseSettings]`, default 25 s | "keep it below the request timeout ... and raise both together" ([SQLite transactions](../../../bc/6.0/sqlite-transactions.md)) |
| the setup wizard on MongoDB | — | the configuration step can take one to three minutes; raise `request_terminate_timeout` or use the console ([Database support](../../database-support.md)) |

> **Tip**
> Long jobs belong on the command line, where the PHP CLI has no execution limit by default: installs with
> `exp:install` or the kickstarter, imports, cache warming and cron jobs. Keep web timeouts sized for pages.

## APCu

APCu is used by the SQL query cache in `shared` mode and by other caches of the installation.

- Check it: Setup > System information, PHP section, or `php -m | grep apcu`, and the pool's
  `php_admin_value[apc.enabled]` ([SQL query cache, Switching it on](../../../bc/6.0/sql-query-cache.md#switching-it-on)).
- APCu is shared: `querycache.ini` says "APCu is one memory for every site of the PHP-FPM pool.
  Full with valid entries, it empties itself completely: every other cache and counter in it is lost." On a machine
  where several customers share one FPM master, one site can push the others' entries out.
- `[QueryCacheSettings] ApcuReserve=25` keeps a quarter of APCu free for the pool's other users; keep it above 0.
- Size `apc.shm_size` for what the sites keep there. Velocity ships `apc.shm_size=256M` for its own process, because
  with 32 MB the response cache and the query cache evicted each other (`settings/velocity.ini`).

## Sessions

The kernel's default session handler lets PHP keep sessions (files, as `session.save_handler` in `php.ini` says;
`site.ini [Session] Handler` empty). The role-aware HTTP cache needs sessions in **files that every server serving the
site can read**, in the same directory ([HTTP cache, Switching it on](../../../bc/6.0/httpcache.md#switching-it-on)).
If you move sessions elsewhere for a plan, the HTTP cache stores nothing for signed-in visitors and says so in
`X-Exp-Cache`.

## Reloading without stalling other sites

After a PHP class, operator or INI change the pool must run the new code. A reload is not confined to one site: "the
master asks every child of every pool to finish its request and starts no new child until the last one has, for up to
`process_control_timeout` (300 s on Plesk)". Measured on one host, an open 25 s event stream made the next page wait
22.4 s after a reload ([Velocity engines](../../../bc/6.0/velocity-engines.md#deploying-a-php-change-expvelocity-deploy)).

That is why `exp:velocity deploy` (usable on Apache-only sites with `--no-velocity`) reloads only when it must:

| `[DeploySettings] PhpFpmReload` | Behavior |
|---|---|
| `auto` (shipped) | when the pool's OPcache revalidates files (`validate_timestamps` on, `revalidate_freq` at most 60 s), wait that long instead of reloading |
| `always` | reload every time |
| `never` | neither reload nor wait |

Force a reload (`deploy --fpm-reload`) after a `php.ini` or pool change, a new PHP extension, or PHP files removed or
renamed in place. `[DeploySettings] PhpFpmService=auto` finds the right master: Plesk's `plesk-php<XY>-fpm`, the
distribution's `php-fpm` or `php<X.Y>-fpm`, Remi's `php<XY>-php-fpm`.

```bash
php bin/php/console exp:velocity deploy --no-velocity --dry-run --allow-root-user   # which FPM, reload or wait
```

## Sizing a pool

The sources give no child count for PHP-FPM; they give a method and some measured behavior:

- A rendered page costs both PHP and database CPU. On the FrankenPHP test installation (12 cores) it was about 0.25 s
  of PHP and 0.25 s of database CPU per page, and more PHP threads than about twice the cores only contended with each
  other ([FrankenPHP, Threads](../../../bc/6.0/frankenphp.md#threads-classic-mode)). The same reasoning applies to a
  pool of FPM children; confirm it on your own sites.
- Cached pages cost far less: the HTTP cache measured 508 anonymous pages a second under Apache against 28 rendered
  ([HTTP cache](../../../bc/6.0/httpcache.md#measured-on-a-reference-installation-2026-09-26-one-content-page-8-concurrent)).
  How much of a site is served from caches decides how many children it needs.
- Measure with `exp:benchmark --cold` at rising concurrency and pick the point where p90 starts to climb
  ([Benchmarking a hosted site](benchmarking-a-hosted-site.md)).

## Checklist

- [ ] One pool per site, as the site's user.
- [ ] `memory_limit`, `max_execution_time`, upload limits and `date.timezone` set in the pool.
- [ ] `request_terminate_timeout` agreed with `SQLiteTransactionWait` and the longest web request you allow.
- [ ] APCu enabled and sized; `ApcuReserve` above 0 where sites share a master.
- [ ] OPcache on with timestamp checks ([OPcache](opcache.md)).
- [ ] Deploys use `exp:velocity deploy` (or the same order by hand) and reload the right master.

## Related pages

- [OPcache](opcache.md)
- [Apache with PHP-FPM](../servers/apache-with-php-fpm.md)
- [PHP settings for Exponential](../basics/php-settings-for-exponential.md)
- [PHP selection and FPM pools](../panels/php-selection-and-fpm-pools.md)
- [SQL query cache](../../../bc/6.0/sql-query-cache.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
