# Logs, INI cache and expiry timestamps per site (multi-site hosting)

Read this page if you host several sites on one Exponential installation, each with a VarDir of its own, and want the
logs of each site in its own directory, the cache of a site on another file system (memory) or the INI cache of each
site cleared on its own. It describes the settings, how they behave under Velocity and on the command line, and what
to do when an installation moves from a setup of its own to them.

## In short

| | |
|---|---|
| Logs per site | `site.ini [FileSettings] UseGlobalLogDir=disabled`: the debug logs (`error.log`, `warning.log`, ...) and the default logs of `eZLog` go to the log directory of the site instead of `var/log`. |
| Logs on another file system | `LogDir` may be an absolute path, as `CacheDir`; `LogVarDir=var_log` puts the logs of every site in `var_log/<site>/log` with one line. |
| Cache in memory | `CacheVarDir=var_cache` puts the caches of every site in `var_cache/<site>/cache`, one memory file system for all of them; or mount one at `var/<site>/cache`. `ExpiryDir` keeps `expiry.php` where the storage is, so a restart does not lose the expiry timestamps. |
| INI cache per site | `INICacheDir=site`: the INI files of the site are cached in its cache directory, and clearing them leaves the other sites alone. |
| Default | Every setting keeps the behaviour of before. |
| Velocity, scripts | Every value is derived again from site.ini when the siteaccess changes; nothing is kept from one request or one site to the next. |
| Who must act | Nobody. |

## Settings

All in `site.ini [FileSettings]`, for each siteaccess of the site (or in an override that applies to all of them, see
[per-site settings inside extensions](multi-site-ini-overrides.md)):

| Key | Default | Meaning |
|---|---|---|
| `LogDir` | `log` | The log directory, inside VarDir; an absolute path is used as it is. `storage.log` and the debug bar's log go there in any case. |
| `UseGlobalLogDir` | `enabled` | `disabled` writes the debug logs and the logs `eZLog::write()` writes to its default directory into the log directory of the site. |
| `LogVarDir` | empty | A tree for the logs of every site: the first directory of VarDir is replaced, `var/example` → `var_log/example`, so the log directory is `var_log/example/log`. A relative or an absolute path. |
| `CacheVarDir` | empty | The same for the caches: `var_cache/example/cache`. A directory inside the installation, so that its public caches can be served. |
| `ExpiryDir` | empty | Where `expiry.php` is kept: empty in the cache directory, otherwise a directory inside VarDir or an absolute path. |
| `INICacheDir` | `global` | `site` caches the INI files of the site in `<cache directory>/ini/`; `global` in `var/cache/ini/`. |

## Many sites with their logs and caches in trees of their own

The setup of an installation with dozens of sites, each in a site extension with `VarDir=var/<site>`: one line each
in `settings/override/site.ini.append.php` for all of them.

```ini
[FileSettings]
UseGlobalLogDir=disabled
LogVarDir=var_log
CacheVarDir=var_cache
ExpiryDir=expiry
INICacheDir=site
```

```
# /etc/fstab: the caches of all sites in memory
tmpfs  /srv/www/exponential/var_cache  tmpfs  size=8g,mode=0775,uid=www-data,gid=www-data  0 0
```

- The logs of `var/example` are in `var_log/example/log`, its cache in `var_cache/example/cache` (with the INI cache
  in `ini/`), `expiry.php` in `var/example/expiry/`. The storage stays in `var/example/storage`.
- Velocity serves the public caches below `var_cache/` (packed scripts and style sheets, text to image) and nothing
  else there: `expVelocity::staticPaths()` adds them to the paths of all three engines when `CacheVarDir` is set. For
  Apache, enable the commented `var_cache` rule in `.htaccess_root`; for nginx the commented `location` in
  [serving the site](../../install/08-serving-the-site.md).
- With a cluster file handler the rewrite rules of `index_cluster.php` name `var/` only; the cache would have to be
  added there as well.
- Set the settings where every siteaccess of every site reads them (the global override): Velocity reads
  `CacheVarDir` from the settings it starts with.

## A site with its cache in memory

```ini
# settings/siteaccess/example/site.ini.append.php (and the other siteaccesses of the site)
[FileSettings]
VarDir=var/example
UseGlobalLogDir=disabled
LogDir=/srv/logs/example
ExpiryDir=expiry
INICacheDir=site
```

```
# /etc/fstab: the cache of the site in memory, emptied at every restart
tmpfs  /srv/www/exponential/var/example/cache  tmpfs  size=2g,mode=0775,uid=www-data,gid=www-data  0 0
```

- The cache directory stays `var/example/cache`, so the files the web server and Velocity serve from it (packed
  scripts and style sheets in `cache/public`, `cache/texttoimage`) keep their addresses. An absolute `CacheDir` would
  move them out of the paths that are served, which is why site.ini calls it unsupported.
- `expiry.php` holds the times at which caches and image aliases were last cleared. The image aliases are in the
  storage, which survives a restart; if `expiry.php` were emptied with the cache, image aliases made before a clear
  would count as current again. With `ExpiryDir=expiry` it is in `var/example/expiry/`.
- The logs are written to `/srv/logs/example/` from the moment the siteaccess is known. Messages written before that
  (while the request is matched to a siteaccess) still go to `var/log`.

## What is cleared where

The cache commands and Setup > Caches work as before; the settings only decide which directory a cache item names.

| Cache | What is removed |
|---|---|
| INI cache (`ini`) | `<cache directory of the site>/ini/`: with `INICacheDir=site`, the INI cache of this site only |
| Global INI cache (`global_ini`) | `var/cache/ini/`: the INI files read before a siteaccess is known, and those of sites with `INICacheDir=global` |
| Caches cleared by timestamp (view cache, template blocks, user info, ...) | A timestamp in the `expiry.php` of the site (in `ExpiryDir` when set); purging removes the expired files from the cache directory |
| Clear all, purge all | Everything in the cache directory of the site and `var/cache/ini/`; `expiry.php` in `ExpiryDir` stays and gets the new timestamps |

Checked on an installation with `bin/php/ezcache.php -s <siteaccess>` for `global_ini`, `ini`, the tag `content` and
all caches, each cleared and purged: the INI cache of the site was removed only by `ini` and the full clears, the
global one only by `global_ini` and the full clears, `expiry.php` in `ExpiryDir` was kept by every one of them and
received the timestamps, and the `expiry.php` in the cache directory was not touched.

A running Velocity holds settings in memory as well: after clearing an INI cache, restart it, as Setup > Caches says.

## Velocity, scripts and siteaccess changes

`eZSiteAccess::change()` sets all three for the siteaccess it changes to: the log directory
(`eZUpdateDebugLogDirectory()`), the INI cache directory (`eZSiteAccess::updateINICacheDirectory()`) and the expiry
file (`eZExpiryHandler::resetForCurrentCacheDirectory()`). It does so for every front controller and for every
script, so `bin/php/ezcache.php -s example` and the cronjobs of a site write into the directories of that site.

- **Velocity** serves the sites of an installation one request after another in the same worker. Between requests it
  removes every global except the type registries of its Exponential preset and restores the static properties. The
  three paths live in globals (`eZDebugLogDir`, `eZINI_CONFIG_CACHE_DIR`, the instances of `eZDebug` and
  `eZExpiryHandler`) and in no static property, so the next request starts with the defaults and takes the paths of
  its own site when its siteaccess is known.
- **Two sites in one process** (a script that changes siteaccess): the second change takes the paths of the second
  site; a site without the settings goes back to `var/log`, `var/cache/ini/` and its cache directory.
- **A directory the installation set itself**: a multi-site wrapper may set `$GLOBALS['eZINI_CONFIG_CACHE_DIR']`
  before startup, for one INI cache shared by all projects. A site with `INICacheDir=site` uses its own; after it, a
  site without the setting gets the wrapper's directory back.

Checked with Velocity 0.0.4.45 (engine qbix, four persistent workers) on an installation with two siteaccesses, one
with `UseGlobalLogDir=disabled`, `INICacheDir=site` and `ExpiryDir` set, one without: of 40 requests alternating
between them, each worker serving both, the 20 errors of the one were all in its own log directory and the 20 of the
other all in `var/log`. Thirty further requests to the site without the settings left the INI cache of the other
untouched and wrote theirs into `var/cache/ini`. With `LogVarDir=var_log`, `CacheVarDir=var_cache`,
`INICacheDir=site` and `ExpiryDir=expiry` in the global override, Velocity served the packed style sheets and scripts
from `var_cache/<site>/cache/public/` and refused the other files there, the errors went to `var_log/<site>/log`, and
clearing and purging the INI cache, the content tag and all caches worked on `var_cache` while `expiry.php` stayed in
`var/<site>/expiry/`. The same with nginx and PHP-FPM.

## Moving an existing multi-site setup to these settings

Installations that already kept logs and caches apart, with changes of their own to the kernel, can do the same with
the settings:

- Logs in a tree of their own (`var_log/<site>/log`): `UseGlobalLogDir=disabled` and `LogVarDir=var_log`.
- Caches in a tree of their own (`var_cache/<site>/cache`): `CacheVarDir=var_cache`. Rules the web server already has
  for `/var_cache/` keep working; Velocity serves the public caches itself.
- An `expiry.php` kept outside the cache under another name: copy it to `var/<site>/expiry/expiry.php` before the
  first request and set `ExpiryDir=expiry`. Without it, image aliases created before the last clear would count as
  current.
- An INI cache per site: `INICacheDir=site`.
- Code that rewrote every `var/<site>/log` path of the installation is not needed and not provided: extensions that
  log through `eZLog` or `eZSys::logDirectory()` follow the settings; one that builds the path from VarDir and LogDir
  itself has to be changed to use `eZSys::logDirectory()`.

## For extension authors

- `eZSys::logDirectory()` returns the log directory of the site, as `eZSys::cacheDirectory()` does for the cache;
  both follow `LogVarDir` and `CacheVarDir` (`eZSys::relocatedVarDirectory()`). Build no log or cache path from
  `VarDir` yourself.
- `eZDebug::instance()->logDirectory()` returns where the debug logs go; `eZLog::write()` without a directory writes
  there. A log written to `var/log` by hand stays in `var/log`.
- `eZExpiryHandler::filePath()` returns the `expiry.php` of the site.

## Tests

- `eZSiteLogAndExpiryPathsTest` (no database): the log directory inside VarDir and absolute; `LogVarDir` and
  `CacheVarDir`, relative and absolute, with VarDir `var` and with an absolute VarDir; the debug logs and `eZLog` there
  and back to `var/log`; `storage.log` in an absolute `LogDir`; `UseGlobalLogDir`; `ExpiryDir` empty, relative,
  absolute and with a moved cache; timestamps that survive an emptied cache directory; the shared expiry instance
  after a change.
- `eZSiteAccessSitePathsTest` (writes siteaccesses of its own, no database): `eZSiteAccess::change()` to a site with
  the settings and then to one without, in the same process; `LogVarDir`, `CacheVarDir` and the INI cache in the
  moved cache; a directory the installation set, kept and restored; a new request without the globals Velocity
  removes.
- `expVelocityEnginesTest`: the public caches below `CacheVarDir` are served and nothing else there; an absolute
  path, `..` or other characters are not.

## Related pages

- [Per-site settings inside extensions (multi-site hosting)](multi-site-ini-overrides.md)
- [Renaming caches aside before deleting them](cache-clear-rename-aside.md)
