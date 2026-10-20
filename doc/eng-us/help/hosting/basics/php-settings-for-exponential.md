---
title: PHP settings for Exponential
audience: hosting, admin
category: hosting
order: 50
summary: The php.ini values the Exponential installer checks, a production set for a PHP-FPM pool, upload limits, time zone, OPcache, error display, the PHP functions some features need, and where Velocity and FrankenPHP take their PHP settings from.
keywords: php.ini, PHP settings, memory_limit, max_execution_time, date.timezone, file_uploads, upload_max_filesize, post_max_size, allow_url_fopen, variables_order, open_basedir, disable_functions, display_errors, expose_php, OPcache, request_terminate_timeout, php_admin_value, PHP-FPM pool, Velocity IniOptions, FrankenPHP
type: help
---
# PHP settings for Exponential

This page is for system administrators who configure PHP for Exponential sites, on a shared server, a panel or a
dedicated machine. It answers: which `php.ini` values the installer insists on, what a production pool should set,
which PHP functions features of Exponential call (and what stops working when a host disables them), and where the
settings live when Velocity or FrankenPHP runs PHP instead of PHP-FPM.

![Setup, Ini settings: read the INI settings of a siteaccess and where each value comes from](../../images/hosting/ini-settings.webp)

## Contents

1. [What the installer checks](#what-the-installer-checks)
2. [A production set for a pool](#a-production-set-for-a-pool)
3. [Time zone](#time-zone)
4. [Error display](#error-display)
5. [OPcache under PHP-FPM](#opcache-under-php-fpm)
6. [Functions some features call](#functions-some-features-call)
7. [Timeouts](#timeouts)
8. [Velocity and FrankenPHP](#velocity-and-frankenphp)
9. [Applying a change](#applying-a-change)
10. [Checklist](#checklist)
11. [Related pages](#related-pages)

## What the installer checks

From [chapter 2.5](../../../install/02-requirements.md#25-phpini-settings) and `settings/setup.ini`:

| Setting | Value | Checked by |
|---|---|---|
| `memory_limit` | at least `64M` (`-1` passes) | critical test `memory_limit`, `[memory_limit] MinMemoryLimit=64M` |
| `max_execution_time` | at least `30` (`0` passes) | critical test `execution_time`, `[execution_time] MinExecutionTime=30` |
| `date.timezone` | set | critical test `timezone` |
| `file_uploads` | `On`, and the upload directory exists and is writable | critical test `file_upload` |
| `upload_tmp_dir` | a writable directory, or empty | when empty the test checks `TMPDIR` or `/tmp` |
| `allow_url_fopen` | `On` | critical test `allow_url_fopen` |
| `variables_order` | contains `E`, for example `EGPCS` | optional test `variables_order` |
| `open_basedir` | empty | optional test `open_basedir` (a warning only) |
| `upload_max_filesize`, `post_max_size` | as large as the files editors upload | not tested; PHP refuses larger uploads |

The 64M floor is what the installer needs, not what a running site should have. The administration's **Setup >
System information** health check reports a `memory_limit` below 128M as a failure and below 256M as a warning, and a
`max_execution_time` below 30 seconds on a web server
([System information, health checks](../../../guides/system-information.md#3-health-checks)).

## A production set for a pool

The minimal production set from [chapter 2](../../../install/02-requirements.md#25-phpini-settings), as a PHP-FPM
pool's `php_admin_value` lines or in `php.ini`:

```ini
memory_limit = 256M
max_execution_time = 60
date.timezone = Europe/Berlin
file_uploads = On
upload_max_filesize = 64M
post_max_size = 64M
allow_url_fopen = On
variables_order = "EGPCS"
```

The values above the minimums are suggestions, not requirements: size them for the largest file a customer uploads
and the longest import run through the browser. A plan can state its upload limit as one of its properties.

> **Note**
> `open_basedir` is common on shared hosting. The installer only warns about it ("open_basedir is in use and can give
> problems running Exponential due to bugs in some PHP versions."). If you set it, include the installation directory,
> the upload temporary directory and the directory of `[FileSettings] TemporaryDir` (`/tmp/` shipped).

## Time zone

`index.php` sets UTC when `php.ini` has no `date.timezone`; the installer's test fails only in that case. A site can
also set its own zone in `settings/site.ini`, `[TimeZoneSettings] TimeZone`, applied by the kernel on every request.
For a host, setting `date.timezone` in every pool to the zone of the server (or UTC) is enough; customers set their
own in the site's settings.

## Error display

From [chapter 13.4](../../../install/13-security-hardening.md#134-debug-output-and-error-display): in the `php.ini` of
PHP-FPM set

```ini
display_errors = Off
log_errors = On
expose_php = Off
```

The System information page flags `display_errors` as a health problem. Exponential's own debug output is a
separate switch, `site.ini [DebugSettings] DebugOutput`, which must be off on production as well.

## OPcache under PHP-FPM

From [chapter 10.12](../../../install/10-after-installing.md#opcache):

```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
opcache.interned_strings_buffer=32
; keep timestamp checks on: Exponential rewrites PHP cache files (INI caches,
; compiled templates, expiry.php) under the same name
opcache.validate_timestamps=1
opcache.revalidate_freq=2
```

> **Warning**
> Never set `opcache.validate_timestamps=0` on an Exponential site. A cache file rewritten after a clear would be
> served in its old form until PHP restarts. Hosts that turn timestamp checks off globally for speed must exempt
> Exponential pools. More in [OPcache](../performance/opcache.md).

## Functions some features call

Hosts often list functions in `disable_functions`. These features of Exponential call functions that are commonly on
such lists; the site keeps running without them, but the feature does not work:

| Function | Used by | Without it | Source |
|---|---|---|---|
| `system` | the ImageMagick converter (`eZImageShellFactory`) | ImageMagick conversions fail; GD still works | `lib/ezimage/classes/ezimageshellhandler.php`, `settings/image.ini [ImageMagick]` |
| `proc_open` | **Setup > Cronjobs**: starting a job from the administration | the page says so; run cron from a shell | [Cronjobs guide](../../../guides/cronjobs.md) |
| `exec` | **Setup > Cronjobs**: reading the crontab | the page says which feature is missing | [Cronjobs guide](../../../guides/cronjobs.md) |
| `posix_kill` | **Setup > Cronjobs**: Stop running job | the button cannot stop a job | [Cronjobs guide](../../../guides/cronjobs.md) |
| `opcache_get_status` | **Setup > System information**: OPcache figures | the cache works; only its figures are not shown | [System information](../../../guides/system-information.md) |
| `pcntl_fork` | asynchronous publishing (`bin/php/ezasynchronouspublisher.php`) | asynchronous publishing is unavailable | `composer.json` suggest `ext-pcntl` |

A command-line PHP for cron and Velocity usually has its own `php.ini` without such a list; check with `php --ini`.

## Timeouts

- `max_execution_time` limits PHP's own CPU time; under PHP-FPM the hard limit of a request is the pool's
  `request_terminate_timeout` (off unless set) ([chapter 9.2](../../../install/09-databases.md#92-sqlite-3-a-production-database-in-one-file)).
- With SQLite, keep `[DatabaseSettings] SQLiteTransactionWait` (25 seconds shipped) below the request timeout, or a
  waiting writer is killed before the driver can report ([SQLite for hosts](sqlite-for-hosts.md)).
- The setup wizard's configuration step on MongoDB can take one to three minutes; raise
  `request_terminate_timeout` for that install or use the console ([MongoDB for hosts](mongodb-for-hosts.md)).
- Sessions: `site.ini [Session] SessionTimeout` (shipped `259200` seconds, three days) overwrites
  `session.gc_maxlifetime` with PHP's own session handler.

## Velocity and FrankenPHP

**Velocity** runs PHP as command line processes and passes its own options from `settings/velocity.ini`,
`[PHPSettings] IniOptions[]`. The shipped list turns on OPcache for the command line (`opcache.enable_cli=1`), sets
`memory_consumption=512`, `max_accelerated_files=20000`, `interned_strings_buffer=64`, `opcache.revalidate_freq=0`,
`opcache.file_update_protection=0` and `apc.shm_size=256M`. The two OPcache values differ from PHP-FPM on purpose: a
Velocity worker is one long request, and with the PHP-FPM values it would keep serving stale cache files
([chapter 10.12](../../../install/10-after-installing.md#opcache)). Put changes in
`settings/override/velocity.ini.append.php`, never in the shipped file.

**FrankenPHP** reads no `php.ini` of the machine. `settings/velocity.ini`, `[FrankenPHPSettings] IniOptions[]` ships
`display_errors=Off`, `log_errors=On`, `memory_limit=512M`, `opcache.enable=1`, `post_max_size=64M` and
`upload_max_filesize=32M`; `PHPIniFile` names a `php.ini` of your own instead
([chapter 8.4](../../../install/08-serving-the-site.md#84-frankenphp)).

## Applying a change

After editing `php.ini` or a pool, reload what runs PHP: PHP-FPM, Apache with mod_php, or Velocity
(`php bin/php/console exp:velocity restart`). `php --ini` shows which files the command line PHP reads; the web PHP
may read others. A change that "does not work" is most often a change to the file the other PHP reads.

## Checklist

- [ ] `memory_limit` 256M or more for production pools (64M is only the installer's floor).
- [ ] `max_execution_time`, `date.timezone`, `file_uploads`, `allow_url_fopen` and `variables_order` set.
- [ ] Upload limits stated in the plan and set in the pool.
- [ ] `display_errors=Off`, `log_errors=On`, `expose_php=Off`.
- [ ] OPcache on, with timestamp checks kept on.
- [ ] `disable_functions` reviewed against the table above.
- [ ] Velocity and FrankenPHP settings changed in `settings/override/velocity.ini.append.php` only.

## Related pages

- [PHP versions and extensions](php-versions-and-extensions.md)
- [PHP-FPM tuning](../performance/php-fpm-tuning.md) and [OPcache](../performance/opcache.md)
- [PHP selection and FPM pools](../panels/php-selection-and-fpm-pools.md)
- [Chapter 2.5: php.ini settings](../../../install/02-requirements.md#25-phpini-settings)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
