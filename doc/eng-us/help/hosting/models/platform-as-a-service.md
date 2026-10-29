---
title: Platform as a service
audience: hosting
category: hosting
order: 80
summary: What a platform-as-a-service offering must provide for Exponential (tier 3 in the sources), why a read-only or ephemeral file system needs var/ on persistent storage and a database server, how VarDir and the cache and log paths behave, and the questions to ask a PaaS before offering Exponential on it.
keywords: platform as a service, PaaS, buildpack, read-only file system, ephemeral file system, persistent disk, VarDir, CacheDir, LogDir, CacheVarDir, LogVarDir, ExpiryDir, database server, long-running process, cron, Velocity, PHP-FPM
type: help
---
# Platform as a service

This page is for hosting companies that run a platform as a service (PaaS), or resell one, and want Exponential on
it. A PaaS fixes the runtime: the customer pushes code, the platform builds and runs it, and the file system the
application sees may be read-only or lost at every restart. The page answers what the project's sources say about
such platforms, which directories Exponential writes and how they can be moved, and which questions to ask a platform
before you offer Exponential on it.

## Contents

1. [What the sources say](#what-the-sources-say)
2. [What Exponential writes at run time](#what-exponential-writes-at-run-time)
3. [Read-only or ephemeral file systems](#read-only-or-ephemeral-file-systems)
4. [The database](#the-database)
5. [Processes: web, cron and long-running servers](#processes-web-cron-and-long-running-servers)
6. [Build and release](#build-and-release)
7. [Questions to ask a platform](#questions-to-ask-a-platform)

## What the sources say

"Platform as a service offerings that run PHP 8 with a writable persistent disk are expected to work (tier 3).
Offerings with a read-only or ephemeral file system need `var/` moved to persistent storage and a database server."
([Containers, cloud and hosting panels](../../os-support.md#containers-cloud-and-hosting-panels))

Tier 3 means "a reasoned expectation, not a test result" ([The four tiers](../../os-support.md#the-four-tiers)): no
specific PaaS product is named or tested in the sources. Run a full test installation on the platform before you
offer it.

## What Exponential writes at run time

The setup wizard's `directory_permissions` test lists the directories that must be writable: `design`, `extension`,
`settings`, `settings/override`, `settings/siteaccess`, `settings/siteaccess/admin`, `var`, `var/cache` and its
subdirectories, `var/log`, `var/storage` and its subdirectories, `var/autoload`
([2.4 The setup wizard's system check](../../../install/02-requirements.md#24-the-setup-wizards-system-check)).

| Path | Written when | Must persist? |
|---|---|---|
| `var/storage` | every upload and image variation; the SQLite database in `var/storage/sqlite3/` | yes |
| `var/cache`, `var/<site>/cache` | on every request that fills a cache | no, but losing it costs a cold start |
| `var/log`, `var/<site>/log` | errors, request logs, cron logs | collect or keep |
| `var/autoload` | when autoloads are generated (also by `composer install`) | rebuilt at build time |
| `settings/override`, `settings/siteaccess` | by the installer and when administrators save settings in the browser | yes |
| `design/`, `extension/` | only while packages are installed from the administration | no, if nothing installs packages at run time |

"After installation you can take write access to `design/` and `extension/` away again if nothing installs packages
from the administration; `settings/` stays writable when administrators change settings in the browser"
([8.7.1](../../../install/08-serving-the-site.md#871-what-must-be-writable)).

> **Tip**
> On a PaaS, settings belong in the code repository. The switch `[ConfigurationSettings] ManagedByDeployment=enabled`
> makes "the configuration of that siteaccess come from the deployment (version control) only": the administration
> no longer writes `*.ini.append.php` or `.tpl` files on the server
> ([Configuration managed by the deployment](../../../features/6.0/managed-by-deployment.md)).

## Read-only or ephemeral file systems

The `var` directory is set by `site.ini [FileSettings] VarDir` (shipped `var`). The comment in `settings/site.ini`
is clear: "Only directories relative to the Exponential root are supported. Absolute directories are NOT
SUPPORTED." So **mount the persistent storage at `var/` inside the installation**, rather than pointing `VarDir`
somewhere else.

Within `var/` the sources offer finer control (`site.ini [FileSettings]`,
[Logs, INI cache and expiry timestamps per site](../../../features/6.0/multi-site-log-and-cache-paths.md)):

| Setting | What it allows |
|---|---|
| `LogDir` | an absolute path is supported: "the logs are never served" |
| `CacheDir` | an absolute path "will be accepted, but won't work with some features and is NOT SUPPORTED": the public caches in it are not served. "To keep the cache on another file system, mount that at VarDir/CacheDir" |
| `LogVarDir`, `CacheVarDir` | one tree for the logs or caches of every site |
| `ExpiryDir` | keeps `expiry.php`, whose timestamps decide whether caches and image aliases are valid, when the cache directory is emptied at a restart |

> **Warning**
> If the cache directory is on storage that is emptied at a restart, set `ExpiryDir` to a directory on the persistent
> volume. Otherwise the expiry timestamps are lost with the cache.

## The database

In the official order: 1. SQLite 3 (the default) · 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL.
Microsoft SQL Server is known to be possible with additional work at this time.

With a read-only or ephemeral file system the sources require a database server. Even with a persistent disk, a
platform that may run more than one instance of the application cannot share one SQLite file between them. Use the
platform's managed database, **2. PostgreSQL** first if it offers several
([Database support](../../database-support.md#the-supported-databases-in-order)).

## Processes: web, cron and long-running servers

- **Web.** If the platform runs PHP behind its own web server (PHP-FPM style), it needs the rewrite rules of
  `.htaccess_root` translated to that server: "only a fixed list of asset paths is served as files; everything else
  is passed to `index.php`" ([13.2](../../../install/13-security-hardening.md#132-what-the-web-server-must-never-hand-out)).
  A generic "serve the file if it exists" configuration would hand out `settings/*.ini`.
- **Velocity** needs a long-running process and PHP 8.0 or later with `sockets`. A platform that runs one process per
  container and routes HTTP to a port can run it; one that only runs PHP per request cannot.
- **Cron.** `runcronjobs.php` must run on a schedule, once per site, with the same code and settings
  ([Cron jobs](../operations/cron-jobs.md)). Ask whether the platform has a scheduler.
- **Sessions.** With more than one instance, sessions must be shared; see [Kubernetes, Sessions](kubernetes.md#sessions).

## Build and release

- Build: `composer install` (which also runs `bin/php/ezpgenerateautoloads.php` through the `legacy-scripts`
  script) ([2.7 Composer](../../../install/02-requirements.md#27-composer)).
- Install once, against the managed database, with `exp:install` and secrets from the environment
  (`EXP_INSTALL_DB_PASSWORD`) ([4.6](../../../install/04-choosing-an-install-method.md#46-unattended-installs-ci-and-containers)).
- Every later release: new code, the same persistent `var/` and database, the schema update files when an upgrade
  calls for them, then a cache clear ([Deploying changes](../operations/deploying-changes.md)).
- `.htaccess` and `config.php` in the build or at start, unattended: `./console exp:setup:htaccess --apply`,
  `./console exp:setup:config --apply`, then `./console exp:config:set <NAME> <value> --apply` with values from the
  platform's environment variables (`--value="$CLUSTER_DB_PASS"` for a secret). Each command asks nothing, ends with
  `PASS:` or `FAIL:`, exits 0, 1 or 2 and changes nothing on a second run. A build step that runs as root needs
  `--allow-root-user` and, so the files do not belong to root, `--owner=www-data:www-data`
  ([In scripts, containers and CI](../../../guides/setup-files-from-the-console.md#11-in-scripts-containers-and-ci)).

## Questions to ask a platform

| Question | Why |
|---|---|
| Which PHP versions and extensions, and can I add `intl`, `xsl` and a database extension? | the required set ([2.3](../../../install/02-requirements.md#23-php-extensions)) |
| Is there a persistent disk, and can it be mounted at `var/` inside the application? | `VarDir` must be relative |
| How many instances may run at once? | one writer for SQLite; shared sessions and storage otherwise |
| Is there a scheduler? | `runcronjobs.php` |
| May a long-running process listen on a port? | Velocity |
| Can I configure the web server's rewrite rules? | the asset list of `.htaccess_root` |
| Which databases are offered as managed services? | the official order |
| How are backups of the disk and the database taken and restored? | the backup set: database, `var/storage`, `settings/override`, `settings/siteaccess` |

## Related pages

- [Choosing a hosting model](choosing-a-hosting-model.md), [Containers: Docker and Podman](containers-docker-and-podman.md)
- [Writable directories and permissions](../basics/writable-directories-and-permissions.md)
- [Storage layout](../operations/storage-layout.md)
- [Setting up .htaccess and config.php from the console](../../../guides/setup-files-from-the-console.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
