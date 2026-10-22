---
title: "7x SQLI Import: start here"
audience: admin, integrator, developer
category: integration
order: 1
summary: What 7x SQLI Import is - the extension that brings CSV, XML, RSS, Atom, JSON, NDJSON and spreadsheet data into Exponential, once or on a schedule - who it is for, what release 2 contains, and where to read on.
keywords: sqliimport, SQLI Import, 7x SQLI Import, import, content import, CSV, XML, RSS, Atom, JSON, NDJSON, XLSX, ODS, spreadsheet, mapping, import editor, handler, job queue, expqueue, schedule, cron, migration, sync, Imports tab, Manage imports, Run imports
type: help
---
# 7x SQLI Import: start here

**7x SQLI Import** (the extension `se7enxweb/sqliimport`) brings content from outside into Exponential: a CSV export
of an ERP, an XML file, an RSS or Atom feed, a JSON or NDJSON dump, an Excel or OpenDocument spreadsheet. An import
runs once, for a migration, or on a
schedule, for a nightly sync. Release 2 is the successor of SQLI Import 1.x and keeps every 1.x handler working.

## Contents

1. [Who it is for](#who-it-is-for)
2. [What it does](#what-it-does)
3. [What this release contains](#what-this-release-contains)
4. [The pages of this section](#the-pages-of-this-section)
5. [Related pages](#related-pages)

## Who it is for

- **Administrators** create imports in the import editor, start runs, follow them in the **Imports** tab and set up
  the cron parts.
- **Integrators** plan imports from other systems and choose between a no-code mapping and a handler.
- **Developers** write handlers, sources and transforms in PHP.

## What it does

- Reads **CSV/TSV, XML, RSS/Atom, JSON/NDJSON and spreadsheets (XLSX, ODS)**, from an uploaded file, an `http(s)`
  address or a file in an allowed directory on the server.
- Maps source fields to the attributes of a content class **without code**, in the **import editor** of the
  administration, with named transforms such as `trim`, `date`, `number`, `richtext`, `image` and `lookup`. A
  preview shows a sample and a dry run before anything is published. A new version is published only when a field
  changed.
- Runs **1.x handlers untouched** and new 2.x handlers for imports that need code.
- Processes every run on the **job queue** (expqueue), in chunks with checkpoints. A run can be cancelled at any
  time, and only its failed records can be retried.
- Keeps **a row per record** (created, updated, unchanged, skipped, failed) and a log per run.
- Starts imports on **schedules**, in a time zone, across daylight saving time.

## What this release contains

Release 2 (2.0.0-alpha.1, and the 2.0.0-beta state in testing) contains the engine, the command line
`./console ext:sqliimport:*` and the Imports tab:

- the dashboard with the imports and runs, and a block "Imports" on the admin dashboard;
- the **import editor** (`sqliimport/edit`): source, processing, mapping, preview, schedule and review;
- a **live page per run**, with Cancel and Retry failed rows;
- the pages **Schedules** (pause, resume, edit, delete), **Catalogue** (the sources, handlers and transforms of the
  site) and **Settings** (the settings as they stand, and the export and import of import definitions as JSON).

Two policies control access: **Manage imports** (`sqliimport/manage`) and **Run imports** (`sqliimport/run`),
both with a handler limitation. Roles made for 1.x keep working.

Requirements: PHP 8.0 or later, Exponential 6.0.14 or later, and the extensions expqueue and expui. Spreadsheet
sources need the PHP extension zip.

## The pages of this section

| Page | What it answers |
|---|---|
| [Getting started](getting-started.md) | Installing the extension, the cron parts, the Imports tab, the two policies, a first dry run |
| [The import editor](the-import-editor.md) | Creating and changing an import step by step, the mapping grid, the preview |
| [Imports and runs](imports-and-runs.md) | What an import and a run are, the dashboard, the live run page, cancel and retry |
| [Schedules and cron](schedules-and-cron.md) | Schedule kinds, time zones, the Schedules page, the cron part `sqliimport` |
| [Upgrading from 1.x](upgrading-from-1x.md) | Installing 2.x over 1.x and migrating handlers, schedules, history and roles |

The full manual of the extension, with the sources, mappings, transforms, handlers, settings and commands, is shown
in the help under Extensions when the extension is active. Its page id is `ext/sqliimport/index.md`.

## Related pages

- [Idempotent imports keyed by remote_id](../integrators/idempotent-imports-keyed-by-remote-id.md)
- [Writing an import script](../integrators/writing-an-import-script.md)
- [Caching after imports](../integrators/caching-after-imports.md)
- [Cronjobs for integrations](../integrators/cronjobs-for-integrations.md)
