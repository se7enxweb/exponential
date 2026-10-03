# ezxmlexport: scheduled XML exports

This page is for sites that still run `ezxmlexport` and for anyone choosing an export tool. `ezxmlexport` ("eZ XML
Export") exports content as XML on a schedule and can deliver the result by FTP.

## What it offers

The module `xmlexport` has these views:

| View | Purpose |
|---|---|
| `edit`, `view`, `delete` | define, show and remove an export |
| `runningexports` | see running exports |
| `createxmlschema` | generate the XML schema |
| `ftptest` | test the FTP delivery |
| `relaunchftptransfert` | relaunch a failed transfer |

It also has a cronjob and a command line. Version 1.3.0 made it compatible with the admin2 design.

## Status

In the Exponential 6 period the repository received package metadata and funding information only (December 2023 to
March 2026); the code is the 1.3 line.

For new work, use [xrowextract](xrowextract.md). It exports to CSV, JSON, XML and content packages on a schedule,
with SFTP, FTPS, S3, WebDAV and HTTP delivery.

## Related pages

- [xrowextract](xrowextract.md)
- [Chronicle](../../../history/extensions/ezxmlexport.md) and [release notes](../../../changelogs/extensions/ezxmlexport.md)
- [Change ledger](../../../history/ledger/ezxmlexport.md)
- [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
- Months: [2023-12](../../../history/extensions/months/2023-12.md), [2024-01](../../../history/extensions/months/2024-01.md), [2026-03](../../../history/extensions/months/2026-03.md)
