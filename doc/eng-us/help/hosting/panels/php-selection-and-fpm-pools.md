---
title: PHP selection and FPM pools
audience: hosting, admin
category: hosting
order: 70
summary: How to give an Exponential site the right PHP on a panel server: the web PHP and the command line PHP must match, one PHP-FPM pool per site running as the site's user, the settings that matter in the pool, and how to reload the PHP-FPM master that really serves the site (auto-detected by exp:velocity deploy).
keywords: PHP version, PHP selector, command line PHP, web PHP, PHP-FPM, pool, php_admin_value, memory_limit, upload_max_filesize, OPcache, validate_timestamps, request_terminate_timeout, disable_functions, open_basedir, reload, PhpFpmService, plesk-php85-fpm, Remi, Debian
type: help
---
# PHP selection and FPM pools

This page is for system administrators and support staff at hosting companies who choose and configure PHP for
Exponential sites on panel servers. It answers which PHP versions to offer, why the command line PHP matters as much
as the web PHP, how a PHP-FPM pool for an Exponential site should look, and how to reload the right PHP after a
change, which on servers with several PHP versions is the most common mistake.

## Contents

1. [Which PHP versions to offer](#which-php-versions-to-offer)
2. [Web PHP and command line PHP](#web-php-and-command-line-php)
3. [One pool per site](#one-pool-per-site)
4. [Pool settings for Exponential](#pool-settings-for-exponential)
5. [OPcache](#opcache)
6. [Reloading the right PHP-FPM](#reloading-the-right-php-fpm)
7. [Restrictions panels often set](#restrictions-panels-often-set)
8. [Checklist](#checklist)

## Which PHP versions to offer

From [2.2 PHP](../../../install/02-requirements.md#22-php):

| PHP | Status |
|---|---|
| 8.0 | the oldest supported version; runs under Apache with mod_php or PHP-FPM; "Velocity does not run on it" |
| 8.1 to 8.4 | supported, Velocity included |
| 8.5 | supported; the newest version tested |
| 7.4 and older | not supported by 6.0.8 and later; "A site that must stay on PHP 7.4 stays on 6.0.7" |

"Use the newest PHP your system offers: each version brings speed, and the older ones lose security support first."
64-bit PHP is recommended "for support of more than 30 languages".

> **Note**
> PHP 8.0 support is new in 6.0.15; the releases `v6.0.8` to `v6.0.14` require PHP 8.1. A customer site on an
> older 6.0.x release needs at least 8.1 ([2.2](../../../install/02-requirements.md#22-php)).

## Web PHP and command line PHP

"Exponential uses PHP twice: the web server's PHP (PHP-FPM, mod_php or Velocity's workers) serves pages, and the
command line PHP (`php`) runs Composer, the installers, the console, the cronjobs and Velocity itself. Both must be of
a supported version and have the same extensions. On machines with several PHP versions (Plesk, Remi) the `php` on
the `PATH` is often not the one the web server uses."
([Command line and web PHP](../../../install/02-requirements.md#command-line-and-web-php))

What goes wrong when they differ: Composer picks package versions the web PHP cannot run, or an install succeeds on
the command line and the browser shows a white page.

```bash
php -v; php -m                       # the PATH's PHP
/opt/plesk/php/8.5/bin/php -v        # a Plesk PHP by its path
php --ini                            # the files the command line PHP reads
```

Compare with the web side on **Setup > System information**, which "first says which server answered" and how it runs
PHP ([System information](../../../guides/system-information.md)).

> **Tip**
> When a panel changes a site's PHP version, change the command line PHP of that site's cron lines and of
> `cronjob.ini [AdminSettings] PhpCliPath` in the same step ([Cron and shell access](cron-and-shell-access.md)).

## One pool per site

- "The pool runs PHP as the site's user" ([8.5.2](../../../install/08-serving-the-site.md#852-php-fpm)).
- When "several customers on one machine need operating-system isolation from each other", use "a traditional server
  with one PHP-FPM pool per site" ([8.1](../../../install/08-serving-the-site.md#81-the-choice-in-one-table)).
- Everything that writes `var/` (pool, cron, console, Velocity workers) runs as that one user
  ([8.7.2](../../../install/08-serving-the-site.md#872-one-user-or-one-group)).

Where pools live, per the sources:

| System | Pool directory | Service |
|---|---|---|
| Plesk | `/opt/plesk/php/<version>/etc/php-fpm.d/<domain>.conf` | `plesk-php<XY>-fpm` |
| Red Hat family, stock PHP | `/etc/php-fpm.d` | `php-fpm` |
| Remi parallel installs | `/etc/opt/remi/php*/php-fpm.d` | `php<XY>-php-fpm` |
| Debian and Ubuntu | `/etc/php/<version>/fpm/pool.d/` | `php<version>-fpm` |

Sources: [Chapter 2, Plesk and Debian](../../../install/02-requirements.md#29-operating-system-notes),
[Velocity engines, Which PHP-FPM](../../../bc/6.0/velocity-engines.md).

## Pool settings for Exponential

"Settings of interest for Exponential (in the pool file or `php.ini`): `memory_limit` (at least 256M; more for large
imports), `max_execution_time`, `upload_max_filesize` and `post_max_size` (your largest upload), OPcache on."
([8.5.2](../../../install/08-serving-the-site.md#852-php-fpm))

Chapter 2 gives "a minimal set for a production `php.ini` (or a PHP-FPM pool's `php_admin_value` lines)":

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

"The values above the minimums are suggestions, not requirements." The setup wizard's floors are `memory_limit` 64M
and `max_execution_time` 30 ([2.5](../../../install/02-requirements.md#25-phpini-settings)).

**Request time limits.** Apart from `max_execution_time`, a pool can kill a request after a wall-clock limit
(`request_terminate_timeout`), which PHP's `set_time_limit()` cannot override. The MongoDB porting notes describe the
setup wizard being cut off by it and raising it for the install only
([MongoDB kernel support](../../../bc/6.0/MONGODB_KERNEL_SUPPORT_EXPANSION.md)). Under Velocity the equivalent is the
engine's `requestTimeout` (30 seconds; the client gets 504); keep SQLite's `SQLiteTransactionWait` (default 25) below
the request timeout ([9.2](../../../install/09-databases.md#92-sqlite-3-a-production-database-in-one-file)).

## OPcache

Under PHP-FPM, enable OPcache in the pool's `php.ini`, and keep timestamp checks on: "Never set
`opcache.validate_timestamps=0` on an Exponential site: a cache file rewritten after a clear would be served in its
old form until PHP restarts." ([10.12 OPcache](../../../install/10-after-installing.md#opcache))

```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
opcache.interned_strings_buffer=32
opcache.validate_timestamps=1
opcache.revalidate_freq=2
```

Velocity sets its own OPcache options in `settings/velocity.ini [PHPSettings] IniOptions[]`; see
[OPcache](../performance/opcache.md).

## Reloading the right PHP-FPM

"After a PHP class, operator or INI change, reload the FPM master that serves the site"
([8.5.2](../../../install/08-serving-the-site.md#852-php-fpm)). "On machines with several PHP versions the master
serving the site is often not the one called `php-fpm`. Find it by its pool file."

`exp:velocity deploy` does this for you. `[DeploySettings] PhpFpmService=auto` (the default in
`settings/velocity.ini`) finds "the pool configuration named after the installation's hosting domain
(`/var/www/vhosts/<domain>/...`, as Plesk names pools) or one that names the installation's directory" in the four
pool directories above. "The step is a reload, so no request is dropped. It needs root"
([Velocity engines](../../../bc/6.0/velocity-engines.md)).

```bash
php bin/php/console exp:velocity deploy --dry-run --allow-root-user                # shows which service it found
php bin/php/console exp:velocity deploy --no-velocity --allow-root-user            # Apache/nginx site: caches + FPM reload
```

Set `PhpFpmService` to the service name to fix it, or to `disabled` when Velocity serves the site alone.

## Restrictions panels often set

| Restriction | Effect on Exponential | Source |
|---|---|---|
| `open_basedir` | the setup wizard warns; ezupdate searches inside the installation for Composer first | [2.4](../../../install/02-requirements.md#24-the-setup-wizards-system-check), [ezupdate](../../../features/6.0/extensions/ezupdate.md) |
| `disable_functions` with `proc_open`, `exec` or `posix_kill` | the cronjob page cannot start a job, read the crontab or stop a job; it says which | [Cronjobs](../../../guides/cronjobs.md#2-before-you-start) |
| `disable_functions` with `opcache_get_status` | **System information** cannot show OPcache figures; the cache still works | [System information](../../../guides/system-information.md) |
| `variables_order` without `E` | the optional test reports "PHP does not register environment variables" | [Chapter 12](../../../install/12-troubleshooting.md) |

## Checklist

- [ ] PHP 8.1 or later offered (8.0 only for sites that do not use Velocity and run 6.0.15 or later).
- [ ] Same version and extensions for web and command line; cron and `PhpCliPath` follow PHP version changes.
- [ ] One pool per site, running as the site's user.
- [ ] Pool settings and OPcache as above; `validate_timestamps` on.
- [ ] `exp:velocity deploy --dry-run` names the right PHP-FPM service.
- [ ] Panel restrictions documented with their effects.

**Demo mode in a pool.** A public demo can get its environment variable from its own pool: `env[EXP_DEMO_MODE] = 1` (it works with `clear_env = yes`), then reload that version's master. Never in a pool other sites share. [Demo mode for hosts](../security/demo-mode-for-hosts.md), [`php-fpm-pool-demo.conf`](../../../examples/demo-mode/php-fpm-pool-demo.conf).

## Related pages

- [Control panels: overview](README.md), [Plesk](plesk.md)
- [PHP versions and extensions](../basics/php-versions-and-extensions.md), [PHP settings for Exponential](../basics/php-settings-for-exponential.md)
- [PHP-FPM tuning](../performance/php-fpm-tuning.md), [Per-customer users and pools](../security/per-customer-users-and-pools.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
