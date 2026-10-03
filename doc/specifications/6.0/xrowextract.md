# xrowextract specification

Reference for the `xrowextract` extension, release line 2.5 (2.5.0 to 2.5.6,
September to October 2026). How to use it is in the
[feature page](../../features/6.0/extensions/xrowextract.md); this page lists every
setting, policy, table, view, class and extension point.

## Module, views and policies

Module `xrowextract` (`modules/xrowextract/module.php`), navigation part
`ezextractnavigationpart`, top menu tab `xrowextract` (name "Export",
`menu.ini [Topmenu_xrowextract]`, URL `xrowextract/csv`).

| View | Policy function | Purpose |
|---|---|---|
| `csv` | `csv` | One class export page: preview, download, manifest, run in background, export as package |
| `archive` | `csv` | Site archive (multi class) |
| `jobs` | `jobs` | Jobs page; delete, cancel, export again |
| `job_status` | `jobs` | JSON progress of one job (`JobID`) |
| `job_download` | `jobs` | Download a job's file or log (`JobID`, `What`) |
| `import` | `import` | Import content file; chunked upload entry; resume |
| `upload_chunk` | `import` | Endpoint of the chunked uploader |
| `package` | `import` | Content packages: upload, inspect, install, build template (`PackageName`) |
| `browse`, `browse_file` | `import` | Package contents browser (`PackageName`, `Offset`, `ViewIndex`) |
| `compare` | `import` | Compare a package with the site or another package |
| `schedules` | `schedule` | Scheduled exports and imports |
| `destinations` | `destinations` | Delivery destinations and credentials |
| `history` | `history` | Export history |

Policy functions not bound to a single view: `all_jobs` (see and manage everyone's
jobs and shared presets; without it a user sees only their own) and
`password_hash` (export the hash and hash type of user accounts). A link to the
contents browser is added to the kernel's own `package/view/full/<name>`.

## Settings

All keys are read with `eZINI::instance( <file> )`. Scope is the whole installation
unless noted; override them in `settings/override/<file>.append.php` or in a
siteaccess.

### csv.ini

| Block | Key | Default | Meaning |
|---|---|---|---|
| General | `ExportableDatatypes[]` | 30 datatypes (ezboolean, eztext, ezinteger, ezstring, eztime, ezurl, ezuser, ezxmltext, ezdate, ezdatetime, ezkeyword, eztags, ezobjectrelation, ezemail, ezfloat, ezidentifier, ezenhancedobjectrelation, ezenhancedselection, ezselection, ezenum, ezcountry, ezimage, ezmedia, ezbinaryfile, ezmatrix, ezobjectrelationlist, hmregexpline, ezprice, xrowmetadata) | Datatypes the export offers |
| General | `StripURLText` | `true` | Export only the URL of an `ezurl` value |
| General | `NeutralizeFormulas` | `enabled` | A cell starting with `=`, `+`, `-` or `@` (not a number) gets a leading `'` |
| General | `AllowPasswordHashExport` | `enabled` | `disabled` removes the password hash columns for everyone; otherwise only holders of `xrowextract/password_hash` |
| Jobs | `RetentionDays` | `7` | Days a finished or failed job is kept before `ext:xrowextract:job --clean` removes it |
| Jobs | `PhpCli` | empty | PHP command line binary for background jobs. Empty: `PHP_BINARY`, mapped from a PHP-FPM or php-cgi binary to the CLI one next to it, else `php` |
| Manifest | `EmbedInJSON` | `enabled` | JSON export as envelope `{"manifest","rows","summary"}`; `disabled` writes a plain array |
| Manifest | `EmbedInXML` | `enabled` | A `<manifest>` element after `<columns>` |
| `<datatype>` | `HandlerFile`, `HandlerClass` | one block per datatype | Handler of that datatype; replace from your extension. `[ezenhancedobjectrelation] OutputRelatedObjectNames=true` writes names, `false` ids |
| Uploads | `RetentionHours` | `24` | Hours an unfinished or never adopted chunked upload is kept |
| Uploads | `MinFreeMarginMB` | `256` | Free disk space that must remain beyond the incoming file |
| Uploads | `QueueThresholdRows` | `2000` | Above this many rows the dry run and the apply run as background jobs |
| Uploads | `QueueThresholdMB` | `20` | The same, by file size |
| Uploads | `JsonOneShotThresholdMB` | `20` | JSON above this size is still read in one go (`json_decode()` cannot stream); CSV and XML never are |

### export.ini

| Block | Key | Default | Meaning |
|---|---|---|---|
| ExportSettings | `ExportClasses[]` | empty (all classes) | Restrict the classes the page offers |
| ExportSettings | `StartNodeID` | empty (root node of the default siteaccess) | Node the page starts with |
| ExportSettings | `DefaultClassID` | empty (class with most objects) | Class the page starts with |
| ExportSettings | `PreselectAttributes` | `true` | Tick every attribute |
| ExportSettings | `Limit`, `Offset` | `0`, `0` | Row window (0 is unlimited) |
| SiteArchive | `SitesParentNodeID` | empty | Node whose children are the "Sites" set; empty uses the content structure |
| SiteArchive | `DefaultSiteNodeIDs[]` | empty | Sites selected at the start; empty uses the default siteaccess root |
| PackageTemplate | `ScratchNodeID` | empty (`content.ini [NodeSettings] MediaRootNode`) | Where the sample-content builder creates its throwaway objects. Never a node the public site renders |

### xrowextract.ini

| Block | Key | Default | Meaning |
|---|---|---|---|
| Schedules | `CronjobEveryMinutes` | `5` | Interval of the crontab line shown on the Schedules page |
| History | `RetentionDays` | `365` | Days history rows are kept (a schedule can set its own) |
| History | `PageSize` | `25` | Rows per page |
| Destinations | `LocalPathRoots[]` | empty | The only folders a local/NAS destination may write to, and a scheduled import may read from. Absolute, or relative to the installation root |
| Destinations | `MaxAttempts` | `3` | Delivery attempts |
| Destinations | `BackoffSeconds` | `10` | Wait between attempts, doubling each time |
| Destinations | `SshBinaryDir` | empty | Where `sftp`, `ssh-keyscan` and `ssh-keygen` are, when not in `/usr/bin` or `/usr/local/bin` |
| Secrets | `KeyFile` | `settings/override/xrowextract-secrets.key` | libsodium secretbox key for destination credentials, generated with mode 0600 on first use |
| Notifications | `WebhookSecret` | empty | When set, webhooks carry `X-Xrowextract-Signature` and `X-Xrowextract-Timestamp` |
| `Preset_<id>` | `Name`, `Description`, `Audience`, `View`, `Extends`, `Definition`, `Placeholders` | | Site presets; see below |

### Other files

| File | Block | Purpose |
|---|---|---|
| `cronjob.ini` | `[CronjobPart-xrowextract] Scripts[]=xrowextract.php` | Cronjob part that starts due schedules, then removes job folders and history rows past retention |
| `extendedattributefilter.ini` | `XrowExtractTranslation`, `XrowExtractHasChildren`, `XrowExtractRelation`, `XrowExtractUserStatus` | Registered extended attribute filters (`createSqlParts`). The translation filter is deliberately not user-choosable: it is applied once per exported language |
| `fetchalias.ini` | `xrowextract_recent_content`, `xrowextract_by_section` | Example named fetches the export can apply |
| `site.ini` | `[RegionalSettings] TranslationExtensions[]=xrowextract` | Translations: `eng-US`, `fre-FR`, `ger-DE`, `ita-IT`, `untranslated` |

## Preset definition

`Definition` is one JSON object. Fields read by `XrowExtractPreset::resolve()`:
`scope`, `subtree`, `subtree_remote_id`, `class_id`, `class_identifier`,
`mainnodeonly`, `limit`, `offset`, `languages`, `attributes`, `filters` (an
`XrowExtractFilters` values array), `sort_field`, `sort_ascending`, `sort_field2`,
`sort_ascending2`, `output_format`, `separator`, `line_separator`, `escape`. A
string may contain `{name}`; `Placeholders` is `{"name": {"default": "..."}}`.
`Extends` is `alias:<fetchalias name>[:<siteaccess>]` or `user:<id>`. A person's
own presets are stored as JSON in `ezsite_data` under the name
`xrowextract_preset_<20 hex>`. The `Audience` groups are Site, Editors,
Developers, Partners, Users and Maintenance; a preset without one is grouped under
Site.

## Tables

Created on first use from `share/db_schema.dba` (the engine-neutral schema; `sql/mysql`,
`sql/oracle`, `sql/postgresql`, `sql/sqlite` carry the same for a manual install).

| Table | Content |
|---|---|
| `xrowextract_schedule` (+ index `xrowextract_schedule_next`) | Saved schedules and their next run |
| `xrowextract_destination` | Delivery destinations; credentials encrypted |
| `xrowextract_history` (+ indexes `_created`, `_job`, `_sched`) | One row per run |

## Classes

| Class | Role |
|---|---|
| `XrowExtractFilters`, `XrowExtractColumns`, `XrowExtractCatalogue`, `XrowExtractSchema` | Filters, columns, column catalogue and class schema |
| `XrowExtractWriter`, `XrowExtractManifest` | File writing (CSV, JSON, XML) and the typed manifest |
| `XrowExtractArchive` | Site archive; formats zip, tar.gz, tar.bz2, tar.xz, 7z, rar |
| `XrowExtractImport`, `XrowExtractUpload` | Streaming importer and chunked upload (`ID_PATTERN` `^[0-9a-f]{32}$`; folder per upload, chunk appended only at the offset the server already holds) |
| `XrowExtractPackage` | Content packages (`.ezpkg`): inspect, compare, install, template build |
| `XrowExtractJob`, `XrowExtractCron`, `XrowExtractScheduler`, `XrowExtractSchedule` | Background jobs and schedules |
| `XrowExtractDestination`, `XrowExtractSecrets`, `XrowExtractNotifier`, `XrowExtractHistory` | Delivery, encrypted credentials, notifications, history |
| `XrowExtractPreset`, `XrowExtractFetchAlias` | Presets and named fetch support |
| `XrowExtractRequirements` | The 19 requirements and 12 features check |
| `classes/parsers/*` | One handler per datatype (`XroweZStringHandler`, ...) |
| `classes/transports/*` | `local`, `ftp`, `sftp`, `s3`, `webdav`, `http` transports |

Since 2.5.5 the command line scripts, the cronjob part and the module views are
classes that the files call (`Exponential\Command\Extension\Xrowextract\...`); see
[CLI, cronjob and view abstractions](../../bc/6.0/cli_cronjob_view_abstractions.md).
Behaviour did not change.

## Command line options

`ext:xrowextract:csv` takes class, node, scope (`node` or `all`), depth and depth
operator (`eq`, `le`, `ge`), main-only, offset, limit, columns, add, sets
(`identity`, `urls`, `publishing`, `location`, `migration`), names, separator,
line endings (`win32`/`crlf`, `unix`/`lf`, `mac`/`cr`), unquoted, languages,
format (`csv`, `json`, `xml`, `ezpkg`), date filters (`date-field`, `since`,
`before`, `date`), section, state, visibility, name, where, sort, order, sort2,
order2, extended-filter, extended-params, fetch-alias, alias-param, preset, param,
list-presets, show-preset, output (`-` is stdout), preview, list-classes,
list-columns, user, progress-file, keep, changed-since, lenient, no-manifest,
schedule and run-mode.

Exit codes: `0` success, `1` failure (bad option, unresolvable class or node),
`3` `--lenient` and nothing was left to export. Warnings are printed as
`WARNING: ...` lines; with `--output=-` they go to stderr so stdout stays the file.

The check script `bin/check.sh` is the release gate (lint, translations, duplicate
sources, PHPStan level 8, unit tests, requirements, CLI, view and POST tests);
`.github/workflows/check.yml` runs it on PHP 8.1 to 8.5. It needs
`EXPONENTIAL_ROOT` to point at an Exponential root.

## Related

* [Feature page](../../features/6.0/extensions/xrowextract.md)
* [Chronicle](../../history/extensions/xrowextract.md) and [release notes](../../changelogs/extensions/xrowextract.md)
