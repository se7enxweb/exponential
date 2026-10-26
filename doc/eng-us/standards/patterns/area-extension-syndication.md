---
title: Patterns in extension/syndication
audience: developer
category: standards
order: 481
summary: Every class, view, fetch function, operator and plugin point of extension/syndication with its design patterns.
keywords: extension/syndication, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/syndication

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/syndication` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (34)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `Exponential\Command\Extension\Syndication\Export` | class extends Command | [Command (console, scripts, cronjobs)](command.md) | `extension/syndication/classes/runnable/commands/php_export.php:14` | ext:syndication:export - write the export cache of the active feeds (the cronjob part export_feed, by hand). |
| `Exponential\Command\Extension\Syndication\Import` | class extends Command | [Command (console, scripts, cronjobs)](command.md) | `extension/syndication/classes/runnable/commands/php_import.php:15` | ext:syndication:import - fetch the item lists of the imports and import the waiting items (the cronjob part import_feed, by hand). |
| `Exponential\Command\Extension\Syndication\Install` | class extends Command | [Command (console, scripts, cronjobs)](command.md) | `extension/syndication/classes/runnable/commands/php_install.php:14` | ext:syndication:install - create the tables of the extension from share/db_schema.dba. |
| `Exponential\Command\Extension\Syndication\Status` | class extends Command | [Command (console, scripts, cronjobs)](command.md) | `extension/syndication/classes/runnable/commands/php_status.php:14` | ext:syndication:status - what is exported, what is imported, the last runs and the problems found. |
| `Exponential\Cronjob\Extension\Syndication\ExportFeed` | class extends CronjobPart | [Command (console, scripts, cronjobs)](command.md) | `extension/syndication/classes/runnable/cronjobs/php_export_feed.php:16` | Cronjob part export_feed: writes the export cache of every active feed. |
| `Exponential\Cronjob\Extension\Syndication\ImportFeed` | class extends CronjobPart | [Command (console, scripts, cronjobs)](command.md) | `extension/syndication/classes/runnable/cronjobs/php_import_feed.php:15` | Cronjob part import_feed: fetches the item list of every active import and imports the waiting items. |
| `eZFilterAttribute` | class extends eZSyndicationFilter | [Active Record (eZPersistentObject)](active-record.md), [Decorator / filter](decorator.md) | `extension/syndication/classes/filter/ezfilterattribute.php:48` | – |
| `eZFilterSection` | class extends eZSyndicationFilter | [Active Record (eZPersistentObject)](active-record.md), [Decorator / filter](decorator.md) | `extension/syndication/classes/filter/ezfiltersection.php:47` | – |
| `eZImportEditFeed` | class extends eZImportEditWizard | – | `extension/syndication/modules/syndication/import_edit/ezimporteditfeed.php:47` | – |
| `eZImportEditFilter` | class extends eZImportEditWizard | [Decorator / filter](decorator.md) | `extension/syndication/modules/syndication/import_edit/ezimporteditfilter.php:47` | – |
| `eZImportEditFinal` | class extends eZImportEditWizard | – | `extension/syndication/modules/syndication/import_edit/ezimporteditfinal.php:47` | – |
| `eZImportEditName` | class extends eZImportEditWizard | – | `extension/syndication/modules/syndication/import_edit/ezimporteditname.php:47` | – |
| `eZImportEditOptions` | class extends eZImportEditWizard | – | `extension/syndication/modules/syndication/import_edit/ezimporteditoptions.php:48` | – |
| `eZImportEditWizard` | class extends eZWizardBase | [Singleton / instance()](singleton.md) | `extension/syndication/modules/syndication/import_edit/ezimporteditwizard.php:47` | – |
| `eZSyndicateType` | class extends eZBooleanType | [Datatype (Strategy for content fields)](datatype.md) | `extension/syndication/datatypes/ezsyndicate/ezsyndicatetype.php:47` | – |
| `eZSyndication` | class | – | `extension/syndication/classes/ezsyndication.php:47` | – |
| `eZSyndicationDashboard` | class | – | `extension/syndication/classes/ezsyndicationdashboard.php:15` | – |
| `eZSyndicationFeed` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `extension/syndication/classes/ezsyndicationfeed.php:47` | – |
| `eZSyndicationFeedCacheManager` | class | [Facade / service](facade.md) | `extension/syndication/classes/ezsyndicationfeedcachemanager.php:48` | – |
| `eZSyndicationFeedItem` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `extension/syndication/classes/ezsyndicationfeeditem.php:48` | – |
| `eZSyndicationFeedItemExport` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `extension/syndication/classes/ezsyndicationfeeditemexport.php:48` | – |
| `eZSyndicationFeedItemStatus` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `extension/syndication/classes/ezsyndicationfeeditemstatus.php:48` | – |
| `eZSyndicationFeedSource` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `extension/syndication/classes/ezsyndicationfeedsource.php:47` | – |
| `eZSyndicationFeedSourceFilter` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Decorator / filter](decorator.md), [Factory and static factory methods](factory.md) | `extension/syndication/classes/ezsyndicationfeedsourcefilter.php:47` | – |
| `eZSyndicationFilter` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Decorator / filter](decorator.md), [Factory and static factory methods](factory.md) | `extension/syndication/classes/ezsyndicationfilter.php:48` | – |
| `eZSyndicationImport` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `extension/syndication/classes/ezsyndicationimport.php:47` | – |
| `eZSyndicationImportFilter` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Decorator / filter](decorator.md), [Factory and static factory methods](factory.md) | `extension/syndication/classes/ezsyndicationimportfilter.php:47` | – |
| `eZSyndicationInstaller` | class | [Utility (static helper)](utility.md) | `extension/syndication/classes/ezsyndicationinstaller.php:17` | – |
| `eZSyndicationJob` | class | [Utility (static helper)](utility.md) | `extension/syndication/classes/ezsyndicationjob.php:16` | – |
| `eZSyndicationJobOutput` | class | – | `extension/syndication/classes/ezsyndicationjoboutput.php:14` | – |
| `eZSyndicationRunner` | class | [Utility (static helper)](utility.md) | `extension/syndication/classes/ezsyndicationrunner.php:15` | – |
| `eZSyndicationUI` | class | [Utility (static helper)](utility.md) | `extension/syndication/classes/ezsyndicationui.php:15` | – |
| `SyndicationAction` | class | – | `extension/syndication/modules/syndication/syndicationaction.php:50` | – |
| `syndicationInfo` | class | [Value object / struct](value-object.md) | `extension/syndication/ezinfo.php:42` | – |

## Module views (13)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/syndication/menu` | `menu.php` | menu | `extension/syndication/modules/syndication/module.php:43` |
| `/syndication/list` | `list.php` | view_export | `extension/syndication/modules/syndication/module.php:48` |
| `/syndication/import_list` | `import_list.php` | view_export | `extension/syndication/modules/syndication/module.php:54` |
| `/syndication/import_edit` | `import_edit.php` | edit_import | `extension/syndication/modules/syndication/module.php:60` |
| `/syndication/pending_edit` | `pending_edit.php` | import_object_status | `extension/syndication/modules/syndication/module.php:67` |
| `/syndication/edit` | `edit.php` | edit_export | `extension/syndication/modules/syndication/module.php:75` |
| `/syndication/add_feed_source` | `add_feed_source.php` | edit_export | `extension/syndication/modules/syndication/module.php:81` |
| `/syndication/list_source_filter` | `list_source_filter.php` | edit_export | `extension/syndication/modules/syndication/module.php:88` |
| `/syndication/edit_source_filter` | `edit_source_filter.php` | edit_export | `extension/syndication/modules/syndication/module.php:94` |
| `/syndication/edit_import_filter` | `edit_import_filter.php` | edit_import | `extension/syndication/modules/syndication/module.php:100` |
| `/syndication/import_info` | `import_info.php` | view_import | `extension/syndication/modules/syndication/module.php:108` |
| `/syndication/feed_info` | `feed_info.php` | view_export_info | `extension/syndication/modules/syndication/module.php:115` |
| `/syndication/job` | `job.php` | menu | `extension/syndication/modules/syndication/module.php:122` |

## Policy functions (11)

| Function | Limitations | Defined in |
|---|---|---|
| `syndication/import_object_status` | – | `extension/syndication/modules/syndication/module.php:133` |
| `syndication/view_export` | – | `extension/syndication/modules/syndication/module.php:135` |
| `syndication/edit_export` | – | `extension/syndication/modules/syndication/module.php:137` |
| `syndication/remove_feed` | – | `extension/syndication/modules/syndication/module.php:139` |
| `syndication/create_feed` | – | `extension/syndication/modules/syndication/module.php:141` |
| `syndication/menu` | – | `extension/syndication/modules/syndication/module.php:143` |
| `syndication/view_import` | – | `extension/syndication/modules/syndication/module.php:145` |
| `syndication/edit_import` | – | `extension/syndication/modules/syndication/module.php:147` |
| `syndication/view_export_info` | – | `extension/syndication/modules/syndication/module.php:149` |
| `syndication/create_import` | – | `extension/syndication/modules/syndication/module.php:151` |
| `syndication/fetch_feed` | FeedID | `extension/syndication/modules/syndication/module.php:153` |

## Cronjob parts (4)

| Cronjob | File | Description |
|---|---|---|
| `php_export_feed` | `extension/syndication/classes/runnable/cronjobs/php_export_feed.php` | – |
| `php_import_feed` | `extension/syndication/classes/runnable/cronjobs/php_import_feed.php` | – |
| `syndication_export` | `extension/syndication/cronjobs/syndication_export.php` | Cronjob part export_feed of the syndication extension. |
| `syndication_import` | `extension/syndication/cronjobs/syndication_import.php` | Cronjob part import_feed of the syndication extension. |

## INI plugin points (1)

| Setting | Class | Set in |
|---|---|---|
| `[Service_transfer.syndication] Class` | `expKernelTransferTool` | `extension/syndication/settings/kernelservices.ini.append.php:10` |
