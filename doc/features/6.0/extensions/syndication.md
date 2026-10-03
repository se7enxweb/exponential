# syndication: share content between installations

`syndication` connects separate Exponential installations so that part of the content tree of one site is **exported as a feed** and
**imported** by another, kept in step by cron. It supports:

* **One-way syndication**: for example shared content or authentication data between separate databases and client sites;
* **Two-way syndication**: for example forums, portal networks, social platforms and large e-commerce networks.

The feeds are cached XML plus binary files, one file per exported object, served through a **SOAP** interface (`soap.ini`
`EnableSOAP=true`, `SOAPExtensions[]=syndication`). It started as an eZ Systems extension and was released in public, ported to PHP 8 and
documented by 7x in September 2025 (1.1.0). Admin entry: `/syndication/menu`.

## Set up an export (the main site)

1. Install the extension, create the tables (`sql/mysql.sql`, or `share/db_schema.dba` for every database since 1.3.1), activate it.
2. Open `/syndication/menu`, choose **Export**, **New Feed**, complete the wizard (a subtree source includes the subtree root).
3. Start the cronjob part regularly (every 3 minutes is suggested, since it carries much load once running):

```bash
php runcronjobs.php export_feed
```

## Set up an import (the second site)

1. Install and activate the extension on the second site.
2. `/syndication/menu`, **Import**, create an import with the feed's server address and the node of your site that receives the content.
3. In 1.2.0 an import can **authenticate** to a protected source (see below).
4. Start `php runcronjobs.php import_feed` regularly. Run both cronjob parts **by hand first** and review the first import; afterwards imports are
   faster and need no regular watching.

## Protect a source with HTTP authentication (1.2.0, 19 July 2026)

The import now sends a **login and password** to the exporting site, so the SOAP endpoint can be protected by HTTP access control. Give them
either in the server address (`https://<login>:<password>@<host>/`) or in the new **Login** and **Password** fields of the import's first
step. The port defaults to 443 for `https` and 80 otherwise. Keep the credentials of the import out of version control.

## Settings

| File | Block and key | Default | Meaning |
|---|---|---|---|
| `syndication.ini` | `[SyndicationFilters] FilterArray[]` | `Section`, `Attribute` | Filters available for a feed source or an import |
| `syndication.ini` | `[Syndication] CacheDir` | `syndication` | Cache folder of feed files |
| `syndication.ini` | `[Syndication] CronUser` | `14` | User id the cronjob parts run as |
| `soap.ini` | `[GeneralSettings] EnableSOAP` | `true` | Serve SOAP |
| `cronjob.ini` | `[CronjobPart-export_feed]`, `[CronjobPart-import_feed]` | `syndication_export.php`, `syndication_import.php` | The two parts |

A kernel patch (`kernel_patch/addrelated.diff`) is optional but recommended; install it as a **kernel override class**, not a manual kernel edit.

## What changed in the Exponential 6 releases

* 1.1.0 (13 September 2025): the PHP 5 extension ported to PHP 8, debug statements and a stray `die()` removed.
* 1.2.0 (19 July 2026): HTTP authentication of imports, and a left menu (Syndication, Feeds, Imports, Feed Sources) for the admin.
* 1.3.0 (22 September): module views are safe on a persistent worker (Velocity).
* 1.3.1 (2 October): `share/db_schema.dba` describes the eleven syndication tables (columns, defaults, primary keys and the SOAP log's three
  indexes) in the engine-neutral schema format, so an installer can create them on every database Exponential supports, not only MySQL; verified by
  loading it into a fresh SQLite database and by generating the MySQL and PostgreSQL schema from it. `ezinfo.php` and `extension.xml` state version,
  license and website.
* 1.3.2: commands and cronjob parts list a description; copyright notices name 1998 - 2026 7x & Exponential Foundation first.

## Related

* [Chronicle](../../../history/extensions/syndication.md) and [release notes](../../../changelogs/extensions/syndication.md)
* [Change ledger](../../../history/ledger/syndication.md)
* [Specification](../../../specifications/6.0/syndication.md)
* [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
* [Velocity engines](../../../bc/6.0/velocity-engines.md)
* [CLI, cronjob and view abstractions](../../../bc/6.0/cli_cronjob_view_abstractions.md)
* [Extension metadata specification](../../../specifications/6.0/extension-metadata.md)
* [Month: 2025-09 (all extensions)](../../../history/extensions/months/2025-09.md)
* [Month: 2026-07 (all extensions)](../../../history/extensions/months/2026-07.md)
* [Month: 2026-09 (all extensions)](../../../history/extensions/months/2026-09.md)
* [Month: 2026-10 (all extensions)](../../../history/extensions/months/2026-10.md)
