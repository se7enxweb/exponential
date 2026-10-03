# Specification: the syndication extension

This page is the reference for the `syndication` extension (1.3.2), which exports content as feeds and imports
feeds from another site: its tables, cronjob parts, settings and how an import authenticates. Read it if you
install, schedule or debug syndication. How to use it is on the
[feature page](../../features/6.0/extensions/syndication.md).

## Tables

Eleven tables, defined in `sql/mysql.sql` and, since 1.3.1, in the engine-neutral `share/db_schema.dba`, so an
installer can create them on every database Exponential supports. The SOAP log has three indexes.

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

| Block in `cronjob.ini` | Script | Job |
|---|---|---|
| `[CronjobPart-export_feed]` | `syndication_export.php` | Writes the export feeds |
| `[CronjobPart-import_feed]` | `syndication_import.php` | Fetches and imports the remote feeds |

Run both from cron every few minutes once a syndication is set up.

## Settings

The keys and their defaults are listed on the [feature page](../../features/6.0/extensions/syndication.md#settings).

| File | Block | Keys |
|---|---|---|
| `syndication.ini` | `SyndicationFilters` | the filter list |
| `syndication.ini` | `Syndication` | `CacheDir`, `CronUser` |
| `soap.ini` | `GeneralSettings` | `EnableSOAP` (`true` in the extension) |
| `soap.ini` | `ExtensionSettings` | `SOAPExtensions[]` (`syndication`) |
| `cronjob.ini` | `CronjobPart-export_feed`, `CronjobPart-import_feed` | the two parts above |

## Import authentication (since 1.2.0)

`eZSyndicationImport` reads a login and password for the exporting site from the import's server address
(`user:password@host`) or from the option array keys `login` and `password`. It connects on port 443 for `https`
and on port 80 otherwise.

## Related pages

- [Feature page](../../features/6.0/extensions/syndication.md), [behaviour changes of the extensions](../../bc/6.0/extensions-behaviour-changes.md)
- [Chronicle](../../history/extensions/syndication.md), [release notes](../../changelogs/extensions/syndication.md)
- Months: [2025-09](../../history/extensions/months/2025-09.md), [2026-03](../../history/extensions/months/2026-03.md), [2026-07](../../history/extensions/months/2026-07.md), [2026-09](../../history/extensions/months/2026-09.md), [2026-10](../../history/extensions/months/2026-10.md)
