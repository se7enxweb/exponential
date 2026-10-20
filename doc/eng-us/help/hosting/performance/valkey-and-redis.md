---
title: Valkey and Redis
audience: hosting, admin
category: hosting
order: 90
summary: Offering Redis or Valkey to Exponential sites with the sevenx_valkey_cache extension: what it moves out of files (content view cache, template blocks, compiled INI cache), setup, settings, sharing one server between customers safely, and the limits.
keywords: Valkey, Redis, sevenx_valkey_cache, valkeycache.ini, ValkeyCacheSettings, KeyPrefix, InstallationHash, DefaultTTL, LockTTL, Persistent, LocalCache, IniCache, valkey-block, ezvalkeycacheclear.php, cluster, cache backend
type: help
---
# Valkey and Redis

This page is for hosting companies that run Redis or Valkey and want to offer it to Exponential sites, for speed or
for sites served from several web nodes. It answers: what the `sevenx_valkey_cache` extension moves into Redis or
Valkey, how to set it up, how several customers can share one server without seeing each other's keys, and what the
limits are. The source is [sevenx_valkey_cache: Redis and Valkey cache backend](../../../features/6.0/extensions/sevenx_valkey_cache.md).

## Contents

1. [What it stores](#what-it-stores)
2. [Requirements](#requirements)
3. [Setting it up](#setting-it-up)
4. [Settings](#settings)
5. [Several customers on one server](#several-customers-on-one-server)
6. [Clearing](#clearing)
7. [What it does not cover](#what-it-does-not-cover)
8. [Checklist](#checklist)

## What it stores

The extension replaces disk storage for:

- `{valkey-block ...}` template blocks;
- the **content view cache** (`content/view` full view);
- the **compiled INI cache**, which "removes the per-request touches of `var/cache/ini/*.php` and makes INI caches
  safe for clusters".

Entries have TTL-based expiry, generation locks against cache stampedes, and reverse node and subtree indexes, so
publishing content purges exactly the entries it affects. The kernel calls the extension only when its classes exist,
so a site without it pays nothing; on a miss or when disabled, the kernel falls back to the files as before.

## Requirements

- Exponential 6.0.15 or later (its kernel calls the extension's hooks).
- The PHP `redis` extension, in every PHP that serves the site (the PHP-FPM pool, or Velocity's PHP) and in the
  command-line PHP that runs cron jobs and cache clears.
- A reachable Redis or Valkey server.

## Setting it up

1. Install and activate the extension:

   ```bash
   composer require se7enxweb/sevenx_valkey_cache
   ```

   ```ini
   # settings/override/site.ini.append.php
   [ExtensionSettings]
   ActiveExtensions[]=sevenx_valkey_cache
   ```

2. Configure the connection in `settings/override/valkeycache.ini.append.php`:

   ```ini
   [ValkeyCacheSettings]
   Host=127.0.0.1
   Port=6379
   Database=0
   KeyPrefix=sevenx_valkey_cache:
   InstallationHash=auto
   DefaultTTL=3600
   LockTTL=30
   Persistent=disabled
   LocalCache=enabled
   IniCache=enabled
   ```

3. Regenerate autoloads and clear the caches (and restart Velocity if it serves the site):

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all --allow-root-user
   ```

4. Check: request a page twice, then list the keys:

   ```bash
   redis-cli --scan --pattern 'sevenx_valkey_cache:*'
   ```

## Settings

All in `valkeycache.ini`, block `[ValkeyCacheSettings]`:

| Key | Default | Meaning |
|---|---|---|
| `Host`, `Port`, `Database` | `127.0.0.1`, `6379`, `0` | the server |
| `KeyPrefix` | `sevenx_valkey_cache:` | prefix of every key; keep it unique per installation when several sites share one server |
| `InstallationHash` | `auto` | a hash of the database name, site name, filesystem root and host name; a fixed string shares a cache between servers |
| `DefaultTTL` | `3600` | seconds, when no expiry is given |
| `LockTTL` | `30` | seconds a generation lock lives if the generating process crashes |
| `Persistent` | `disabled` | `pconnect`; "use with care when several databases share a php-fpm pool" |
| `LocalCache` | `enabled` | a per-request copy of retrieved entries |
| `IniCache` | `enabled` | compiled INI arrays in Redis or Valkey |

The content view cache entry is stored for 3600 seconds, a value set in
`kernel/private/classes/views/content/view.php`, not in an INI file.

## Several customers on one server

The extension's own advice is a unique `KeyPrefix` per installation. For a host:

| Measure | Why |
|---|---|
| a unique `KeyPrefix` per customer installation, for example `<customer>:` | keys of one site never collide with another's |
| `InstallationHash=auto` unless the same site runs on several web nodes | separates installations that share a prefix by mistake |
| a fixed `InstallationHash` string for one site on several web nodes | every node then reads the same entries |
| `Persistent=disabled` where pools are shared | the setting's own caution |
| a database number or a separate server per customer, where your Redis or Valkey setup allows access control | a prefix separates keys; it does not stop one customer's code from reading another's |

> **Warning**
> A shared Redis or Valkey server is shared memory: every site on it can read every key unless the server's own access
> control prevents it. Treat it like a shared database server in your isolation design
> ([Multi-tenant isolation](../security/multi-tenant-isolation.md)), and check your Redis or Valkey documentation for
> its access control.

Size the server for the number of pages and siteaccesses and set an eviction policy; "a server outage turns into cache
misses"; the extension decides how errors are reported.

## Clearing

- Clearing the INI cache deletes the INI entries; publishing purges the view and block entries of the affected nodes
  and their ancestors.
- The extension's page names `bin/php/ezvalkeycacheclear.php` (added 20 July 2026), which clears all caches through the same handlers the
  administration uses, flushes the keys the extension wrote, and regenerates extension autoloads. It is the fallback
  when **Clear all caches** cannot clean up after an extension is removed or a cache backend is disabled.

## What it does not cover

| Cache | Where it stays |
|---|---|
| Sessions | PHP's session handler (files by default); the role-aware HTTP cache needs files |
| Velocity's response cache | APCu and `var/cache/qbix-reverse` |
| The SQL query cache | APCu or files (`querycache.ini Storage`) |
| Compiled templates, image aliases | files in `var/` |

`settings/file.ini` mentions that a cache layer such as Memcached or Redis can be added in the cluster backend; that is
a separate, clustering topic ([Clustering and shared storage](../operations/clustering-and-shared-storage.md)).

## Checklist

- [ ] PHP `redis` extension in web and command-line PHP.
- [ ] Unique `KeyPrefix` per installation; `InstallationHash` decided.
- [ ] Server sized, eviction policy set, access control between customers considered.
- [ ] Keys appear after two page views; the behavior with the Redis or Valkey server stopped tested on staging.
- [ ] Support knows `ezvalkeycacheclear.php`.

## Related pages

- [Caching layers](caching-layers.md)
- [Several web nodes](../operations/several-web-nodes.md)
- [Cache clearing and warming](cache-clearing-and-warming.md)
- [sevenx_valkey_cache](../../../features/6.0/extensions/sevenx_valkey_cache.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
