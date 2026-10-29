---
title: Enable the static cache
audience: government, admin, hosting
category: public-sector
order: 290
summary: Step by step - generate static copies of public pages that the web server sends without starting PHP, check the result, switch StaticCache on, and keep it fresh, for sites that must stay fast under heavy public demand.
keywords: static cache, makestaticcache, StaticCache, cache.php static, public pages, performance, high demand, web server, var/site/static
type: help
---
# Enable the static cache

This task is for administrators of public sites that must cope with sudden demand (an announcement, an emergency).
The static cache stores public pages as plain files that the web server sends without starting Exponential. It is off
in a new installation ([Operating a site, section 4](../../../../guides/operating-a-site.md#4-turn-on-the-static-cache)).

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

## Contents
1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check it worked](#check-it-worked)
4. [Pitfalls](#pitfalls)
5. [Related pages](#related-pages)

## Before you start

- Only siteaccesses that need no login are offered, because their pages are the same for everybody.
- The web server rules of your installation that serve the static files are active (Apache with the shipped
  `.htaccess`); under Velocity the response cache is the usual alternative
  ([Velocity response cache and HTTP cache](../../operations/availability/velocity-response-cache-and-http-cache.md)).
- A change approved for a settings change.

## Steps

1. See what it would do:

   ```bash
   php bin/php/cache.php static status --allow-root-user
   ```

2. Generate pages of one site, limited first so you see it work:

   ```bash
   php bin/php/makestaticcache.php --site=site --max-pages=500 --max-depth=6 --allow-root-user
   ```

   Omit `--site` for every public siteaccess. In the browser: **Setup > Cache management**, section *Static content
   cache*, with a **Stop** button.
3. Switch it on in `settings/override/site.ini.append.php`:

   ```ini
   [ContentSettings]
   StaticCache=enabled
   ```

4. `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`, then reload or deploy.

## Check it worked

- `php bin/php/cache.php static status --allow-root-user` counts the files per site in `var/site/static`.
- Publish a change and check the page updates (publishing clears its static pages).

## Pitfalls

- Pages that show something personal (a signed-in user's name, a form with a token) must not be static.
- Remove the files with `php bin/php/cache.php static clear --allow-root-user` when you switch it off again.

## Related pages
- [Static cache](../../operations/availability/static-cache.md)
- [Static cache generator](../../../../features/6.0/static-cache-generator.md)
- [Static cache (hosting)](../../../hosting/performance/static-cache.md)
- [DDoS and probe floods](../../operations/incidents/ddos-and-probe-floods.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
