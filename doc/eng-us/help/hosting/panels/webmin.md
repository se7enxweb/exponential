---
title: Webmin
audience: hosting
category: hosting
order: 60
summary: Webmin is not mentioned in the Exponential sources and is not placed in a tier. This page says so, explains why the installation book's own instructions for Apache, nginx, PHP-FPM, cron and certificates are the reference on such a server, and gives the order of work and a test plan.
keywords: Webmin, not tested, no tier, server administration, Apache, nginx, PHP-FPM, systemd, cron, certificates, configuration files, installation book, test plan
type: help
---
# Webmin

This page is for hosting companies and administrators whose servers are administered through Webmin and who want to
run Exponential on them. **The Exponential sources do not mention Webmin.** It is not placed in a tier, and nothing
here comes from a test. The page explains how to proceed anyway: on a server where you edit the web server, PHP and
cron configuration yourself (through any interface), the installation book's own instructions are the reference, and
this page lists them in the order you need them.

> **Note**
> Nothing on this page describes Webmin's modules or screens. For how Webmin edits a given configuration file or
> service, check Webmin's documentation. Whatever interface you use, the files and commands below are what the
> Exponential sources describe.

## Contents

1. [Status in the sources](#status-in-the-sources)
2. [The installation book is the reference](#the-installation-book-is-the-reference)
3. [Order of work](#order-of-work)
4. [Things an administration interface must not undo](#things-an-administration-interface-must-not-undo)
5. [A test plan](#a-test-plan)
6. [Checklist](#checklist)

## Status in the sources

| Question | Answer |
|---|---|
| Is Webmin named in the documentation or code? | no |
| Which tier? | none: it is not placed |
| What decides support instead? | the operating system and web server below it, which are placed in tiers ([Operating system support](../../os-support.md)) |

If the distribution below is tier 1 or 2 (for example AlmaLinux 9, Red Hat Enterprise Linux 9 and 10, Debian 12 and
13, Ubuntu 22.04 and 24.04) and the web server is Apache with PHP-FPM (tier 1) or nginx with PHP-FPM (tier 2), the
stack Exponential sees is a placed one; only the administration interface on top is not.

## The installation book is the reference

| Task | Where the book describes it |
|---|---|
| PHP packages per distribution | [2.9 Operating system notes](../../../install/02-requirements.md#29-operating-system-notes) |
| `php.ini` values | [2.5](../../../install/02-requirements.md#25-phpini-settings) |
| Apache virtual host with PHP-FPM | [8.5.1 Step by step](../../../install/08-serving-the-site.md#851-step-by-step) |
| nginx server block (derived, not shipped) | [8.6](../../../install/08-serving-the-site.md#86-nginx-with-php-fpm) |
| PHP-FPM settings and reload | [8.5.2](../../../install/08-serving-the-site.md#852-php-fpm) |
| Velocity, with a systemd unit | [8.3](../../../install/08-serving-the-site.md#83-exponential-velocity-the-recommended-way), [8.3.11](../../../install/08-serving-the-site.md#8311-running-velocity-as-a-service) |
| File permissions and ownership | [8.7](../../../install/08-serving-the-site.md#87-file-permissions-and-ownership) |
| Databases: create database and user | [Chapter 9](../../../install/09-databases.md) |
| Crontab | [10.3 Cronjobs](../../../install/10-after-installing.md#103-cronjobs) |
| HTTPS and headers | [13.9](../../../install/13-security-hardening.md#139-security-headers-and-https) |

On such a server you also have the full choice of engines: Exponential Velocity, recommended for every stage, needs
no separate web server and serves HTTPS itself
([8.1](../../../install/08-serving-the-site.md#81-the-choice-in-one-table)).

## Order of work

1. **PHP** 8.0 or later (Velocity included) with the required extensions, for web and command line alike.
2. **Database**, in the official order: 1. SQLite 3 (the default, no server) · 2. PostgreSQL · 3. MongoDB ·
   4. MariaDB · 5. Oracle · 6. MySQL; Microsoft SQL Server is known to be possible with additional work at this time.
   Create the database and its user before installing ([Databases and users](databases-and-users.md)).
3. **Code**: `git clone` and `composer install`, as the site's user ([Chapter 3](../../../install/03-getting-the-code.md)).
4. **Install**: `exp:install`, the kickstarter or the setup wizard
   ([Chapter 4](../../../install/04-choosing-an-install-method.md)).
5. **Serve**: Velocity with a boot service, or Apache/nginx with a PHP-FPM pool running as the site's user.
6. **Cron**: `runcronjobs.php` lines in the site user's crontab.
7. **HTTPS**: a certificate for every name; redirect HTTP; HSTS once everything works.
8. **Backups**: database and `var/storage`, `settings/override`, `settings/siteaccess`, restored once elsewhere.

```bash
php -v
php bin/php/console exp:velocity ext check
php bin/php/console exp:install --dry-run
```

`exp:install --dry-run` checks "configuration and packages, nothing written"
([4.6](../../../install/04-choosing-an-install-method.md#46-unattended-installs-ci-and-containers)).

## Things an administration interface must not undo

Any tool that rewrites configuration files can silently revert a hand-made change. For Exponential, protect these:

| Setting | Why |
|---|---|
| The rewrite rules (`.htaccess` or the nginx block) | they decide which files are served; without them `settings/*.ini` and the SQLite database are exposed ([13.2](../../../install/13-security-hardening.md#132-what-the-web-server-must-never-hand-out)) |
| The PHP-FPM pool's `user` | one user writes `var/` ([8.7.2](../../../install/08-serving-the-site.md#872-one-user-or-one-group)) |
| `AllowOverride` for the document root (Apache) | `.htaccess` must be read |
| The crontab lines of the site user | scheduled publishing, notifications, clean-up |
| Firewall rules for Velocity's ports, if visitors reach Velocity directly | "add the port with whatever tool owns the ruleset on the box" ([FrankenPHP](../../../bc/6.0/frankenphp.md)) |

> **Tip**
> After any configuration change made through an interface, run the file-exposure check of
> [13.2](../../../install/13-security-hardening.md#132-what-the-web-server-must-never-hand-out) again. It takes
> seconds and catches the most dangerous regression.

## A test plan

| Step | Pass when |
|---|---|
| Install | every critical test passes; site and `/admin` answer |
| File-exposure check from outside | 404 or an Exponential page for every path, never the file |
| Reboot the server | the site and, if used, Velocity come back on their own |
| Change a PHP setting through the interface | the change is visible in **Setup > System information** after the reload |
| Cron | runs appear on **Setup > Cronjobs** |
| Backup and restore | restored site works on another machine |

## Checklist

- [ ] Distribution and web server of a placed tier below the interface.
- [ ] The installation book's steps followed for PHP, server, permissions, cron and HTTPS.
- [ ] Rewrite rules, pool user and crontab protected from being overwritten.
- [ ] File-exposure check repeated after every configuration change.
- [ ] The test plan passed.

## Related pages

- [Control panels: overview](README.md), [ISPConfig](ispconfig.md)
- [VPS hosting](../models/vps-hosting.md), [Dedicated servers](../models/dedicated-servers.md)
- [Checklist: new server](../checklists/new-server.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
