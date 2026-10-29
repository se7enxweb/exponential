---
title: Plesk
audience: hosting, admin
category: hosting
order: 20
summary: Running Exponential on Plesk, the panel of the project's own development server (AlmaLinux 9, tier 1): Plesk's PHP builds and per-domain PHP-FPM pools, the plesk-phpXY-fpm services, the domain settings, nginx in front of Apache, certificates, logs, cron with the domain's PHP, and Velocity beside Plesk.
keywords: Plesk, AlmaLinux 9, /opt/plesk/php, plesk-php85-fpm, PHP-FPM pool, FPM application served by Apache, Tools & Settings, PHP Settings, Additional configuration directives, Let's Encrypt, /usr/local/psa/var/certificates, access_ssl_log, mod_remoteip, firewall, Velocity, exp:velocity deploy
type: help
---
# Plesk

This page is for hosting companies and administrators who run Exponential on servers managed by Plesk. Plesk is the
panel the project itself uses: its development server is AlmaLinux 9 with Plesk, which makes Plesk on AlmaLinux 9
**tier 1**, tested every day ([Tier 1](../../os-support.md#tier-1-tested-here)). The page answers how Plesk's PHP and
pools are laid out, how to set up a domain for Exponential, what to reload after a change, how certificates, logs and
cron work there, and how Velocity runs beside Plesk.

Every Plesk detail on this page comes from the Exponential sources cited inline. For anything else in Plesk, check
Plesk's documentation.

## Contents

1. [What runs on the development server](#what-runs-on-the-development-server)
2. [Plesk's PHP: versions, extensions, command line](#plesks-php-versions-extensions-command-line)
3. [Setting up a domain](#setting-up-a-domain)
4. [PHP-FPM pools and the right service](#php-fpm-pools-and-the-right-service)
5. [nginx in front of Apache](#nginx-in-front-of-apache)
6. [Certificates](#certificates)
7. [Logs](#logs)
8. [Cron](#cron)
9. [Velocity beside Plesk](#velocity-beside-plesk)
10. [Known effects of Plesk settings](#known-effects-of-plesk-settings)
11. [Checklist](#checklist)

## What runs on the development server

"AlmaLinux 9 (x86_64, RHEL 9 family): The project's development server: Apache 2.4 with PHP-FPM 8.5 from the Plesk
panel, and Exponential Velocity on the same installation; SQLite, MariaDB, MongoDB and Oracle (in a container) sites
run there, and a PostgreSQL server is available" ([Tier 1](../../os-support.md#tier-1-tested-here)). Plesk hosts on
the other tier 2 distributions are tier 2 ([Tier 2](../../os-support.md#tier-2-known-to-work)).

## Plesk's PHP: versions, extensions, command line

From [Chapter 2, Plesk](../../../install/02-requirements.md#plesk):

- "Plesk installs each PHP version under `/opt/plesk/php/<version>/` (for example `/opt/plesk/php/8.5/bin/php`) next
  to the system's PHP. A domain uses the version chosen in its hosting settings; the command line `php` is the
  system's. Run Composer and the console with the domain's PHP."
- "The bundled extensions ship with Plesk's PHP and are switched on under **Tools & Settings > PHP Settings**; PECL
  extensions are installed with `/opt/plesk/php/<version>/bin/pecl install <extension>`."

```bash
/opt/plesk/php/8.5/bin/php -v
/opt/plesk/php/8.5/bin/php -m
/opt/plesk/php/8.5/bin/php bin/php/console exp:velocity ext check
```

> **Warning**
> The `php` on the `PATH` is the system's PHP, not the domain's. Composer run with the wrong PHP "picks package
> versions the web PHP cannot run", and a missing extension then shows as a white page
> ([Command line and web PHP](../../../install/02-requirements.md#command-line-and-web-php)). Always call the domain's
> PHP by its path for Composer, the console, the installers and cron.

## Setting up a domain

From [8.5.3 Plesk](../../../install/08-serving-the-site.md#853-plesk): "Plesk generates the virtual host itself; do not
edit its files. In Plesk:

- point the domain's document root at the installation root (the directory with `index.php`);
- choose 'FPM application served by Apache' in the PHP settings, and the PHP version;
- keep `.htaccess` (copied from `.htaccess_root`); Plesk's Apache reads it;
- install the certificate in Plesk (its Let's Encrypt extension or your own) and switch on the redirect to HTTPS."

```bash
cd /var/www/vhosts/<domain>/<docroot>
cp .htaccess_root .htaccess
```

The PHP values the setup wizard checks (`memory_limit` 64M or more, `max_execution_time` 30 or more,
`date.timezone`, `file_uploads`, `allow_url_fopen`) are set in the domain's PHP settings
([2.5](../../../install/02-requirements.md#25-phpini-settings)). The MongoDB porting notes set FPM directives such as
`request_terminate_timeout` in the domain's PHP settings, field **Additional configuration directives**
("Plesk → Domains → your domain → PHP Settings")
([MongoDB kernel support](../../../bc/6.0/MONGODB_KERNEL_SUPPORT_EXPANSION.md)).

## PHP-FPM pools and the right service

- "Each domain has its own PHP-FPM pool in `/opt/plesk/php/<version>/etc/php-fpm.d/<domain>.conf`, run by the service
  `plesk-php<XY>-fpm` (for example `plesk-php85-fpm`), not by `php-fpm`. After a PHP change, reload that one."
  ([Chapter 2, Plesk](../../../install/02-requirements.md#plesk))
- "`systemctl restart php-fpm` restarts a different master that no Plesk site uses."
  ([8.5.3](../../../install/08-serving-the-site.md#853-plesk))

```bash
ls /opt/plesk/php/*/etc/php-fpm.d/ | grep <domain>     # which PHP version serves the domain
systemctl reload plesk-php85-fpm                        # graceful, no dropped requests
```

`exp:velocity deploy` finds the right service itself: `settings/velocity.ini [DeploySettings] PhpFpmService=auto`
recognizes "the pool configuration named after the installation's hosting domain (`/var/www/vhosts/<domain>/...`, as
Plesk names pools)"; `--dry-run` shows what was found and why ([Velocity engines](../../../bc/6.0/velocity-engines.md)).
To fix it explicitly:

```ini
# settings/override/velocity.ini.append.php
[DeploySettings]
PhpFpmService=plesk-php85-fpm
```

More on pools: [PHP selection and FPM pools](php-selection-and-fpm-pools.md).

## nginx in front of Apache

"Plesk puts nginx in front of Apache and Apache's `mod_remoteip` (`RemoteIPHeader X-Forwarded-For`,
`RemoteIPInternalProxy 127.0.0.1`) sets `REMOTE_ADDR` to the visitor; nginx talks to Apache over HTTPS, so Apache
sets `HTTPS=on`. The kernel then sees the visitor as the peer and needs no forwarded header: nothing to configure, and
`ClientIpByCustomHTTPHeader` can stay `false`." ([Trusted proxies](../../../bc/6.0/trusted-proxies.md))

## Certificates

- Install the domain's certificate in Plesk (its Let's Encrypt extension or your own) and switch on the redirect to
  HTTPS ([8.5.3](../../../install/08-serving-the-site.md#853-plesk)).
- When Velocity or FrankenPHP serves the same domain on another port, they can use the certificate Plesk holds: the
  FrankenPHP notes point `[HTTPSSettings] Certificate` and `Key` at a copy of "the Let's Encrypt cert Plesk holds for
  the host", exported from `/usr/local/psa/var/certificates/` outside the document root.
- "Plesk renews the Let's Encrypt cert on its own schedule, writing a **new file under a new name**; the exported copy
  then goes stale." The export must be "repeated after each renewal", followed by a reload
  ([FrankenPHP, Renewal](../../../bc/6.0/frankenphp.md)). Velocity checks its certificate files every 60 seconds and
  swaps a changed, valid pair in live
  ([8.3.9](../../../install/08-serving-the-site.md#839-https-served-by-velocity-itself)), but only if the path it reads
  gets the new file.

See [TLS certificates in panels](tls-certificates-in-panels.md).

## Logs

Exponential's own logs are under `var/log` and `var/<site>/log`. To show Plesk's web server logs in
**Setup > Log files**, name them in `logviewer.ini` ([Log viewer](../../../features/6.0/log-viewer.md)):

```ini
# settings/override/logviewer.ini.append.php
[LogViewerSettings]
LogPath[webserver-access]=/var/www/vhosts/<domain>/logs/access_ssl_log
LogPath[webserver-error]=/var/www/vhosts/<domain>/logs/error_log
```

"The web server's user must be able to read the file" (`settings/logviewer.ini`). The keys `webserver-access` and
`webserver-error` are in the shipped `LogList[]`.

## Cron

The cronjob page in the administration needs a command line PHP; "On a host with several PHP versions set
`PhpCliPath` to the one the site runs on, for example `/opt/plesk/php/8.5/bin/php`"
([Cronjobs, Before you start](../../../guides/cronjobs.md#2-before-you-start)):

```bash
/opt/plesk/php/8.5/bin/php bin/php/console exp:ini set cronjob.ini/AdminSettings/PhpCliPath /opt/plesk/php/8.5/bin/php global
```

A crontab line with the domain's PHP, as the page proposes it:

```text
17 * * * * cd /var/www/vhosts/<domain>/<docroot> && /opt/plesk/php/8.5/bin/php runcronjobs.php -s site infrequent >/dev/null 2>&1
```

Install the lines in the crontab of the domain's system user, so the files the jobs write stay writable for the pool
([Cron and shell access](cron-and-shell-access.md)).

## Velocity beside Plesk

The development server runs Velocity next to Plesk's Apache on the same installation. Points to settle:

- **Ports.** Plesk's Apache and nginx own 80 and 443; Velocity keeps its own ports (8088 and 8080 as shipped) or sits
  behind Plesk's nginx ([8.3.5](../../../install/08-serving-the-site.md#835-address-ports-user-and-group)).
- **Firewall.** "Some Plesk installs drive firewalld or nftables through the Plesk Firewall extension. If a rule you
  add with `firewall-cmd` disappears on the next Plesk apply, add it in Plesk (Tools & Settings → Firewall) instead."
  ([FrankenPHP](../../../bc/6.0/frankenphp.md))
- **One user.** Velocity's workers run as the domain's system user (`[ServerSettings] User`, `Group`), the same user
  as the Plesk pool ([8.7.2](../../../install/08-serving-the-site.md#872-one-user-or-one-group)).
- **Deploys.** `exp:velocity deploy` reloads `plesk-php<XY>-fpm` and restarts Velocity in one run.

## Known effects of Plesk settings

| Symptom | Cause in the sources | What to do |
|---|---|---|
| **Setup > System information** says OPcache "statistics not available" | "Plesk can put `opcache_get_status` in `disable_functions` for a domain"; "The cache works; only its figures cannot be read from PHP" ([System information](../../../guides/system-information.md)) | nothing, or allow the function for the domain |
| A PHP change has no effect | the wrong master was reloaded ([Chapter 12](../../../install/12-troubleshooting.md)) | reload `plesk-php<XY>-fpm` |
| A test fails in the browser but `php -m` looks fine | "the two PHPs differ: install the extension for the web PHP (on Plesk, the domain's PHP version)" | install it in the domain's PHP version and reload |
| ezupdate does not find a system Composer | "`open_basedir` (default Plesk) hides the system folders", so ezupdate searches below the installation root first ([ezupdate settings](../../../features/6.0/extensions/ezupdate.md)) | set `[ComposerSettings] Path`/`Binary`, or let its *Get Composer* download a verified `composer.phar` into `var/ezupdate/` |
| A long setup wizard step is cut off | the pool's `request_terminate_timeout` | raise it for the install, lower it afterwards |

## Checklist

- [ ] Domain's PHP version 8.0 or later (Velocity included); "FPM application served by Apache".
- [ ] Document root = installation root; `.htaccess` copied from `.htaccess_root`.
- [ ] With SOAP on: `index_soap.php` let through in **Additional directives for HTTPS** (or `.htaccess`), not only the
      old `soap.php` ([the SOAP entry script is now index_soap.php](../../../bc/6.0/soap-entry-script-index-soap.md#plesk)).
- [ ] Composer, console and cron use `/opt/plesk/php/<version>/bin/php`.
- [ ] `PhpCliPath` set; cron lines in the domain user's crontab.
- [ ] Reload `plesk-php<XY>-fpm` after PHP changes; `PhpFpmService` correct in `exp:velocity deploy --dry-run`.
- [ ] Certificate in Plesk, redirect to HTTPS on; export renewed after each renewal if Velocity or FrankenPHP uses it.
- [ ] Web server logs added to the log viewer.

**A public demo on Plesk.** Set `EXP_DEMO_MODE=1` for the demo's subscription only: `SetEnv EXP_DEMO_MODE 1` in its `.htaccess` (Apache or LiteSpeed) or its **Additional Apache directives**, or `env[EXP_DEMO_MODE] = 1` through the domain's PHP-FPM settings, and a `EXP_DEMO_MODE=1` line in its **Scheduled Tasks**. Plesk runs each PHP version with its own FPM master: reload the one of the domain's version. Step by step: [Recipe: run a public demo on LiteSpeed and Plesk](../servers/run-a-public-demo-on-litespeed-and-plesk.md).

## Related pages

- [Control panels: overview](README.md)
- [Apache with PHP-FPM](../servers/apache-with-php-fpm.md), [Velocity beside Apache or nginx](../servers/velocity-beside-apache-or-nginx.md)
- [Chapter 8: Serving the site](../../../install/08-serving-the-site.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
