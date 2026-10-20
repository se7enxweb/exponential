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
| Who must act | Nobody, unless `[FileSettings] AllowedDeletionDirs` relied on an entry that does not exist or on a prefix of a directory name (see [what is cleared where](#what-is-cleared-where)). |

## Settings

All in `site.ini [FileSettings]`, for each siteaccess of the site (or in an override that applies to all of them, see
[per-site settings inside extensions](multi-site-ini-overrides.md)):

| Key | Default | Meaning |
|---|---|---|
| `LogDir` | `log` | The log directory, inside VarDir; an absolute path is used as it is. `storage.log` and the debug bar's log go there in any case. |
| `UseGlobalLogDir` | `enabled` | `disabled` writes the debug logs and the logs `eZLog::write()` writes to its default directory into the log directory of the site. |
| `LogVarDir` | empty | A tree for the logs of every site: the first directory of VarDir is replaced, `var/example` → `var_log/example`, so the log directory is `var_log/example/log`. A relative or an absolute path. |
| `CacheVarDir` | empty | The same for the caches: `var_cache/example/cache`. A directory inside the installation, so that its public caches can be served; an absolute path is used, but its public caches are not served, as with an absolute `CacheDir`. |
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
- With a cluster file handler, see [cluster](#cluster-dfs) below.
- Set the settings where every siteaccess of every site reads them (the global override). Velocity writes its list of
  served paths when it starts, without a siteaccess: it reads `CacheVarDir` from the global settings and from the
  full settings of every siteaccess in `[SiteAccessSettings] AvailableSiteAccessList`, so a `CacheVarDir` set for
  some sites only is served as well. After changing it, restart Velocity.

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

Directories outside the installation: `eZDir::recursiveDelete()`, which every cache clear goes through, deletes only
inside the root and inside `[FileSettings] AllowedDeletionDirs`. It also deletes inside the directories the current
site writes to, however they are set (an absolute `CacheDir`, `LogDir` or `ExpiryDir`, a `CacheVarDir` or
`LogVarDir` outside the root, a `CacheVarDir` that is a link to a memory file system, a
`$GLOBALS['eZINI_CONFIG_CACHE_DIR']` set by a wrapper): their contents, and the cache directory as a whole, as
inside the root; never the log or expiry directory itself, nothing beside them, never a file system root, and never
the installation or a directory that contains it (a `LogDir` of `/var/www` allows nothing). An `AllowedDeletionDirs`
entry that does not exist allows nothing (before, it allowed every path), and `/srv/cache` no longer allows
`/srv/cache-other`.

## Velocity, scripts and siteaccess changes

`eZSiteAccess::change()` sets all three for the siteaccess it changes to: the log directory
(`eZSiteAccess::updateLogDirectory()`), the INI cache directory (`eZSiteAccess::updateINICacheDirectory()`) and the
expiry file (`eZExpiryHandler::resetForCurrentCacheDirectory()`). It does so for every front controller, including
`soap.php` and `webdav.php`, and for every script, so `bin/php/ezcache.php -s example` and the cronjobs of a site
write into the directories of that site.

With `INICacheDir=site` the `site.ini` of the siteaccess itself is cached in the INI cache of the site as well:
`change()` reads it from the shared `var/cache/ini/` first (that is where it learns `INICacheDir`) and once more from
the directory of the site, so clearing the INI cache of the site refreshes it. That second read costs one cached file
per request for such a site. A site changed to afterwards in the same process never leaves a copy of its `site.ini`
in the INI cache of the site before.

- **Velocity** serves the sites of an installation one request after another in the same worker. Between requests it
  puts back the globals and static properties of its warm-up and removes every other global, except the type
  registries of its Exponential preset. The three paths live in globals (`eZDebugLogDir`, `eZINI_CONFIG_CACHE_DIR`,
  the instances of `eZDebug` and `eZExpiryHandler`) and in no static property, so the next request starts with the
  defaults and takes the paths of its own site when its siteaccess is known.
- **The Velocity warm-up** renders a site in the parent process before the workers are forked. It puts the log and
  INI cache directories that site set back to those of a request without a siteaccess
  (`eZSiteAccess::resetSitePaths()`) before it removes the globals of the render, so no request starts with the
  directories of the site the warm-up rendered.
- **Two sites in one process** (a script that changes siteaccess, or the content cache clearing that visits every
  siteaccess): the second change takes the paths of the second site; a site without the settings goes back to
  `var/log`, `var/cache/ini/` and its cache directory. Changing to the same site twice changes nothing.
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

## What reads the logs

- **Setup log** (`var/log/setup.log`, the installation runs): the errors and warnings of each step are counted in the
  `error.log` and `warning.log` eZDebug writes when the step begins, the log directory of the site with
  `UseGlobalLogDir=disabled`, `var/log` otherwise. `setup.log` itself stays in `var/log`.
- **Setup > System information** names the log directory of the current site in the Storage card, warns when it or
  `var/log` cannot be written, and finds the cronjob logs (`cronjob*.log`) in `var/log` and in the log directory of the
  site. It describes the siteaccess it runs in; the log directories of other sites are not listed.
- **Audit records** (`audit.ini [AuditSettings] LogDir`, `log/audit` by default) stay inside VarDir: they belong with
  the storage of the site, are kept for years and are checked against their hash chain, so `LogVarDir`, `LogDir` and
  `UseGlobalLogDir` do not move them (a `LogVarDir` on a memory file system would lose them). An absolute
  `[AuditSettings] LogDir` moves them. The old text files of 4.x names (`[AuditCompatSettings] LegacyFiles`) are
  written into the same directory, and an absolute `VarDir` is used as it is.
- **The view counter cronjob** (`updateviewcount`) keeps `updateview.log` in the log directory of the site.

## Cluster (DFS)

- With `eZDFSFileHandler` the files of the cache are kept in the cluster database and on the DFS mount under the same
  names as on disk, so under `CacheVarDir` they are named `var_cache/<site>/cache/...`. The split cache table
  (`MetaDataTableNameCache`) takes every name that contains `CacheDir` and not `StorageDir`, which a name below
  `var_cache` does. Emptying the split cache table (`deleteCacheFiles()`) takes the names below the cache directory
  of the site, `eZSys::cacheDirectory()`.
- `index_cluster.php` serves the name the request asks for when the cluster database has it, but the web server only
  sends it the paths its rewrite rules name. Add the public caches below `CacheVarDir` next to those of `var/`:

  ```
  RewriteRule ^/var_cache/([^/]+/)?cache/(texttoimage|public)/.* /index_cluster.php [L]
  ```

  Velocity serves the public caches from the local disk, as it does below `var/`.
- `expiry.php` is a cluster file as well: its master copy is in the cluster database and on the mount, so a memory
  file system on the web server does not lose it, and `ExpiryDir` is not needed for that. With `ExpiryDir=expiry` its
  name is `var/<site>/expiry/expiry.php`, which is kept in the main table (`ezdfsfile`), not the cache table, and is
  not removed when the cache table is emptied.

## Limits

- **Compiled templates** go to `<cache directory>/template/compiled/`, derived once per process
  (`eZTemplateCompiler::compilationDirectory()`). A script or a preview that changes siteaccess after compiling a
  template keeps writing compiled templates into the cache directory of the first site. This was so before with a
  `VarDir` per site; `CacheVarDir` does not change it: the compiled file is named after the template it compiles, so
  it is only kept in another site's cache, never used in place of one of that site's own. Velocity removes the
  directory with the other globals after every request.
- **INI files read before the siteaccess is known** (other than `site.ini`) hold no setting of a siteaccess and stay
  in the shared `var/cache/ini/`. Front controllers and scripts read every INI file again after the change
  (`eZINI::resetAllInstances( false )`), and that copy is cached in the INI cache of the site.
- **Windows paths**: an absolute path is one that starts with `/`, as before for `CacheDir`. `C:\...` in `CacheDir`,
  `LogDir`, `LogVarDir`, `CacheVarDir` or `ExpiryDir` is taken as a directory inside VarDir; on Windows use paths
  inside the installation.

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
  `VarDir` yourself. For the cache directory of another siteaccess, pass its settings to
  `eZSys::cacheDirectoryOf( eZINI::instance( 'site.ini.append', <its settings directory> ) )`, as the classic menu and
  the toolbar do. A `VarDir` that starts with `..` is not relocated.
- `eZDebug::instance()->logDirectory()` returns where the debug logs go; `eZLog::write()` without a directory writes
  there, and so does `eZLog::write()` with the directory `var/log` (the old default, which callers also pass
  explicitly). Any other directory given to `eZLog::write()`, and a file written into `var/log` without `eZLog`,
  stays where it is.
- `eZExpiryHandler::filePath()` returns the `expiry.php` of the site.

## Tests

- `eZSiteLogAndExpiryPathsTest` (no database): the log directory inside VarDir and absolute; `LogVarDir` and
  `CacheVarDir`, relative and absolute, with VarDir `var` and with an absolute VarDir; the debug logs and `eZLog` there
  and back to `var/log`; `storage.log` in an absolute `LogDir`; `UseGlobalLogDir`; `ExpiryDir` empty, relative,
  absolute and with a moved cache; timestamps that survive an emptied cache directory; the shared expiry instance
  after a change; the defaults, which give exactly the directories of before; a `VarDir` starting with `..` or `./`;
  empty `CacheDir` and `LogDir`; the cache directory of another siteaccess; `eZDir::recursiveDelete()` inside an
  absolute cache, log and expiry directory outside the root and nowhere beside them, an `AllowedDeletionDirs` entry
  that does not exist or is only a prefix (needs a system temp directory outside the installation).
- `eZDirDeletionGuardTest` (no database): `eZDir::recursiveDelete()` and the cache clears that go through it in every
  layout: the default `var/<site>/cache`, a `CacheVarDir` inside the root and one that is a link outside it, an
  absolute `CacheDir` and `CacheVarDir`; inside the cache and the cache directory as a whole, never its parent, the
  cache of another site or what is beside it; the codepage, template compile and override caches and a package
  import directory cleared with and without `RenameBeforeDelete`; a `LogDir` or `CacheDir` that is or contains the
  installation allows nothing; a cache directory that does not exist allows nothing.
- `eZSiteAccessSitePathsTest` (writes siteaccesses of its own, no database): `eZSiteAccess::change()` to a site with
  the settings and then to one without, in the same process; `LogVarDir`, `CacheVarDir` and the INI cache in the
  moved cache; a directory the installation set, kept and restored; a new request without the globals Velocity
  removes; `site.ini` cached in the INI cache of its own site and none left in that of the site before; the same site
  twice; the log directory without the function file of `eZUpdateDebugLogDirectory()`; an INI file read before the
  change and read again into the cache of the site.
- `eZSiteLogReadersTest` (no database): the setup log counts the errors in the log directory of the site; the system
  report names that directory and finds the cronjob log there; audit records stay inside VarDir whatever `LogVarDir`
  and `LogDir` say, also with an absolute VarDir; `eZSiteAccess::resetSitePaths()`.
- `expVelocityEnginesTest`: the public caches below `CacheVarDir` are served and nothing else there; an absolute
  path, `..` or other characters are not; `./var_cache/` is served as `var_cache`; a `CacheVarDir` set for one
  siteaccess only is served too; the warm-up resets the paths before it sweeps the globals.

## Related pages

- [Per-site settings inside extensions (multi-site hosting)](multi-site-ini-overrides.md)
- [Renaming caches aside before deleting them](cache-clear-rename-aside.md)
