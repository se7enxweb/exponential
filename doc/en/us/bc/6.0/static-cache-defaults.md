# Static cache defaults

Read this page if you use, or plan to use, the **static cache** (`settings/staticcache.ini`,
`bin/php/makestaticcache.php`). Before this change the shipped settings generated nothing on a stock installation.
The new defaults work out of the box, which means an existing override may now behave differently.

## In short

| | |
|---|---|
| What changed | Six `[CacheSettings]` defaults in `settings/staticcache.ini`; the files now go to `var/<site>/static`. |
| Who is affected | Installations that override `StaticStorageDir`, serve static files from the web server, or relied on the old `CachedURLArray[]`. |
| How to check | `grep -v '^#' settings/staticcache.ini \| grep -v '^$'` |
| How to fix | Follow the three steps under "How to fix". |

## What changed

Before, `HostName=localhost` sent every fetch to a host that is not your site, and the failure was silent. Pages
were written to `./static` in the installation root, where no cache tool looked.

File `settings/staticcache.ini`, block `[CacheSettings]`, scope: installation.

| Key | Before | Now |
|---|---|---|
| `HostName` | `localhost` | empty |
| `SourceProtocol` | did not exist | `http` |
| `StaticStorageDir` | `static`, meaning `./static` in the root | `static`, meaning `var/<site>/static` |
| `MaxCacheDepth` | `3` | `12` |
| `CachedURLArray[]` | `/`, `/news*`, `/weblog*` | `/`, `/*` |
| `CachedSiteAccesses[]` | empty, meaning nothing | empty, meaning every public siteaccess |

## How to check

```bash
grep -v '^#' settings/staticcache.ini | grep -v '^$'
php bin/php/makestaticcache.php --help --allow-root-user
```

The first command prints the shipped defaults from the table above. The second lists the generator options.

## How to fix

1. **You override `StaticStorageDir=static` and have files in `./static`.** Either keep that directory by setting
   `StaticStorageDir=/full/path/to/static` (an absolute path is used as given), or generate the cache again and
   delete the old directory yourself.
2. **You serve the files from the web server.** Switch to `.htaccess_root_static` (or the rules in
   `doc/examples/staticcache_offload.conf`) and check `EZ_VAR_SITE`. `.htaccess_root` is the plain rules again and
   does not serve static files.
3. **You relied on `CachedURLArray[]` listing only `/news*` and `/weblog*`.** Put those lines back in your override.
   The new default caches the whole site, limited by `MaxCacheDepth`.

## Related pages

- [Static cache generator](../../features/6.0/static-cache-generator.md)
- [Preload Sites](../../features/6.0/preload-sites-view.md)
- [Cache from the console](cache-console.md) and [HTTP caching](http-caching.md)
- [Static cache in the September 2026 chronicle](../../history/2026/2026-09a.md#13-september-caches-you-can-see-cronjobs-you-can-run)
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)
