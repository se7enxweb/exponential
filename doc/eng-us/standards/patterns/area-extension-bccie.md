---
title: Patterns in extension/bccie
audience: developer
category: standards
order: 402
summary: Every class, view, fetch function, operator and plugin point of extension/bccie with its design patterns.
keywords: extension/bccie, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/bccie

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/bccie` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (46)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `BaseHandler` | class | [Strategy / handler](strategy-handler.md) | `extension/bccie/classes/basehandler.php:11` | – |
| `bccieDashboard` | class | – | `extension/bccie/classes/bccieDashboard.php:14` | What the start page of the module shows: the forms with collected information and their counts, the last exports, the settings of the scheduled expor… |
| `bccieExporter` | class | – | `extension/bccie/classes/bccieExporter.php:17` | Writes the rows of an export as CSV or SYLK, line by line, so that a large export never needs the whole file in memory. |
| `bccieExportFormatOutputHandler` | class | [Singleton / instance()](singleton.md), [Strategy / handler](strategy-handler.md) | `extension/bccie/classes/bccieExportFormatOutputHandler.php:11` | – |
| `bccieExportFormatOutputHandlerCP1252` | class extends bccieExportFormatOutputHandler | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/bccie/classes/exportFormatOutputHandlers/bccieExportFormatOutputHandlerCP1252.php:11` | – |
| `bccieExportFormatOutputHandlerUtf8` | class extends bccieExportFormatOutputHandler | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/bccie/classes/exportFormatOutputHandlers/bccieExportFormatOutputHandlerUtf8.php:11` | – |
| `bccieExportFormatOutputHandlerUtf8Bom` | class extends bccieExportFormatOutputHandler | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/bccie/classes/exportFormatOutputHandlers/bccieExportFormatOutputHandlerUtf8Bom.php:11` | – |
| `bccieExportFormatOutputHandlerUtf16Le` | class extends bccieExportFormatOutputHandler | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/bccie/classes/exportFormatOutputHandlers/bccieExportFormatOutputHandlerUtf16Le.php:11` | – |
| `bccieExportPage` | class | [Utility (static helper)](utility.md) | `extension/bccie/classes/bccieExportPage.php:15` | The export page of one form: the options form, the download, the start of an export in the background and the removal of the collected information. |
| `bccieExportUtils` | class | [Utility (static helper)](utility.md) | `extension/bccie/classes/bccieExportUtils.php:11` | – |
| `bccieInfo` | class | [Value object / struct](value-object.md) | `extension/bccie/ezinfo.php:10` | – |
| `bccieJob` | class | [Utility (static helper)](utility.md) | `extension/bccie/classes/bccieJob.php:15` | An export in the background, started from the admin: the same console command the shell uses (ext:bccie:export), started with expProcessTools like th… |
| `bccieJobOutput` | class | – | `extension/bccie/classes/bccieJobOutput.php:13` | Output sink with the two methods of eZCLI the runner uses; a line goes to the terminal and to the log of a job. |
| `bccieRunner` | class | [Utility (static helper)](utility.md) | `extension/bccie/classes/bccieRunner.php:15` | The work of the exports as methods, so that the admin, the console commands and the cronjob parts do the same thing: validate the options, write an e… |
| `bccieUI` | class | [Utility (static helper)](utility.md) | `extension/bccie/classes/bccieUI.php:13` | Helpers shared by the module views: flash notices and the parameters of a list view (paging, sorting, filter). |
| `Exponential\Command\Extension\Bccie\Export` | class extends Command | [Command (console, scripts, cronjobs)](command.md) | `extension/bccie/classes/runnable/commands/php_export.php:16` | ext:bccie:export - export the collected information of a form to a CSV or SYLK file, or run the scheduled export (the cronjob parts exportcsv and exp… |
| `Exponential\Command\Extension\Bccie\Purge` | class extends Command | [Command (console, scripts, cronjobs)](command.md) | `extension/bccie/classes/runnable/commands/php_purge.php:14` | ext:bccie:purge - remove the collected information of a form, all of it or what is older than a date. |
| `Exponential\Command\Extension\Bccie\Status` | class extends Command | [Command (console, scripts, cronjobs)](command.md) | `extension/bccie/classes/runnable/commands/php_status.php:15` | ext:bccie:status - the dashboard of the admin as text: the forms with collected information, the last exports and the problems found. |
| `Exponential\Cronjob\Extension\Bccie\Exportcsv` | class extends CronjobPart | [Command (console, scripts, cronjobs)](command.md) | `extension/bccie/classes/runnable/cronjobs/exportcsv.php:16` | Cronjob part exportcsv: writes the collected information of the objects in [CieSettings] Collection[] to CSV files in [CieSettings] Directory. |
| `Exponential\Cronjob\Extension\Bccie\Exportsylk` | class extends CronjobPart | [Command (console, scripts, cronjobs)](command.md) | `extension/bccie/classes/runnable/cronjobs/exportsylk.php:16` | Cronjob part exportsylk: writes the collected information of the objects in [CieSettings] Collection[] to SYLK files in [CieSettings] Directory. |
| `Exponential\View\Extension\Bccie\Bccie\Doexport` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/bccie/classes/runnable/views/bccie/doexport.php:22` | The code of extension/bccie/modules/bccie/doexport.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Bccie\Bccie\Export` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/bccie/classes/runnable/views/bccie/export.php:22` | The code of extension/bccie/modules/bccie/export.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Bccie\Bccie\Overview` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/bccie/classes/runnable/views/bccie/overview.php:22` | The code of extension/bccie/modules/bccie/overview.php, moved into a class (#207 stage 1), rebuilt in 1.1.12 as a dashboard with a list. |
| `eZBirthdayExportHandler` | class extends BaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/bccie/classes/handlers/ezbirthdayexporthandler.php:13` | – |
| `eZBirthdayHandler` | class extends BaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/bccie/classes/handlers/ezbirthdayhandler.php:11` | – |
| `eZBooleanHandler` | class extends BaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/bccie/classes/handlers/ezbooleanhandler.php:11` | – |
| `eZCountryHandler` | class extends BaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/bccie/classes/handlers/ezcountrtyhandler.php:11` | – |
| `eZDateHandler` | class extends BaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/bccie/classes/handlers/ezdatehandler.php:11` | – |
| `eZDateTimeHandler` | class extends BaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/bccie/classes/handlers/ezdatetimehandler.php:11` | – |
| `eZEmailHandler` | class extends BaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/bccie/classes/handlers/ezemailhandler.php:11` | – |
| `ezenhancedobjectrelationHandler` | class extends BaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/bccie/classes/handlers/ezenhancedobjectrelationhandler.php:11` | – |
| `eZExampleCustomHandler` | class extends BaseHandler | [Strategy / handler](strategy-handler.md) | `extension/bccie/classes/handlers/ezexamplecustomhandler.php:11` | – |
| `eZFloatHandler` | class extends BaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/bccie/classes/handlers/ezfloathandler.php:11` | – |
| `eZIdentifierHandler` | class extends BaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/bccie/classes/handlers/ezidentifierhandler.php:11` | – |
| `eZIntegerHandler` | class extends BaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/bccie/classes/handlers/ezintegerhandler.php:11` | – |
| `eZObjectRelationHandler` | class extends BaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/bccie/classes/handlers/ezobjectrelationhandler.php:11` | – |
| `eZOptionHandler` | class extends BaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/bccie/classes/handlers/ezoptionhandler.php:11` | – |
| `eZSelectionHandler` | class extends BaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/bccie/classes/handlers/ezselectionhandler.php:11` | – |
| `eZStringHandler` | class extends BaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/bccie/classes/handlers/ezstringhandler.php:11` | – |
| `eZTextHandler` | class extends BaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/bccie/classes/handlers/eztexthandler.php:11` | – |
| `eZTimeHandler` | class extends BaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/bccie/classes/handlers/eztimehandler.php:11` | – |
| `eZURLHandler` | class extends BaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/bccie/classes/handlers/ezurlhandler.php:11` | – |
| `eZXMLTextHandler` | class extends BaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/bccie/classes/handlers/ezxmltexthandler.php:11` | – |
| `OWEnhancedSelectionHandler` | class extends BaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/bccie/classes/handlers/owenhancedselectionhandler.php:11` | – |
| `Parser` | class | – | `extension/bccie/classes/parser.php:15` | Turns the collected information of one object into rows and writes them as CSV or SYLK. |
| `smileobjectrelationlistHandler` | class extends BaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/bccie/classes/handlers/smileobjectrelationlisthandler.php:11` | – |

## Module views (5)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/bccie/overview` | `overview.php` | read | `extension/bccie/modules/bccie/module.php:27` |
| `/bccie/export` | `export.php` | read | `extension/bccie/modules/bccie/module.php:40` |
| `/bccie/doexport` | `doexport.php` | read | `extension/bccie/modules/bccie/module.php:49` |
| `/bccie/download` | `download.php` | read | `extension/bccie/modules/bccie/module.php:59` |
| `/bccie/job` | `job.php` | read | `extension/bccie/modules/bccie/module.php:69` |

## Policy functions (2)

| Function | Limitations | Defined in |
|---|---|---|
| `bccie/read` | – | `extension/bccie/modules/bccie/module.php:79` |
| `bccie/remove` | – | `extension/bccie/modules/bccie/module.php:80` |

## Cronjob parts (4)

| Cronjob | File | Description |
|---|---|---|
| `exportcsv` | `extension/bccie/classes/runnable/cronjobs/exportcsv.php` | – |
| `exportsylk` | `extension/bccie/classes/runnable/cronjobs/exportsylk.php` | – |
| `exportcsv` | `extension/bccie/cronjobs/exportcsv.php` | – |
| `exportsylk` | `extension/bccie/cronjobs/exportsylk.php` | – |

## INI plugin points (42)

| Setting | Class | Set in |
|---|---|---|
| `[CieSettings] ExportOutputFormatHandlers[utf8]` | `bccieExportFormatOutputHandlerUtf8` | `extension/bccie/settings/cie.ini.append.php:90` |
| `[CieSettings] ExportOutputFormatHandlers[utf8bom]` | `bccieExportFormatOutputHandlerUtf8Bom` | `extension/bccie/settings/cie.ini.append.php:91` |
| `[CieSettings] ExportOutputFormatHandlers[utf16le]` | `bccieExportFormatOutputHandlerUtf16Le` | `extension/bccie/settings/cie.ini.append.php:92` |
| `[CieSettings] ExportOutputFormatHandlers[cp1252]` | `bccieExportFormatOutputHandlerCP1252` | `extension/bccie/settings/cie.ini.append.php:93` |
| `[ezboolean] HandlerClass` | `eZBooleanHandler` | `extension/bccie/settings/export.ini:31` |
| `[ezcountry] HandlerClass` | `eZCountryHandler` | `extension/bccie/settings/export.ini:35` |
| `[ezdate] HandlerClass` | `eZDateHandler` | `extension/bccie/settings/export.ini:39` |
| `[ezdatetime] HandlerClass` | `eZDateTimeHandler` | `extension/bccie/settings/export.ini:43` |
| `[ezemail] HandlerClass` | `eZEmailHandler` | `extension/bccie/settings/export.ini:47` |
| `[ezenhancedobjectrelation] HandlerClass` | `ezenhancedobjectrelationHandler` | `extension/bccie/settings/export.ini:51` |
| `[ezfloat] HandlerClass` | `eZFloatHandler` | `extension/bccie/settings/export.ini:58` |
| `[ezidentifier] HandlerClass` | `eZIdentifierHandler` | `extension/bccie/settings/export.ini:62` |
| `[ezinteger] HandlerClass` | `eZIntegerHandler` | `extension/bccie/settings/export.ini:66` |
| `[ezobjectrelation] HandlerClass` | `eZObjectRelationHandler` | `extension/bccie/settings/export.ini:70` |
| `[ezoption] HandlerClass` | `eZOptionHandler` | `extension/bccie/settings/export.ini:77` |
| `[ezselection] HandlerClass` | `eZSelectionHandler` | `extension/bccie/settings/export.ini:81` |
| `[ezstring] HandlerClass` | `eZStringHandler` | `extension/bccie/settings/export.ini:85` |
| `[eztext] HandlerClass` | `eZTextHandler` | `extension/bccie/settings/export.ini:89` |
| `[eztime] HandlerClass` | `eZTimeHandler` | `extension/bccie/settings/export.ini:93` |
| `[ezurl] HandlerClass` | `eZURLHandler` | `extension/bccie/settings/export.ini:97` |
| `[ezxmltext] HandlerClass` | `eZXMLTextHandler` | `extension/bccie/settings/export.ini:101` |
| `[owenhancedselection] HandlerClass` | `OWEnhancedSelectionHandler` | `extension/bccie/settings/export.ini:105` |
| `[smileobjectrelationlist] HandlerClass` | `smileobjectrelationlistHandler` | `extension/bccie/settings/export.ini:109` |
| `[ezbirthday] HandlerClass` | `eZBirthdayHandler` | `extension/bccie/settings/export.ini:113` |
| `[ezstring] HandlerClass` | `eZStringHandler` | `extension/bccie/settings/export.ini.append.php:32` |
| `[ezinteger] HandlerClass` | `eZIntegerHandler` | `extension/bccie/settings/export.ini.append.php:36` |
| `[ezxmltext] HandlerClass` | `eZXMLTextHandler` | `extension/bccie/settings/export.ini.append.php:40` |
| `[ezboolean] HandlerClass` | `eZBooleanHandler` | `extension/bccie/settings/export.ini.append.php:44` |
| `[ezidentifier] HandlerClass` | `eZIdentifierHandler` | `extension/bccie/settings/export.ini.append.php:48` |
| `[ezfloat] HandlerClass` | `eZFloatHandler` | `extension/bccie/settings/export.ini.append.php:52` |
| `[ezemail] HandlerClass` | `eZEmailHandler` | `extension/bccie/settings/export.ini.append.php:56` |
| `[ezurl] HandlerClass` | `eZURLHandler` | `extension/bccie/settings/export.ini.append.php:60` |
| `[eztext] HandlerClass` | `eZTextHandler` | `extension/bccie/settings/export.ini.append.php:64` |
| `[ezenhancedobjectrelation] HandlerClass` | `ezenhancedobjectrelationHandler` | `extension/bccie/settings/export.ini.append.php:68` |
| `[ezselection] HandlerClass` | `eZSelectionHandler` | `extension/bccie/settings/export.ini.append.php:75` |
| `[ezoption] HandlerClass` | `eZOptionHandler` | `extension/bccie/settings/export.ini.append.php:79` |
| `[ezcountry] HandlerClass` | `eZCountryHandler` | `extension/bccie/settings/export.ini.append.php:83` |
| `[ezdatetime] HandlerClass` | `eZDateTimeHandler` | `extension/bccie/settings/export.ini.append.php:87` |
| `[eztime] HandlerClass` | `eZTimeHandler` | `extension/bccie/settings/export.ini.append.php:91` |
| `[ezdate] HandlerClass` | `eZDateHandler` | `extension/bccie/settings/export.ini.append.php:95` |
| `[smileobjectrelationlist] HandlerClass` | `smileobjectrelationlistHandler` | `extension/bccie/settings/export.ini.append.php:99` |
| `[ezbirthday] HandlerClass` | `eZBirthdayExportHandler` | `extension/bccie/settings/export.ini.append.php:103` |
