---
title: OPcache
audience: hosting, admin
category: hosting
order: 30
summary: OPcache for hosted Exponential sites: the settings under PHP-FPM, Velocity and FrankenPHP, why Velocity's differ, the one setting never to use, what JIT measured, and how to check and profile the cache.
keywords: OPcache, opcode cache, opcache.enable, opcache.enable_cli, validate_timestamps, revalidate_freq, file_update_protection, memory_consumption, max_accelerated_files, interned_strings_buffer, JIT, opcache profile, Velocity, PHP-FPM, FrankenPHP
type: help
---
# OPcache

This page is for system administrators at hosting companies who configure PHP for Exponential sites. It answers:
which OPcache settings to use under each server, why Velocity's settings differ from PHP-FPM's, which setting breaks
Exponential, what the project measured, and how to check the cache on a live site. The source is
[Chapter 10, section 10.12](../../../install/10-after-installing.md#opcache) and
[Velocity and the opcode cache](../../../features/6.0/velocity-opcode-cache-and-profile.md).

## Contents

1. [Why it matters](#why-it-matters)
2. [Under PHP-FPM](#under-php-fpm)
3. [Under Velocity](#under-velocity)
4. [Under FrankenPHP](#under-frankenphp)
5. [JIT](#jit)
6. [Checking and profiling](#checking-and-profiling)
7. [Checklist](#checklist)

## Why it matters

The comments in `settings/velocity.ini` record a measurement on a content page with four workers: no opcode cache
526.6 ms (1.9 requests a second), OPcache 390.8 ms (2.6 requests a second), recorded there as +25.8 %. "So: the cache alone is the win." Timestamp checks cost
nothing measurable, and the tracing JIT was slower than no JIT.

## Under PHP-FPM

Enable OPcache in the pool's `php.ini` (chapter 10):

```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
opcache.interned_strings_buffer=32
; keep timestamp checks on: Exponential rewrites PHP cache files (INI caches,
; compiled templates, expiry.php) under the same name
opcache.validate_timestamps=1
opcache.revalidate_freq=2
```

> **Warning**
> Never set `opcache.validate_timestamps=0` on an Exponential site: a cache file rewritten after a clear would be
> served in its old form until PHP restarts.

With timestamp checks on, `exp:velocity deploy` (with `[DeploySettings] PhpFpmReload=auto`) can wait
`revalidate_freq` seconds for changed files instead of reloading the FPM master, which spares the other sites of that
master ([PHP-FPM tuning](php-fpm-tuning.md#reloading-without-stalling-other-sites)).

## Under Velocity

Velocity passes its own PHP options to the server process (`settings/velocity.ini [PHPSettings] IniOptions[]`),
because the command-line PHP may have OPcache off system-wide:

| Option | Shipped | Why |
|---|---|---|
| `opcache.enable_cli` | `1` | the server is a command-line process |
| `opcache.memory_consumption` | `256` | MB |
| `opcache.max_accelerated_files` | `20000` | |
| `opcache.interned_strings_buffer` | `32` | |
| `opcache.revalidate_freq` | `0` | check every include |
| `opcache.file_update_protection` | `0` | see below |
| `apc.shm_size` | `256M` | APCu for the response cache and the query cache |

OPcache compares a file's time against the **request's start time**, and a Velocity worker is one long request that
started when the worker did:

- with `revalidate_freq` at its default of 2, a worker never checked a file again, so a rewritten cache file was
  served as it was when the worker first compiled it;
- with `file_update_protection` at its default, every file written after the worker started (INI caches, override
  caches, compiled templates, all regenerated after a deploy or a clear) counted as "too new" for the worker's whole
  life and was compiled again on every include. Measured on the demonstration server: the cache served 2.7 % of
  includes under Velocity against 97 % under PHP-FPM, and a rendered page cost 644 ms of CPU against 166 ms.

The server itself reads an include whole and drops the cached copy of a file changed in the last two seconds, so
nothing half written is served. No setting needs to change; a restart applies the shipped values. Chapter 10 sums it
up: "Any PHP setting that compares against 'the request time' behaves as frozen in a Velocity worker; suspect that
first when something works under PHP-FPM and not under Velocity."

## Under FrankenPHP

The embedded PHP reads no machine `php.ini`. It gets `opcache.enable=1` from `[FrankenPHPSettings] IniOptions[]` and
the tuning above from `[PHPSettings] IniOptions[]`. `revalidate_freq=0` is harmless there, because each request has
its own request time ([FrankenPHP](../servers/frankenphp.md)).

## JIT

Measured in `settings/velocity.ini`: tracing JIT 416.7 ms against 390.8 ms for OPcache alone on the same page, so
"Tracing JIT is slower than no JIT on this workload and is deliberately absent." The FrankenPHP page reports the same.
If a customer asks for JIT, measure their site with and without it before you change a plan default
([Benchmarking a hosted site](benchmarking-a-hosted-site.md)).

## Checking and profiling

```bash
grep -n "opcache\." settings/velocity.ini                     # Velocity's shipped options
php bin/php/cache.php opcache status --allow-root-user        # the cache of the process that answers
```

Setup > Caches has **Reset OPcache** and **Empty APCu** for the server process that answered the page; the other
server keeps its own ([Caches](../../../guides/caches.md#4-velocity)).

To see how requests use the cache, switch on the opcode cache profile. It is off unless a marker file exists; while it
exists every request appends one JSON line to `var/tmp/opcache_profile.log`:

```bash
touch var/tmp/opcache_profile.on
curl -s -o /dev/null https://example.com/
tail -n 3 var/tmp/opcache_profile.log
```

Read `included_not_cached` on the second and later requests of a process: close to 0 is healthy, many means files are
compiled again on every include. `opcache_ini` shows the settings the process runs with. Switch it off again by moving
the `.on` file out of `var/tmp/`; the log grows with every request while it is on.

## Checklist

- [ ] OPcache enabled in every PHP-FPM pool; `validate_timestamps=1`.
- [ ] Velocity's shipped `[PHPSettings] IniOptions[]` left in place.
- [ ] JIT changed only after measuring.
- [ ] The profile switched off after use.

## Related pages

- [PHP-FPM tuning](php-fpm-tuning.md)
- [Velocity workers and pools](../servers/velocity-workers-and-pools.md)
- [Caching layers](caching-layers.md)
- [Velocity and the opcode cache](../../../features/6.0/velocity-opcode-cache-and-profile.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
