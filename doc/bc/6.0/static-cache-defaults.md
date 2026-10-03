# Static cache defaults

## Updated: the shipped `staticcache.ini` now works

Before this change the static cache generated nothing on a stock installation:
`HostName=localhost` sent every fetch to a host that is not your site (the
failure was silent), and pages were written to `./static` in the installation
root where no cache tool looked.

| Key (`[CacheSettings]`) | Before | Now |
|---|---|---|
| `HostName` | `localhost` | empty |
| `SourceProtocol` | did not exist | `http` |
| `StaticStorageDir` | `static`, meaning `./static` in the root | `static`, meaning `var/<site>/static` |
| `MaxCacheDepth` | `3` | `12` |
| `CachedURLArray[]` | `/`, `/news*`, `/weblog*` | `/`, `/*` |
| `CachedSiteAccesses[]` | empty, meaning nothing | empty, meaning every public siteaccess |

## What to do on an existing installation

1. If you override `StaticStorageDir=static` and have files in `./static`, either
   keep that directory by setting `StaticStorageDir=/full/path/to/static`
   (an absolute path is used as given), or generate again and delete the old
   directory yourself.
2. If you serve the files from the web server, switch to `.htaccess_root_static`
   (or the rules in `doc/examples/staticcache_offload.conf`) and check
   `EZ_VAR_SITE`. `.htaccess_root` is the plain rules again and does not serve
   static files.
3. If you relied on `CachedURLArray[]` listing only `/news*` and `/weblog*`,
   put those lines back in your override; the new default caches the whole site.

Feature description: [Static cache generator](../../features/6.0/static-cache-generator.md).
