---
title: Patterns in extension/xrowextract
audience: developer
category: standards
order: 476
summary: Every class, view, fetch function, operator and plugin point of extension/xrowextract with its design patterns.
keywords: extension/xrowextract, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/xrowextract

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/xrowextract` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (91)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `Exponential\Command\Extension\Xrowextract\Archive` | class extends Command | [Command (console, scripts, cronjobs)](command.md) | `extension/xrowextract/classes/runnable/commands/php_archive.php:30` | The code of extension/xrowextract/bin/php/archive.php, moved into a class (#207 stage 1). |
| `Exponential\Command\Extension\Xrowextract\Csv` | class extends Command | [Command (console, scripts, cronjobs)](command.md) | `extension/xrowextract/classes/runnable/commands/php_csv.php:28` | The code of extension/xrowextract/bin/php/csv.php, moved into a class (#207 stage 1). |
| `Exponential\Command\Extension\Xrowextract\Destination` | class extends Command | [Command (console, scripts, cronjobs)](command.md) | `extension/xrowextract/classes/runnable/commands/php_destination.php:31` | The code of extension/xrowextract/bin/php/destination.php, moved into a class (#207 stage 1). |
| `Exponential\Command\Extension\Xrowextract\History` | class extends Command | [Command (console, scripts, cronjobs)](command.md) | `extension/xrowextract/classes/runnable/commands/php_history.php:23` | The code of extension/xrowextract/bin/php/history.php, moved into a class (#207 stage 1). |
| `Exponential\Command\Extension\Xrowextract\Import` | class extends Command | [Command (console, scripts, cronjobs)](command.md) | `extension/xrowextract/classes/runnable/commands/php_import.php:30` | The code of extension/xrowextract/bin/php/import.php, moved into a class (#207 stage 1). |
| `Exponential\Command\Extension\Xrowextract\Job` | class extends Command | [Command (console, scripts, cronjobs)](command.md) | `extension/xrowextract/classes/runnable/commands/php_job.php:27` | The code of extension/xrowextract/bin/php/job.php, moved into a class (#207 stage 1). |
| `Exponential\Command\Extension\Xrowextract\Package` | class extends Command | [Command (console, scripts, cronjobs)](command.md) | `extension/xrowextract/classes/runnable/commands/php_package.php:46` | The code of extension/xrowextract/bin/php/package.php, moved into a class (#207 stage 1). |
| `Exponential\Command\Extension\Xrowextract\Requirements` | class extends Command | [Command (console, scripts, cronjobs)](command.md) | `extension/xrowextract/classes/runnable/commands/php_requirements.php:29` | The code of extension/xrowextract/bin/php/requirements.php, moved into a class (#207 stage 1). |
| `Exponential\Command\Extension\Xrowextract\Schedule` | class extends Command | [Command (console, scripts, cronjobs)](command.md) | `extension/xrowextract/classes/runnable/commands/php_schedule.php:29` | The code of extension/xrowextract/bin/php/schedule.php, moved into a class (#207 stage 1). |
| `Exponential\Cronjob\Extension\Xrowextract\Xrowextract` | class extends CronjobPart | [Command (console, scripts, cronjobs)](command.md) | `extension/xrowextract/classes/runnable/cronjobs/xrowextract.php:25` | The code of extension/xrowextract/cronjobs/xrowextract.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Xrowextract\Xrowextract\Archive` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/xrowextract/classes/runnable/views/xrowextract/archive.php:19` | The code of extension/xrowextract/modules/xrowextract/archive.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Xrowextract\Xrowextract\Browse` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/xrowextract/classes/runnable/views/xrowextract/browse.php:42` | The code of extension/xrowextract/modules/xrowextract/browse.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Xrowextract\Xrowextract\BrowseFile` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/xrowextract/classes/runnable/views/xrowextract/browse_file.php:30` | The code of extension/xrowextract/modules/xrowextract/browse_file.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Xrowextract\Xrowextract\Compare` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/xrowextract/classes/runnable/views/xrowextract/compare.php:25` | The code of extension/xrowextract/modules/xrowextract/compare.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Xrowextract\Xrowextract\Csv` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/xrowextract/classes/runnable/views/xrowextract/csv.php:95` | Read an export back the way a spreadsheet does (same separator and quoting) for the preview table: header, rows with a flag per cell, and what the pr… |
| `Exponential\View\Extension\Xrowextract\Xrowextract\Destinations` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/xrowextract/classes/runnable/views/xrowextract/destinations.php:19` | The code of extension/xrowextract/modules/xrowextract/destinations.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Xrowextract\Xrowextract\History` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/xrowextract/classes/runnable/views/xrowextract/history.php:20` | The code of extension/xrowextract/modules/xrowextract/history.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Xrowextract\Xrowextract\Import` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/xrowextract/classes/runnable/views/xrowextract/import.php:21` | The code of extension/xrowextract/modules/xrowextract/import.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Xrowextract\Xrowextract\JobDownload` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/xrowextract/classes/runnable/views/xrowextract/job_download.php:22` | The code of extension/xrowextract/modules/xrowextract/job_download.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Xrowextract\Xrowextract\Jobs` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/xrowextract/classes/runnable/views/xrowextract/jobs.php:19` | The code of extension/xrowextract/modules/xrowextract/jobs.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Xrowextract\Xrowextract\JobStatus` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/xrowextract/classes/runnable/views/xrowextract/job_status.php:19` | The code of extension/xrowextract/modules/xrowextract/job_status.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Xrowextract\Xrowextract\Package` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/xrowextract/classes/runnable/views/xrowextract/package.php:24` | The code of extension/xrowextract/modules/xrowextract/package.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Xrowextract\Xrowextract\Schedules` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/xrowextract/classes/runnable/views/xrowextract/schedules.php:21` | The code of extension/xrowextract/modules/xrowextract/schedules.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Xrowextract\Xrowextract\SubtreeClasses` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/xrowextract/classes/runnable/views/xrowextract/subtree_classes.php:13` | The code of extension/xrowextract/modules/xrowextract/subtree_classes.php: the classes below a node for the content tree's Advanced › Export › "Subtr… |
| `Exponential\View\Extension\Xrowextract\Xrowextract\UploadChunk` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/xrowextract/classes/runnable/views/xrowextract/upload_chunk.php:23` | The code of extension/xrowextract/modules/xrowextract/upload_chunk.php, moved into a class (#207 stage 1). |
| `ParserInterface` | class | – | `extension/xrowextract/classes/parserinterface.php:8` | – |
| `XrowBaseHandler` | class | [Strategy / handler](strategy-handler.md) | `extension/xrowextract/classes/xrowbasehandler.php:3` | – |
| `XrowExtractArchive` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `extension/xrowextract/classes/xrowextractarchive.php:12` | The site archive: the objects of many classes below many nodes, one CSV file per class, with a manifest, packed as zip, tar.gz, tar.bz2, tar.xz, 7z o… |
| `XrowExtractCatalogue` | class | [Utility (static helper)](utility.md) | `extension/xrowextract/classes/xrowextractcatalogue.php:13` | The column catalogue beyond the class attributes as they are: |
| `XrowExtractColumns` | class | [Utility (static helper)](utility.md) | `extension/xrowextract/classes/xrowextractcolumns.php:8` | The columns of an export and their values: the special (non class attribute) columns, a class's attribute columns, and one object as a row of CSV cel… |
| `XrowExtractCron` | class | [Utility (static helper)](utility.md) | `extension/xrowextract/classes/xrowextractcron.php:16` | When a schedule runs: the simple choices of the Schedules page (hourly at minute M, daily at HH:MM, weekly on a day at HH:MM, monthly on a day at HH:… |
| `XrowExtractDestination` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `extension/xrowextract/classes/xrowextractdestination.php:9` | A named, reusable delivery destination (table xrowextract_destination). |
| `XrowExtractEntry` | class | [Utility (static helper)](utility.md) | `extension/xrowextract/classes/xrowextractentry.php:12` | The entry parameters of xrowextract/csv: the content tree's Advanced › Export menu opens the CSV form pre-filled for a node with xrowextract/csv/(nod… |
| `XrowExtractFetchAlias` | class | [Utility (static helper)](utility.md) | `extension/xrowextract/classes/xrowextractfetchalias.php:18` | Named fetches from fetchalias.ini usable in the extract view and the CLI: a "tree" / "list" / "tree_count" / "list_count" alias's Constant values rea… |
| `XrowExtractFileModes` | trait | [Trait (mixin)](trait.md) | `extension/xrowextract/classes/xrowextractfilemodes.php:14` | The modes the extension gives its files and directories, within the limits EZP_FILE_MODE_MAX / EZP_DIR_MODE_MAX the kernel reads from config.php (see… |
| `XrowExtractFilters` | class | [Decorator / filter](decorator.md), [Factory and static factory methods](factory.md) | `extension/xrowextract/classes/xrowextractfilters.php:16` | Filters of an export: a date range (published, modified, or a date attribute of the class; any, today, the last N days, since, before, between, futur… |
| `XrowExtractFunctionCollection` | class | [Fetch function (Query Object, Facade)](fetch-function.md), [Utility (static helper)](utility.md) | `extension/xrowextract/classes/xrowextractfunctioncollection.php:4` | The xrowextract module's template fetch functions (modules/xrowextract/function_definition.php). |
| `XrowExtractHasChildrenFilter` | class | [Decorator / filter](decorator.md), [INI plugin point (extension point)](ini-plugin-point.md) | `extension/xrowextract/classes/xrowextracthaschildrenfilter.php:11` | Extended attribute filter "XrowExtractHasChildren": only objects whose node has at least one child (leaf content otherwise). |
| `XrowExtractHistory` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `extension/xrowextract/classes/xrowextracthistory.php:12` | The export history index (table xrowextract_history): one row per run - a background job (started by hand, from the command line or by a schedule) or… |
| `XrowExtractImport` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `extension/xrowextract/classes/xrowextractimport.php:22` | The other direction of the export: reads a CSV or JSON file written by XrowExtractWriter (any column set, in particular "migration") back into conten… |
| `xrowextractInfo` | class | [Value object / struct](value-object.md) | `extension/xrowextract/ezinfo.php:10` | – |
| `XrowExtractJob` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `extension/xrowextract/classes/xrowextractjob.php:16` | A background export job: one folder per job below the var directory (var//xrowextract-jobs//), holding job.json (state), progress.json (written by th… |
| `XrowExtractManifest` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `extension/xrowextract/classes/xrowextractmanifest.php:20` | The typed column manifest of an export: what every column of a file is (its id, the name in the file, the datatype, the chosen format and language),… |
| `XrowExtractNotifier` | class | [Utility (static helper)](utility.md) | `extension/xrowextract/classes/xrowextractnotifier.php:13` | What a schedule's owner (and whoever else it names) hears about a run: - e-mail on failure (a failed or skipped run, or a failed delivery): always to… |
| `XrowExtractNotifierHttp` | class extends XrowExtractTransport | – | `extension/xrowextract/classes/xrowextractnotifier.php:216` | The webhook's HTTP call: XrowExtractTransport's curl helper with a JSON body. |
| `XrowExtractPackage` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `extension/xrowextract/classes/xrowextractpackage.php:18` | Content-package (.ezpkg) support for xrowextract/package: inspecting a package's content classes and content objects before anything is written (the… |
| `XrowExtractPreset` | class | [Utility (static helper)](utility.md) | `extension/xrowextract/classes/xrowextractpreset.php:25` | A saved export preset: a complete, named CSV or archive export definition (scope, node(s), class or sets, columns, languages, every filter, sort, out… |
| `XrowExtractRelationFilter` | class | [Decorator / filter](decorator.md), [INI plugin point (extension point)](ini-plugin-point.md) | `extension/xrowextract/classes/xrowextractrelationfilter.php:17` | Extended attribute filter "XrowExtractRelation": only objects that relate to a given object (relation_type ignored — any relation counts, including o… |
| `XrowExtractRequirements` | class | [Utility (static helper)](utility.md) | `extension/xrowextract/classes/xrowextractrequirements.php:19` | What the extension needs from PHP and the server, in one place: each requirement (a PHP version, a PHP extension, a function a hosting may disable, a… |
| `XrowExtractSchedule` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `extension/xrowextract/classes/xrowextractschedule.php:20` | A scheduled export or import (table xrowextract_schedule, created lazily by XrowExtractSchema). |
| `XrowExtractScheduler` | class | [Utility (static helper)](utility.md) | `extension/xrowextract/classes/xrowextractscheduler.php:15` | Runs schedules: finds the due ones (the cronjob part, cronjobs/xrowextract.php, or bin/php/schedule.php --cron), turns each into an ordinary backgrou… |
| `XrowExtractSchema` | class | [Utility (static helper)](utility.md) | `extension/xrowextract/classes/xrowextractschema.php:11` | The extension's own tables (xrowextract_schedule, xrowextract_destination, xrowextract_history), created lazily and idempotently the first time one o… |
| `XrowExtractSecrets` | class | [Utility (static helper)](utility.md) | `extension/xrowextract/classes/xrowextractsecrets.php:15` | Destination credentials at rest: libsodium secretbox (XSalsa20-Poly1305) with a 32 byte key kept in a key file outside the database. |
| `XrowExtractTranslationFilter` | class | [Decorator / filter](decorator.md), [INI plugin point (extension point)](ini-plugin-point.md) | `extension/xrowextract/classes/xrowextracttranslationfilter.php:24` | Extended attribute filter "XrowExtractTranslation": only objects that have a translation in the given language. |
| `XrowExtractTransport` | abstract class | [Strategy / handler](strategy-handler.md), [Template Method / abstract base class](template-method.md) | `extension/xrowextract/classes/transports/xrowextracttransport.php:10` | One way of delivering a file to (or, for a scheduled import, fetching one from) a destination. |
| `XrowExtractTransportFtp` | class extends XrowExtractTransport | – | `extension/xrowextract/classes/transports/xrowextracttransportftp.php:9` | FTP and FTPS through the curl extension: plain FTP (unencrypted: user, password and file travel in clear text - the Destinations page says so), expli… |
| `XrowExtractTransportHttp` | class extends XrowExtractTransport | – | `extension/xrowextract/classes/transports/xrowextracttransporthttp.php:10` | HTTP POST of the file (a webhook upload): multipart/form-data with the file ("file" by default), its manifest ("manifest", when there is one) and a J… |
| `XrowExtractTransportLocal` | class extends XrowExtractTransport | – | `extension/xrowextract/classes/transports/xrowextracttransportlocal.php:8` | A local folder, or a NAS share mounted on this server. |
| `XrowExtractTransportS3` | class extends XrowExtractTransport | – | `extension/xrowextract/classes/transports/xrowextracttransports3.php:9` | S3 compatible object storage (AWS S3, MinIO, Wasabi, ...) with AWS Signature Version 4, written here directly (no SDK, no composer): PUT Object with… |
| `XrowExtractTransportSftp` | class extends XrowExtractTransport | – | `extension/xrowextract/classes/transports/xrowextracttransportsftp.php:18` | SFTP. |
| `XrowExtractTransportWebdav` | class extends XrowExtractTransport | – | `extension/xrowextract/classes/transports/xrowextracttransportwebdav.php:7` | WebDAV (Nextcloud, ownCloud, Apache mod_dav, a NAS): the folder is created with MKCOL where missing, the file is PUT under a temporary name and MOVEd… |
| `XrowExtractUpload` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `extension/xrowextract/classes/xrowextractupload.php:22` | A chunked upload: one folder per upload (var//xrowextract/uploads//), holding meta.json (owner, original name, total size, bytes received so far, whe… |
| `XrowExtractUserStatusFilter` | class | [Decorator / filter](decorator.md), [INI plugin point (extension point)](ini-plugin-point.md) | `extension/xrowextract/classes/xrowextractuserstatusfilter.php:12` | Extended attribute filter "XrowExtractUserStatus": only user objects that are enabled, or only ones that are disabled, via the standard `ezuser_setti… |
| `XrowExtractWriter` | class | – | `extension/xrowextract/classes/xrowextractwriter.php:13` | Writes export rows as CSV, JSON or XML. |
| `XroweZBinaryfileExportHandler` | class extends XrowBaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/xrowextract/classes/parsers/xrowezbinaryfilehandler.php:2` | – |
| `XroweZBooleanHandler` | class extends XrowBaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/xrowextract/classes/parsers/xrowezbooleanhandler.php:2` | – |
| `XroweZDateHandler` | class extends XrowBaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/xrowextract/classes/parsers/xrowezdatehandler.php:2` | – |
| `XroweZDateTimeHandler` | class extends XrowBaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/xrowextract/classes/parsers/xrowezdatetimehandler.php:2` | – |
| `XroweZEmailHandler` | class extends XrowBaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/xrowextract/classes/parsers/xrowezemailhandler.php:3` | – |
| `XroweZenhancedobjectrelationHandler` | class extends XrowBaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/xrowextract/classes/parsers/xrowezenhancedobjectrelationhandler.php:3` | – |
| `XroweZEnumHandler` | class extends XrowBaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/xrowextract/classes/parsers/xrowezenumhandler.php:2` | – |
| `XroweZExampleHandler` | class extends XrowBaseHandler | [Strategy / handler](strategy-handler.md) | `extension/xrowextract/classes/parsers/xrowezexamplehandler.php:2` | – |
| `XroweZFloatHandler` | class extends XrowBaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/xrowextract/classes/parsers/xrowezfloathandler.php:3` | – |
| `XroweZIdentifierHandler` | class extends XrowBaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/xrowextract/classes/parsers/xrowezidentifierhandler.php:3` | – |
| `XroweZImageExportHandler` | class extends XrowBaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/xrowextract/classes/parsers/xrowezimagehandler.php:2` | – |
| `XroweZIntegerHandler` | class extends XrowBaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/xrowextract/classes/parsers/xrowezintegerhandler.php:3` | – |
| `XroweZKeywordHandler` | class extends XrowBaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/xrowextract/classes/parsers/xrowezkeywordhandler.php:2` | – |
| `XroweZMatrixExportHandler` | class extends XrowBaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/xrowextract/classes/parsers/xrowezmatrixhandler.php:3` | – |
| `XroweZMediaExportHandler` | class extends XrowBaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/xrowextract/classes/parsers/xrowezmediahandler.php:2` | – |
| `XroweZObjectRelationHandler` | class extends XrowBaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/xrowextract/classes/parsers/xrowezobjectrelationhandler.php:3` | – |
| `XroweZObjectRelationListHandler` | class extends XrowBaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/xrowextract/classes/parsers/xrowezobjectrelationlisthandler.php:3` | – |
| `XroweZPriceHandler` | class extends XrowBaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/xrowextract/classes/parsers/xrowezpricehandler.php:3` | – |
| `XroweZSelectionHandler` | class extends XrowBaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/xrowextract/classes/parsers/xrowezselectionhandler.php:2` | – |
| `XroweZStringHandler` | class extends XrowBaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/xrowextract/classes/parsers/xrowezstringhandler.php:3` | – |
| `XroweZTextHandler` | class extends XrowBaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/xrowextract/classes/parsers/xroweztexthandler.php:3` | – |
| `XroweZTimeHandler` | class extends XrowBaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/xrowextract/classes/parsers/xroweztimehandler.php:2` | – |
| `XroweZURLHandler` | class extends XrowBaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/xrowextract/classes/parsers/xrowezurlhandler.php:3` | – |
| `XroweZUserHandler` | class extends XrowBaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/xrowextract/classes/parsers/xrowezuserhandler.php:3` | – |
| `XroweZXMLTextHandler` | class extends XrowBaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/xrowextract/classes/parsers/xrowezxmltexthandler.php:2` | – |
| `XrowhmregexplineHandler` | class extends XrowBaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/xrowextract/classes/parsers/xrowhmregexplinehandler.php:3` | – |
| `XrowxrowmetadataHandler` | class extends XrowBaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/xrowextract/classes/parsers/xrowxrowmetadatahandler.php:7` | xrowmetadata (SEO and sitemap meta data): the title by default. |

## Module views (9)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/xrowextract/csv` | `csv.php` | csv | `extension/xrowextract/modules/xrowextract/module.php:6` |
| `/xrowextract/upload_chunk` | `upload_chunk.php` | import | `extension/xrowextract/modules/xrowextract/module.php:56` |
| `/xrowextract/package` | `package.php` | import | `extension/xrowextract/modules/xrowextract/module.php:65` |
| `/xrowextract/schedules` | `schedules.php` | schedule | `extension/xrowextract/modules/xrowextract/module.php:74` |
| `/xrowextract/destinations` | `destinations.php` | destinations | `extension/xrowextract/modules/xrowextract/module.php:80` |
| `/xrowextract/history` | `history.php` | history | `extension/xrowextract/modules/xrowextract/module.php:86` |
| `/xrowextract/browse` | `browse.php` | import | `extension/xrowextract/modules/xrowextract/module.php:96` |
| `/xrowextract/compare` | `compare.php` | import | `extension/xrowextract/modules/xrowextract/module.php:105` |
| `/xrowextract/browse_file` | `browse_file.php` | import | `extension/xrowextract/modules/xrowextract/module.php:111` |

## Policy functions (8)

| Function | Limitations | Defined in |
|---|---|---|
| `xrowextract/schedule` | – | `extension/xrowextract/modules/xrowextract/module.php:117` |
| `xrowextract/destinations` | – | `extension/xrowextract/modules/xrowextract/module.php:119` |
| `xrowextract/history` | – | `extension/xrowextract/modules/xrowextract/module.php:121` |
| `xrowextract/csv` | – | `extension/xrowextract/modules/xrowextract/module.php:122` |
| `xrowextract/password_hash` | – | `extension/xrowextract/modules/xrowextract/module.php:124` |
| `xrowextract/jobs` | – | `extension/xrowextract/modules/xrowextract/module.php:126` |
| `xrowextract/all_jobs` | – | `extension/xrowextract/modules/xrowextract/module.php:128` |
| `xrowextract/import` | – | `extension/xrowextract/modules/xrowextract/module.php:130` |

## Fetch functions (2)

| Fetch | Implemented by | Defined in |
|---|---|---|
| `fetch( 'xrowextract', 'schedule_alerts' )` | `XrowExtractFunctionCollection::fetchScheduleAlerts()` | `extension/xrowextract/modules/xrowextract/function_definition.php:10` |
| `fetch( 'xrowextract', 'requirement_notices' )` | `XrowExtractFunctionCollection::fetchRequirementNotices()` | `extension/xrowextract/modules/xrowextract/function_definition.php:17` |

## Cronjob parts (2)

| Cronjob | File | Description |
|---|---|---|
| `xrowextract` | `extension/xrowextract/classes/runnable/cronjobs/xrowextract.php` | The code of extension/xrowextract/cronjobs/xrowextract.php, moved into a class (#207 stage 1). |
| `xrowextract` | `extension/xrowextract/cronjobs/xrowextract.php` | The xrowextract cronjob part (runcronjobs.php xrowextract): starts every due schedule as a background job through the same runner "Run in the backgro… |

## INI plugin points (33)

| Setting | Class | Set in |
|---|---|---|
| `[ezstring] HandlerClass` | `XroweZStringHandler` | `extension/xrowextract/settings/csv.ini.append.php:67` |
| `[ezenhancedselection] HandlerClass` | `XroweZStringHandler` | `extension/xrowextract/settings/csv.ini.append.php:71` |
| `[ezinteger] HandlerClass` | `XroweZIntegerHandler` | `extension/xrowextract/settings/csv.ini.append.php:75` |
| `[ezxmltext] HandlerClass` | `XroweZXMLTextHandler` | `extension/xrowextract/settings/csv.ini.append.php:79` |
| `[ezidentifier] HandlerClass` | `XroweZIdentifierHandler` | `extension/xrowextract/settings/csv.ini.append.php:83` |
| `[ezfloat] HandlerClass` | `XroweZFloatHandler` | `extension/xrowextract/settings/csv.ini.append.php:87` |
| `[ezemail] HandlerClass` | `XroweZEmailHandler` | `extension/xrowextract/settings/csv.ini.append.php:91` |
| `[ezurl] HandlerClass` | `XroweZURLHandler` | `extension/xrowextract/settings/csv.ini.append.php:95` |
| `[eztext] HandlerClass` | `XroweZTextHandler` | `extension/xrowextract/settings/csv.ini.append.php:99` |
| `[ezenhancedobjectrelation] HandlerClass` | `XroweZenhancedobjectrelationHandler` | `extension/xrowextract/settings/csv.ini.append.php:103` |
| `[ezselection] HandlerClass` | `XroweZSelectionHandler` | `extension/xrowextract/settings/csv.ini.append.php:109` |
| `[ezenum] HandlerClass` | `XroweZEnumHandler` | `extension/xrowextract/settings/csv.ini.append.php:113` |
| `[ezuser] HandlerClass` | `XroweZUserHandler` | `extension/xrowextract/settings/csv.ini.append.php:117` |
| `[ezdate] HandlerClass` | `XroweZDateHandler` | `extension/xrowextract/settings/csv.ini.append.php:121` |
| `[ezboolean] HandlerClass` | `XroweZBooleanHandler` | `extension/xrowextract/settings/csv.ini.append.php:125` |
| `[ezcountry] HandlerClass` | `XroweZSelectionHandler` | `extension/xrowextract/settings/csv.ini.append.php:129` |
| `[ezimage] HandlerClass` | `XroweZImageExportHandler` | `extension/xrowextract/settings/csv.ini.append.php:133` |
| `[ezmedia] HandlerClass` | `XroweZMediaExportHandler` | `extension/xrowextract/settings/csv.ini.append.php:137` |
| `[ezbinaryfile] HandlerClass` | `XroweZBinaryfileExportHandler` | `extension/xrowextract/settings/csv.ini.append.php:141` |
| `[ezmatrix] HandlerClass` | `XroweZMatrixExportHandler` | `extension/xrowextract/settings/csv.ini.append.php:145` |
| `[ezobjectrelationlist] HandlerClass` | `XroweZObjectRelationListHandler` | `extension/xrowextract/settings/csv.ini.append.php:149` |
| `[hmregexpline] HandlerClass` | `XrowhmregexplineHandler` | `extension/xrowextract/settings/csv.ini.append.php:153` |
| `[ezprice] HandlerClass` | `XroweZPriceHandler` | `extension/xrowextract/settings/csv.ini.append.php:157` |
| `[ezdatetime] HandlerClass` | `XroweZDateTimeHandler` | `extension/xrowextract/settings/csv.ini.append.php:161` |
| `[eztime] HandlerClass` | `XroweZTimeHandler` | `extension/xrowextract/settings/csv.ini.append.php:165` |
| `[ezkeyword] HandlerClass` | `XroweZKeywordHandler` | `extension/xrowextract/settings/csv.ini.append.php:169` |
| `[eztags] HandlerClass` | `XroweZKeywordHandler` | `extension/xrowextract/settings/csv.ini.append.php:173` |
| `[ezobjectrelation] HandlerClass` | `XroweZObjectRelationHandler` | `extension/xrowextract/settings/csv.ini.append.php:177` |
| `[xrowmetadata] HandlerClass` | `XrowxrowmetadataHandler` | `extension/xrowextract/settings/csv.ini.append.php:181` |
| `[XrowExtractTranslation] ClassName` | `XrowExtractTranslationFilter` | `extension/xrowextract/settings/extendedattributefilter.ini.append.php:8` |
| `[XrowExtractHasChildren] ClassName` | `XrowExtractHasChildrenFilter` | `extension/xrowextract/settings/extendedattributefilter.ini.append.php:15` |
| `[XrowExtractRelation] ClassName` | `XrowExtractRelationFilter` | `extension/xrowextract/settings/extendedattributefilter.ini.append.php:23` |
| `[XrowExtractUserStatus] ClassName` | `XrowExtractUserStatusFilter` | `extension/xrowextract/settings/extendedattributefilter.ini.append.php:30` |
