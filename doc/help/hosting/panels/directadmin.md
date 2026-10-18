---
title: DirectAdmin
audience: hosting
category: hosting
order: 40
summary: What the Exponential sources say about DirectAdmin hosts (tier 3, expected to work, in the same row as cPanel), the Exponential requirements translated into generic panel tasks, the questions to answer about your DirectAdmin servers, and a test plan.
keywords: DirectAdmin, tier 3, shared hosting, per-account PHP, Apache, LiteSpeed, nginx, .htaccess, setup wizard, Composer, cron, Velocity, open_basedir, test plan
type: help
---
# DirectAdmin

This page is for hosting companies that run DirectAdmin servers and want to offer Exponential on them. It answers what
the Exponential sources say about DirectAdmin, which panel tasks an Exponential site needs, and how to test the
combination before you sell it.

> **Note**
> The Exponential sources describe no DirectAdmin screens, menus or file locations. This page refers to panel features
> generically; check DirectAdmin's documentation for how your version does each task.

## Contents

1. [What the sources say](#what-the-sources-say)
2. [Questions about your DirectAdmin servers](#questions-about-your-directadmin-servers)
3. [The panel tasks for an Exponential site](#the-panel-tasks-for-an-exponential-site)
4. [Web server choices on the server](#web-server-choices-on-the-server)
5. [A test plan](#a-test-plan)
6. [Checklist](#checklist)

## What the sources say

DirectAdmin appears in exactly two places in the sources, both in
[Operating system support](../../os-support.md):

- In the tier 3 table, in one row shared with cPanel: "EasyApache (EA-PHP 8.x), per-account PHP" as the reason the
  row should work, and "shared hosting may forbid long-running processes, so Velocity may not be allowed; use Apache
  or LiteSpeed with `.htaccess`" as what to watch. The point that applies to DirectAdmin is a per-account PHP 8.x
  ([Tier 3](../../os-support.md#tier-3-expected-to-work)).
- In "Containers, cloud and hosting panels": "cPanel and DirectAdmin are tier 3; on shared hosting without shell
  access, the setup wizard in the browser is the installer"
  ([Containers, cloud and hosting panels](../../os-support.md#containers-cloud-and-hosting-panels)).

Tier 3 is "a reasoned expectation, not a test result". Nobody in the project has watched Exponential run on
DirectAdmin.

## Questions about your DirectAdmin servers

Answer these for your own server build before writing a product description:

| Question | Why it matters for Exponential |
|---|---|
| Which PHP 8.x versions can an account choose, and are `intl` and `xsl` built in? | both are critical tests of the setup wizard ([2.4](../../../install/02-requirements.md#24-the-setup-wizards-system-check)) |
| Does PHP run as the account's own user (PHP-FPM per account or equivalent)? | the one-user rule for `var/` and isolation between accounts ([8.7.2](../../../install/08-serving-the-site.md#872-one-user-or-one-group)) |
| Which web server serves the accounts, and does it read `.htaccess`? | the rewrite rules ship as `.htaccess_root` |
| Is `open_basedir` set per account? | the wizard only warns, but tools that look for system binaries are affected ([ezupdate](../../../features/6.0/extensions/ezupdate.md)) |
| Which functions are in `disable_functions`? | the cronjob page needs `proc_open`, `exec` and `posix_kill` ([Cronjobs](../../../guides/cronjobs.md#2-before-you-start)) |
| Do accounts get a shell? | Composer and the console need one; without it the setup wizard installs |
| Which database servers can accounts create databases on? | the official order and its practical minimums |

## The panel tasks for an Exponential site

| Task | What Exponential needs | Details |
|---|---|---|
| PHP version | 8.0 or later for web and command line, the same extensions in both | [PHP selection and FPM pools](php-selection-and-fpm-pools.md) |
| PHP settings | `memory_limit` 64M or more, `max_execution_time` 30 or more, `date.timezone`, `file_uploads`, `allow_url_fopen`; upload sizes for the editors' files | [2.5](../../../install/02-requirements.md#25-phpini-settings) |
| Document root | the installation root, where `index.php` is | [8.5.1](../../../install/08-serving-the-site.md#851-step-by-step) |
| Rewrite rules | `.htaccess` copied from `.htaccess_root` | [13.2](../../../install/13-security-hardening.md#132-what-the-web-server-must-never-hand-out) |
| Database | in the official order: 1. SQLite 3 (no server) · 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL; Microsoft SQL Server is known to be possible with additional work at this time | [Databases and users](databases-and-users.md) |
| Cron | `runcronjobs.php` with the account's PHP, every few minutes for `frequent` | [Cron and shell access](cron-and-shell-access.md) |
| Certificate | HTTPS for every name of the site, redirect from HTTP | [TLS certificates in panels](tls-certificates-in-panels.md) |

## Web server choices on the server

The sources do not say which web server a DirectAdmin server runs. What they say about the candidates:

| Server | Status in the sources |
|---|---|
| Apache httpd 2.4 with PHP-FPM | tier 1; rules in `.htaccess_root` |
| nginx with PHP-FPM | tier 2; the server block is "derived, not shipped" ([8.6](../../../install/08-serving-the-site.md#86-nginx-with-php-fpm)) |
| LiteSpeed, OpenLiteSpeed | tier 3; "reads Apache's rewrite rules in most cases" |
| nginx in front of Apache | Apache reads `.htaccess`; set the trusted-proxy settings as for any proxy in front ([13.10](../../../install/13-security-hardening.md#1310-behind-a-proxy-trusted-headers)) |

Source: [Web servers](../../os-support.md#web-servers). If accounts are served by nginx alone, an account cannot use
`.htaccess`; the derived server block must then be part of the server configuration you manage.

## A test plan

| Step | Pass when |
|---|---|
| Fresh account, setup wizard | every critical test passes; site and `/admin` answer |
| `/settings/site.ini`, `/.env`, `/composer.json` from outside | 404 or an Exponential page, never the file |
| Upload an image | image and variations served |
| Cron line from the panel | the run appears on **Setup > Cronjobs** |
| Backup and restore into a second account | restored site works |
| Two accounts on one server | neither account's PHP can read the other's `settings/override` |

Report a successful run to the project so the tier can move up ([The four tiers](../../os-support.md#the-four-tiers)).

## Checklist

- [ ] The questions above answered for your server build and written down.
- [ ] PHP 8.x with every required extension; one PHP user per account.
- [ ] Document root, `.htaccess` and database ready.
- [ ] Cron and certificate set up.
- [ ] The test plan passed, including the isolation test.

## Related pages

- [Control panels: overview](README.md), [cPanel](cpanel.md)
- [Shared hosting](../models/shared-hosting.md), [Multi-tenant isolation](../security/multi-tenant-isolation.md)
- [Operating system support](../../os-support.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
