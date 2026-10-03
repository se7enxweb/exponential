# ezxmlexport: scheduled XML exports

`ezxmlexport` ("eZ XML Export") exports content as XML on a schedule and can deliver the result by FTP. Its module `xmlexport` has views to define
an export (`edit`, `view`, `delete`), see running exports (`runningexports`), generate the XML schema (`createxmlschema`), test the FTP delivery
(`ftptest`) and relaunch a failed transfer (`relaunchftptransfert`), plus a cronjob and a command line. Version 1.3.0 made it compatible with the
admin2 design.

In the Exponential 6 period the repository received package metadata and funding information only (December 2023 to March 2026); the code is the
1.3 line. For new work, [xrowextract](xrowextract.md) exports to CSV, JSON, XML and content packages on a schedule, with SFTP, FTPS, S3, WebDAV and
HTTP delivery.

## Related

* [xrowextract](xrowextract.md)
* [Chronicle](../../../history/extensions/ezxmlexport.md) and [release notes](../../../changelogs/extensions/ezxmlexport.md)
* [Change ledger](../../../history/ledger/ezxmlexport.md)
