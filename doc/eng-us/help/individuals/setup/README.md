---
title: Setup for individuals
audience: individual, website-creator
category: personal-sites
order: 1630
summary: The setup decisions one person makes for a personal site, portfolio, blog, family site or club site on Exponential, in order - install method, site package and theme, a small budget, shared hosting or a small server, and the domain name and HTTPS that are arranged outside Exponential.
keywords: setup, install, install method, setup wizard, exp:install, kickstarter, site package, theme, budget, SQLite, Velocity, shared hosting, VPS, domain, DNS, HTTPS, certificate
type: help
---
# Setup for individuals

This section is for individuals who are about to install Exponential for a site of their own. It puts the decisions
in the order you meet them and links to the installation book for the detail
([Installation book](../../../install/README.md)).

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

## Contents

1. [The decisions in order](#the-decisions-in-order)
2. [Three common setups](#three-common-setups)
3. [What you need before you start](#what-you-need-before-you-start)

## The decisions in order

| # | Decision | Page |
|---|---|---|
| 1 | How to install: the browser wizard, the console, or the kickstarter | [Choosing an install method](choosing-an-install-method.md) |
| 2 | Which site package and theme to start from | [Choosing a site package and theme](choosing-a-site-package-and-theme.md) |
| 3 | How to keep costs and moving parts low | [Running on a small budget](running-on-a-small-budget.md) |
| 4 | Where the site runs: shared hosting, a small server, your own machine | [Shared hosting or a small server](shared-hosting-or-a-small-server.md) |
| 5 | The address and the lock: domain name, DNS and HTTPS | [Domain names and HTTPS](domain-names-and-https.md) |

## Three common setups

| Setup | Install with | Database | Served by | Suits |
|---|---|---|---|---|
| **Try it on your own computer** | `exp:install` with its defaults | SQLite | PHP's built-in server or Velocity | Learning, building before going live |
| **Shared hosting** | The setup wizard in the browser | SQLite, or the panel's database | Apache or LiteSpeed with `.htaccess` | A low monthly cost, no server to look after |
| **A small virtual server** | `exp:install` or the kickstarter | SQLite or PostgreSQL | Velocity, or Apache or nginx with PHP-FPM | Full control, Velocity's speed and certificates |

Sources: [Choosing an install method](../../creators/website-creators/choosing-an-install-method.md),
[Serving the site](../../../install/08-serving-the-site.md), [Shared hosting](../../hosting/models/shared-hosting.md).

## What you need before you start

- [ ] PHP 8.0 or later (PHP 7.4 usable without support) with the required extensions, or a host that offers it
  ([Requirements at a glance](../../hosting/basics/requirements-at-a-glance.md)).
- [ ] For a console install: a shell and Composer. For the wizard: a browser and FTP.
- [ ] A real mail address for the administrator.
- [ ] The [site plan worksheet](../checklists/site-plan-worksheet.md) filled in.
- [ ] A few hours without interruptions.

## Related pages

- [Individuals: start here](../individuals-start-here.md)
- [Choosing an install method (website creators)](../../creators/website-creators/choosing-an-install-method.md)
- [Hosting for a website](../../creators/website-creators/hosting-for-a-website.md)
- [Getting started](../../getting-started.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
