---
title: Getting started with 7x SQLI Import
audience: admin, integrator, developer
category: integration
order: 10
summary: Installing 7x SQLI Import 2 - the extensions it needs, the tables, the two cron parts, the Imports tab and its menu.ini pitfall, the two policies Manage imports and Run imports - and checking it with a first dry run.
keywords: sqliimport, install, composer, ActiveExtensions, install-schema, expqueue, expui, ext-zip, runcronjobs, cron part, Imports tab, TopAdminMenu, Tabs, menu.ini, policy, Manage imports, Run imports, sqliimport/manage, sqliimport/run, dry run
type: help
---
# Getting started with 7x SQLI Import

This page installs 7x SQLI Import 2 and checks that it works.

## Contents

1. [Requirements](#requirements)
2. [Install](#install)
3. [The Imports tab](#the-imports-tab)
4. [A first dry run](#a-first-dry-run)

## Requirements

- Exponential 6.0.14 or later, PHP 8.0 or later.
- The extension **expqueue** (the job queue): its workers process every run.
- The extension **expui** (the shared components of the administration).
- Optional: the PHP extension **zip**, for spreadsheet sources (XLSX and ODS).

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

Two policies control the tab, both with a handler limitation (`SQLIImport_Type`):

| Policy | Allows |
|---|---|
| **Manage imports** (`sqliimport/manage`) | Everything: create, edit, copy, enable, disable and delete imports, schedules, the Settings page with the export and import of definitions, and running |
| **Run imports** (`sqliimport/run`) | Looking at the tab and running imports: Run now, Dry run, Cancel, Retry failed rows |

Roles made for 1.x keep working: `sqliimport/manageimports` counts as Manage imports, and
`sqliimport/listimports` lets a user look at the tab. The functions `view`, `edit` and `settings` of
2.0.0-alpha.1 no longer exist; give such roles `manage` or `run`.

The left menu of the tab links the dashboard, **New import** (the import editor), running and failed runs,
**Schedules**, the job queue, **Catalogue**, **Settings** and the 1.x pages. A link is shown only to users who may
open its page.

## A first dry run

In the administration, create a first import with **New import** ([The import editor](the-import-editor.md)). Its
preview step shows a sample of the source and a dry run of the first records before anything is published.

From the command line, list the imports. After a fresh install the list is empty, unless you created an import,
1.x handlers are configured or you migrated from 1.x ([Upgrading from 1.x](upgrading-from-1x.md)):

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

Next: [The import editor](the-import-editor.md).
