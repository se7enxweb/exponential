# syndication specification

Reference for the `syndication` extension (1.3.2). How to use it is on the [feature page](../../features/6.0/extensions/syndication.md).

## Tables

Eleven tables, defined in `sql/mysql.sql` and, since 1.3.1, in the engine-neutral `share/db_schema.dba` (so an installer can create them on every database Exponential supports).
The SOAP log has three indexes.

| Table | Content |
|---|---|
| `ezsyndication_feed` | A feed (an export definition) |
| `ezsyndication_feed_source` | The content tree sources of a feed |
| `ezsyndication_feed_source_filter` | Filters of a feed source |
| `ezsyndication_filter` | Filter definitions |
| `ezsyndication_feed_item` | The objects of a feed |
| `ezsyndication_feed_item_export` | Export state per item |
| `ezsyndication_feed_item_status` | Status of an item |
| `ezsyndication_feed_cache` | Cached feed files |
| `ezsyndication_import` | An import definition (server, login, option array, target node) |
| `ezsyndication_import_filter` | Filters of an import |
| `ezx_ezpnet_soap_log` | Log of SOAP calls |

## Cronjob parts

`[CronjobPart-export_feed]` runs `syndication_export.php`; `[CronjobPart-import_feed]` runs `syndication_import.php`. Run them from cron every few minutes once a syndication is set up.

## Settings

See the [feature page](../../features/6.0/extensions/syndication.md#settings): `syndication.ini` (`SyndicationFilters`, `Syndication` with `CacheDir` and `CronUser`), `soap.ini` (`EnableSOAP`, `SOAPExtensions[]`),
`cronjob.ini`.

## Import authentication (1.2.0)

`eZSyndicationImport` reads a login and password for the exporting site from the import's server address (`user:password@host`) or from the option array keys `login` and `password`, and
connects on port 443 for `https` and 80 otherwise.

## Related

* [Feature page](../../features/6.0/extensions/syndication.md), [chronicle](../../history/extensions/syndication.md), [release notes](../../changelogs/extensions/syndication.md)

## See also

* [behaviour changes of the extensions](../../bc/6.0/extensions-behaviour-changes.md)
* months: [2025-09](../../history/extensions/months/2025-09.md), [2026-03](../../history/extensions/months/2026-03.md), [2026-07](../../history/extensions/months/2026-07.md), [2026-09](../../history/extensions/months/2026-09.md), [2026-10](../../history/extensions/months/2026-10.md)
