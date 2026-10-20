---
title: PHP-FPM pool
audience: admin, developer
category: examples
order: 90
summary: A PHP-FPM pool for one Exponential 6 installation, used behind Apache, nginx or Caddy: its own user and socket, worker sizing, the PHP limits Exponential needs, sessions, OPcache, and which master to reload after a change.
keywords: PHP-FPM, FPM, pool, php-fpm.d, listen, socket, pm, max_children, memory_limit, upload_max_filesize, post_max_size, OPcache, session.save_path, php_admin_value, reload, Plesk, plesk-php85-fpm, example
type: example
---
# PHP-FPM pool

Apache, nginx and Caddy hand PHP to a **PHP-FPM pool**. One pool per site keeps sites apart: its own
user, its own socket, its own limits. [`php-fpm-pool.conf`](php-fpm-pool.conf) is a pool for one installation, derived
from [chapter 8, section 8.5.2](../../install/08-serving-the-site.md#852-php-fpm) and the hardening chapter. Velocity
and FrankenPHP do not need it: they run PHP themselves.

## Install it

1. Copy the file to the pool directory of the PHP that serves the site:
   `/etc/php-fpm.d/` (Red Hat style), `/etc/php/8.x/fpm/pool.d/` (Debian and Ubuntu). On Plesk the pool files are
   written by Plesk (`/opt/plesk/php/<version>/etc/php-fpm.d/<domain>.conf`): set the values in the domain's PHP
   settings instead.
2. Replace `example.com`, the user and group (the owner of the installation's files) and `listen.owner` /
   `listen.group` (the web server's user: `apache`, `www-data`, `nginx` or `caddy`).
3. Create the log directory and, for file sessions, the session directory (`mkdir -p
   /var/lib/php/session/example.com; chown example: ...; chmod 700 ...`).
4. Check and reload:

   ```bash
   php-fpm -t                     # or php-fpm8.3 -t, the binary of that PHP version
   systemctl reload php-fpm       # the master that serves the site, see below
   ```

## The file

```ini
; Exponential 6: a PHP-FPM pool for one installation
;
; Free documentation: GFDL 1.3 or later, see doc/en/us/README-LICENSE.md.
;
; Derived from doc/en/us/install/08-serving-the-site.md (8.5.2) and 13 (security
; hardening); not shipped by the kernel. Explained in php-fpm.md next to this
; file. One pool per site, running as the user the installation's files
; belong to, listening on a socket only the web server can open.
;
; Install: copy to the pool directory of the PHP that serves the site
; (/etc/php-fpm.d/ on Red Hat style systems, /etc/php/8.x/fpm/pool.d/ on
; Debian, /opt/plesk/php/8.x/etc/php-fpm.d/ is Plesk's own and written by
; Plesk), replace "example" and the paths, check with "php-fpm -t" and reload
; the master that serves the site ("systemctl reload php-fpm", or the unit
; [DeploySettings] PhpFpmService=auto finds: exp:velocity deploy --dry-run).

[example.com]
; The user and group the installation's files belong to (8.7)
user = example
group = example

; The socket the web server passes requests to: the same path as
; "proxy:unix:...|fcgi://localhost" (Apache), fastcgi_pass (nginx) or
; reverse_proxy unix//... (Caddy). Only the web server's user may open it.
listen = /run/php-fpm/example.com.sock
listen.owner = apache
listen.group = apache
listen.mode = 0660

; Workers: "ondemand" starts them when requests come and suits small sites;
; "dynamic" keeps spares for busy ones. Size max_children by memory: each
; worker can use up to memory_limit.
pm = dynamic
pm.max_children = 16
pm.start_servers = 4
pm.min_spare_servers = 2
pm.max_spare_servers = 6
pm.max_requests = 1000

; Requests slower than this are logged with a backtrace
request_slowlog_timeout = 10s
slowlog = /var/log/php-fpm/example.com-slow.log
; A hard limit per request, a little above max_execution_time
request_terminate_timeout = 300s

; The front controllers are .php files; no other extension runs
security.limit_extensions = .php

; Environment: FPM clears it by default, which is right; PATH for the
; programs the kernel may start (image conversion, git for the help)
clear_env = yes
env[PATH] = /usr/local/bin:/usr/bin:/bin

; PHP settings of this pool (php_admin_*: a script cannot change them)
php_admin_value[memory_limit] = 256M
php_admin_value[max_execution_time] = 120
php_admin_value[upload_max_filesize] = 32M
php_admin_value[post_max_size] = 64M
php_admin_value[max_input_vars] = 10000
php_admin_flag[display_errors] = off
php_admin_flag[log_errors] = on
php_admin_value[error_log] = /var/log/php-fpm/example.com-error.log
php_admin_flag[expose_php] = off
php_admin_value[date.timezone] = UTC

; Sessions: with site.ini [Session] Handler empty (the default) PHP keeps
; them, in files. A directory of this site only, owned by the pool's user,
; mode 0700 (not shared with other sites on the machine). With
; Handler=ezpSessionHandlerDB the database keeps them and this is unused.
php_value[session.save_handler] = files
php_value[session.save_path] = /var/lib/php/session/example.com
php_admin_flag[session.cookie_httponly] = on
php_admin_flag[session.use_strict_mode] = on

; OPcache: on, with room for the kernel and the extensions. After a change of
; a PHP class, reload the pool (or set revalidate_freq above 0 and wait).
php_admin_flag[opcache.enable] = on
php_admin_value[opcache.memory_consumption] = 256
php_admin_value[opcache.max_accelerated_files] = 20000
php_admin_value[opcache.interned_strings_buffer] = 32
php_admin_value[opcache.revalidate_freq] = 2
```

## What matters for Exponential

- **Limits.** `memory_limit` at least 256M (more for large imports and image work), `upload_max_filesize` and
  `post_max_size` above your largest upload (and the web server's own limit: `client_max_body_size` in nginx,
  `maxAllowedContentLength` in IIS), `max_input_vars` high enough for big class and role forms.
- **`php_admin_*`** values cannot be changed by a script; `php_value` ones can (the session path, so a site may move it).
- **Sessions.** With `site.ini [Session] Handler` empty (the default), PHP keeps sessions in files: give each site a
  directory of its own, not the shared default. With `Handler=ezpSessionHandlerDB` the database keeps them.
- **OPcache** on, with room for the kernel and the extensions. With `revalidate_freq=2` a changed file is seen within
  two seconds; set it to `0` while developing, or keep it higher and reload the pool on every deploy.
- **`security.limit_extensions = .php`**: only `.php` files run, and the web server rules send only the three front
  controllers there.

## Reload the right master

After a change of a PHP class, operator or INI file, reload the FPM master that **serves the site**, then clear the
caches that hold rendered output. On machines with several PHP versions it is often not the one called `php-fpm`
(Plesk: `plesk-php85-fpm` for PHP 8.5). `exp:velocity deploy` finds it from the pool files
(`[DeploySettings] PhpFpmService=auto`) and runs the steps in the right order:

```bash
php bin/php/console exp:velocity deploy --dry-run --no-velocity --allow-root-user   # shows the unit it found and why
php bin/php/console exp:velocity deploy --no-velocity --allow-root-user
```

How this example was checked: `php-fpm -t` with a throwaway global configuration that includes the pool (socket and
log paths moved to a scratch directory); the pool was not started.

## Related pages

- [Chapter 8, section 8.5.2: PHP-FPM](../../install/08-serving-the-site.md#852-php-fpm) and
  [section 8.3.14: Deploying a change](../../install/08-serving-the-site.md#8314-deploying-a-change-expvelocity-deploy).
- [Chapter 8, section 8.7: File permissions and ownership](../../install/08-serving-the-site.md#87-file-permissions-and-ownership).
- [Apache virtual host](apache-virtual-host.md), [nginx](nginx.md), [Caddy](caddy.md).
- [Web server examples](README.md) and [all examples](../README.md).
- [Free documentation](../../help/free-documentation.md): this page is free to copy, translate and improve.
