---
title: Caddy
audience: hosting
category: hosting
order: 50
summary: What the Exponential sources say about Caddy, the two ways to use it (FrankenPHP, which is Caddy with PHP built in, or Caddy with PHP-FPM), the routing any Caddy setup must reproduce, and the traps to avoid.
keywords: Caddy, Caddyfile, FrankenPHP, php_fastcgi, php_server, PHP-FPM, rewrite, route, automatic HTTPS, SiteInclude, tier 3
type: help
---
# Caddy

This page is for system administrators at hosting companies who use Caddy, or want to. It answers: how Caddy relates
to Exponential, which of the two Caddy routes the project supports best, what routing a Caddy configuration must
implement, and what the sources do and do not provide.

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
> panel: [Plesk](../panels/plesk.md), [cPanel](../panels/cpanel.md), [DirectAdmin](../panels/directadmin.md), [ISPConfig](../panels/ispconfig.md).

## Contents

1. [Two ways to use Caddy](#two-ways-to-use-caddy)
2. [FrankenPHP: Caddy managed by Exponential](#frankenphp-caddy-managed-by-exponential)
3. [Caddy with PHP-FPM](#caddy-with-php-fpm)
4. [The routing every Caddy configuration needs](#the-routing-every-caddy-configuration-needs)
5. [HTTPS](#https)
6. [Checklist](#checklist)

## Two ways to use Caddy

| | FrankenPHP (through `exp:velocity`) | Caddy with PHP-FPM |
|---|---|---|
| What it is | "Caddy with PHP built in, downloaded as one checksum-verified binary" (`README.md`) | the Caddy web server passing requests to a PHP-FPM pool |
| Status | production-ready alternative engine | tier 3, expected to work ([Operating system support](../../os-support.md#web-servers)) |
| Configuration | a Caddyfile **generated** from `settings/velocity.ini` on every start | a Caddyfile you write |
| Shipped by the project | yes, driven by `exp:velocity` | no configuration ships |

For a host that likes Caddy, FrankenPHP is the route the project documents and generates configuration for. Read
[FrankenPHP](frankenphp.md).

## FrankenPHP: Caddy managed by Exponential

`exp:velocity` writes `var/vc/frankenphp/run/Caddyfile` on every `start`, `graceful` and `restart`, validates it with
`frankenphp validate` before it replaces the old one, and controls the server through Caddy's admin API on an
owner-only socket ([Chapter 8, FrankenPHP](../../../install/08-serving-the-site.md#84-frankenphp)). The file header
says it is rewritten every time, so never edit it; put your own directives in a file named by
`[FrankenPHPSettings] SiteInclude`, which is imported into every site block ([FrankenPHP as the Exponential web
engine](../../../bc/6.0/frankenphp.md)).

```bash
php bin/php/console exp:velocity ctl caddyfile --engine=frankenphp --allow-root-user   # print the generated Caddyfile
php bin/php/console exp:velocity ctl validate  --engine=frankenphp --allow-root-user   # write and validate it
```

## Caddy with PHP-FPM

The sources describe this setup in one line: "`php_fastcgi` and a rewrite to `index.php`"
([Operating system support](../../os-support.md#web-servers)), and the README's table of traditional servers lists
Caddy's `rewrite`, `handle` and `try_files`. No Caddyfile for PHP-FPM ships with Exponential, so the configuration is
yours to write and test, as with nginx.

> **Warning**
> Do not use a directive that serves every existing file below the root. The FrankenPHP integration deliberately
> avoids Caddy's `php_server` because its default "would serve `settings/*.ini`, the SQLite DB, kernel sources"
> ([FrankenPHP, The generated Caddyfile](../../../bc/6.0/frankenphp.md#5-the-generated-caddyfile)). Whatever you write
> for PHP-FPM must send only the listed asset paths as files and everything else to a front controller. Check your
> Caddy version's documentation for how its PHP directives treat existing files.

A practical way to start: generate the FrankenPHP Caddyfile on a test machine with `exp:velocity ctl caddyfile
--engine=frankenphp`, read its `route` block, and reproduce the same matchers in front of your PHP-FPM handler.

## The routing every Caddy configuration needs

Caddy never reads `.htaccess`, so `./console exp:setup:htaccess --apply` changes nothing for it; the routing lives in
the Caddyfile ([Setting up .htaccess and config.php from the console](../../../guides/setup-files-from-the-console.md)).
The generated Caddyfile implements `.htaccess_root` like this
([FrankenPHP, What it contains](../../../bc/6.0/frankenphp.md#what-it-contains-verified-output)):

| Step | Matcher | Action |
|---|---|---|
| 1 | `@static`: `expVelocity::STATIC_PATHS` and not `expVelocity::NEVER_STATIC` | serve the file, with `Cache-Control` when `StaticMaxAge` is above 0 |
| 2 | `@rest`: `^/(api/|index_rest\.php)` | rewrite to `/index_rest.php` |
| 3 | `@treemenu`: `^/([^/]+/)?content/treemenu` | rewrite to `/index_treemenu.php` |
| 4 | `@front`: everything else | rewrite to `/index.php` |

`STATIC_PATHS` matches design and extension stylesheets, images, scripts and fonts, `share/icons/`, the icon
themes of extensions (`extension/<name>/icons/`, images with exactly one extension),
`var/*/storage/images`, stored image originals, stored PDF exports, the public and texttoimage caches, package styles,
`var/storage/packages/`, `favicon.ico`, `robots.txt`, `index.js`, `sw.js` and `w3c/p3p.xml`. `NEVER_STATIC` is
`(?i)(\.(php\d?|phtml|phar)$|/\.)`: no PHP source and no dot file is ever served as a file, even below an asset
directory. Both constants live in `kernel/classes/expvelocity.php`.

## HTTPS

The README notes Caddy's "automatic HTTPS out of the box". In the FrankenPHP integration that is switched off
(`auto_https off`): Velocity's settings decide the certificate, either `[HTTPSSettings] Certificate` and `Key` or a
self-signed certificate made for the machine. For a stand-alone Caddy with PHP-FPM, certificate handling is Caddy's
own; follow its documentation. Whoever terminates TLS, Exponential must learn that the request was HTTPS: directly
(`HTTPS` passed to PHP) or from a trusted proxy ([Varnish and reverse proxies](../performance/varnish-and-reverse-proxies.md)).

## Checklist

- [ ] Decided between FrankenPHP (generated configuration) and Caddy with PHP-FPM (your own configuration).
- [ ] For PHP-FPM: the routing table above reproduced; nothing outside the asset list is served as a file.
- [ ] The four routing checks of [Web servers and engines](README.md#what-every-setup-has-in-common) pass.
- [ ] The setup was tested on staging before it was sold (tier 3).
- [ ] The upgrade checklist includes comparing your rules with `.htaccess_root`.

## Related pages

- [FrankenPHP](frankenphp.md)
- [nginx with PHP-FPM](nginx-with-php-fpm.md)
- [Choosing a web server](choosing-a-web-server.md)
- [Velocity engines](../../../bc/6.0/velocity-engines.md)
- [Setting up .htaccess and config.php from the console](../../../guides/setup-files-from-the-console.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
