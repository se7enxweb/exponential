---
title: Patterns in lib/ezutils
audience: developer
category: standards
order: 553
summary: Every class, view, fetch function, operator and plugin point of lib/ezutils with its design patterns.
keywords: lib/ezutils, design patterns, classes, extension points
type: catalogue
---
# Patterns in lib/ezutils

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `lib/ezutils` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (47)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expDebugBarReport` | class | [Utility (static helper)](utility.md) | `lib/ezutils/classes/expdebugbarreport.php:30` | The HTML debug report as the "Exp Debug" bar: a bar pinned to the bottom of the window with a live summary of the request, and a tabbed panel above i… |
| `expInfo` | class | [Utility (static helper)](utility.md) | `lib/ezutils/classes/expinfo.php:21` | – |
| `eZCacheHelper` | class | – | `lib/ezutils/classes/ezcachehelper.php:12` | – |
| `eZCLI` | class | [Singleton / instance()](singleton.md) | `lib/ezutils/classes/ezcli.php:31` | – |
| `eZDateTimeValidator` | class extends eZInputValidator | [Utility (static helper)](utility.md) | `lib/ezutils/classes/ezdatetimevalidator.php:18` | – |
| `eZDebug` | class | [Factory and static factory methods](factory.md), [Singleton / instance()](singleton.md) | `lib/ezutils/classes/ezdebug.php:59` | – |
| `eZDebugSetting` | class | [Utility (static helper)](utility.md) | `lib/ezutils/classes/ezdebugsetting.php:39` | Provides conditional debug output |
| `eZExecution` | class | [Registry](registry.md), [Utility (static helper)](utility.md) | `lib/ezutils/classes/ezexecution.php:34` | – |
| `eZExpiryHandler` | class | [Registry](registry.md), [Singleton / instance()](singleton.md), [Strategy / handler](strategy-handler.md) | `lib/ezutils/classes/ezexpiryhandler.php:16` | Keeps track of expiry keys and their timestamps |
| `eZExtension` | class | [Utility (static helper)](utility.md) | `lib/ezutils/classes/ezextension.php:18` | – |
| `eZFatalUserError` | class extends Error | [Exception hierarchy](exception.md), [Factory and static factory methods](factory.md) | `lib/ezutils/classes/ezfatalusererror.php:35` | A fatal error raised by the kernel itself, in place of a user error of the level E_USER_ERROR, which PHP 8.4 deprecates for trigger_error() ("throw a… |
| `eZFileExtensionBlackListValidator` | class extends eZInputValidator | – | `lib/ezutils/classes/ezfileextensionblacklistvalidator.php:17` | – |
| `eZFileTransport` | class extends eZMailTransport | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `lib/ezutils/classes/ezfiletransport.php:18` | – |
| `eZFloatValidator` | class extends eZRegExpValidator | – | `lib/ezutils/classes/ezfloatvalidator.php:18` | – |
| `eZFunctionHandler` | class | [Strategy / handler](strategy-handler.md), [Utility (static helper)](utility.md) | `lib/ezutils/classes/ezfunctionhandler.php:18` | – |
| `eZHTTPFile` | class | – | `lib/ezutils/classes/ezhttpfile.php:23` | – |
| `eZHTTPPersistence` | class | – | `lib/ezutils/classes/ezhttppersistence.php:21` | Object persistence using HTTP post variables |
| `eZHTTPTool` | class | [Factory and static factory methods](factory.md), [INI plugin point (extension point)](ini-plugin-point.md), [Singleton / instance()](singleton.md) | `lib/ezutils/classes/ezhttptool.php:25` | – |
| `eZINI` | class | [Factory and static factory methods](factory.md), [INI plugin point (extension point)](ini-plugin-point.md), [Singleton / instance()](singleton.md) | `lib/ezutils/classes/ezini.php:53` | ### EXP-MULTI-SITE-OVERRIDE-SETTINGS ### - lib/ezutils/classes/ezini.php - new ini setting ext-siteaccess-override - - new ini setting ext-siteaccess… |
| `eZInputValidator` | class | – | `lib/ezutils/classes/ezinputvalidator.php:78` | – |
| `eZIntegerValidator` | class extends eZRegExpValidator | – | `lib/ezutils/classes/ezintegervalidator.php:18` | – |
| `eZMail` | class | – | `lib/ezutils/classes/ezmail.php:60` | – |
| `eZMailTransport` | class | [Strategy / handler](strategy-handler.md) | `lib/ezutils/classes/ezmailtransport.php:18` | – |
| `eZMath` | class | [Utility (static helper)](utility.md) | `lib/ezutils/classes/ezmath.php:21` | – |
| `eZMimeType` | class | [Singleton / instance()](singleton.md) | `lib/ezutils/classes/ezmimetype.php:34` | – |
| `eZModule` | class | [Factory and static factory methods](factory.md), [Front Controller](front-controller.md) | `lib/ezutils/classes/ezmodule.php:36` | The eZModule class is used to instanciate and use modules & views. |
| `eZModuleFunctionInfo` | class | – | `lib/ezutils/classes/ezmodulefunctioninfo.php:18` | – |
| `eZModuleOperationInfo` | class | – | `lib/ezutils/classes/ezmoduleoperationinfo.php:18` | – |
| `eZMutex` | class | – | `lib/ezutils/classes/ezmutex.php:18` | – |
| `eZOperationHandler` | class | [Strategy / handler](strategy-handler.md), [Utility (static helper)](utility.md) | `lib/ezutils/classes/ezoperationhandler.php:18` | – |
| `eZOperationMemento` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `lib/ezutils/classes/ezoperationmemento.php:18` | – |
| `eZPHPCreator` | class | – | `lib/ezutils/classes/ezphpcreator.php:53` | – |
| `ezpRepairQueue` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `lib/ezutils/classes/ezprepairqueue.php:37` | Repairs an installation whose Composer libraries are missing, from the error page that says so (eZExecution::renderErrorCasePage('dependencies')), th… |
| `eZProcess` | class | [Singleton / instance()](singleton.md) | `lib/ezutils/classes/ezprocess.php:18` | – |
| `eZRegExpValidator` | class extends eZInputValidator | – | `lib/ezutils/classes/ezregexpvalidator.php:18` | – |
| `eZRemoteIdUtility` | class | – | `lib/ezutils/classes/ezremoteidutility.php:16` | This class provides tools around remote ids |
| `eZSendmailTransport` | class extends eZMailTransport | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `lib/ezutils/classes/ezsendmailtransport.php:20` | – |
| `eZSMTPTransport` | class extends eZMailTransport | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `lib/ezutils/classes/ezsmtptransport.php:18` | – |
| `eZStringUtils` | class | [Utility (static helper)](utility.md) | `lib/ezutils/classes/ezstringutils.php:18` | – |
| `eZSys` | class | [Factory and static factory methods](factory.md), [Singleton / instance()](singleton.md) | `lib/ezutils/classes/ezsys.php:33` | Easy access to various system settings |
| `eZSysInfo` | class | – | `lib/ezutils/classes/ezsysinfo.php:32` | – |
| `eZTextTool` | class | [Utility (static helper)](utility.md) | `lib/ezutils/classes/eztexttool.php:19` | – |
| `eZTimestamp` | class | [Utility (static helper)](utility.md) | `lib/ezutils/classes/eztimestamp.php:12` | – |
| `eZTrustedProxy` | class | [Adapter / bridge / wrapper](adapter.md), [Utility (static helper)](utility.md) | `lib/ezutils/classes/eztrustedproxy.php:30` | Decides which forwarded request headers can be believed. |
| `eZURI` | class | [Factory and static factory methods](factory.md), [Front Controller](front-controller.md), [Singleton / instance()](singleton.md) | `lib/ezutils/classes/ezuri.php:32` | Provides access to the HTTP uri |
| `eZWizardBase` | class | – | `lib/ezutils/classes/ezwizardbase.php:18` | – |
| `eZWizardBaseClassLoader` | class | [Factory and static factory methods](factory.md) | `lib/ezutils/classes/ezwizardbaseclassloader.php:18` | – |

## Event hooks (2)

| Event | Kind | Called in |
|---|---|---|
| `response/output` | filter | `lib/ezutils/classes/ezdebug.php:1562` |
| `module/functionlist` | filter | `lib/ezutils/classes/ezmodule.php:314` |
