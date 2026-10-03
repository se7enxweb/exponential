# Cache clears that move directories aside

This page is for administrators of busy or large sites who clear caches while the site is serving. Clearing a big
cache used to delete it file by file inside the request. While that ran, other requests were already generating the
cache again into the same tree, and the delete could take what they had just written; the request that cleared it
waited for all of it. Since 24 and 25 September 2026 a clear **renames** the cache directory out of the way in one step
and deletes it from there. It is on by default; there is nothing to set up.

## What you get

- A cache clear (Setup > Caches, `php bin/php/ezcache.php`, `exp:cache`, `eZCache::clearAll()` and friends) returns
  quickly, even for a cache with hundreds of thousands of files.
- Nothing generated while the old files are being deleted is caught by the delete.
- A clear in the administration leaves nothing behind. The view cache, cache-block, user info, translation and packer
  caches used to stop being served (a timestamp in `expiry.php`) but stayed on disk until the `cache_cleanup` cronjob
  ran.
- The text to image cache and `ezjscore`'s public packer cache (javascript and stylesheets) are renamed aside instead
  of deleted in place.

## Run the cleanup cronjob

Clearing the view cache or cache-block only moves a timestamp, so files stayed until the same key was generated again,
and keys that never come back stayed for good. The cronjob part `cache_cleanup` removes:

- files older than the global expiry of their cache;
- files older than `MaxAge`;
- the directories that leaves empty;
- what `DelayedCacheBlockCleanup` parks in `template-block-expiry`.

It runs for the var directory of the given siteaccess, so run it once per `VarDir`:

```bash
php runcronjobs.php -s site cache_cleanup
```

After a global clear, the cleanup moves `content/` and `template-block/` aside in one rename instead of looking at each
file (tested with 200,000 files; a second overlapping run started with 123,121 left, and both finished with nothing
left). Which clear was last handled is kept in `cachecleanup-state.json`, not a PHP file, so the opcode cache cannot
serve an old copy. The cronjob reads `expiry.php` through its own `eZExpiryHandler`, because `runcronjobs.php` creates
the shared instance before switching to the siteaccess, so that one holds the timestamps of the default var directory.

## Settings

| File | Block | Key | Default | Scope | Meaning |
|---|---|---|---|---|---|
| `settings/site.ini` | `FileSettings` | `RenameBeforeDelete` | `enabled` | global | Rename a cache directory aside before deleting. `disabled` deletes file by file. |
| `settings/site.ini` | `FileSettings` | `RenameExpiredCaches` | `enabled` | global | Also move the view cache, cache-block, user info and translation cache aside when cleared in the administration or with `ezcache.php`. Needs `RenameBeforeDelete`. Publishing content still only moves timestamps. |
| `settings/cronjob.ini` | `CacheCleanupSettings` | `MaxAge` | `172800` | global | Seconds after which a file is removed whatever its expiry. `0` removes only expired files. |
| `settings/cronjob.ini` | `CacheCleanupSettings` | `IterationSleep` | `0` | global | Microseconds to sleep after every 1000 files |
| `settings/cronjob.ini` | `CacheCleanupSettings` | `RenameAfterClear` | `enabled` | global | After a global clear, move the whole directory aside. `disabled` sweeps file by file. |
| `settings/cronjob.ini` | `CronjobPart-cache_cleanup` | `Scripts[]` | `cachecleanup.php`, `httpcache_cleanup.php` | global | What the part runs |

## How it works

`eZCacheTrash` renames a directory into `.cleanup-trash` in the cache directory, named after the cache and the time,
for example `template-compiled-20260924-151112-<id>`, `override-...`, `ini-...`, and deletes it from there. The id
keeps two clears in the same second apart.

- Between `eZCacheTrash::begin()` and `end()` nothing is deleted. All renames come first, then the file-by-file sweep
  of caches that were not cleared, then the deleting, so no cache waits behind another. `end()` runs in a `finally`
  block since 24 September. Before, an exception in one cache item left the depth raised, and nothing that process
  moved aside afterwards was ever deleted.
- A directory outside the cache directory, such as the global INI cache in `var/cache`, goes into the trash of the
  directory it is in. Where the rename would cross a file system (part of the cache is linked to another disk), a
  trash next to the directory is used. A directory that is itself a link is emptied into a trash inside it. Deleting
  never follows a link, and the cache directory itself is never renamed.
- A trash directory stays in place and only its contents go, so an overlapping clear can always rename into it.
- A local `SharedTranslationCacheDir` is left alone: it belongs to every installation that shares it.

## Limits

Only the file system handler (`eZFSFileHandler`) renames. A clustered cache goes through its own handler.

## Related pages

- [Cache console](../../bc/6.0/cache-console.md) (`exp:cache`)
- [Static cache generator](static-cache-generator.md), [Velocity response cache](velocity-response-cache.md)
- [Velocity: running Exponential in a persistent-worker web server](velocity-persistent-worker-server.md)
- [Behaviour changes, 16 to 30 September 2026](../../bc/6.0/behaviour-changes-2026-09b.md)
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)
- [Chronicle, 16 to 30 September 2026](../../history/2026/2026-09b.md)
