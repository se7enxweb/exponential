---
title: Web servers and engines in the ecosystem
audience: ecosystem, admin, hosting, developer
category: ecosystem
order: 33
summary: The ways an Exponential site is served - Velocity with its Qbix, FrankenPHP and PHP built-in engines, and the traditional servers Apache, Nginx, LiteSpeed, Caddy, IIS, lighttpd, H2O and Traefik - with what each is for.
keywords: web servers, engines, Velocity, Qbix, FrankenPHP, PHP built-in server, Apache, Nginx, LiteSpeed, Caddy, IIS, lighttpd, H2O, Traefik, PHP-FPM, rewrite
type: help
---
# Web servers and engines in the ecosystem

"Exponential runs behind any web server that can rewrite a URL" and "does not require any particular one"
(`README.md`, "How Exponential is served").

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

1. [Velocity and its engines](#velocity-and-its-engines)
2. [Traditional servers](#traditional-servers)
3. [Choosing](#choosing)

## Velocity and its engines

| Engine | Role |
|---|---|
| Qbix | Velocity's own server; "The recommended engine for every stage, from development to production, and the fastest" |
| FrankenPHP | "The production-ready alternative" |
| PHP's built-in server | For development; "the shipped default because it always works" |

More: [Exponential Velocity](../products/exponential-velocity.md), [Velocity engines](../../../bc/6.0/velocity-engines.md).

## Traditional servers

| Server | How it rewrites | Notes from the README |
|---|---|---|
| Apache HTTP Server | `mod_rewrite`, `.htaccess` | "The most widely used; `.htaccess` ships with Exponential" |
| Nginx | `try_files` / `rewrite` | Rules in the server block |
| LiteSpeed, OpenLiteSpeed | `.htaccess`-compatible | Reads Apache rules in most cases |
| Caddy | `rewrite` / `handle` / `try_files` | Automatic HTTPS |
| Microsoft IIS | URL Rewrite Module (`web.config`) | Windows hosting |
| lighttpd | `mod_rewrite` | Small footprint |
| H2O | `redirect` / `file.custom-handler` | HTTP/2 focused |
| Traefik | `ReplacePathRegex` | Usually in front of another server |

PHP's built-in server with `php -S` is "Development only [...] Never expose it to the internet" (`README.md`).

## Choosing

| Situation | Choice |
|---|---|
| Your own server or VM | Velocity with the Qbix engine |
| You prefer a Caddy-based single binary | Velocity with FrankenPHP |
| Shared hosting without long-lived processes | Apache or LiteSpeed with `.htaccess` |
| Several customers needing operating-system isolation | One Velocity instance per site, or per-site PHP-FPM pools |
| A tuned Nginx or Apache already in front | Keep it; Velocity can run behind it |

Source: `README.md`, "When a traditional server is still the right answer". Hosting models:
[Hosting companies: start here](../../hosting/hosting-companies-start-here.md).

## Related pages

- [The PHP stack](README.md)
- [Serving the site](../../../install/08-serving-the-site.md)
- [Velocity web server](../../../features/6.0/velocity-web-server.md)
- [Web server and Solr commands](../../../features/6.0/web-server-and-solr-commands.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
