# syndication: share content between installations

This page is for administrators who run several Exponential installations that share content. `syndication` exports
part of the content tree of one site **as a feed**, and another site **imports** it, kept in step by cron. It supports:

- **One-way syndication**: for example shared content or authentication data between separate databases and client
  sites.
- **Two-way syndication**: for example forums, portal networks, social platforms and large e-commerce networks.

The feeds are cached XML plus binary files, one file per exported object, served through a **SOAP** interface
(`soap.ini` `EnableSOAP=true`, `SOAPExtensions[]=syndication`). The admin entry is `/syndication/menu`.

It started as an eZ Systems extension and was released in public, ported to PHP 8 and documented by 7x in September
2025 (1.1.0).

## Set up an export (the main site)

1. Install the extension, create the tables (`sql/mysql.sql`, or `share/db_schema.dba` for every database since
   1.3.1) and activate it.
2. Open `/syndication/menu`, choose **Export**, then **New Feed**, and complete the wizard. A subtree source includes
   the subtree root.
3. Run the cronjob part regularly. Every 3 minutes is suggested, since it carries much load once running:

```bash
php runcronjobs.php export_feed
```

## Set up an import (the second site)

1. Install and activate the extension on the second site.
2. Open `/syndication/menu`, choose **Import**, and create an import with the feed's server address and the node of
   your site that receives the content.
3. If the source is protected, give a login and password (see below).
4. Run the cronjob part by hand first and review the first import:

```bash
php runcronjobs.php import_feed
```

5. Then run it regularly from cron. After the first run, imports are faster and need no regular watching.

## Protect a source with HTTP authentication

Since 1.2.0 (19 July 2026) the import sends a **login and password** to the exporting site, so the SOAP endpoint can be
protected by HTTP access control. Give them either:

- in the server address: `https://<login>:<password>@<host>/`, or
- in the **Login** and **Password** fields of the import's first step.

The port defaults to 443 for `https` and 80 otherwise. Keep the credentials of the import out of version control.

## Settings

| File | Block | Key | Default | Meaning |
|---|---|---|---|---|
| `syndication.ini` | `SyndicationFilters` | `FilterArray[]` | `Section`, `Attribute` | Filters available for a feed source or an import |
| `syndication.ini` | `Syndication` | `CacheDir` | `syndication` | Cache folder of feed files |
| `syndication.ini` | `Syndication` | `CronUser` | `14` | User id the cronjob parts run as |
| `soap.ini` | `GeneralSettings` | `EnableSOAP` | `true` | Serve SOAP |
| `cronjob.ini` | `CronjobPart-export_feed`, `CronjobPart-import_feed` | | `syndication_export.php`, `syndication_import.php` | The two parts |

A kernel patch (`kernel_patch/addrelated.diff`) is optional but recommended. Install it as a **kernel override class**,
not as a manual kernel edit.

## What changed in the Exponential 6 releases

| Version | Date | Change |
|---|---|---|
| 1.1.0 | 13 September 2025 | The PHP 5 extension ported to PHP 8; debug statements and a stray `die()` removed. |
| 1.2.0 | 19 July 2026 | HTTP authentication of imports, and a left menu (Syndication, Feeds, Imports, Feed Sources) in the admin. |
| 1.3.0 | 22 September 2026 | Module views are safe on a persistent worker (Velocity). |
| 1.3.1 | 2 October 2026 | `share/db_schema.dba` describes the eleven syndication tables (columns, defaults, primary keys and the SOAP log's three indexes) in the engine-neutral schema format, so an installer can create them on every database Exponential supports, not only MySQL. Verified by loading it into a fresh SQLite database and by generating the MySQL and PostgreSQL schema from it. `ezinfo.php` and `extension.xml` state version, license and website. |
| 1.3.2 | | Commands and cronjob parts list a description; copyright notices name 1998 - 2026 7x & Exponential Foundation first. |

## Related pages

- [Specification](../../../specifications/6.0/syndication.md)
- [Velocity engines](../../../bc/6.0/velocity-engines.md)
- [CLI, cronjob and view abstractions](../../../bc/6.0/cli_cronjob_view_abstractions.md)
- [Extension metadata specification](../../../specifications/6.0/extension-metadata.md)
- [Chronicle](../../../history/extensions/syndication.md) and [release notes](../../../changelogs/extensions/syndication.md)
- [Change ledger](../../../history/ledger/syndication.md)
- [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
- Months: [2025-09](../../../history/extensions/months/2025-09.md), [2026-07](../../../history/extensions/months/2026-07.md), [2026-09](../../../history/extensions/months/2026-09.md), [2026-10](../../../history/extensions/months/2026-10.md) (all extensions)
