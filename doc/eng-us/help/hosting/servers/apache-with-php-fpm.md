---
title: Apache with PHP-FPM
audience: hosting, admin
category: hosting
order: 30
summary: How a host serves Exponential with Apache httpd 2.4 and a PHP-FPM pool per site: the shipped rewrite rules, the virtual host, HTTPS, the right FPM master to reload, Plesk, and the checks before go-live.
keywords: Apache, httpd, PHP-FPM, mod_rewrite, mod_proxy_fcgi, htaccess, .htaccess_root, .htaccess_root_static, virtual host, AllowOverride, mod_ssl, mod_http2, Plesk, plesk-php85-fpm, pool, shared hosting
type: help
---
# Apache with PHP-FPM

This page is for system administrators at hosting companies who serve Exponential with Apache httpd 2.4 and PHP-FPM,
the classic setup and the usual one on shared hosting and control panels. It answers: which modules and rules are
needed, how the virtual host looks, how HTTPS and the PHP-FPM pool fit in, which FPM master to reload after a change,
and how to check the result. The source is
[Chapter 8, Apache with PHP-FPM](../../../install/08-serving-the-site.md#85-apache-with-php-fpm).

## Contents

1. [Why Apache](#why-apache)
2. [Step by step](#step-by-step)
3. [Rules in .htaccess or in the virtual host](#rules-in-htaccess-or-in-the-virtual-host)
4. [The PHP-FPM pool](#the-php-fpm-pool)
5. [Reloading the right PHP-FPM](#reloading-the-right-php-fpm)
6. [Plesk](#plesk)
7. [Static cache offload](#static-cache-offload)
8. [What can go wrong](#what-can-go-wrong)
9. [Checklist](#checklist)

## Why Apache

- It needs no long-running process of the customer's own, so it fits shared hosting.
- The rewrite rules ship with Exponential as `.htaccess_root`; Apache (and LiteSpeed) read them directly.
- [Operating system support](../../os-support.md#web-servers) lists Apache httpd 2.4 with PHP-FPM (or mod_php) as
  tier 1. PHP-FPM is preferred over mod_php, which "ties PHP to Apache's prefork model"
  ([PHP SAPIs](../../os-support.md#php-sapis)).
- It runs Exponential on PHP 8.0 and later, so it is the answer on machines with the stock PHP 8.0 of Red Hat
  Enterprise Linux 9 and its rebuilds.

## Step by step

1. **Enable the rewrite rules.** Copy, do not move, so an update can bring a new version:

   ```bash
   cp .htaccess_root .htaccess
   ```

   Use `.htaccess_root_static` instead only when the site serves a generated static cache
   ([Static cache](../performance/static-cache.md)).

2. **Load the modules:** `mod_rewrite`, `mod_proxy` and `mod_proxy_fcgi` (Debian: `a2enmod rewrite proxy_fcgi`);
   for HTTPS `mod_ssl`, for HTTP/2 `mod_http2`.

3. **Create the virtual host** (adjust paths and the PHP-FPM socket):

   ```apache
   <VirtualHost *:80>
       ServerName example.com
       ServerAlias www.example.com
       # The installation root, where index.php is
       DocumentRoot /var/www/vhosts/example.com/httpdocs
       DirectoryIndex index.php

       <Directory /var/www/vhosts/example.com/httpdocs>
           Options FollowSymLinks
           # Lets Apache read the .htaccess created in step 1
           AllowOverride All
           Require all granted
       </Directory>

       # Every PHP script goes to the PHP-FPM pool of this site
       <FilesMatch \.php$>
           SetHandler "proxy:unix:/run/php-fpm/example.com.sock|fcgi://localhost"
       </FilesMatch>
   </VirtualHost>
   ```

4. **HTTPS.** Add a `*:443` virtual host with the same content plus:

   ```apache
       SSLEngine on
       SSLCertificateFile    /etc/ssl/example.com/fullchain.pem
       SSLCertificateKeyFile /etc/ssl/example.com/privkey.pem
       Protocols h2 http/1.1
   ```

   Obtain the certificate with certbot or the panel. Once HTTPS works, redirect port 80 in the `*:80` host
   (`Redirect permanent / https://example.com/`).

5. **Check and reload:**

   ```bash
   apachectl configtest           # Syntax OK
   systemctl reload apache2       # httpd on Red Hat style systems
   curl -sI https://example.com/ | head -1
   ```

6. **Security headers.** The kernel sends them on its pages (`site.ini [HTTPHeaderSettings] SecurityHeaders[]`). For
   static files add the same with `mod_headers` if you want them there too
   ([Chapter 13](../../../install/13-security-hardening.md)).

## Rules in .htaccess or in the virtual host

| | `.htaccess` | rules in the virtual host |
|---|---|---|
| Needs | `AllowOverride All` | `AllowOverride None` |
| Cost | a file lookup per directory | `AllowOverride None` saves that lookup |
| Customer can change it | yes, which suits shared hosting | no, only the host |
| Upgrade | copy the new `.htaccess_root` over `.htaccess` | copy the changed rules into the vhost |

Inside `<Directory>` the rules of `.htaccess_root` keep their form. In server context (outside `<Directory>`) every
pattern needs a leading slash, as in [doc/eng-us/examples/ezpublish.conf](../../../examples/ezpublish.conf). That example's
rule list predates the current `.htaccess_root`, so take the rules from `.htaccess_root` itself
([Chapter 8, step 3](../../../install/08-serving-the-site.md#851-step-by-step)).

> **Warning**
> After every upgrade, compare your copied rules with the new `.htaccess_root`. A rule that is missing can expose
> files that should never be sent; a rule that is added can be the fix for exactly that.

## The PHP-FPM pool

The pool runs PHP as the site's user. The settings chapter 8 names for Exponential (in the pool file or `php.ini`):

| Setting | Guidance from the sources |
|---|---|
| `memory_limit` | at least 256M in production; more for large imports. The setup wizard's floor is 64M ([Chapter 2](../../../install/02-requirements.md#25-phpini-settings)) |
| `max_execution_time` | the wizard's floor is 30; chapter 2's production example uses 60 |
| `upload_max_filesize`, `post_max_size` | the customer's largest upload |
| `date.timezone` | set it; a missing value fails the wizard's critical test |
| OPcache | on, with timestamp checks on ([OPcache](../performance/opcache.md)) |

One pool per customer, running as that customer's user, is the isolation unit on a shared machine. Sizing the pool
(process manager, child counts, timeouts) is on [PHP-FPM tuning](../performance/php-fpm-tuning.md); panels and pools
on [PHP selection and FPM pools](../panels/php-selection-and-fpm-pools.md).

## Reloading the right PHP-FPM

After a PHP class, operator or INI change, **reload the FPM master that serves the site**. On machines with several
PHP versions it is often not the one called `php-fpm`.

```bash
systemctl reload php-fpm                                                   # the distribution's single PHP
php bin/php/console exp:velocity deploy --no-velocity --allow-root-user    # finds the right one, clears caches in order
php bin/php/console exp:velocity deploy --no-velocity --dry-run --allow-root-user   # shows what it found and why
```

`exp:velocity deploy` works on Apache-only sites too: `--no-velocity` skips the Velocity steps.
`[DeploySettings] PhpFpmService=auto` (in `settings/velocity.ini`) looks for the pool named after the installation's
hosting domain, or one that names its directory, in `/opt/plesk/php/*/etc/php-fpm.d`, `/etc/php-fpm.d`,
`/etc/php/*/fpm/pool.d` and `/etc/opt/remi/php*/php-fpm.d` ([Velocity engines, Which PHP-FPM](../../../bc/6.0/velocity-engines.md#which-php-fpm)).

> **Note**
> A reload is not confined to one site. The master asks every child of every pool to finish, and requests on other
> sites of the same PHP version wait meanwhile. That is why `[DeploySettings] PhpFpmReload=auto` waits for OPcache to
> revalidate the changed files instead of reloading, when the pool's OPcache settings allow it
> ([Velocity engines](../../../bc/6.0/velocity-engines.md#deploying-a-php-change-expvelocity-deploy)).

## Plesk

Plesk generates the virtual host itself; do not edit its files ([Chapter 8, Plesk](../../../install/08-serving-the-site.md#853-plesk)):

- point the domain's document root at the installation root;
- choose "FPM application served by Apache" in the PHP settings, and the PHP version;
- keep `.htaccess` (copied from `.htaccess_root`); Plesk's Apache reads it;
- install the certificate in Plesk and switch on the redirect to HTTPS.

Plesk names each pool after the domain in `/opt/plesk/php/<version>/etc/php-fpm.d/<domain>.conf`, served by the unit
`plesk-php<XY>-fpm` (for example `plesk-php85-fpm`). `systemctl restart php-fpm` restarts a different master that no
Plesk site uses. More on [Plesk](../panels/plesk.md).

## Static cache offload

With `.htaccess_root_static`, or the rules of [doc/eng-us/examples/staticcache_offload.conf](../../../examples/staticcache_offload.conf)
in the vhost of the **public** site, Apache answers a generated static page from its file without starting PHP. Never
add those rules to the administration vhost. See [Static cache](../performance/static-cache.md).

## What can go wrong

| Symptom | Cause | Fix |
|---|---|---|
| 404 for every page | `.htaccess` not read (`AllowOverride`) or `mod_rewrite` off | allow overrides, load `mod_rewrite` |
| 404 for every asset under `design/` | the document root is not the installation root | point `DocumentRoot` at the directory with `index.php` |
| 500 | a PHP or permission error | read the Apache and PHP-FPM error logs, then `var/log/error.log` |
| 200 for `/.git/config` or a kernel `.php` path | rules not active | fix before the site is public |
| A change "does not work" | the reloaded FPM master is not the one that serves the site | `exp:velocity deploy --no-velocity --dry-run` |
| Files in `var/` owned by root | a console script run as root | run scripts as the site user ([Chapter 8, One user, or one group](../../../install/08-serving-the-site.md#872-one-user-or-one-group)) |

## Checklist

- [ ] `.htaccess` copied from `.htaccess_root` (or the rules in the vhost), `mod_rewrite` and `mod_proxy_fcgi` loaded.
- [ ] Document root is the installation root.
- [ ] One PHP-FPM pool per site, running as the site's user; OPcache on.
- [ ] HTTPS with a CA certificate; port 80 redirected once HTTPS works.
- [ ] The four routing checks of [Web servers and engines](README.md#what-every-setup-has-in-common) pass.
- [ ] Support knows which FPM unit serves the site (`exp:velocity deploy --no-velocity --dry-run`).

**Demo mode with Apache and PHP-FPM.** For a public demo, `SetEnv EXP_DEMO_MODE 1` in its virtual host or `.htaccess` reaches PHP-FPM as a FastCGI parameter through `mod_proxy_fcgi`, which `getenv()` reads; `env[EXP_DEMO_MODE] = 1` in the demo's pool does the same. [Demo mode examples](../../../examples/demo-mode/README.md), [Demo mode for hosts](../security/demo-mode-for-hosts.md).

## Related pages

- [Choosing a web server](choosing-a-web-server.md)
- [nginx with PHP-FPM](nginx-with-php-fpm.md)
- [PHP-FPM tuning](../performance/php-fpm-tuning.md)
- [Per-customer users and pools](../security/per-customer-users-and-pools.md)
- [Chapter 8: Serving the site](../../../install/08-serving-the-site.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
