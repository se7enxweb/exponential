---
title: "Module 1: Serving the site"
audience: trainer, hosting, admin
category: training
order: 10
summary: The first module of Hosting and operations, how a request is served, Apache and nginx with PHP-FPM, PHP-FPM pools and tuning, OPcache and APCu. Four lessons, 2 hours 45 minutes.
keywords: hosting and operations, module 1, serving the site, Apache, nginx, PHP-FPM, OPcache, APCu, routing, htaccess_root
type: help
---
# Module 1: Serving the site

Before anyone can run an Exponential site well, they need to know what serves it. This module gives the map (four
ways to serve a site and one shared routing list), then teaches the traditional setup with Apache or nginx and
PHP-FPM, the PHP values and timeouts of a pool, and the opcode cache that makes PHP fast. Velocity, the recommended
server, has its own module next.

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
| Course | [Hosting and operations](../README.md) |
| Duration | 2 hours 45 minutes (four lessons) |
| Lessons | 4 |
| Level | Intermediate |
| Format | Hands-on; one Linux virtual machine with root per learner |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [1.1 How a request is served](how-a-request-is-served.md) | 40 min | Compare the servers and prove the routing rules with four requests |
| [1.2 Apache and nginx with PHP-FPM](apache-and-nginx-with-php-fpm.md) | 45 min | Write a virtual host, reload safely, fix file ownership |
| [1.3 PHP-FPM pools and tuning](php-fpm-pools-and-tuning.md) | 45 min | Set pool values, check timeouts and APCu, preview a reload |
| [1.4 OPcache and APCu](opcache-and-apcu.md) | 35 min | Check OPcache settings and read the opcode cache profile |

## Preparing this module

- One Linux virtual machine per learner with root, Apache (or nginx), PHP-FPM, `curl` and a training installation
  made with `exp:install` ([The console install](../../../../install/07-console-install.md)).
- Both command lists ready for Debian style (`apache2`, `a2enmod`) and Red Hat style (`httpd`) systems if the class
  mixes them.
- Rehearse every exercise on the same operating system and PHP version the learners use.
- Check that `./console exp:setup:status` runs on each training installation: lesson 1.2 copies `.htaccess` with
  `exp:setup:htaccess` ([Setting up .htaccess and config.php from the console](../../../../guides/setup-files-from-the-console.md)).
- Print the routing diagram of [Chapter 8, section 8.2](../../../../install/08-serving-the-site.md#82-how-a-request-is-served)
  as a handout.

## Timing plan

| Time | Item |
|---|---|
| 0:00 | Lesson 1.1 (40 min) |
| 0:40 | Lesson 1.2 (45 min) |
| 1:25 | Break (15 min) |
| 1:40 | Lesson 1.3 (45 min) |
| 2:25 | Lesson 1.4 (35 min) |
| 3:00 | End (2 h 45 min of lessons plus one break) |

## Related pages

- [Hosting and operations: course overview](../README.md)
- [Web servers and engines](../../../hosting/servers/README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
