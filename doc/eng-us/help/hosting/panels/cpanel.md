---
title: cPanel
audience: hosting
category: hosting
order: 30
summary: What the Exponential sources say about cPanel hosts (tier 3, expected to work; EasyApache PHP per account; Velocity may not be allowed) and a hedged, generic approach to setting up an Exponential account there, with a test plan to turn the expectation into evidence.
keywords: cPanel, EasyApache, EA-PHP, per-account PHP, tier 3, shared hosting, Apache, LiteSpeed, .htaccess, setup wizard, Composer, /opt/cpanel/composer/bin, ezupdate, cron, Velocity, test plan
type: help
---
# cPanel

This page is for hosting companies that run cPanel servers and want to offer Exponential on them. It answers what the
Exponential sources say about cPanel, what that means in practice, and how to test it before you sell it. The project
has not watched Exponential run on cPanel; this page is therefore careful to separate what the sources state from
general advice.

> **Note**
> The Exponential sources describe no cPanel screens or menus. Where this page refers to a panel feature, it does so
> generically ("the panel's PHP selector", "the panel's cron page"); check cPanel's documentation for the names and
> steps in your version.

## Contents

1. [What the sources say](#what-the-sources-say)
2. [What it means in practice](#what-it-means-in-practice)
3. [Setting up an account (generic)](#setting-up-an-account-generic)
4. [Composer on cPanel](#composer-on-cpanel)
5. [Velocity on cPanel](#velocity-on-cpanel)
6. [A test plan](#a-test-plan)
7. [Checklist](#checklist)

## What the sources say

| Statement | Source |
|---|---|
| cPanel hosts are **tier 3**, expected to work: "a reasoned expectation, not a test result" | [Tier 3](../../os-support.md#tier-3-expected-to-work) |
| "EasyApache (EA-PHP 8.x), per-account PHP" | the same table |
| "shared hosting may forbid long-running processes, so Velocity may not be allowed; use Apache or LiteSpeed with `.htaccess`" | the same table |
| "on shared hosting without shell access, the setup wizard in the browser is the installer" | [Containers, cloud and hosting panels](../../os-support.md#containers-cloud-and-hosting-panels) |
| ezupdate looks for Composer in `/opt/cpanel/composer/bin/`, among other places | [ezupdate settings](../../../features/6.0/extensions/ezupdate.md) |

That is everything the sources say about cPanel specifically.

## What it means in practice

Exponential's needs do not depend on the panel. A cPanel account that offers the following runs Exponential, by the
same reasoning that puts cPanel in tier 3:

- PHP 7.4 or later for the account, with the required extensions: `dom`, `libxml`, `simplexml`, `xsl`, `mbstring`,
  `iconv`, `intl`, `pcre`, `json`, `session`, `zlib`, one database extension, `gd` or ImageMagick
  ([2.3 PHP extensions](../../../install/02-requirements.md#23-php-extensions)). `xsl` and `intl` are critical tests
  of the setup wizard, and are the ones most often missing from a default PHP build.
- The `php.ini` values the wizard checks ([2.5](../../../install/02-requirements.md#25-phpini-settings)).
- Apache (or LiteSpeed) reading `.htaccess`, with `mod_rewrite`.
- A database: 1. SQLite 3 needs none; otherwise one of the servers in the official order, 2. PostgreSQL ·
  3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL (Microsoft SQL Server is known to be possible with additional work
  at this time). On a typical cPanel server this is the MariaDB or MySQL server the panel manages.
- A cron job for `runcronjobs.php`.

## Setting up an account (generic)

The following is general advice derived from the Exponential requirements, not from cPanel documentation:

1. **Choose PHP 8.x for the account** in the panel's PHP selector, and enable the required extensions in the panel's
   extension settings. Use the same version for the command line if the account has a shell.
2. **Point the domain's document root at the installation root** (the directory with `index.php`).
3. **Copy the rewrite rules**: `cp .htaccess_root .htaccess` (or upload `.htaccess_root` as `.htaccess`)
   ([8.5.1](../../../install/08-serving-the-site.md#851-step-by-step)).
4. **Create the database and its user** in the panel, if not SQLite. For MariaDB or MySQL use `utf8mb4`
   ([9.3](../../../install/09-databases.md#93-mysql-and-mariadb)). Since 6.0.15 the wizard needs privileges on that
   database only ([A clean installation that fits shared hosting](../../../features/6.0/clean-install-defaults.md)).
5. **Upload the code with `vendor/`**, or run Composer in a shell if there is one (next section).
6. **Open the setup wizard** in the browser and follow its system check
   ([Chapter 5](../../../install/05-setup-wizard.md)).
7. **Add the cron line** in the panel's cron page, with the account's PHP
   ([Cron and shell access](cron-and-shell-access.md)).
8. **Install the certificate** with the panel's certificate management and redirect HTTP to HTTPS
   ([TLS certificates in panels](tls-certificates-in-panels.md)).

> **Warning**
> Do not delete rules from `.htaccess` to make something work. The rules decide which files Apache may hand out;
> everything else goes to `index.php`, which keeps `settings/*.ini` and the SQLite database private
> ([13.2](../../../install/13-security-hardening.md#132-what-the-web-server-must-never-hand-out)).

## Composer on cPanel

- With a shell: run `composer install` with the account's PHP version, so Composer resolves packages for the PHP that
  will run them ([Command line and web PHP](../../../install/02-requirements.md#command-line-and-web-php)).
- In the administration: the ezupdate extension searches `/opt/cpanel/composer/bin/` among its default Composer
  locations, and can download a SHA-256 verified `composer.phar` into `var/ezupdate/` with *Get Composer*. Updating
  and installing from the browser are off until `[UpdateSettings] AllowUpdate` and `AllowInstall` are enabled
  ([ezupdate](../../../features/6.0/extensions/ezupdate.md)).
- Without either: build the tree with `composer install` elsewhere, on the same PHP version, and upload it.

## Velocity on cPanel

Velocity is a long-running process. On shared cPanel accounts it "may not be allowed"; the sources' answer is Apache
or LiteSpeed with `.htaccess`. On a cPanel server where you, the host, control the machine, Velocity could run beside
the panel's Apache as on the project's Plesk server, but that combination is not described in the sources. If you try
it, read [Velocity beside Apache or nginx](../servers/velocity-beside-apache-or-nginx.md) and keep the one-user rule
for `var/` ([8.7.2](../../../install/08-serving-the-site.md#872-one-user-or-one-group)).

## A test plan

Turn tier 3 into evidence on your own platform before customers depend on it:

| Step | Pass when |
|---|---|
| Install with the setup wizard on a fresh account | every critical test passes; the site and `/admin` answer |
| Request `/settings/site.ini`, `/.env`, `/composer.json` | 404 or an Exponential page, never the file's content ([13.2](../../../install/13-security-hardening.md#132-what-the-web-server-must-never-hand-out)) |
| Upload an image in the administration | the image and its variations are served |
| Run `runcronjobs.php` from the panel's cron | output in the cron log, **Setup > Cronjobs** shows the run |
| Back up and restore to a second account | the restored site works ([10.9](../../../install/10-after-installing.md#109-backups-and-restore)) |
| Update one extension (ezupdate or Composer) | the site works after the update |

If it all passes, the project wants to hear about it: "If you run Exponential on a tier 3 system, please tell the
project ... and the row moves up" ([The four tiers](../../os-support.md#the-four-tiers)).

## Checklist

- [ ] PHP 8.x with `xsl`, `intl` and the other required extensions for the account.
- [ ] Document root at the installation root; `.htaccess` from `.htaccess_root`.
- [ ] Database (or SQLite) ready; UTF-8.
- [ ] `vendor/` present; Composer route decided.
- [ ] Cron line with the account's PHP.
- [ ] Certificate and HTTPS redirect.
- [ ] The test plan passed on your platform.

**A public demo on cPanel.** Set `EXP_DEMO_MODE=1` for the demo's account only: `SetEnv EXP_DEMO_MODE 1` in the site's `.htaccess` (Apache or LiteSpeed), and `EXP_DEMO_MODE=1` on its own line at the top of the account's cron jobs. [Demo mode for hosts](../security/demo-mode-for-hosts.md), [Demo mode examples](../../../examples/demo-mode/README.md).

## Related pages

- [Control panels: overview](README.md), [DirectAdmin](directadmin.md)
- [Shared hosting](../models/shared-hosting.md)
- [Setup wizard on shared hosting](../provisioning/setup-wizard-on-shared-hosting.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
