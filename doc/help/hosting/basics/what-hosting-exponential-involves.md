---
title: What hosting Exponential involves
audience: hosting
category: hosting
order: 20
summary: The parts a hosting company provides for an Exponential site (PHP for the web and the command line, a database, a web server or Velocity, cron, mail, writable storage, backups and TLS), what runs all the time, and the options for dividing the work between host and customer.
keywords: hosting, Exponential hosting, what is needed, components, PHP, PHP-FPM, Velocity, database, cron, runcronjobs, mail, storage, backups, TLS, responsibilities, managed hosting, shared hosting, offer
type: help
---
# What hosting Exponential involves

This page is for product managers and system administrators at a hosting company who are about to offer Exponential
hosting and want the whole picture first. It answers: what Exponential is from a server's point of view, which parts
the host provides, what keeps running after the install, and how the work can be divided between host and customer.
The details of each part are on the other pages of [Hosting basics](README.md).

## Contents

1. [Exponential from the server's point of view](#exponential-from-the-servers-point-of-view)
2. [The parts a host provides](#the-parts-a-host-provides)
3. [What keeps running after the install](#what-keeps-running-after-the-install)
4. [How a site gets installed](#how-a-site-gets-installed)
5. [Dividing the work: options](#dividing-the-work-options)
6. [Checklist](#checklist)
7. [Related pages](#related-pages)

## Exponential from the server's point of view

Exponential is an open source content management system written in PHP and published under the GNU GPL
(`composer.json`: `"license": "GPL-2.0-or-later"`). It is maintained by 7x ([Who is 7x?](../../who-is-7x.md)) and
continues a code base whose original product its creator discontinued after almost 18 years.

For a server, an installation is:

- **Plain PHP files** in one directory (the kernel, `lib/`, `extension/`, `design/`, `vendor/`), with one front
  controller, `index.php`, that every page request goes through
  ([chapter 3](../../../install/03-getting-the-code.md#36-a-tour-of-the-installation-root)).
- **A database**: one of six supported engines, SQLite 3 by default, which needs no database server at all
  ([Databases in the official order](databases-in-the-official-order.md)).
- **A writable run-time tree**, `var/`, holding caches, logs, uploaded files and image variations, and (with SQLite)
  the database file, plus `settings/override/` and `settings/siteaccess/` with the installation's own settings.
- **Command line tools**: the console `bin/php/console`, the scripts in `bin/php/`, and `runcronjobs.php`, which runs
  the periodic jobs.

It has no compiled code of its own, so it runs wherever a supported PHP runs
([Operating systems for hosts](operating-systems-for-hosts.md)).

## The parts a host provides

| Part | What Exponential needs | Page |
|---|---|---|
| PHP for the web | 8.0 or later (8.1 or later for Velocity), with the required extensions | [PHP versions and extensions](php-versions-and-extensions.md) |
| PHP on the command line | the same version and extensions as the web PHP | [PHP versions and extensions](php-versions-and-extensions.md) |
| `php.ini` | the values the installer checks, sized for uploads and imports | [PHP settings for Exponential](php-settings-for-exponential.md) |
| A web server | Exponential Velocity (recommended), or Apache, nginx or another server with PHP-FPM | [Choosing a web server](../servers/choosing-a-web-server.md) |
| A database | SQLite 3 needs only the `sqlite3` extension; the others need a server and an account | [Databases in the official order](databases-in-the-official-order.md) |
| Composer | to install and update the code and its extensions | [Composer and Git on servers](composer-and-git-on-servers.md) |
| Writable storage | `var/` and `settings/` writable by the site's user | [Writable directories and permissions](writable-directories-and-permissions.md) |
| Cron | `runcronjobs.php` at regular intervals | [Cron jobs](../operations/cron-jobs.md) |
| Mail | a way to send: sendmail or SMTP | [Mail delivery](../operations/mail-delivery.md) |
| TLS | a certificate for each site address | [TLS and HTTPS](../security/tls-and-https.md) |
| Backups | the database and the files, kept off the machine | [What to back up](../operations/backups-what-to-back-up.md) |

> **Note**
> A web server is not needed during a command-line install (the kickstarter or `exp:install`); the setup wizard needs
> one, because it runs in the browser ([chapter 2](../../../install/02-requirements.md#28-web-server)).

## What keeps running after the install

An Exponential site is not finished when the installer ends. These keep running for the life of the site:

| What | Why | Where it is described |
|---|---|---|
| **Cronjobs** | notifications, search indexing with delayed indexing, link checks, cache and session cleanup | [chapter 10, cronjobs](../../../install/10-after-installing.md#103-cronjobs) |
| **Velocity**, if it serves the site | a long-running PHP server process with a pool of workers | [Velocity for hosts](../servers/velocity-for-hosts.md) |
| **PHP-FPM**, if Apache or nginx serves the site | one pool per site is usual | [PHP-FPM tuning](../performance/php-fpm-tuning.md) |
| **A database server**, unless SQLite is used | PostgreSQL, MongoDB, MariaDB, Oracle or MySQL | the page of each database |
| **Logs** | `var/log/` and the web server's own logs grow and need rotation | [Log viewer and rotation](../operations/log-viewer-and-rotation.md) |
| **Caches** | `var/cache/` grows and is cleared after changes | [Cache clearing and warming](../performance/cache-clearing-and-warming.md) |

The example crontab in [chapter 10](../../../install/10-after-installing.md#a-crontab) runs the `frequent` part every
5 minutes, the default part every 15 minutes, `infrequent` hourly, and the cleanup parts once a night, all from the
crontab of the site user.

> **Warning**
> Hosting that forbids long-running processes cannot run Velocity; Apache or nginx with PHP-FPM serves the site there
> instead. Hosting without cron cannot run the periodic jobs, and notifications and delayed search indexing then
> never happen. Decide both before you design an offer.

## How a site gets installed

Every install method runs the same installer steps; they differ only in where the answers come from
([chapter 4](../../../install/04-choosing-an-install-method.md#41-one-installer-three-front-ends)):

| Method | Answers from | Fits |
|---|---|---|
| Setup wizard | forms in the browser | shared hosting without shell access |
| Kickstarter | a `kickstart.ini` file | repeatable installs, provisioning scripts |
| Console install (`exp:install`) | command-line options | automation, containers, one-command installs |

All three run the same critical system tests first and stop with the cause and the fix when the machine is not ready.
For a hosting company the kickstarter and `exp:install` are the natural tools for automated provisioning; see
[One-click installers](../provisioning/one-click-installers.md) and [exp:install](../provisioning/exp-install.md).

## Dividing the work: options

The repository does not prescribe how a host and its customers share the work. These are the usual options; choose
per plan, and write the choice into the plan's description.

| Task | Host does it | Customer does it |
|---|---|---|
| Operating system, PHP, web server, TLS | typical in every model | only on unmanaged servers |
| Database server and accounts | typical | on a server of the customer's own |
| Installing Exponential | as a provisioning option | with the setup wizard or the console |
| Updating Exponential and extensions | as a managed option ([automated updates](../operations/automated-updates-with-ezupdate.md)) | with Composer or the `ezupdate` extension |
| Cron | the host installs the crontab | the customer, where the panel allows it |
| Backups | typical, with restore tests | in addition, for their own copy |
| Content, templates, extensions of their own | rarely | typical |

> **Tip**
> Write down for each plan which of these rows belongs to whom. Most support questions about Exponential sites come
> from a row nobody thought was theirs: cron that was never installed, or a PHP upgrade that left the command line PHP
> behind the web PHP.

## Checklist

- [ ] PHP 8.0 or later for the web and the command line, the same version and extensions in both.
- [ ] PHP 8.1 or later with `sockets`, `pcntl` and `openssl` where Velocity is offered.
- [ ] A database offer chosen from the official order.
- [ ] `var/` and `settings/` writable by the site's user; one user or one group for every process.
- [ ] Cron available to run `runcronjobs.php`.
- [ ] Mail sending configured (sendmail or SMTP).
- [ ] TLS for every site address.
- [ ] Backups of the database and the files, with a tested restore.
- [ ] The division of work written into each plan.

## Related pages

- [Hosting basics](README.md) and [Hosting companies: start here](../hosting-companies-start-here.md)
- [Requirements at a glance](requirements-at-a-glance.md)
- [Choosing a hosting model](../models/choosing-a-hosting-model.md)
- [Chapter 2: Requirements](../../../install/02-requirements.md) and
  [chapter 10: After installing](../../../install/10-after-installing.md)
- [Choosing hosting, database and operating system](../../leadership/choosing-hosting-database-and-os.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
