# sevenx_valkey_cache: Redis and Valkey cache backend

`sevenx_valkey_cache` stores Exponential's caches in **Redis or Valkey** instead of files. It replaces disk storage for:

* `{valkey-block ...}` template blocks (a cache-block that lives in Redis/Valkey);
* the **content view cache** (`content/view` full view);
* the **compiled INI cache** (`eZINI` arrays), which removes the per-request touches of `var/cache/ini/*.php` and makes INI caches safe for clusters.

Entries have TTL based expiry, generation locks (no cache stampede) and reverse node and subtree indexes, so publishing content purges exactly the entries it
affects. It needs **Exponential 6.0.15 or later**, whose kernel calls its hooks (`kernel/content/view.php` calls `sevenxValkeyCacheBlock`;
`lib/ezutils/classes/ezini.php` calls `sevenxValkeyINICache` from `loadCache()`, `saveCache()` and `resetCache()`), the PHP `redis` extension (or igbinary) and a
reachable Redis or Valkey server. It first shipped on 20 July 2026 (1.0.0), and 1.0.1 documented 6.0.15 as the required platform.

## Set it up

```bash
composer require se7enxweb/sevenx_valkey_cache
# settings/override/site.ini.append.php
#   [ExtensionSettings]
#   ActiveExtensions[]=sevenx_valkey_cache
php bin/php/ezpgenerateautoloads.php
```

Then configure the connection in `settings/override/valkeycache.ini.append.php`:

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

## Related

* [Chronicle](../../../history/extensions/sevenx_valkey_cache.md) and [release notes](../../../changelogs/extensions/sevenx_valkey_cache.md)
* [HTTP caching](../../../bc/6.0/http-caching.md), [SQL query cache](../../../bc/6.0/sql-query-cache.md)
* [Change ledger](../../../history/ledger/sevenx_valkey_cache.md)
* [Month: 2026-07 (all extensions)](../../../history/extensions/months/2026-07.md)
