# Specification: the bccie extension

This page is the reference for the `bccie` extension (1.1.12), "BC CIE", shown as **CIE** in the admin. It exports the
**collected information** of content objects (the answers sent through a contact form, a poll, a job application) to
CSV or SYLK (Excel) files: from the admin, in the background, from the console and by cron. The page lists the module
views and policies, the export options and what the files contain, the console commands, the cronjob parts and every
setting. The short guide for site owners is the [feature page](../../features/6.0/extensions/bccie.md).

## In short

- Admin entry point: `/bccie/overview` (the **CIE** tab): a dashboard with the forms that collected information, the last exports, the scheduled export and the problems found, and a list of the forms with filter, sort and paging.
- One form is exported on `/bccie/export/<object id>`: fields, date range, type (CSV or SYLK), separator and character set. A small export is written while you wait; a large one runs in the background and is offered for download.
- The same work is available as console commands (`ext:bccie:export`, `status`, `purge`) and as two cronjob parts (`exportcsv`, `exportsylk`). All use the runner class `bccieRunner`.
- Every text cell is made safe for a spreadsheet: a cell that starts with `=`, `+`, `-` or `@` (or a tab) gets a leading single quote.
- No tables: the extension reads `ezinfocollection` and `ezinfocollection_attribute`. Its own state (the last exports) is two rows of `ezsite_data`; background runs keep files in `var/<site>/bccie/jobs/`.

## Example: export a form from the console and look at the file

```bash
./console ext:bccie:status                                   # which forms have data
./console ext:bccie:export --object=123 --dry-run            # how many collections would be written
./console ext:bccie:export --object=123 --charset=utf8bom --from=2026-10-01 --output=var/export
```

Open the file in a spreadsheet: the first line is the header with the field names, one line follows per collection, and a
cell that started with `=` shows a leading quote instead of a result.

## Module `bccie`

Every view uses the navigation part `ezbccienavigationpart`, so the left menu comes from
`design/standard/templates/parts/bccie/menu.tpl` in every admin design (the links are `[Leftmenu_bccie]` in `menu.ini`).

| View | Policy function | Parameters | Script |
|---|---|---|---|
| `overview` | `read` | `(offset)`, `(q)`, `(sort)`, `(order)` | `overview.php`: the dashboard and the list of forms; removing selected forms asks for confirmation first |
| `export` | `read` | `ObjectID`, `(job)` | `export.php`: the options form, the download, "Run in the background", removal of all collected information |
| `doexport` | `read` | `ObjectID` | `doexport.php`: the address the form of earlier versions posts to; it does the same as `export` |
| `job` | `read` | `JobID` | `job.php`: the state and log of a background export as JSON (HTTP 404 for an unknown job) |
| `download` | `read` | `JobID` | `download.php`: the file of a finished background export |

Policy functions: `read` (every view) and, since 1.1.12, `remove` (removing collected information, from the list and from
the export page; the **Admin** role has it). The view scripts are thin calls to runnable classes in
`classes/runnable/views/bccie/`; the work is in `bccieExportPage`, `bccieRunner` and `bccieExportUtils`.

## Export options

| Option | Values | Notes |
|---|---|---|
| Fields | the collection id (`contentobjectid`), class attribute ids that collect information, `-1` (an empty column), `-2` (ignored) | One header cell and one data cell per column, in the order given. The old form sends them as `field_0`, `field_1`, ...; the new form as `include_id` and `columns[]`. |
| Date range | `start_date`, `end_date` as `YYYY-MM-DD` (or the old `start_day`, `start_month`, `start_year` ...) | A collection counts for the day it was created. An invalid date, or a start after the end, is refused with a message. |
| Type | `csv`, `sylk` | Any other value is refused. |
| Separator | `;` `,` `:` `\|` `#` | CSV only. The console accepts the names `semicolon`, `comma`, `colon`, `pipe`, `hash`. |
| Character set | the keys of `ExportOutputFormatHandlers`: `utf8`, `utf8bom`, `utf16le`, `cp1252` | Default `ExportOutputFormatHandlerDefault`. `cp1252` writes a character the code page lacks as `?`. |
| Creation and modification date | on or off | One more column each, ISO 8601. |

### What the files contain

- **CSV**: every cell in double quotes, a double quote inside a cell doubled, cells separated by the separator, no separator after the last cell, one line per collection, a newline inside a cell written as a space. Cells are trimmed.
- **SYLK**: the format records, a bold header row of the field names, then one row per collection; the collection id is a number, all other cells are text (`"` doubled, `;` doubled).
- **Text** is UTF-8 and is kept as it is; only the character set handler converts the finished file.
- **Formulas**: a text cell that starts with `=`, `+`, `-` or `@`, a tab or a carriage return gets a leading single quote (`=1+1` becomes `'=1+1`). A plain number, also with a sign (`+4912345`, `-5`), is left alone because a spreadsheet reads it as a number, not a formula.
- **File name**: `<ExportFileName><object name>-on-<date>.csv` (`.slk`), with the name reduced to letters, digits, dot, dash and underscore.
- **A datatype with a handler** (see `export.ini`) is written as that handler says (the option's text, the related object's name, the country's name); **any other datatype** writes its stored text.

### Direct and background exports

A download is written while the page waits, up to `DirectExportLimit` collections (2000). Above that, or when "Run in the
background" is pressed, `bccieJob` starts `extension/bccie/bin/php/export.php --job=<id>` with `expProcessTools` (`setsid`, the
PHP command line). The page polls `bccie/job/<id>` for the state, the progress (`done` of `total`) and the log, and shows a
**Download** button when the state is `finished`. The newest 20 jobs and their files are kept. A job that shows no sign of
life for ten minutes is reported as failed.

## Console commands

| Command | Class | Options | Does |
|---|---|---|---|
| `./console ext:bccie:export` (`@alias cie-export`) | `Exponential\Command\Extension\Bccie\Export` | `--object=ID`, `--format`, `--separator`, `--charset`, `--fields=a,b,c`, `--from`, `--to`, `--days=N`, `--output=DIR`, `--creation-date`, `--modification-date`, `--remove-exported`, `--dry-run`, `--cron`, `--job=ID` | Writes one form's collected information to a file, or with `--cron` runs the scheduled export of the settings |
| `./console ext:bccie:status` (`@alias cie-status`) | `...\Status` | | Prints the dashboard as text |
| `./console ext:bccie:purge` (`@alias cie-purge`) | `...\Purge` | `--object=ID`, `--before=YYYY-MM-DD`, `--dry-run`, `--yes` | Removes collected information; without `--yes` nothing is removed |

Every command shows its options with `--help`. One export runs at a time (a lock in the cache directory, whoever starts it).
A run records its time and what it wrote for the dashboard and raises the kernel's runnable events, so the audit sees it like any
kernel command; a removal is also written to the audit as `bccie-purge`.

## Cronjob parts

| Block in `cronjob.ini` | Script (`cronjobs/`) | Class | Job |
|---|---|---|---|
| `[CronjobPart-exportcsv]` | `exportcsv.php` | `Exponential\Cronjob\Extension\Bccie\Exportcsv` | One CSV file per object of `Collection[]` in `Directory` |
| `[CronjobPart-exportsylk]` | `exportsylk.php` | `Exponential\Cronjob\Extension\Bccie\Exportsylk` | The same as SYLK files |

Both files are stubs that call the classes; `ext:bccie:export --cron --format=sylk` runs the same code by hand. The files are
named `<object>_export_<date>_<time>.csv`, or `<object>_<from>_to_<to>.csv` with `ExportLimitedRange`. Every field of the form
is written, except the class attributes of `ExcludeAttributeID`, with the collection id first. With `RemoveExported=enabled`
the collections a file holds are removed after the file was written; nothing is removed when the file could not be written.

## Settings

Scope: extension (`extension/bccie/settings/`). Override them in `settings/override/` or a siteaccess.

| File | Block | Key | Default | Meaning |
|---|---|---|---|---|
| `cie.ini` | `CieSettings` | `Directory` | `var/export` | Where the scheduled exports and `ext:bccie:export` write |
| `cie.ini` | `CieSettings` | `Collection[]` | empty | Object ids the scheduled export covers |
| `cie.ini` | `CieSettings` | `ExcludeAttributeID[]` | empty | Class attribute ids left out of the scheduled export |
| `cie.ini` | `CieSettings` | `ExportLimitedRange`, `DateRangeToExport` | `disabled`, `7` | Only the last N days in the scheduled export |
| `cie.ini` | `CieSettings` | `RemoveExported` | `disabled` | Remove what a scheduled export wrote |
| `cie.ini` | `CieSettings` | `CsvSeparator`, `SylkSeparator` | `;` | Separators of the scheduled export |
| `cie.ini` | `CieSettings` | `CronUser` | `14` | User id the cronjob parts and commands run as (since 1.1.12) |
| `cie.ini` | `CieSettings` | `DirectExportLimit` | `2000` | Collections an export of the admin may have to be written while you wait (since 1.1.12) |
| `cie.ini` | `CieSettings` | `ExportExecutionTimeLimit` | `180` | Seconds a download may take |
| `cie.ini` | `CieSettings` | `ExportZeroToEmptyString` | `disabled` | Write 0 as an empty cell |
| `cie.ini` | `CieSettings` | `ExportUsingDaysCalcualation` | `disabled` | Range by days (key spelled as shipped) |
| `cie.ini` | `CieSettings` | `ExportFileName`, `ExportFileNameDateFormat` | `bccie_cie_export-`, `Y-m-d_H-i-s` | File name prefix and date |
| `cie.ini` | `CieSettings` | `ExportOutputFormatHandlers[]`, `ExportOutputFormatHandlerDefault` | four handlers, `utf8` | The character sets and the default |
| `export.ini` | `General` | `ExportableDatatypes[]` and a block per datatype | see file | The handler class of a datatype |
| `cronjob.ini` | `CronjobSettings` | `ExtensionDirectories[]` | `bccie` | Where the cronjob scripts are found |
| `menu.ini` | `TopAdminMenu`, `Leftmenu_bccie` | `Tabs[]`, `Links[]` | `bccie_overview` | The tab and the left menu |

## Related pages

- [Feature page](../../features/6.0/extensions/bccie.md), [behaviour changes of the extensions](../../bc/6.0/extensions-behaviour-changes.md)
- [CLI, cronjob and view abstractions](../../bc/6.0/cli_cronjob_view_abstractions.md), [Cronjobs in the console](../../features/6.0/cronjobs-console.md)
- [Chronicle](../../history/extensions/bccie.md) and [release notes](../../changelogs/extensions/bccie.md)
