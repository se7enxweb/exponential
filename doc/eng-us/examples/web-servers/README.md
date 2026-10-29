---
title: Web server examples
audience: admin, developer
category: examples
order: 10
summary: Complete, checked configurations for serving Exponential 6 with Velocity, Apache, nginx, Caddy, FrankenPHP, LiteSpeed, IIS and PHP-FPM, each with the same routing rules as the shipped .htaccess_root.
keywords: web server, Apache, .htaccess, virtual host, nginx, Caddy, FrankenPHP, LiteSpeed, OpenLiteSpeed, IIS, web.config, PHP-FPM, Velocity, HTTPS, rewrite rules, configuration, example
type: example
---
# Web server examples

Exponential is a directory of PHP files. Whatever serves it, the document root is the **installation root** (the
directory with `index.php`) and the same routing applies: a short list of asset paths is sent as files, three front
controllers run, and **every other request is answered by `index.php`**, which never hands out a file. That is what
keeps `settings/`, `var/`, `vendor/`, `kernel/`, `composer.json` and the SQLite database safe although they lie inside
the document root. The list is written down once, in `.htaccess_root`, and Velocity implements it rule for rule
(`expVelocity::STATIC_PATHS`, `FRONT_CONTROLLERS` and `ENTRY_SCRIPTS` in `kernel/classes/expvelocity.php`).

Every example on these pages is a complete file you can copy, with `example.com` and `/var/www/example.com` to
replace. Each one implements the same list, in this order:

1. a dot file or dot directory (`.git`, `.env`, `.htaccess`) answers 404 anywhere; `.well-known` is exempt;
2. an existing `.php`, `.phtml` or `.phar` below the root answers 404: no script runs because its path was asked for;
3. `/api/...` and `/index_rest.php` go to `index_rest.php`, `/content/treemenu...` (with or without a siteaccess in
   front) to `index_treemenu.php`;
4. the asset list is sent as files: design and extension design directories, `share/icons`, the icon
   themes of extensions (`extension/<name>/icons/`: SVG, PNG and GIF images with exactly one extension), stored
   images, the image originals, stored PDF exports, the public caches, package previews, `favicon.ico`, `robots.txt`,
   `index.js`, `sw.js` and `w3c/p3p.xml`;
5. everything else goes to `index.php`.

## Which one

| Server | Page | Raw file to copy | How it was checked |
|---|---|---|---|
| **Velocity** (recommended) | [Velocity](velocity.md) | `velocity.ini.append.php.example`, `exponential-velocity.service` | every setting exists in `settings/velocity.ini`; `systemd-analyze verify` |
| Apache, `.htaccess` | [Apache .htaccess](apache-htaccess.md) | `../htaccess` (= `.htaccess_root` + optional blocks) | `httpd -t`; a throwaway Apache answered every test request as expected |
| Apache, virtual host | [Apache virtual host](apache-virtual-host.md) | `../ezpublish.conf` | `httpd -t`; a throwaway Apache, HTTP and HTTPS, answered every test request as expected |
| nginx | [nginx](nginx.md) | `nginx.conf` | `nginx -t`; a throwaway nginx, HTTP and HTTPS, answered every test request as expected |
| Caddy | [Caddy](caddy.md) | `Caddyfile.example` | `frankenphp validate` (Caddy 2.11 inside FrankenPHP 1.12.7) |
| FrankenPHP | [FrankenPHP](frankenphp.md) | `frankenphp.Caddyfile.example` | `frankenphp validate` |
| LiteSpeed, OpenLiteSpeed | [LiteSpeed](litespeed.md) | `openlitespeed-vhost.conf` | not run on LiteSpeed; its rewrite rules answered every test request as expected in Apache's `mod_rewrite` |
| IIS | [IIS](iis.md) | `web.config` | well-formed XML (`xmllint`) only; not run on IIS |
| PHP-FPM (behind Apache, nginx, Caddy) | [PHP-FPM pool](php-fpm.md) | `php-fpm-pool.conf` | `php-fpm -t` |

The test requests: `/.git/config`, `/.env`, scripts below the root (in `extension/`, `kernel/`, `vendor/`, the package
store, an asset directory) and a dot file in an asset directory must answer 404 without their content;
`/settings/site.ini`, `/composer.json`, `/config.php`, `/var/log/error.log` and a stored non-image original must reach
`index.php` and never be sent; `/api/...` must reach `index_rest.php` and `/content/treemenu/...` `index_treemenu.php`;
each kind of listed asset must be sent (200), and an uploaded SVG must carry the `sandbox` content security policy.
Run the same requests against your own server with the commands in
[chapter 13, section 13.2](../../install/13-security-hardening.md#132-what-the-web-server-must-never-hand-out).

## Two things every setup shares

- **Never use a generic "serve the file if it exists, else `index.php`" configuration** (nginx `try_files $uri
  /index.php`, Caddy's `php_fastcgi` or `php_server`): it hands out every file that exists, `settings/*.ini`
  included.
- **`.well-known` is reachable but not on the asset list.** The dot rule leaves it alone, then the request goes to
  `index.php` like any other. A certificate client that places challenge files there (`certbot --webroot`) needs the
  plain-HTTP host to serve them, as the port 80 hosts of the Apache and nginx examples do; Caddy, FrankenPHP and
  Velocity's `qbix` engine answer ACME challenges themselves.

**Which servers read `.htaccess`.** Apache (with `AllowOverride All`) and LiteSpeed Enterprise read it on the next
request: there, `./console exp:setup:htaccess --apply` copies `.htaccess_root` into place (`--static` copies
`.htaccess_root_static`) and nothing else is needed. OpenLiteSpeed reads only its rewrite rules, only with
`autoLoadHtaccess` on and only after a restart. nginx, Caddy, FrankenPHP and IIS never read it: their rules are in the
configurations on these pages. Velocity has the rules of `.htaccess_root` built in; copy the file anyway when Apache may
serve the same tree. See [Setting up .htaccess and config.php from the console](../../guides/setup-files-from-the-console.md).

**WebDAV** is not routed by any of these examples: it is off by default. Since 6.0.15 its entry script is
`index_webdav.php` (`webdav.php` only forwards to it until a later major version). To serve it, add a rule for
`index_webdav.php` on Apache, nginx or IIS, or a WebDAV host of its own; Velocity, Caddy and FrankenPHP do not run it.
Before/after rules for every server, and how to test them:
[The WebDAV entry script is now index_webdav.php](../../bc/6.0/webdav-entry-script-index-webdav.md).

## Also in this directory

- [`../staticcache_offload.conf`](../staticcache_offload.conf): Apache rules that serve a page generated by the static
  content cache without starting PHP (`.htaccess_root_static` is `.htaccess_root` with them already in place).
- [`../README.md`](../README.md): every example of the documentation.

**Public demos.** The settings that switch demo mode on for each of these servers (`EXP_DEMO_MODE=1`) are in [Demo mode examples](../demo-mode/README.md).

## Related pages

- [Chapter 8, Serving the site](../../install/08-serving-the-site.md): Velocity, Apache, nginx and FrankenPHP step by step.
- [Chapter 13, Security hardening](../../install/13-security-hardening.md): what the web server must never hand out, headers, HTTPS.
- [Velocity engines](../../bc/6.0/velocity-engines.md) and [FrankenPHP](../../bc/6.0/frankenphp.md).
- [Setting up .htaccess and config.php from the console](../../guides/setup-files-from-the-console.md): `exp:setup:htaccess`, `exp:setup:config`, `exp:config:*`.
- [Free documentation](../../help/free-documentation.md): this page is free to copy, translate and improve.
