---
title: ISPConfig
audience: hosting
category: hosting
order: 50
summary: ISPConfig is not mentioned in the Exponential sources and is not placed in any tier. This page says so plainly, lists what an ISPConfig-managed server must provide for an Exponential site, maps each need to a generic panel task, and gives a test plan for hosts who want to offer the combination.
keywords: ISPConfig, not tested, no tier, hosting panel, PHP-FPM, Apache, nginx, .htaccess, rewrite rules, open_basedir, cron, database, TLS, test plan, requirements
type: help
---
# ISPConfig

This page is for hosting companies whose servers are managed with ISPConfig and who want to offer Exponential on
them. It answers the first question honestly: **the Exponential sources do not mention ISPConfig at all.** It is not
placed in a tier, and nothing here comes from a test. What the page can give you is the list of what an Exponential
site needs, translated into the tasks any hosting panel performs, and a plan to test the combination yourself.

> **Warning**
> **`.htaccess` slows down every request.** When `.htaccess` files are enabled (`AllowOverride` anything but `None`), let alone
> used, Apache looks for them and reads and evaluates them on every request, in every directory of the path, and
> page loading times go up drastically ([measured](../../../guides/setup-files-from-the-console.md#measured-htaccess-against-rules-in-the-virtual-host)). The only real fix is to switch the feature off for
> the virtual host: `AllowOverride None` in the HTTPS virtual host, and the rules in the virtual host's own
> configuration as mod_rewrite rules. That is far faster in production speed tests and well worth the time it takes
> to optimize a production configuration. Switch over right away: copy the rules of the
> [example HTTPS virtual host](../../../examples/web-servers/apache-virtual-host.md) into your virtual host (with the static cache, also
> [`staticcache_offload.conf`](../../../examples/staticcache_offload.conf)), set `AllowOverride None`, run `apachectl configtest`, reload Apache and
> test the site. The same rules for other servers: [nginx](../../../examples/web-servers/nginx.md), [LiteSpeed](../../../examples/web-servers/litespeed.md),
> [Caddy](../../../examples/web-servers/caddy.md), [IIS](../../../examples/web-servers/iis.md), [Velocity](../../../examples/web-servers/velocity.md) (built in); where virtual host rules go in a control
> panel: [Plesk](plesk.md), [cPanel](cpanel.md), [DirectAdmin](directadmin.md), [ISPConfig](ispconfig.md).
> **On LiteSpeed or OpenLiteSpeed,** the server's own page cache can also answer anonymous visitors and crawlers without PHP: [Page caching with LiteSpeed and OpenLiteSpeed](../../../guides/litespeed-page-cache.md).

> **Note**
> Nothing on this page describes ISPConfig's screens, options or files. For how ISPConfig performs each task below,
> check ISPConfig's documentation.

## Contents

1. [Status in the sources](#status-in-the-sources)
2. [Why it may still work](#why-it-may-still-work)
3. [What the server must provide](#what-the-server-must-provide)
4. [Two things to check first](#two-things-to-check-first)
5. [A test plan](#a-test-plan)
6. [Checklist](#checklist)

## Status in the sources

| Question | Answer |
|---|---|
| Is ISPConfig named in the documentation or code? | no |
| Which tier? | none: it is not placed |
| Has the project run Exponential on it? | not that the sources record |

The tiers are defined in [Operating system support](../../os-support.md#the-four-tiers). Panels that are placed:
Plesk (tier 1 on the development server), cPanel and DirectAdmin (tier 3)
([Control panels: overview](README.md#panels-in-the-sources)).

## Why it may still work

The reasoning that places cPanel and DirectAdmin in tier 3 is that Exponential needs nothing from a panel itself:
"It is plain PHP files plus a database; it has no compiled code of its own" and "runs on almost any operating system
that has PHP 8.0 or later (PHP 7.4 usable without support) and a web server" ([Operating system support](../../os-support.md)). Any server whose panel
can provide the items below, on a supported operating system, meets the requirements. That is an inference, not a
test result: treat ISPConfig as at most tier 3 until you have tested it.

## What the server must provide

| Need | Value | Panel task (generic) |
|---|---|---|
| Operating system | a tier 1 or 2 distribution, for example Debian 12 or 13, Ubuntu 22.04 or 24.04, AlmaLinux 9 | the base install below the panel ([Tier 2](../../os-support.md#tier-2-known-to-work)) |
| PHP | 8.0 or later (Velocity included), with `dom`, `libxml`, `simplexml`, `xsl`, `mbstring`, `iconv`, `intl`, `pcre`, `json`, `session`, `zlib`, one database extension, `gd` or ImageMagick | PHP version and modules per site ([2.3](../../../install/02-requirements.md#23-php-extensions)) |
| PHP for the command line | the same version and extensions as the site's | for Composer, the console and cron ([Command line and web PHP](../../../install/02-requirements.md#command-line-and-web-php)) |
| `php.ini` | `memory_limit` 64M or more, `max_execution_time` 30 or more, `date.timezone`, `file_uploads` on, `allow_url_fopen` on | per-site PHP settings ([2.5](../../../install/02-requirements.md#25-phpini-settings)) |
| PHP user | PHP runs as the site's own user | per-site PHP-FPM pool ([8.7.2](../../../install/08-serving-the-site.md#872-one-user-or-one-group)) |
| Document root | the installation root (with `index.php`) | the site's document root |
| Rewrite rules | Apache: `.htaccess` from `.htaccess_root`; nginx: the derived server block | the site's web server directives ([8.5](../../../install/08-serving-the-site.md#85-apache-with-php-fpm), [8.6](../../../install/08-serving-the-site.md#86-nginx-with-php-fpm)) |
| Database | in the official order: 1. SQLite 3 (no server) · 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL; Microsoft SQL Server is known to be possible with additional work at this time | a database and user, UTF-8 ([Databases and users](databases-and-users.md)) |
| Scheduled jobs | `runcronjobs.php` with the site's PHP | the site's cron jobs ([Cron and shell access](cron-and-shell-access.md)) |
| HTTPS | a certificate for every name of the site | the site's certificate settings ([TLS certificates in panels](tls-certificates-in-panels.md)) |

## Two things to check first

**1. Which web server serves the site, and how its rules are set.** Under Apache the shipped `.htaccess` works when
`.htaccess` files are read (`AllowOverride`) and `mod_rewrite` is on. Under nginx there is no `.htaccess`; the
Exponential rules must go into the server's configuration. The project ships no nginx file: chapter 8 gives a server
block that is "derived, not shipped" and asks you to "test it on your site before you rely on it"
([8.6](../../../install/08-serving-the-site.md#86-nginx-with-php-fpm)). Under Apache, `php bin/php/console
exp:setup:htaccess --apply`, run as the site's shell user, copies `.htaccess_root` into place; under nginx that copy
protects nothing ([Setting up .htaccess and config.php from the console](../../../guides/setup-files-from-the-console.md)).

> **Warning**
> "Never serve the installation with a generic configuration such as nginx's 'serve the file if it exists, else
> `index.php`'": "those hand out every file that exists, including `settings/*.ini` and the SQLite database"
> ([13.2](../../../install/13-security-hardening.md#132-what-the-web-server-must-never-hand-out)). A panel's default
> PHP site template is exactly such a configuration unless you add the Exponential rules.

**2. `open_basedir` and `disable_functions`.** Panels often restrict both per site. The setup wizard only warns about
`open_basedir`; the cronjob page needs `proc_open`, `exec` and `posix_kill` for its run, crontab and stop features
([Cronjobs, Before you start](../../../guides/cronjobs.md#2-before-you-start)); ezupdate looks for Composer below the
installation root first because `open_basedir` can hide system folders
([ezupdate](../../../features/6.0/extensions/ezupdate.md)).

## A test plan

| Step | Pass when |
|---|---|
| Install on a fresh site (setup wizard, or `exp:install` with a shell) | every critical test passes; site and `/admin` answer |
| Request `/settings/site.ini`, `/.env`, `/composer.json`, `/kernel/classes/ezini.php` | 404 or an Exponential page, never the file ([13.2](../../../install/13-security-hardening.md#132-what-the-web-server-must-never-hand-out)) |
| Upload an image, view it | image and variations served |
| Cron line | the run appears on **Setup > Cronjobs** |
| Change PHP version in the panel | the site still answers; both web and command line PHP changed |
| Backup and restore to a second site | the restored site works |
| Two sites on the server | neither site's PHP can read the other's `settings/override` |

If everything passes, tell the project, so ISPConfig can be placed in a tier
([The four tiers](../../os-support.md#the-four-tiers)).

## Checklist

- [ ] Operating system of tier 1 or 2 below the panel.
- [ ] PHP 8.x with every required extension, web and command line alike, one user per site.
- [ ] Exponential rewrite rules in place (`.htaccess` or the derived nginx block); file-exposure test passed.
- [ ] `open_basedir` and `disable_functions` decisions documented.
- [ ] Database, cron and certificate set up.
- [ ] The test plan passed; offer documented as not covered by the project's own tests.

## Related pages

- [Control panels: overview](README.md), [Webmin](webmin.md)
- [nginx with PHP-FPM](../servers/nginx-with-php-fpm.md), [Apache with PHP-FPM](../servers/apache-with-php-fpm.md)
- [Requirements at a glance](../basics/requirements-at-a-glance.md)
- [Setting up .htaccess and config.php from the console](../../../guides/setup-files-from-the-console.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
