---
title: The import editor of 7x SQLI Import
audience: admin, integrator
category: integration
order: 15
summary: Creating and changing an import in the administration with the import editor of 7x SQLI Import - the six steps source, processing, mapping, preview, schedule and review, Auto-match by name, the one-line text of a transform chain, the preview with a dry run, and copying, disabling and deleting imports.
keywords: sqliimport, import editor, sqliimport/edit, New import, source, processing, mapping, mapping grid, Auto-match by name, transforms, transform chain, preview, sample, dry run, schedule, review, Save and run, Duplicate, Enable, Disable, Delete, spreadsheet, XLSX, ODS
type: help
---
# The import editor of 7x SQLI Import

The import editor (`sqliimport/edit`) creates and changes imports in the administration, without PHP. It takes an
import through six steps. Each step is a form that is saved on its own, so nothing is lost half-way and every step
can be opened again later.

## Contents

1. [Opening the editor](#opening-the-editor)
2. [The six steps](#the-six-steps)
3. [The mapping grid](#the-mapping-grid)
4. [The preview](#the-preview)
5. [Copying, disabling and deleting](#copying-disabling-and-deleting)

## Opening the editor

- **New import** on the dashboard of the Imports tab, or in its left menu, opens `sqliimport/edit/new`.
- **Edit** next to an import in the list opens the import at its first step.
- **Edit** on the Schedules page opens the schedule step of the import.

The editor needs the policy **Manage imports** (`sqliimport/manage`), or `sqliimport/manageimports` of 1.x. With a
handler limitation, only imports of the allowed handlers can be opened. A user with **Run imports**
(`sqliimport/run`) alone sees and runs the imports, but cannot change them.

Every step has the buttons **Save and continue** (save, then the next step), **Save** (save and stay) and **Back to
the imports**. Every value is checked on the server; a step with errors is shown again with the fields marked.

## The six steps

| Step | What it sets |
|---|---|
| 1. Source | Name, identifier (for the command line; empty: made from the name), description; the source type (CSV, XML, RSS/Atom, JSON, Spreadsheet XLSX/ODS, or a 1.x handler); where the data comes from (an uploaded file, an `http(s)` address read at every run, or a file in an allowed directory on the server); the options of the source type |
| 2. Processing | The no-code mapping or another handler with its options; the parent node, section and language of new content; the user a run runs as; records per chunk; a limit per run; whether existing content is updated |
| 3. Mapping | The content class, how a record finds its object, where new objects go, and one line per attribute (only for the no-code mapping) |
| 4. Preview | A sample of the source and a dry run of the first records (not for 1.x handlers) |
| 5. Schedule | Never, once, every N minutes, hourly, daily, weekly, monthly or a cron expression, in a time zone, with e-mail addresses for failures; the next five run times are shown |
| 6. Review | The summary, what is still missing, and whether the import is enabled; **Save**, or **Save and run** to start a run at once |

A new import is created when the source step is saved for the first time, disabled. It can be enabled in the review
step once nothing is missing.

## The mapping grid

Choose the content class and save the step to see its attributes. Then say how a record finds its object: by a
remote ID made from the record, such as `product-{sku}`, or by the value of an attribute. Each line of the grid is
one attribute:

| Column | Meaning |
|---|---|
| Source field | A field of the record, such as `title` or `author.name`; the fields of the sample are listed above the grid |
| Transforms | A chain of transforms as one line of text |
| Default | The value when the source value is empty |
| Required | The record fails when the value is still empty |

A transform chain is written with the steps separated by `|` and the arguments in parentheses:

```text
trim | date(format=d.m.Y, timezone=Europe/Berlin)
date(format=excel)
map(table={"D":"Germany","A":"Austria"}, otherwise=Other)
replace(search=",", replace=.)
```

A value with a comma, a parenthesis or a `|` is put in double quotes; a table or a list is written as JSON.
`date(format=excel)` reads the date numbers of a spreadsheet, such as `45231` for 1 November 2023.

**Auto-match by name** fills the lines that have no source field yet: it compares the names of the attributes with
the field names of the sample, without case and accents, and adds `richtext`, `date`, `image` or `bool` where the
datatype needs it. It uses no external service. Check the filled lines, then save.

## The preview

The preview runs in the request; it starts no job and stores no run. It shows:

- the first 20 records of the source as a table, with what the source detected (the delimiter and encoding of a CSV
  file, the sheets of a spreadsheet);
- a dry run of the first 20 records (`[RunSettings] DryRunRows`): for each record what would happen (created,
  updated, unchanged, skipped, failed) and the fields that would change, with the current and the new value.

Nothing is published and no image or file is downloaded.

## Copying, disabling and deleting

Next to each import in the list, users with **Manage imports** find **Edit** and, under **More**:

- **Duplicate**: a disabled copy with the source, processing and mapping, without schedules and runs, opened in the
  editor;
- **Enable** or **Disable**: switches the import on or off;
- **Delete**: removes the import with its mapping and schedules after a confirmation. Its runs stay in the history.
  An import that runs now cannot be deleted.

To move imports from a staging installation to the live one, export them as JSON on the **Settings** page of the
Imports tab and import the file there. Uploaded source files are not part of an export; upload them again in the
source step.

Next: [Imports and runs](imports-and-runs.md).
