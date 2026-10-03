# sevenx_valkey_cache: Redis and Valkey cache backend

This page is for administrators who want faster pages or run several web servers. `sevenx_valkey_cache` stores
Exponential's caches in **Redis or Valkey** instead of files. It replaces disk storage for:

- `{valkey-block ...}` template blocks (a cache block that lives in Redis or Valkey);
- the **content view cache** (`content/view` full view);
- the **compiled INI cache** (`eZINI` arrays). This removes the per-request touches of `var/cache/ini/*.php` and makes
  INI caches safe for clusters.

Entries have TTL based expiry, generation locks (no cache stampede) and reverse node and subtree indexes, so publishing
content purges exactly the entries it affects. It first shipped on 20 July 2026 (1.0.0); 1.0.1 documented 6.0.15 as the
required platform.

## Requirements

- **Exponential 6.0.15 or later**, whose kernel calls the extension's hooks (see "What the kernel does").
- The PHP `redis` extension (or igbinary).
- A reachable Redis or Valkey server.

## Set it up

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

3. Regenerate autoloads and clear the caches:

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all --allow-root-user
```

4. Check that it works: call a page twice, then list the keys:

```bash
redis-cli --scan --pattern 'sevenx_valkey_cache:*'
```

   Keys are listed. With eZINI debug on, a notice says `Loading Redis cache '...' for file '...'` when INI data come
   from the server.

## Settings

All keys are in `valkeycache.ini`, block `ValkeyCacheSettings`; set them in `settings/override`.

| Key | Default | Meaning |
|---|---|---|
| `Host`, `Port`, `Database` | `127.0.0.1`, `6379`, `0` | Server |
| `KeyPrefix` | `sevenx_valkey_cache:` | Prefix of every key. Keep it unique per installation when several sites share one server |
| `InstallationHash` | `auto` | A hash of the database name, site name, filesystem root and host name; use a fixed string to share a cache between servers |
| `DefaultTTL` | `3600` | Seconds, when no expiry is given |
| `LockTTL` | `30` | Seconds a generation lock lives if the generating process crashes |
| `Persistent` | `disabled` | `pconnect`; use with care when several databases share a php-fpm pool |
| `LocalCache` | `enabled` | A per-request copy of retrieved entries, saving round trips. Disable only when debugging cache consistency |
| `IniCache` | `enabled` | Compiled INI arrays in Redis/Valkey. Needs Exponential 6.0.15 |

## Use it in templates

```
{valkey-block expiry=3600 subtree_expiry=$node.node_id keys=array( 'my-block', $node.node_id )}
    ... expensive template logic ...
{/valkey-block}
```

| Parameter | Meaning |
|---|---|
| `expiry` | TTL in seconds (default 7200) |
| `subtree_expiry` | Node id as a subtree dependency: any change under it purges the block |
| `node_expiry` | Node id as a node dependency |
| `keys` | More key components (array or scalar) |
| `lock` | Generation locks against stampedes (default `true`) |
| `ignore_content_expiry` | Ignore the global `template-block-cache` and `global-template-block-cache` timestamps |

From code: `sevenxValkeyCacheBlock::instance()` offers `put( $key, $content, $ttl )`, `get( $key )`, `purge( $key )` and `flush()`. The class
`sevenxValkeyCacheHandler` is registered as `ContentSettings.StaticCacheHandler` and purges the view and block entries indexed by the affected node ids and their
ancestors when content changes.

## Clear everything from the shell

`bin/php/ezvalkeycacheclear.php` (added 20 July 2026) clears all caches through the same PHP cache handlers the admin uses, flushes the Redis/Valkey keys the
extension wrote, and regenerates the extension autoloads, passing `--exclude` for every extension directory that is not in `ActiveExtensions[]`, so
disabled-but-present extensions disappear from `var/autoload/ezp_extension.php` and from `class_exists()` checks. It is the fallback when the admin's **Clear all
caches** cannot clean up after an extension is removed or a cache backend is disabled.

## Faster INI cache (1.0.2, 30 July 2026)

The compiled INI cache is **one Redis hash** keyed by the file's MD5 (`HGET`, `HSET`, `HDEL`) instead of one key per file. The previous design scanned the whole
keyspace (`SCAN` and `MGET`) on every request, which is expensive on a busy server; the request-scoped in-memory cache stays and is filled on demand.

## What the kernel does (hooks)

The kernel calls the extension only when its classes exist (`class_exists()` is checked first), so a site without the extension pays nothing. Hooks added on
20 July 2026 (commits `fa17892025`, `6ef123636c`, `908994bb03`):

| Where | File | Behaviour with the extension enabled |
|---|---|---|
| Content view cache | `kernel/private/classes/views/content/view.php` (entry `kernel/content/view.php`) | The page's entry is read with `sevenxValkeyCacheBlock::get()`; on a miss the page is generated and stored with `put()` for 3600 seconds, keyed by the node id. The 3600 seconds are set in that file, not in an INI file. |
| Pages that must not be cached | same class | A result that says `no_cache` is generated every time and never stored (26 July 2026 fix). |
| Compiled INI cache | `lib/ezutils/classes/ezini.php` | `sevenxValkeyINICache::instance()->isEnabled()` is asked first; compiled INI data are loaded from or saved to the server instead of `var/cache/ini/`. Clearing the INI cache deletes the entries. |

On a miss or when disabled, the kernel falls back to the cluster file handler and the files, exactly as before.

## Limits

* Entries live in memory: size the server for the number of pages and siteaccesses, and set an eviction policy.
* A server outage turns into cache misses; the extension decides how errors are reported.

## Related pages

- [Cache clear: rename aside](../cache-clear-rename-aside.md)
- [HTTP caching](../../../bc/6.0/http-caching.md), [SQL query cache](../../../bc/6.0/sql-query-cache.md)
- [6.0.15 changelog](../../../changelogs/6.0/6.0.15.md), [July 2026](../../../history/2026/2026-07.md)
- [Chronicle](../../../history/extensions/sevenx_valkey_cache.md) and [release notes](../../../changelogs/extensions/sevenx_valkey_cache.md)
- [Change ledger](../../../history/ledger/sevenx_valkey_cache.md)
- [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
- [Month: 2026-07 (all extensions)](../../../history/extensions/months/2026-07.md)
