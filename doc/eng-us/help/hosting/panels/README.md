---
title: Control panels: overview
audience: hosting
category: hosting
order: 10
summary: The overview of the control panels section: which hosting panels the Exponential sources mention and at which tier (Plesk tier 1, cPanel and DirectAdmin tier 3, ISPConfig and Webmin not mentioned), what every panel must provide for an Exponential site, and one line on each page.
keywords: control panel, hosting panel, Plesk, cPanel, DirectAdmin, ISPConfig, Webmin, tier, PHP selection, PHP-FPM pool, cron, shell access, databases, TLS certificates, overview
type: help
---
# Control panels: overview

This section is for hosting companies that run Exponential sites on servers managed by a hosting control panel, and
for their support staff. It answers which panels the project's sources know, how far each is verified, and how the
everyday panel tasks (choosing PHP, setting up cron, creating databases, installing certificates) apply to an
Exponential site.

Back to [Hosting companies: start here](../hosting-companies-start-here.md).

> **Note**
> This section states only what the Exponential sources say about each panel. For anything inside a panel that the
> sources do not describe (menu names, screens, options), check your panel's documentation. Where these pages give
> operational advice that the sources do not cover, they say so.

## Contents

1. [The pages of this section](#the-pages-of-this-section)
2. [Panels in the sources](#panels-in-the-sources)
3. [What every panel must provide](#what-every-panel-must-provide)
4. [Velocity on a panel server](#velocity-on-a-panel-server)

## The pages of this section

| Page | What it answers |
|---|---|
| [Plesk](plesk.md) | The panel of the project's own development server: PHP paths, per-domain pools, the `plesk-php<XY>-fpm` services, certificates, logs |
| [cPanel](cpanel.md) | What the sources say (tier 3, EasyApache PHP), and how to approach it |
| [DirectAdmin](directadmin.md) | What the sources say (tier 3), and how to approach it |
| [ISPConfig](ispconfig.md) | Not mentioned in the sources; the generic requirements |
| [Webmin](webmin.md) | Not mentioned in the sources; the generic requirements |
| [PHP selection and FPM pools](php-selection-and-fpm-pools.md) | The site's PHP version for web and command line, one pool per site, reloading the right service |
| [Cron and shell access](cron-and-shell-access.md) | Scheduling `runcronjobs.php` with the right PHP, and what works without a shell |
| [Databases and users](databases-and-users.md) | Creating the database and its user for each engine, in the official order |
| [TLS certificates in panels](tls-certificates-in-panels.md) | Certificates from the panel, and when Velocity or FrankenPHP serves HTTPS instead |

## Panels in the sources

| Panel | Tier in the sources | What the sources say |
|---|---|---|
| Plesk | **1** (tested here) on AlmaLinux 9; Plesk hosts on the tier 2 distributions are tier 2 | "The project's development server: Apache 2.4 with PHP-FPM 8.5 from the Plesk panel, and Exponential Velocity on the same installation" |
| cPanel | 3 (expected to work) | "EasyApache (EA-PHP 8.x), per-account PHP"; "shared hosting may forbid long-running processes, so Velocity may not be allowed; use Apache or LiteSpeed with `.htaccess`" |
| DirectAdmin | 3 (expected to work) | the same row as cPanel |
| ISPConfig | not placed | not mentioned in the sources |
| Webmin | not placed | not mentioned in the sources |

Source: [Operating system support](../../os-support.md) (tiers 1 to 3 and "Containers, cloud and hosting panels").
Tier 3 is "a reasoned expectation, not a test result"; a panel that is not placed has no statement at all. Plan a
test installation before you offer either.

## What every panel must provide

Whatever the panel, an Exponential site needs ([Chapter 2](../../../install/02-requirements.md)):

| Need | In panel terms |
|---|---|
| PHP 8.0 or later (for Velocity too) with the required extensions | the panel's PHP selector and extension switches, for the site and for its command line |
| `php.ini`: `memory_limit` 64M or more, `max_execution_time` 30 or more, `date.timezone`, `file_uploads`, `allow_url_fopen` | the panel's per-site PHP settings |
| Rewrite rules: only listed asset paths served as files, everything else to `index.php` | `.htaccess` from `.htaccess_root` under Apache or LiteSpeed; for nginx the derived server block ([8.6](../../../install/08-serving-the-site.md#86-nginx-with-php-fpm)) |
| The document root at the installation root | the panel's document root setting for the domain |
| One database of the six, in the official order: 1. SQLite 3 (the default, no server) · 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL; Microsoft SQL Server is known to be possible with additional work at this time | the panel's database page, or nothing for SQLite |
| Writable `var/` and `settings/` for the site's user | the account's own user |
| Scheduled `runcronjobs.php` | the panel's cron page |
| HTTPS | the panel's certificate management |

## Velocity on a panel server

Panels manage Apache or nginx and their PHP-FPM pools. Velocity is a separate long-running process, so on a panel
server it runs **beside** the panel's web server, as on the project's development server, where Plesk's Apache with
PHP-FPM and Velocity serve the same installation ([Tier 1](../../os-support.md#tier-1-tested-here)). Then:

- both must write `var/` as the same user ([8.7.2](../../../install/08-serving-the-site.md#872-one-user-or-one-group));
- a deploy must reload the right PHP-FPM and restart Velocity; `exp:velocity deploy` does both in order
  ([8.3.14](../../../install/08-serving-the-site.md#8314-deploying-a-change-expvelocity-deploy));
- the panel's firewall must allow Velocity's ports if visitors reach it directly.

See [Velocity beside Apache or nginx](../servers/velocity-beside-apache-or-nginx.md).

## Related pages

- [Hosting companies: start here](../hosting-companies-start-here.md)
- [Shared hosting](../models/shared-hosting.md)
- [Operating system support](../../os-support.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
