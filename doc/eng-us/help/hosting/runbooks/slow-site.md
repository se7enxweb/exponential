---
title: "Runbook: slow site"
audience: hosting, admin
category: hosting
order: 30
summary: What to do when an Exponential site is slow: measure first with exp:benchmark, separate cold from warm, check the caches, OPcache, workers or PHP-FPM children, the database and the request logs, then apply the levers in order of effect.
keywords: runbook, slow site, performance, latency, benchmark, exp:benchmark, cache, view cache, response cache, OPcache, PHP-FPM, workers, database, SQLite ANALYZE, warm, static cache
type: help
---
# Runbook: slow site

For on-call staff and administrators when a customer reports that their Exponential site is slow. The levers and
their order come from [10.12 Performance tuning](../../../install/10-after-installing.md#1012-performance-tuning).

## Contents

1. [Measure first](#measure-first)
2. [Is it everyone, or one page?](#is-it-everyone-or-one-page)
3. [Checks in order](#checks-in-order)
4. [Fixes in order of effect](#fixes-in-order-of-effect)
5. [After the incident](#after-the-incident)

## Measure first

A slow site is a number, not a feeling. Measure the front page and the pages the customer named:

```bash
curl -s -o /dev/null -w '%{http_code} %{time_total}\n' https://www.example.com/
php bin/php/console exp:benchmark --allow-root-user
```

`exp:benchmark` requests the front page, the first menu pages, the search page and the admin login page, 100 times
each after 5 warm-up requests, 4 at a time, and prints median, p90 and p99. If you saved a baseline when the site was
healthy, compare with it:

```bash
php bin/php/console exp:benchmark --baseline=var/benchmark/base.json --threshold=15 --allow-root-user
```

> **Warning**
> A benchmark is load. On a site that is already struggling, run a small one (`--requests=20`) or test from a
> staging copy. Details: [Benchmarking a hosted site](../performance/benchmarking-a-hosted-site.md).

## Is it everyone, or one page?

| Pattern | Likely cause |
|---|---|
| Every page slow, also cached ones | the machine: CPU, memory, swap, disk; or the engine has no free worker |
| Only the first request after a change is slow | caches being rebuilt after a clear or a deploy: normal, warm them |
| Only signed-in users (editors) are slow | they bypass the anonymous caches; check the database and OPcache |
| One page slow | that page's template or fetch; check its log lines |
| Slow at certain times | a cron job, a backup, an import, or a crawler; check the request logs and cron times |

## Checks in order

1. **The machine**: `uptime`, `free -m`, `df -h`, `iostat` if available. Swap use or a full disk explain everything
   else ([Disk full](disk-full.md)).
2. **The caches**: `php bin/php/cache.php status --json --allow-root-user` should end in `PASS`. Check that
   `[ContentSettings] ViewCaching=enabled` and `[TemplateSettings] TemplateCache=enabled` (both shipped) were not
   switched off in an override.
3. **The engine**:
   - Velocity: `php bin/php/console exp:velocity status --allow-root-user` (workers as configured);
     `exp:velocity cache stats` for the response cache.
   - PHP-FPM: are all children busy? See [PHP-FPM tuning](../performance/php-fpm-tuning.md).
4. **OPcache**: `php bin/php/cache.php opcache status --allow-root-user`. Never run an Exponential site with
   `opcache.validate_timestamps=0`; under Velocity the shipped settings matter ([OPcache](../performance/opcache.md)).
5. **The database**: on SQLite after an install or a large import, "the query planner has no statistics": run
   `sqlite3 var/storage/sqlite3/exponential.db ANALYZE` once ([12.10](../../../install/12-troubleshooting.md#1210-databases)).
   On MongoDB, check that the indexes from `bin/mongodb/create_indexes.js` exist.
6. **Traffic**: `security.log` and `404.log` show bursts of probes and not-found requests; a crawler or an attack can
   look like slowness ([Request logs](../operations/request-logs.md)).
7. **The static cache**: it should be off (`[ContentSettings] StaticCache=disabled`, the shipped value). With it on,
   every publish regenerates affected pages inside the editor's request ([Static cache](../performance/static-cache.md)).

## Fixes in order of effect

From 10.12:

1. **Serve with Velocity and its response cache**; persistent workers keep the application warm.
2. **Keep the content view cache and template-block cache on**, and warm the site after a clear:
   `php bin/php/warm.php --allow-root-user`.
3. **OPcache**, configured as [OPcache](../performance/opcache.md) describes.
4. **APCu** for Velocity's response cache memory tier and the SQL query cache in `shared` mode.
5. **The SQL query cache**, measured on the site.
6. **The database** tuning of [chapter 9](../../../install/09-databases.md).

Change one lever at a time and measure again with the baseline.

## After the incident

- [ ] Save a new baseline when the site is healthy again:
      `php bin/php/console exp:benchmark --save=var/benchmark/base.json --allow-root-user`.
- [ ] Note the cause and the fix in the site's record.
- [ ] If the site has outgrown its plan, tell the customer with the measurements ([Designing hosting plans](../business/designing-hosting-plans.md)).

## Related pages

- [Caching layers](../performance/caching-layers.md)
- [Cache clearing and warming](../performance/cache-clearing-and-warming.md)
- [Benchmarking Exponential](../../../guides/benchmarking.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
