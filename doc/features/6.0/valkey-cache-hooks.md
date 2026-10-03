# Keep the content and INI caches in Redis or Valkey

On a busy site, or on several web servers behind one balancer, the disk caches
of the content view and of the compiled INI files become the bottleneck: many
processes read and lock the same cache files, and every server has its own copy.
On 20 July 2026 the kernel gained hooks (commits `fa17892025`, `6ef123636c`,
`908994bb03`) that let an extension keep both caches in a Redis or Valkey server
instead. The extension that uses the hooks is `sevenx_valkey_cache`
(`se7enxweb/sevenx_valkey_cache`, suggested in `composer.json`). It needs the PHP
`redis` extension and a Redis or Valkey server.

Without the extension nothing changes: the kernel checks `class_exists()` first
and uses the disk cache. A site without it pays nothing.

## What the kernel does

| Where | File | Behaviour with `sevenx_valkey_cache` enabled |
|---|---|---|
| Content view cache | `kernel/private/classes/views/content/view.php` (entry point `kernel/content/view.php`) | The page's cache entry is read with `sevenxValkeyCacheBlock::get()`. On a miss the page is generated and stored with `put()` for 3600 seconds, keyed by the node id. |
| Pages that must not be cached | same class | A result that says `no_cache` is generated every time and is never stored (26 July fix). |
| Compiled INI cache | `lib/ezutils/classes/ezini.php` | `sevenxValkeyINICache::instance()->isEnabled()` is asked first; the compiled INI data are loaded from it, or saved to it, instead of `var/cache/ini/`. Clearing the INI cache deletes the entries. |

Disabled, or on a miss, the kernel falls back to the cluster file handler and the
files, exactly as before.

## Turn it on

1. Install the extension and the PHP `redis` extension on every web server.
2. Activate the extension in `settings/override/site.ini.append.php`:

   ```ini
   [ExtensionSettings]
   ActiveExtensions[]=sevenx_valkey_cache
   ```
3. Set the server address and enable the cache in the extension's own settings
   (look in `extension/sevenx_valkey_cache/settings/` for the INI file and its keys).
4. Refresh and clear:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all --allow-root-user
   ```

## Check that it works

Call a page twice and watch the Valkey keys, for example with `redis-cli --scan`
on the server. With eZINI debug on (`eZINI::setIsDebugEnabled()`), a notice says
`Loading Redis cache '...' for file '...'` when INI data come from the server.

## Limits

- Cache entries live in memory; size the server for the number of pages and
  siteaccesses, and set an eviction policy.
- A Valkey outage turns into cache misses; the extension decides how errors are reported.
- The 3600 second lifetime is set in `kernel/private/classes/views/content/view.php`, not in an INI file.

## Related

- [HTTP caching](../../bc/6.0/http-caching.md) and [SQL query cache](../../bc/6.0/sql-query-cache.md) for the other cache layers.
- Month page: [July 2026](../../history/2026/2026-07.md); [6.0.15 changelog](../../changelogs/6.0/6.0.15.md); [Cache clear: rename aside](cache-clear-rename-aside.md)

Not checked here: the extension `sevenx_valkey_cache` is not installed in this tree (it is only suggested in `composer.json`), so its own INI file and key names are not listed; read `extension/sevenx_valkey_cache/settings/` after installing it.
