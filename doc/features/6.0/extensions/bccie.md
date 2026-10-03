# bccie: export collected information to CSV or Excel

This page is for site owners who collect form answers (contact forms, polls) and need them in a spreadsheet. `bccie`
("BC CIE", shown as **CIE** in the admin menu since 1.1.4) exports the **collected information** of content objects
to **CSV** or **SYLK (Excel)** files. You can export from the admin on demand, or on a schedule with its cronjob parts.
The original author is Brookins Consulting (2006 to 2017, based on work by an earlier contributor).

## Use it

1. Open the **CIE** tab in the admin (`/bccie/overview`; the role needs `bccie/read`).
2. Choose the object whose answers you want, the format (CSV or SYLK (Excel)) and the options.
3. Export. The file downloads.

For scheduled exports, set `Directory` and the range settings below and add the cronjob parts to cron; the files are
written to `var/export`.

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
| 1.1.9 to 1.1.11 | 30 September to 2 October 2026 | The description names Exponential; commands and cronjob parts are classes the files call, each with a description; English and German translations for every string the admin showed untranslated. |

## Related pages

- [xrowextract](xrowextract.md): the newer export tool with its own handlers; [birthday](birthday.md) ships a handler for it
- [CLI, cronjob and view abstractions](../../../bc/6.0/cli_cronjob_view_abstractions.md)
- [Chronicle](../../../history/extensions/bccie.md) and [release notes](../../../changelogs/extensions/bccie.md)
- [Change ledger](../../../history/ledger/bccie.md)
- [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
- Months: [2026-09](../../../history/extensions/months/2026-09.md), [2026-10](../../../history/extensions/months/2026-10.md) (all extensions)
