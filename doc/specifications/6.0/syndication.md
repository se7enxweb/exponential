# Specification: the syndication extension

This page is the reference for the `syndication` extension (1.3.3). One site **exports** part of its content tree
as a feed; another site **imports** that feed over SOAP and keeps it in step by cron. The page lists the module
views and policies, the SOAP functions, the tables, the cronjob parts, every setting and how an import
authenticates. Read it if you install, schedule or debug syndication. The step-by-step setup is on the
[feature page](../../features/6.0/extensions/syndication.md).

## In short

- Admin entry point: `/syndication/menu` (the **Syndication** tab of the admin): a dashboard with what is exported, what is imported, the last runs and the problems found.
- Two cronjob parts do the work: `export_feed` on the exporting site, `import_feed` on the importing site.
- The importing site calls the exporting site's SOAP server; since 1.2.0 it can send a login and password.
- Eleven tables, created from `sql/mysql.sql` or, on every database, from `share/db_schema.dba` (since 1.3.1).

## Example: run one export and one import by hand

After a feed and an import are set up in `/syndication/menu`, run each part once from the installation root and
read its output before you put it in cron:

```bash
# on the exporting site
php runcronjobs.php export_feed

# on the importing site
php runcronjobs.php import_feed
```

Then review the imported objects in **Syndication > Imports**. Run both parts from cron every few minutes; every
3 minutes is suggested for the export.

## Module `syndication`

Every view uses the navigation part `ezsyndicationnavigationpart` (since 1.3.3; it was `ezsyndicationpart`), so the
left menu comes from `design/standard/templates/parts/syndication/menu.tpl` in every admin design. The old
`parts/s/menu.tpl` stays and includes it.

| View | Policy function | Parameters | Script |
|---|---|---|---|
| `menu` | `menu` | `(job)` | `menu.php`: the dashboard; creates the tables; starts "export all" and "fetch all" in the background |
| `list` | `view_export` | `(offset)`, `(q)`, `(sort)`, `(order)` | `list.php`: filter, sort, page; remove asks for confirmation first |
| `import_list` | `view_export` | `(offset)`, `(q)`, `(sort)`, `(order)` | `import_list.php` |
| `import_edit` | `edit_import` | `ImportID`, `(step)` | `import_edit.php`: the five-step wizard |
| `import_info` | `view_import` | `ImportID`, `(offset)`, `(job)` | `import_info.php`: settings, item states, "fetch now", "import now" |
| `pending_edit` | `import_object_status` | `ImportID`, `(offset)`, `(statusFilter)` | `pending_edit.php` |
| `edit` | `edit_export` | `FeedID` | `edit.php`: validates the name, the identifier (unique) and the numbers |
| `feed_info` | `view_export_info` | `FeedID`, `(offset)`, `(job)` | `feed_info.php`: settings, sources, exported items, "export now" |
| `add_feed_source` | `edit_export` | `FeedID`, `Step`, `(source_type)` | `add_feed_source.php`; without a feed it sends you to the feed list |
| `list_source_filter` | `edit_export` | `SourceFeedID` | `list_source_filter.php` |
| `edit_source_filter` | `edit_export` | `SourceFilterID` | `edit_source_filter.php` |
| `edit_import_filter` | `edit_import` | `ImportFilterID` | `edit_import_filter.php` |
| `job` | `menu` | `JobID` | `job.php`: the state and log of a background run as JSON |

Policy functions: `import_object_status`, `view_export`, `edit_export`, `remove_feed`, `create_feed`, `menu`,
`view_import`, `edit_import`, `view_export_info`, `create_import`, and `fetch_feed` with the limitation `Feed`.
Before 1.3.3 `import_info` named the script `import_info` (no `.php`) and the function `import_view`, which the function
list did not define, and `feed_info` declared no script; both views are complete now.

## Console commands, cronjob parts and background runs (since 1.3.3)

The work is in runnable classes (the kernel's #207 pattern); the files in `bin/php/` and `cronjobs/` are one call each.
A run takes a lock (one export and one import at a time, whoever starts it), writes its time and result for the
dashboard and raises the kernel's runnable events, so the audit sees it like any kernel cronjob.

| Command | Class | Options | Does |
|---|---|---|---|
| `./console ext:syndication:export` (`@alias syn-export`) | `Exponential\Command\Extension\Syndication\Export` | `--dry-run`, `--feed=ID`, `--max-objects=N` | Writes the export cache of the active feeds |
| `./console ext:syndication:import` (`@alias syn-import`) | `...\Import` | `--dry-run`, `--import=ID`, `--fetch-only`, `--import-only`, `--limit=N` | Fetches the item lists and imports the waiting items |
| `./console ext:syndication:install` | `...\Install` | `--dry-run` | Creates the tables from `share/db_schema.dba` |
| `./console ext:syndication:status` | `...\Status` | | Prints the dashboard as text |

| Cronjob part | Class | Stub |
|---|---|---|
| `export_feed` | `Exponential\Cronjob\Extension\Syndication\ExportFeed` | `cronjobs/syndication_export.php` |
| `import_feed` | `Exponential\Cronjob\Extension\Syndication\ImportFeed` | `cronjobs/syndication_import.php` |

The cronjob and the commands skip feeds and imports that are not active, and run as the user of `[Syndication] CronUser`.
The admin's "run now" buttons start the same command in the background with `expProcessTools` (`setsid`, the PHP
command line) and show its state and output on the page; state files are in `var/<site>/syndication/jobs/`, the newest 20 are kept.

## SOAP functions

`soap/initialize.php` registers these functions on the exporting site's SOAP server (`soap.ini`
`[ExtensionSettings] SOAPExtensions[]=syndication`):

| Function | Parameters | Used for |
|---|---|---|
| `fetchSyndicationFeedList` | | The feeds an importing site can choose from |
| `fetchSyndicationFeedItemList` | `feedID`, `modified` | The items of a feed changed since a time |
| `fetchSyndicationFeedObjectList` | `feedID` | The objects of a feed |
| `fetchSyndicationFeedContentObject` | `feedID`, `remoteID` | One object, by remote id |
| `fetchSyndicationFeedRelatedContentObject` | `feedID`, `remoteID`, `relatedRemoteID` | An object related to a feed object |
| `hostID` | | The identity of the exporting host |

## Classes

All in `classes/`: `eZSyndication`, `eZSyndicationFeed`, `eZSyndicationFeedCacheManager`, `eZSyndicationFeedItem`,
`eZSyndicationFeedItemExport`, `eZSyndicationFeedItemStatus`, `eZSyndicationFeedSource`,
`eZSyndicationFeedSourceFilter`, `eZSyndicationFilter`, `eZSyndicationImport`, `eZSyndicationImportFilter`. The
filters of `[SyndicationFilters]` are `eZFilterSection` and `eZFilterAttribute` in `classes/filter/`; both extend `eZSyndicationFilter`.

## Tables

Eleven tables, defined in `sql/mysql.sql` and, since 1.3.1, in the engine-neutral `share/db_schema.dba`, so an
installer can create them on every database Exponential supports. The SOAP log has three indexes.
(`sql/mysql.sql` also holds two `ezsyndicate_export_event` tables, commented out; they are not created.)

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

| Block in `cronjob.ini` | Script (`cronjobs/`) | Job |
|---|---|---|
| `[CronjobPart-export_feed]` | `syndication_export.php` | Writes the export feeds |
| `[CronjobPart-import_feed]` | `syndication_import.php` | Fetches and imports the remote feeds |

The extension adds itself to `[CronjobSettings] ExtensionDirectories[]`, so `runcronjobs.php` finds both scripts. Both are stubs that call the runnable classes above.

## Settings

All values are the extension's own defaults (scope: extension, `extension/syndication/settings/`). Override them in
`settings/override/` or a siteaccess.

| File | Block | Key | Default | Meaning |
|---|---|---|---|---|
| `syndication.ini` | `SyndicationFilters` | `FilterArray[]` | `Section`, `Attribute` | Filters offered for a feed source or an import |
| `syndication.ini` | `Syndication` | `CacheDir` | `syndication` | Cache folder of feed files |
| `syndication.ini` | `Syndication` | `CronUser` | `14` | User id the cronjob parts run as |
| `soap.ini` | `GeneralSettings` | `EnableSOAP` | `true` | Serve SOAP (needed on the exporting site) |
| `soap.ini` | `ExtensionSettings` | `SOAPExtensions[]` | `syndication` | Loads `soap/initialize.php` |
| `cronjob.ini` | `CronjobSettings` | `ExtensionDirectories[]` | `syndication` | Where the cronjob scripts are found |
| `cronjob.ini` | `CronjobPart-export_feed` | `Scripts[]` | `syndication_export.php` | The export part |
| `cronjob.ini` | `CronjobPart-import_feed` | `Scripts[]` | `syndication_import.php` | The import part |
| `browse.ini` | `SyndicationFeedSourceBrowse` | `StartNode`, `TopLevelNodes[]` | `content`; `content`, `media` | Where a feed source can be picked |
| `browse.ini` | `SyndicationSetImportPlacement` | `StartNode`, `TopLevelNodes[]` | `content`; `content`, `users`, `media` | Where an import can place content |
| `content.ini` | `CustomTagSettings` | `AvailableCustomTags[]` | `syndication` | The `syndication` custom tag |
| `menu.ini` | `TopAdminMenu` | `Tabs[]` | `syndication` | The admin tab; left menu links to `menu`, `list`, `import_list`, `add_feed_source` (each shown only to a user the policy lets in) |

## Import authentication (since 1.2.0)

`eZSyndicationImport` reads a login and password for the exporting site from the import's server address
(`https://<login>:<password>@<host>/`) or from the option array keys `login` and `password` (the **Login** and
**Password** fields of the import's first step). It connects on port 443 for `https` and on port 80 otherwise.
Protect the exporting site's SOAP endpoint with HTTP access control and keep these credentials out of version
control.

## Related pages

- [Feature page](../../features/6.0/extensions/syndication.md), [behaviour changes of the extensions](../../bc/6.0/extensions-behaviour-changes.md)
- [Cronjobs in the console](../../features/6.0/cronjobs-console.md), [Operating a site](../../guides/operating-a-site.md)
- [Chronicle](../../history/extensions/syndication.md), [release notes](../../changelogs/extensions/syndication.md)
- Months: [2025-09](../../history/extensions/months/2025-09.md), [2026-03](../../history/extensions/months/2026-03.md), [2026-07](../../history/extensions/months/2026-07.md), [2026-09](../../history/extensions/months/2026-09.md), [2026-10](../../history/extensions/months/2026-10.md)
