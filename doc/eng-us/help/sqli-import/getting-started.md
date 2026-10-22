---
title: Getting started with 7x SQLI Import
audience: admin, integrator, developer
category: integration
order: 10
summary: Installing 7x SQLI Import 2.0.0-alpha.1 - the extensions it needs, the tables, the two cron parts, the Imports tab and its menu.ini pitfall - and checking it with a first dry run.
keywords: sqliimport, install, composer, ActiveExtensions, install-schema, expqueue, expui, runcronjobs, cron part, Imports tab, TopAdminMenu, Tabs, menu.ini, dry run
type: help
---
# Getting started with 7x SQLI Import

This page installs 7x SQLI Import 2.0.0-alpha.1 and checks that it works.

## Contents

1. [Requirements](#requirements)
2. [Install](#install)
3. [The Imports tab](#the-imports-tab)
4. [A first dry run](#a-first-dry-run)

## Requirements

- Exponential 6.0.14 or later, PHP 8.0 or later.
- The extension **expqueue** (the job queue): its workers process every run.
- The extension **expui** (the shared components of the administration).

## Install

1. Require the package:

   ```bash
   composer require se7enxweb/sqliimport:^2.0@alpha
   ```

2. Activate `expqueue`, `expui` and `sqliimport` in `settings/override/site.ini.append.php`
   (`[ExtensionSettings] ActiveExtensions[]`), or for the administration only in `ActiveAccessExtensions[]`.
3. Regenerate the autoloads and create the tables. Both `install-schema` commands are idempotent; the second also
   upgrades the tables of 1.x:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   ./console ext:expqueue:install-schema
   ./console ext:sqliimport:install-schema
   ```

4. Run both cron parts every minute:

   ```bash
   * * * * * cd /path/to/exponential && php runcronjobs.php -q expqueue
   * * * * * cd /path/to/exponential && php runcronjobs.php -q sqliimport
   ```

5. Clear the INI cache: `php bin/php/ezcache.php --clear-tag=ini`.

## The Imports tab

The extension adds the tab **Imports** to the administration. A siteaccess that resets `[TopAdminMenu] Tabs[]` in
its own `menu.ini.append.php` (a line `Tabs[]` without a value) must list `Tabs[]=sqliimport` there itself, or the
tab does not show in that siteaccess.

The tab needs the policy `sqliimport/view`; starting runs needs `sqliimport/run`. Roles made for 1.x with
`sqliimport/listimports` or `sqliimport/manageimports` keep working.

## A first dry run

List the imports. After a fresh install the list is empty, unless 1.x handlers are configured or you migrated
from 1.x ([Upgrading from 1.x](upgrading-from-1x.md)):

```bash
./console ext:sqliimport:list
```

A dry run publishes nothing. It reads the first 20 records and records what would happen:

```bash
./console ext:sqliimport:run <identifier> --dry-run --wait
./console ext:sqliimport:status <run id>
```

In the administration: open **Imports**, choose the import and press **Dry run**, then follow the run on its page.
If the dashboard warns that the tables or the job queue are missing, run the matching `install-schema` command.

Next: [Imports and runs](imports-and-runs.md).
