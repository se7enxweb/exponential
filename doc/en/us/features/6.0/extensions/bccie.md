# bccie: export collected information to CSV or Excel

This page is for site owners who collect form answers (contact forms, polls) and need them in a spreadsheet. `bccie`
("BC CIE", shown as **CIE** in the admin menu since 1.1.4) exports the **collected information** of content objects
to **CSV** or **SYLK (Excel)** files. You can export from the admin on demand, or on a schedule with its cronjob parts.
The original author is Brookins Consulting (2006 to 2017, based on work by an earlier contributor).

## Use it

1. Open the **CIE** tab in the admin (`/bccie/overview`; the role needs `bccie/read`). The start page shows the forms that collected information, how many answers each has, the last exports and the problems found (for example a scheduled export that names an object that does not exist).
2. Open a form with **Export**. Choose the fields, the date range, the format (CSV or SYLK (Excel)), the separator and the character set. A wrong date or no field at all is refused with a message beside the form.
3. **Do export** downloads the file. A form with more than `DirectExportLimit` answers (2000) is exported with **Run in the background**: the page shows the progress and a **Download** button when the file is ready.
4. To remove the answers of a form, use **Remove selected** in the list or **Remove all collected information** on the export page; both ask first and need the policy `bccie/remove`. Export the data before you remove it.

Spreadsheet safety: an answer that starts with `=`, `+`, `-` or `@` is written with a leading single quote, so Excel does not
run it as a formula. Text is UTF-8; for Excel on Windows choose `utf8bom`, on macOS `utf16le`.

### From the console and cron

```bash
./console ext:bccie:status
./console ext:bccie:export --object=123 --format=sylk --dry-run
./console ext:bccie:export --cron          # the scheduled export of cie.ini
./console ext:bccie:purge --object=123 --before=2026-01-01 --yes
```

For scheduled exports, set `Collection[]`, `Directory` and the range settings below and add the cronjob parts to cron
(`php runcronjobs.php exportcsv`); the files are written to `var/export`. The reference is the
[specification](../../../specifications/6.0/bccie.md).

## Settings

All keys are in `cie.ini`, block `CieSettings`.

| Key | Default | Meaning |
|---|---|---|
| `Debug`, `Log`, `LogDebug` | `disabled`, `var/log/cie.log`, `disabled` | Debug output and log file |
| `Directory` | `var/export` | Where cron exports are written |
| `ExportLimitedRange`, `DateRangeToExport` | `disabled`, `7` | Export only the last N days |
| `RemoveExported` | `disabled` | Delete collected information after export |
| `Collection[]`, `ExcludeAttributeID[]` | empty | Restrict what is exported |
| `CsvFormat`, `CsvSeparator`, `SylkFormat`, `SylkSeparator` | `csv`, `;`, `sylk`, `;` | Formats and separators |
| `ExportZeroToEmptyString` | `disabled` | Write 0 as an empty cell |
| `ExportExecutionTimeLimit` | `180` | Seconds |
| `ExportUsingDaysCalcualation` | `disabled` | Range by days (key spelled as shipped) |
| `DisplayLeaveEmptyOption` | `enabled` | Show the "leave empty" option |
| `ExportFileName` | `bccie_cie_export-` | File name prefix |

## What changed in the Exponential 6 releases

| Version | Date | Change |
|---|---|---|
| 1.1.4 | 20 September 2026 | The top menu name is **CIE** instead of "BC CIE Export". |
| 1.1.5 to 1.1.7 | | The license is named in full (GPL v2 or later); every visible text is a translation string with German, including the export option "SYLK (Excel)"; the tab, tooltip and navigation part name have German translations. |
| 1.1.8 | 29 September 2026 | Opening the export without a valid form answers with the kernel's "not available" page instead of HTTP 500. The `export` and `doexport` views ended with `EZ_ERROR_KERNEL_NOT_AVAILABLE`, a constant the kernel no longer defines, so PHP 8 stopped with an undefined constant error; they use `eZError::KERNEL_NOT_AVAILABLE` now. |
| 1.1.12 | 4 October 2026 | A dashboard start page; an export page that validates its input; large exports in the background with progress and download; console commands `ext:bccie:export`, `status` and `purge`; the cronjob parts rewritten on the same runner. Fixed: every datatype was written by the base handler (PHP 8 does not call a constructor named after the class); characters outside Latin-1 became question marks; spreadsheet formulas in answers ran in Excel; a double quote broke a CSV cell; the SYLK export lost its first collection and its header; the header did not match the columns; removing answers needed only the read policy (it needs `bccie/remove` now). |
| 1.1.9 to 1.1.11 | 30 September to 2 October 2026 | The description names Exponential; commands and cronjob parts are classes the files call, each with a description; English and German translations for every string the admin showed untranslated. |

## Related pages

- [Specification](../../../specifications/6.0/bccie.md): views, policies, files, commands, settings
- [xrowextract](xrowextract.md): the newer export tool with its own handlers; [birthday](birthday.md) ships a handler for it
- [CLI, cronjob and view abstractions](../../../bc/6.0/cli_cronjob_view_abstractions.md)
- [Chronicle](../../../history/extensions/bccie.md) and [release notes](../../../changelogs/extensions/bccie.md)
- [Change ledger](../../../history/ledger/bccie.md)
- [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
- Months: [2026-09](../../../history/extensions/months/2026-09.md), [2026-10](../../../history/extensions/months/2026-10.md) (all extensions)
