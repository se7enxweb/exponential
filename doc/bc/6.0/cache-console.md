# Cache control from the console — `exp:cache`

**Introduced:** Exponential CMS 6.0.15  
**Location:** `bin/php/cache.php` (run as `./console exp:cache` or `php bin/php/cache.php`)  
**Shared code:** `kernel/classes/expcachemanager.php` (`expCacheManager`)

---

## What it is

Everything the administration page **Setup > Cache** (`/setup/cache`) can do,
and the newer caches of this installation, as one command:

```
./console exp:cache <group> [action] [options]
```

The page and the command call the same functions (`expCacheManager`), which in
turn call the functions the rest of the kernel uses (`eZCache`,
`eZDBQueryCache`, `ezpHttpCacheListener`, `expStaticCacheRunner`,
`expVelocity`). Nothing is implemented twice, so a button and its command clear
the same files.

Every group has its own help:

```bash
./console exp:cache --help              # the overview
./console exp:cache static --help       # one group
./console exp:cache help httpcache      # the same
```

## Options for every group

| Option | Effect |
|---|---|
| `--dry-run` | list what would be cleared (caches, directories, files, sizes, tags) and change nothing: no file is written, removed or fetched |
| `--json` | one JSON object on stdout: `ok`, `message`, `items`, `dry_run`, `data` |
| `--siteaccess=<name>` | the siteaccess whose settings and `var/` directory are used (standard eZScript option) |
| `--allow-root-user` | required when run as root, as for every Exponential script |

The last line is `PASS <message>` or `FAIL <message>` (`DRY RUN PASS ...` for a
dry run). Exit codes: **0** done, **1** failed, **2** usage error.

Permissions: like every Exponential script it needs shell access to the
installation and refuses to run as root without `--allow-root-user`. Run it as
the web server's user, so that what it recreates stays writable for the web
server.

## Command reference

### The cache list: ids and tags

| Command | Setup > Cache | What it does |
|---|---|---|
| `exp:cache list [--sizes]` | the cache table | every cache (kernel and extensions): id, on/off, tags, how it is cleared, where it lives; `--sizes` counts files and bytes |
| `exp:cache tags` | — | every tag and the caches it reaches |
| `exp:cache clear --all` | Clear all caches | every cache of the list |
| `exp:cache clear --tag=content[,template]` | Clear content caches / template caches | every cache with the tag |
| `exp:cache clear --id=template-override,classid` | Clear selected | exactly these caches |
| `exp:cache clear ... --purge [--expiry='-2 days']` | — | remove the entries for real instead of expiring them (as `ezcache.php --purge`); `--iteration-sleep`, `--iteration-max` pace it |
| `exp:cache content` | Clear content caches | shortcut for `clear --tag=content` |
| `exp:cache template` | Clear template caches | shortcut for `clear --tag=template` (compiled templates, override cache, template blocks, design base) |
| `exp:cache ini` | Clear Ini caches | shortcut for `clear --tag=ini` |
| `exp:cache all` | Clear all caches | shortcut for `clear --all` |
| `exp:cache imagealias [clear\|purge]` | Clear selected: Image alias | `clear` expires every alias (made again when next viewed); `purge` deletes the alias files and keeps the originals |

**INI caches: use the tag.** `--tag=ini` clears every configuration cache: the
global INI cache in `var/cache/ini`, the per-site INI cache, the active
extensions list, the query cache and the SSL zones. That is what the INI button
does. `--id=ini` clears only the per-site INI cache and leaves `var/cache/ini`,
where the merged settings are read from, in place: after a settings change the
old values can still be served. `ezcache.php` behaves the same way
(`--clear-tag=ini` versus `--clear-id=ini`).

```bash
./console exp:cache clear --tag=ini --dry-run
  querycache           off content,ini                new query cache generation: every stored SQL result becomes stale
  global_ini           on  ini                        directory removed
                          var/cache/ini 96 files, 626.2 KB
  ini                  on  ini                        directory removed
                          var/site/cache/ini  (does not exist) 0 files, 0 B
  active_extensions    on  ini                        active extensions list cache expired
  sslzones             off ini                        handler eZSSLZone::clearCache()
DRY RUN PASS  would clear tag ini: 5 caches
```

On that installation every merged setting was in `var/cache/ini`, and
`--id=ini` would have cleared an empty directory.

### Static cache

| Command | Setup > Cache | What it does |
|---|---|---|
| `exp:cache static status` | the static cache table | where pages are written, which sites can be generated, what each holds |
| `exp:cache static regenerate [--site=<sa>]` | Create new | fetches every page the site links to and stores it; the site's stored pages are removed first unless `--keep`; `--max-pages`, `--max-depth` bound the crawl |
| `exp:cache static regenerate --path=/about-us[,/news]` | — | only these pages (relative to the site) |
| `exp:cache static regenerate --node=62[,63]` | — | only the pages of these nodes, on every site that serves them (PathPrefix is honoured) |
| `exp:cache static clear [--site=<sa>]` | — | removes the stored pages; the web server then asks the CMS again |
| `exp:cache static clear --path=/about-us` or `--node=62` | — | removes single stored pages |

`--site` (not `--siteaccess`, which selects the script's own settings) chooses
the site; the default is every cacheable one. The crawl is the one
`bin/php/makestaticcache.php` and the page use (`expStaticCacheRunner`).

### Role-aware HTTP cache (`httpcache.ini`, `var/<site>/cache/exphttpcache`)

| Command | Setup > Cache / System information | What it does |
|---|---|---|
| `exp:cache httpcache status` | System information, HTTP cache | enabled, entries on disk, generation, purged tags, hit rate |
| `exp:cache httpcache clear` | Clear HTTP cache | every page in every permission context (a new generation) |
| `exp:cache httpcache purge --node=62[,63]` | — | the pages of these nodes (tag `l62`) |
| `exp:cache httpcache purge --url=https://example.com/about-us` | — | the page a url shows: the url is resolved to its node (a first element naming a siteaccess and that siteaccess's PathPrefix are taken into account), then purged in every context |
| `exp:cache httpcache purge --tag=c17,pl2` | — | pages carrying these tags (`l<node>`, `c<object>`, `pl<parent>`, `ct<class>`, `s<section>`, `p<path node>`, `dq`, `ez-all`) |
| `exp:cache httpcache gc` | System information, Clean up | removes expired and purged entries, orphaned bodies, old user records |
| `exp:cache httpcache reset-stats` | System information, Reset counters | starts the hit/miss counters again |

With the HTTP cache switched off in `httpcache.ini` the clearing actions answer
FAIL "switched off", exactly as the button does.

### SQL query cache

| Command | Setup > Cache | What it does |
|---|---|---|
| `exp:cache querycache status` | the query cache row | on/off and mode |
| `exp:cache querycache clear` | Clear query cache | a new generation: every stored result is stale on every server sharing `var/` |

### Velocity: response cache and precompressed static files

| Command | Same as | What it does |
|---|---|---|
| `exp:cache velocity status` | `exp:velocity cache stats` | the response cache directory, files, bytes, when it was last cleared |
| `exp:cache velocity clear` | `exp:velocity cache clear` | touches the generation marker: every page stored before now is rendered again (with the HTTP cache on, its generation too); no restart |
| `exp:cache precompress status` | — | `var/tmp/precompress`: files, bytes, `velocity.ini` PrecompressStatic/MaxFiles/MinSize |
| `exp:cache precompress clear` | — | removes the precompressed `.gz` files the engine wrote (nothing else in the directory); each is compressed again on its next request |

Both velocity commands call `expVelocity::clearCache()` and
`expCacheManager::velocityCacheStatus()`; `exp:velocity cache stats` is new and
uses the same function. The engine has no precompress rebuild of its own (a
file is compressed on its first request, and an edited file gets a new entry
because the name carries its mtime), so `precompress rebuild` answers with a
usage error saying so.

### PHP caches of this process

| Command | Setup > Cache | What it does |
|---|---|---|
| `exp:cache opcache status\|reset` | Reset OPcache | OPcache of **this** process |
| `exp:cache apcu status\|clear` | Empty APCu | APCu of **this** process |

OPcache and APCu live in the shared memory of the process that runs the code.
A command-line script has its own (only with `opcache.enable_cli` /
`apc.enable_cli`), so these commands never reach a php-fpm pool, a Velocity
server or FrankenPHP: reset theirs on Setup > Cache, which runs inside that
server, or reload the server.

### Everything at once

```bash
./console exp:cache status          # every group in one screen
./console exp:cache status --json   # for monitoring
```

## Examples

```bash
# After a settings change
./console exp:cache ini

# After a template change: templates, the Velocity response cache, the HTTP cache
./console exp:cache template
./console exp:cache velocity clear

# One page changed outside the editor: its static file and its HTTP cache entries
./console exp:cache static regenerate --node=62
./console exp:cache httpcache purge --node=62

# See what "clear everything" would touch before doing it
./console exp:cache all --dry-run
```

## For developers

`expCacheManager` returns a result array for every action (`ok`, `message`,
`items`, `dry_run`, `data`); `kernel/setup/cache.php` turns it into the page's
feedback, `bin/php/cache.php` into PASS/FAIL lines or JSON. A new cache belongs
in `site.ini [Cache] CacheItems[]` (it then shows up in `list`, `clear --id`
and on the page); a new kind of action belongs in `expCacheManager`, called from
both. Tests: `tests/tests/kernel/classes/expCacheManagerTest.php`.

See also (September 2026): [Cache clears that move directories aside](../../features/6.0/cache-clear-rename-aside.md), [Behaviour changes, 16 to 30 September 2026](behaviour-changes-2026-09b.md).
