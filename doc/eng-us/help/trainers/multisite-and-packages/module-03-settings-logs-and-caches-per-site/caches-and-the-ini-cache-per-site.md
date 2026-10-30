---
title: "Lesson 3.4: Caches and the INI cache per site"
audience: trainer, admin, developer
category: training
order: 34
summary: Learners give the second site an INI cache of its own, clear it without touching the others, and plan CacheVarDir, ExpiryDir and a memory file system for many sites, including what Velocity and AllowedDeletionDirs need. 50 minutes.
keywords: multisite and site packages, caches per site, INICacheDir, CacheVarDir, ExpiryDir, expiry.php, tmpfs, global_ini, ini cache, ezcache.php -s, AllowedDeletionDirs, Velocity, public caches
type: help
---
# Lesson 3.4: Caches and the INI cache per site

On a busy multisite, clearing a cache for one site should not slow down the others, and caches are often moved to a
memory file system. Four settings of `site.ini [FileSettings]` decide where each site's caches, INI cache and expiry
timestamps live. This lesson gives the second site its own INI cache, clears it on its own, and plans the setup for a
server with many sites.

> **Warning**
> **`.htaccess` slows down every request.** When `.htaccess` files are enabled (`AllowOverride` anything but `None`), let alone
> used, Apache looks for them and reads and evaluates them on every request, in every directory of the path, and
> page loading times go up drastically ([measured](../../../../guides/setup-files-from-the-console.md#measured-htaccess-against-rules-in-the-virtual-host)). The only real fix is to switch the feature off for
> the virtual host: `AllowOverride None` in the HTTPS virtual host, and the rules in the virtual host's own
> configuration as mod_rewrite rules. That is far faster in production speed tests and well worth the time it takes
> to optimize a production configuration. Switch over right away: copy the rules of the
> [example HTTPS virtual host](../../../../examples/web-servers/apache-virtual-host.md) into your virtual host (with the static cache, also
> [`staticcache_offload.conf`](../../../../examples/staticcache_offload.conf)), set `AllowOverride None`, run `apachectl configtest`, reload Apache and
> test the site. The same rules for other servers: [nginx](../../../../examples/web-servers/nginx.md), [LiteSpeed](../../../../examples/web-servers/litespeed.md),
> [Caddy](../../../../examples/web-servers/caddy.md), [IIS](../../../../examples/web-servers/iis.md), [Velocity](../../../../examples/web-servers/velocity.md) (built in); where virtual host rules go in a control
> panel: [Plesk](../../../hosting/panels/plesk.md), [cPanel](../../../hosting/panels/cpanel.md), [DirectAdmin](../../../hosting/panels/directadmin.md), [ISPConfig](../../../hosting/panels/ispconfig.md).
> **On LiteSpeed or OpenLiteSpeed,** the server's own page cache can also answer anonymous visitors and crawlers without PHP: [Page caching with LiteSpeed and OpenLiteSpeed](../../../../guides/litespeed-page-cache.md).

## At a glance

| | |
|---|---|
| Course | [Multisite and site packages](../README.md) |
| Module | [Module 3: Settings, logs and caches per site](README.md) |
| Duration | 50 minutes (20 presenting, 30 exercise) |
| Level | Advanced |
| Audience | Administrators and developers who operate several sites |
| Prerequisites | [Lesson 3.3](logs-per-site.md) |

## Learning objectives

After this lesson, learners can:

- explain `CacheVarDir`, `ExpiryDir` and `INICacheDir` and their defaults;
- give one site its own INI cache and clear it with `ezcache.php -s <siteaccess>` without touching the others;
- tell the cache ids `ini` and `global_ini` apart;
- explain why `expiry.php` must survive a memory file system's restart;
- name what Velocity and `[FileSettings] AllowedDeletionDirs` need after such a change.

## Trainer's notes

### How to present it

Source: [Logs, INI cache and expiry timestamps per site](../../../../features/6.0/multi-site-log-and-cache-paths.md).

1. **The settings** (6 minutes):

   | Key | Default | Meaning |
   |---|---|---|
   | `CacheVarDir` | empty | a tree for the caches of every site: `var_cache/<site>/cache`; inside the installation so its public caches can be served |
   | `ExpiryDir` | empty | where `expiry.php` is kept: empty means in the cache directory |
   | `INICacheDir` | `global` | `site` caches the site's INI files in `<cache directory>/ini/`; `global` in `var/cache/ini/` |

2. **What is cleared where** (5 minutes). The cache commands and **Setup** > **Cache management** work as before; the
   settings only decide which directory a cache item names. The id `ini` clears `<cache directory of the site>/ini/`;
   `global_ini` clears `var/cache/ini/`, the INI files read before a siteaccess is known and those of sites with
   `INICacheDir=global`. "Clear all" clears the site's cache directory and `var/cache/ini/`; an `expiry.php` in
   `ExpiryDir` stays and gets the new timestamps.
3. **Memory file systems** (5 minutes). `CacheVarDir=var_cache` with a `tmpfs` mounted there puts the caches of every
   site in memory. `expiry.php` holds the times at which caches and image aliases were last cleared; if it were
   emptied with the cache, image aliases made before a clear would count as current again. `ExpiryDir=expiry` keeps
   it in `var/<site>/expiry/`.
4. **Serving and deleting** (4 minutes). Velocity serves the public caches below `CacheVarDir` (packed scripts and
   style sheets, text to image) and nothing else there; restart Velocity after changing it. For Apache, enable the
   commented `var_cache` rule in `.htaccess_root`. Cache clears delete only inside the root, inside
   `[FileSettings] AllowedDeletionDirs` and inside the directories the current site writes to; an
   `AllowedDeletionDirs` entry that does not exist allows nothing.

### Common questions

**Why `--clear-tag=ini` in every other lesson?** The tag clears every INI cache, the site's and the global one. That
is what you want after a settings change; the ids are for clearing one of them on purpose.

**Can an absolute `CacheDir` do the same as `CacheVarDir`?** It moves the cache out of the paths that are served,
which is why `settings/site.ini` calls it unsupported; use `CacheVarDir` or mount a memory file system at
`var/<site>/cache`.

**Are compiled templates per site?** They go to `<cache directory>/template/compiled/`, derived once per process, so a
script that changes siteaccess after compiling keeps writing them into the first site's cache directory. They are
named after their template, so they are never used in place of the other site's.

**Does a cluster need anything else?** With `eZDFSFileHandler`, add the public caches below `CacheVarDir` to the
rewrite rules for `index_cluster.php`; the feature page has the rule.

### Pitfalls

- **Shared `VarDir` in the lab.** Both lab sites share one `VarDir`, so they share one cache directory; the exercise
  shows the INI cache split, which works all the same.
- **Velocity keeps settings in memory.** After clearing an INI cache, restart it, as **Setup** > **Cache management**
  says.
- **Forgetting the web server.** A `CacheVarDir` that Apache does not serve leaves the packed style sheets
  unreachable: the site looks unstyled.

## Hands-on exercise

Use your training installation from lesson 3.3.

1. List the cache ids: `php bin/php/ezcache.php --list-ids`. Find `ini` and `global_ini`.
2. Read the cache settings for `site_b`:

   ```bash
   php bin/php/console exp:ini get site.ini/FileSettings/VarDir -s site_b
   php bin/php/console exp:ini get site.ini/FileSettings/CacheDir -s site_b
   php bin/php/console exp:ini get site.ini/FileSettings/INICacheDir -s site_b
   ```

3. Give `site_b` its own INI cache:

   ```bash
   php bin/php/console exp:ini set site.ini/FileSettings/INICacheDir site extension:site_clubb:siteaccess:site_b
   ```

4. Restart Velocity if it serves the lab, then request `/site_b` and `/site` once each with `curl -s -o /dev/null`.
5. Count the files of both INI caches (replace `var/site` with the `VarDir` of step 2):

   ```bash
   ls var/site/cache/ini | wc -l
   ls var/cache/ini | wc -l
   ```

6. Clear only the site's INI cache, then count again:

   ```bash
   php bin/php/ezcache.php -s site_b --clear-id=ini
   ```

7. Clear only the global INI cache and count again: `php bin/php/ezcache.php --clear-id=global_ini`.
8. Request both sites again and confirm they still answer `200`.
9. On paper, write the `[FileSettings]` block for fifty sites with their caches in memory, logs in a tree of their
   own and the expiry timestamps kept on disk.

## Expected result

- Step 5: both directories hold cached INI files after the requests.
- Step 6 empties `var/site/cache/ini` (the site's INI cache) and leaves `var/cache/ini` as it was; step 7 the
  opposite.
- Step 8: both sites answer `200`; the caches are rebuilt on the next request.
- Step 9:

  ```ini
  [FileSettings]
  UseGlobalLogDir=disabled
  LogVarDir=var_log
  CacheVarDir=var_cache
  ExpiryDir=expiry
  INICacheDir=site
  ```

  with a `tmpfs` at `var_cache`, the `var_cache` rule enabled for Apache and a Velocity restart.
- The trainer checks steps 6 and 7 on one lab on the projector.

## Quiz

1. Which cache id clears the INI cache of one site with `INICacheDir=site`?
   A. `global_ini` B. `ini` C. `content` D. `template`
2. Why should `expiry.php` not live on a memory file system that is emptied at restart?
3. What does `CacheVarDir=var_cache` do for a site with `VarDir=var/shop`?
4. You changed `CacheVarDir`. What must happen for Velocity, and what for Apache?
5. An `AllowedDeletionDirs` entry names a directory that does not exist. What does it allow?

<details>
<summary>Answers</summary>

1. **B.** `global_ini` clears `var/cache/ini/`.
2. It holds when caches and image aliases were last cleared; emptied, image aliases made before a clear would count as
   current again ([Logs, INI cache and expiry timestamps per site](../../../../features/6.0/multi-site-log-and-cache-paths.md#a-site-with-its-cache-in-memory)).
3. Its caches go to `var_cache/shop/cache`.
4. Restart Velocity (it writes its list of served paths when it starts); for Apache enable the `var_cache` rule in
   `.htaccess_root`.
5. Nothing.

</details>

## Further reading

- [Logs, INI cache and expiry timestamps per site](../../../../features/6.0/multi-site-log-and-cache-paths.md):
  every case, the cluster and the tests.
- [Caches](../../../../guides/caches.md): the cache ids and tags.
- [Renaming caches aside before deleting them](../../../../features/6.0/cache-clear-rename-aside.md).
- [Storage layout](../../../hosting/operations/storage-layout.md): the host's view of `var/`.

## Related pages

- [Module 3: Settings, logs and caches per site](README.md)
- [Multisite and site packages: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
