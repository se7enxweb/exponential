---
title: Web server and engine choices for bundles
audience: bundler, hosting
category: bundling
order: 260
summary: Which web servers and engines can serve a bundled Exponential - Velocity (recommended), FrankenPHP, Apache, nginx, Caddy, LiteSpeed, IIS with PHP-FPM - the routing rules every one of them must implement, the checked example files to start from, and how to choose for each kind of bundle.
keywords: web server, engine, Velocity, qbix, FrankenPHP, Apache, nginx, Caddy, LiteSpeed, IIS, PHP-FPM, .htaccess_root, routing, document root, example configuration, bundle
type: help
---
# Web server and engine choices for bundles

This page is for bundlers who decide what serves the site inside their bundle. It describes only what the project's
documentation and examples show; the examples are in `doc/eng-us/examples/web-servers/`.

## Contents

1. [The rule every server follows](#the-rule-every-server-follows)
2. [The choices](#the-choices)
3. [Velocity's engines](#velocitys-engines)
4. [Which one for which bundle](#which-one-for-which-bundle)
5. [Before you ship a server configuration](#before-you-ship-a-server-configuration)

## The rule every server follows

"Whatever serves it, the document root is the **installation root** (the directory with `index.php`) and the same
routing applies: a short list of asset paths is sent as files, three front controllers run, and **every other request
is answered by `index.php`**, which never hands out a file. That is what keeps `settings/`, `var/`, `vendor/`, `kernel/`,
`composer.json` and the SQLite database safe although they lie inside the document root"
([Web server examples](../../examples/web-servers/README.md)).

The list is written once in `.htaccess_root`, and Velocity implements it rule for rule. A bundle that writes its own
web server configuration must implement the same list, in the same order; the examples do.

## The choices

| Server | Example to start from | How the example was checked |
|---|---|---|
| **Velocity** (recommended) | [Velocity](../../examples/web-servers/velocity.md), `velocity.ini.append.php.example`, `exponential-velocity.service` | every setting exists in `settings/velocity.ini`; `systemd-analyze verify` |
| Apache, `.htaccess` | [Apache .htaccess](../../examples/web-servers/apache-htaccess.md) | `httpd -t` and test requests |
| Apache, virtual host | [Apache virtual host](../../examples/web-servers/apache-virtual-host.md) | `httpd -t`, HTTP and HTTPS test requests |
| nginx | [nginx](../../examples/web-servers/nginx.md), `nginx.conf` | `nginx -t`, test requests |
| Caddy | [Caddy](../../examples/web-servers/caddy.md), `Caddyfile.example` | `frankenphp validate` |
| FrankenPHP | [FrankenPHP](../../examples/web-servers/frankenphp.md), `frankenphp.Caddyfile.example` | `frankenphp validate` |
| LiteSpeed, OpenLiteSpeed | [LiteSpeed](../../examples/web-servers/litespeed.md) | rules tested in Apache's `mod_rewrite`, not on LiteSpeed |
| IIS | [IIS](../../examples/web-servers/iis.md), `web.config` | well-formed XML only, not run on IIS |
| PHP-FPM behind any of them | [PHP-FPM pool](../../examples/web-servers/php-fpm.md) | `php-fpm -t` |

Source: the table on [Web server examples](../../examples/web-servers/README.md#which-one). Note the last two rows:
a bundle on LiteSpeed or IIS must do its own testing.

## Velocity's engines

`settings/velocity.ini [ServerSettings] Engine` chooses Velocity's default engine
([Velocity engines](../../bc/6.0/velocity-engines.md#in-short)):

| Engine | Role | Notes |
|---|---|---|
| `qbix` | recommended: every stage, development to production | persistent workers, response cache, TLS, HTTP/2 |
| `frankenphp` | production-ready alternative | one downloaded binary, SHA-256 checked; PHP in classic mode; no response cache yet |
| `php` | development (the shipped default) | PHP's built-in server; plain HTTP |

"The shipped default engine stays `php`"; "To use the server, set `Engine=qbix`". A bundle that serves with Velocity
sets the engine on purpose ([Velocity in a bundle](velocity-in-a-bundle.md)).

## Which one for which bundle

| Bundle | Server |
|---|---|
| Container image | Velocity in the container's foreground loop ([Container images](container-images.md)), or your platform's PHP-FPM image behind a proxy |
| Server image | Velocity with a systemd unit, or Apache or nginx with PHP-FPM |
| Panel installer | whatever the panel runs (Apache or nginx with PHP-FPM, LiteSpeed); install the matching rules ([Hosting panel installers](hosting-panel-installers.md)) |
| Appliance | Velocity: one product from request to page, with its own TLS |
| Demo or classroom | Velocity, or `php` for a laptop |

The hosting section compares them in more depth: [Choosing a web server](../hosting/servers/choosing-a-web-server.md).

## Before you ship a server configuration

- [ ] It routes exactly as `.htaccess_root` does; the test requests of the examples page (`/.git/config`, `/.env`,
      scripts below the root) return 404 in your bundle.
- [ ] The document root is the installation root.
- [ ] HTTPS is configured or clearly left to the recipient.
- [ ] The PHP that serves the site has the extensions `exp:velocity ext check` asks for.
- [ ] Trusted proxy settings match the bundle's network ([13.10](../../install/13-security-hardening.md#1310-behind-a-proxy-trusted-headers)).

## Related pages

- [Chapter 8: Serving the site](../../install/08-serving-the-site.md)
- [Velocity in a bundle](velocity-in-a-bundle.md)
- [Velocity for hosts](../hosting/servers/velocity-for-hosts.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
