# bccie: export collected information to CSV or Excel

`bccie` ("BC CIE", shown as **CIE** in the admin menu since 1.1.4) exports the **collected information** of content objects (the answers
visitors submitted through information-collection forms) to **CSV** or **SYLK (Excel)** files. It provides cronjob parts, class methods
and module views, so you can export from the admin on demand or on a schedule. The original author is Brookins Consulting (2006 to 2017,
based on work by an earlier contributor).

## Settings (`cie.ini`, block `CieSettings`)

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
| `ExportUsingDaysCalcualation` | `disabled` | |
| `DisplayLeaveEmptyOption` | `enabled` | |
| `ExportFileName` | `bccie_cie_export-` | File name prefix |

## What changed in the Exponential 6 releases

* 1.1.4 (20 September 2026): the top menu name is **CIE** instead of "BC CIE Export", a clearer, shorter name now that it ships with
  Exponential 6.
* 1.1.5 to 1.1.7: the license is named in full (GPL v2 or later); every visible text is a translation string with German, including the
  export option "SYLK (Excel)"; the tab, tooltip and navigation part name have German translations.
* 1.1.8 (29 September): opening the export without a valid form answers with the kernel's "not available" page instead of HTTP 500. The
  `export` and `doexport` views ended with `EZ_ERROR_KERNEL_NOT_AVAILABLE`, a constant the kernel no longer defines, so PHP 8 stopped with an
  undefined constant error; they use `eZError::KERNEL_NOT_AVAILABLE` now.
* 1.1.9 to 1.1.11 (30 September to 2 October): the description names Exponential; commands and cronjob parts are classes the files call
  with a description each; English and German translations for every string the admin showed untranslated.

## Related

* [xrowextract](xrowextract.md): the newer export tool with its own handlers; [birthday](birthday.md) ships a handler for it
* [Chronicle](../../../history/extensions/bccie.md) and [release notes](../../../changelogs/extensions/bccie.md)
