---
title: Hosting requirements for a website
audience: website-creator, admin
category: website-creation
order: 70
summary: What a website on Exponential needs from its hosting - PHP, extensions, database, web server or Velocity, disk, cronjobs and mail - and the questions to ask a hosting provider.
keywords: hosting, requirements, PHP, database, web server, Velocity, Apache, nginx, FrankenPHP, shared hosting, VPS, cron, mail, disk, hosting provider, website creator
type: help
---
# Hosting requirements for a website

This page is for website creators who choose or check hosting for a site. It summarizes what Exponential needs, which
choices you have, and the questions to send a hosting provider. The full reference is
[Requirements](../../../install/02-requirements.md) and [Serving the site](../../../install/08-serving-the-site.md).

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
> panel: [Plesk](../../hosting/panels/plesk.md), [cPanel](../../hosting/panels/cpanel.md), [DirectAdmin](../../hosting/panels/directadmin.md), [ISPConfig](../../hosting/panels/ispconfig.md).
> **On LiteSpeed or OpenLiteSpeed,** the server's own page cache can also answer anonymous visitors and crawlers without PHP: [Page caching with LiteSpeed and OpenLiteSpeed](../../../guides/litespeed-page-cache.md).

## Contents

1. [The checklist](#the-checklist)
2. [Choosing how the site is served](#choosing-how-the-site-is-served)
3. [Choosing the database](#choosing-the-database)
4. [What else the site needs](#what-else-the-site-needs)
5. [Questions for a hosting provider](#questions-for-a-hosting-provider)

## The checklist

From [2.1 The checklist](../../../install/02-requirements.md#21-the-checklist):

| Need | Minimum | Recommended |
|---|---|---|
| PHP | 8.0 supported, Layouts included (7.4 usable without support) | the newest 8.x the system offers (8.5 is supported) |
| PHP for Velocity | 8.0 with `sockets` (from Velocity 0.0.4.54) | plus `pcntl` and `openssl` |
| `php.ini` | `memory_limit` 64M, `max_execution_time` 30, `date.timezone` set, `file_uploads` on, `allow_url_fopen` on | |
| Database | SQLite 3 (no server) | in the official order: SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL |
| Composer | 2.x | the current 2.x |
| Web server | none (Velocity or PHP's built-in server) | Velocity, or Apache/nginx with PHP-FPM |
| Image conversion | GD or ImageMagick's `convert` | both |

The setup wizard's system check, and the `SystemCheck` step of the command-line installers, test all of this and
print the fix for each failure.

## Choosing how the site is served

From [8.1 The choice in one table](../../../install/08-serving-the-site.md#81-the-choice-in-one-table):

| | Velocity | FrankenPHP (through Velocity) | Apache + PHP-FPM | nginx + PHP-FPM |
|---|---|---|---|---|
| Role | recommended for every stage | production-ready alternative | traditional | traditional |
| PHP | 8.0 or later | built into the binary | 8.0 or later | 8.0 or later |
| Own HTTPS certificates | yes, including ACME | yes | needs `mod_ssl` and a certificate tool | needs a certificate tool |
| Response cache in front of PHP | yes | not yet | no | no |
| Suits shared hosting | usually not (long-running process) | usually not | yes | rarely |

So:

- **A server or VPS you control:** Velocity ([Exponential Velocity](../../../features/6.0/velocity-web-server.md)).
- **Shared hosting:** Apache with PHP-FPM and the shipped `.htaccess_root` rules
  ([8.5 Apache with PHP-FPM](../../../install/08-serving-the-site.md#85-apache-with-php-fpm)).
- **An nginx you already run:** [8.6 nginx with PHP-FPM](../../../install/08-serving-the-site.md#86-nginx-with-php-fpm).
- **Only PHP 8.0 available:** any of them; Velocity runs on PHP 8.0 too, from its release 0.0.4.54
  ([PHP 8.0 and Red Hat Enterprise Linux](../../../install/02-requirements.md#php-80-and-red-hat-enterprise-linux)).

## Choosing the database

| Site | Database |
|---|---|
| Small site, one server, few editors | SQLite 3: nothing to install, one file in `var/storage/sqlite3/` |
| Site with many editors or a growing amount of content | PostgreSQL, or MariaDB/MySQL if your hosting offers only that |
| Hosting that offers MongoDB or Oracle | supported; see the notes per engine |

Details, limits and the support order: [Database support](../../database-support.md),
[Databases](../../../install/09-databases.md).

## What else the site needs

| Need | Why | Read |
|---|---|---|
| Writable `var/` and `settings/` for the web server's user | caches, uploads, logs, the SQLite file | [8.7 File permissions and ownership](../../../install/08-serving-the-site.md#87-file-permissions-and-ownership) |
| A cron job every few minutes | `runcronjobs.php` runs the periodic jobs: notifications, link checks, cleanup | [10.3 Cronjobs](../../../install/10-after-installing.md#103-cronjobs) |
| Outgoing mail | forms, registration, password resets, newsletters | [10.4 Mail](../../../install/10-after-installing.md#104-mail-the-file-transport-and-real-sending) |
| HTTPS | every public site; Velocity can fetch certificates itself | [Velocity HTTPS certificates](../../../features/6.0/velocity-https-certificates.md) |
| Backups of the database, `var/storage` and your settings | restore after a mistake | [Backups and restores](backups-and-restores.md) |
| Disk for media | images and files grow with the site | [Images and image variations](images-and-image-variations.md) |
| Optional: a Solr server | search for large sites with `ezfind` | [Search with ezfind and Solr](search-with-ezfind-and-solr.md) |

## Questions for a hosting provider

Copy and send:

- [ ] Which PHP versions do you offer, and can I choose the newest 8.x per site?
- [ ] Are the PHP extensions in [2.3 PHP extensions](../../../install/02-requirements.md#23-php-extensions) installed?
- [ ] May I run a long-running process (for Velocity), or only PHP-FPM behind your web server?
- [ ] Which databases do you offer, and may I create a database user for the site alone?
- [ ] May I install a crontab entry that runs every five minutes?
- [ ] Do you allow outgoing mail, and from which sender domains?
- [ ] Do I get shell (SSH) access to run Composer and the console?
- [ ] How are backups made, how long are they kept, and how do I restore one?
- [ ] Can I use my own HTTPS certificate, or do you provide one?
- [ ] Is ImageMagick installed?

> **Note**
> 7x offers hosting for Exponential; what it includes is on the 7x website, not in this help
> ([Who is 7x?](../../who-is-7x.md)).

## Related pages

- [Choosing an install method for a new site](choosing-an-install-method.md)
- [Going live: hosts, HTTPS and siteaccesses](going-live-hosts-https.md)
- [Operating system support](../../os-support.md)
- [Choosing hosting, database and operating system](../../leadership/choosing-hosting-database-and-os.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
