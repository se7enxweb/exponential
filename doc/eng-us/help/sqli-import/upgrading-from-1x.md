---
title: Upgrading from SQLI Import 1.x
audience: admin, integrator, developer
category: integration
order: 40
summary: Moving an installation of SQLI Import 1.x to 7x SQLI Import 2 - installing over 1.x, the dry run and the --apply and --roles options of migrate-1x, what becomes of handlers, scheduled imports, history and roles, what changes in the administration, and the 1.x bugs fixed on Exponential 6.
keywords: sqliimport, SQLI Import 1.x, upgrade, migrate-1x, migration, legacy handler, ISQLIImportHandler, scheduled import, sqliimport_scheduled, sqliimport_item, listimports, manageimports, roles, sqliimport_run, sqliimport/items
type: help
---
# Upgrading from SQLI Import 1.x

Release 2 keeps every 1.x handler, setting, table, policy and page working. Nothing of 1.x is deleted.

## Contents

1. [Steps](#steps)
2. [What the migration does](#what-the-migration-does)
3. [What changes](#what-changes)
4. [Fixed 1.x bugs](#fixed-1x-bugs)

## Steps

1. Back up the database.
2. Install 2.x over 1.x and activate expqueue and expui ([Getting started](getting-started.md)).
3. Create the tables:

   ```bash
   ./console ext:expqueue:install-schema
   ./console ext:sqliimport:install-schema
   ```

4. See what the migration would do. Nothing is changed:

   ```bash
   ./console ext:sqliimport:migrate-1x
   ```

5. Migrate. Add `--roles` to give every role with `sqliimport/manageimports` the policy `sqliimport/manage`
   (Manage imports) with the same handler limitation:

   ```bash
   ./console ext:sqliimport:migrate-1x --apply --roles
   ```

6. Replace the 1.x cron parts with `expqueue` and `sqliimport`, both every minute
   ([Schedules and cron](schedules-and-cron.md)).

## What the migration does

| Part | Result |
|---|---|
| Handlers | Each 1.x handler becomes an import with the identifier `legacy-<handler>` |
| Scheduled imports | Each becomes an import with its options and a schedule of the same frequency. The 1.x scheduled import is switched off, not removed. |
| History | Past 1.x import requests become read-only runs (status, time, user) |
| Roles (`--roles`) | Roles with `manageimports` get `manage` (Manage imports) with the same handler limitation. Roles with only `listimports` need nothing new: `listimports` still lets them look at the Imports tab. Roles with `sqliimport/*` are left alone. |

The migration can be run again; what was migrated before is left alone. Pending 1.x import requests are started as
real runs by the cron part `sqliimport`.

## What changes

- Imports are edited in the import editor ([The import editor](the-import-editor.md)). A migrated import of a 1.x
  handler has the steps source, processing, schedule and review.
- Two policies control access: Manage imports (`manage`) and Run imports (`run`). `manageimports` still counts as
  Manage imports, and `listimports` lets a user look ([Getting started](getting-started.md#the-imports-tab)).
- 1.x handlers run untouched on the job queue. Their records have the outcome "processed", and their progression
  notes go to the run's log. Retrying only failed rows is not available for them.
- `sqliimport/list` is now the 2.x dashboard. The 1.x list of import requests moved to `sqliimport/items`. The other
  1.x pages (scheduled imports, new import request) stay in the left menu under "sqliimport 1.x".
- The actions of the 1.x pages (cancel, interrupt, remove, purge) are POST forms with a confirmation.
- One import no longer blocks the others: 1.x had one global lock, 2.x runs different imports in parallel.

## Fixed 1.x bugs

On Exponential 6, release 2 fixes these 1.x bugs:

- The fields of 1.x import requests and scheduled imports read as empty, because their getter bypassed the kernel's
  persistent storage, so the 1.x list and cron job saw nothing.
- Options serialized with NUL bytes were cut on SQLite.
- Option text with a line without "=" warned on PHP 8.

The extension manual (page id `ext/sqliimport/index.md`, under Extensions in the help) has the full migration
page, including how to replace a simple 1.x handler with a no-code mapping.
