---
title: Registry
audience: developer
category: standards
order: 323
summary: A central place where objects or callbacks are registered by name and looked up later. 30 instances in Exponential.
keywords: design pattern, registry, structure, pattern catalogue
type: catalogue
---
# Registry

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

A central place where objects or callbacks are registered by name and looked up later.

| | |
|---|---|
| Group | Structure |
| Instances | 30 |
| Recognized by | Class name contains Registry, or has static register*() methods. |

## Classes by area

### extension/expservices (1)

[All patterns of extension/expservices](area-extension-expservices.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expAccountServices` | class extends expUsersBase | `extension/expservices/classes/users/expaccountservices.php:13` | expaccount: the account flows of a person: registration and activation, forgotten password and reset, changing one's own password and e-mail, and the… | extension/expservices/settings/ezjscore.ini [ezjscServer_expaccount] Class |

### extension/ezoe (1)

[All patterns of extension/ezoe](area-extension-ezoe.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expOEEditor` | class | `extension/ezoe/classes/expoeeditor.php:18` | The editor engine registry and the helpers every caller shares: the templates, the ezoe module, the ezjscore functions and the expservices class all… | – |

### extension/nxc_powercontent (1)

[All patterns of extension/nxc_powercontent](area-extension-nxc-powercontent.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZContentOperationCollection` | class | `extension/nxc_powercontent/modules/content/ezcontentoperationcollection.php:20` | – | – |

### kernel/classes (5)

[All patterns of kernel/classes](area-kernel-classes.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZCacheTrash` | class | `kernel/classes/ezcachetrash.php:38` | Removes a cache directory by renaming it out of the way first. | – |
| `eZDataType` | class | `kernel/classes/ezdatatype.php:46` | – | – |
| `eZWorkflowEventType` | class extends eZWorkflowType | `kernel/classes/ezworkfloweventtype.php:18` | – | – |
| `eZWorkflowGroupType` | class extends eZWorkflowType | `kernel/classes/ezworkflowgrouptype.php:18` | – | – |
| `eZWorkflowType` | class | `kernel/classes/ezworkflowtype.php:18` | – | – |

### kernel/classes/audit (4)

[All patterns of kernel/classes/audit](area-kernel-classes-audit.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expAudit` | class | `kernel/classes/audit/expaudit.php:26` | The audit: what happens in Exponential, recorded as hash-chained JSON lines per channel (doc/eng-us/bc/6.0/audit.md, "The developer API (Z3)"). | – |
| `expAuditFormatRegistry` | class | `kernel/classes/audit/format/expauditformatregistry.php:16` | The archive format registry: [AuditArchiveSettings] FormatHandlers[]= (RAD survey registry "auditformats"). | – |
| `expAuditSinkRegistry` | class | `kernel/classes/audit/sinks/expauditsinkregistry.php:23` | The sink registry and delivery (doc/eng-us/bc/6.0/audit.md, "Sinks (Q7)", "The sink registry"). | – |
| `expAuditSourceRegistry` | class | `kernel/classes/audit/console/expauditsourceregistry.php:16` | The registry of the audit console's sources (expAuditSource), from audit.ini [AuditSourceSettings] Sources[]: which are registered, which work here,… | – |

### kernel/classes/datatypes (1)

[All patterns of kernel/classes/datatypes](area-kernel-classes-datatypes.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZURL` | class extends eZPersistentObject | `kernel/classes/datatypes/ezurl/ezurl.php:23` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |

### kernel/classes/debugbar (1)

[All patterns of kernel/classes/debugbar](area-kernel-classes-debugbar.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expDebugBarRegistry` | class | `kernel/classes/debugbar/expdebugbarregistry.php:20` | Reads settings/debugbar.ini (and every extension's debugbar.ini.append.php, merged by eZINI): | – |

### kernel/classes/help (2)

[All patterns of kernel/classes/help](area-kernel-classes-help.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expHelpContextRegistry` | class | `kernel/classes/help/exphelpcontextregistry.php:44` | Context help: which help page answers "help for this page", for a module, view and design. | – |
| `expHelpHighlighter` | class | `kernel/classes/help/exphelphighlighter.php:29` | Server-side syntax highlighting of the code blocks of the help pages (exphelp, doc/eng-us/features/6.0/help-system.md), without JavaScript or an external li… | – |

### kernel/classes/ini (2)

[All patterns of kernel/classes/ini](area-kernel-classes-ini.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expIniActionRegistry` | class | `kernel/classes/ini/actions/expiniactionregistry.php:22` | The actions of the exp:ini command and the scope providers of the settings editor, read from ini.ini [IniCommandSettings]: | – |
| `expIniEditor` | class | `kernel/classes/ini/expinieditor.php:27` | The INI editor. | – |

### kernel/classes/mailpreferences (1)

[All patterns of kernel/classes/mailpreferences](area-kernel-classes-mailpreferences.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expMailCategoryRegistry` | class | `kernel/classes/mailpreferences/expmailcategoryregistry.php:26` | All categories of e-mail, in the order the preference page shows them: | – |

### kernel/classes/notification (1)

[All patterns of kernel/classes/notification](area-kernel-classes-notification.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZNotificationEventType` | class | `kernel/classes/notification/eznotificationeventtype.php:18` | – | – |

### kernel/classes/subitems (1)

[All patterns of kernel/classes/subitems](area-kernel-classes-subitems.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expSubitemsColumnRegistry` | class | `kernel/classes/subitems/expsubitemscolumnregistry.php:23` | The registry of the admin subitems list columns ("Table options"). | – |

### kernel/classes/workflowtypes (1)

[All patterns of kernel/classes/workflowtypes](area-kernel-classes-workflowtypes.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZPaymentGatewayType` | class extends eZWorkflowEventType | `kernel/classes/workflowtypes/event/ezpaymentgateway/ezpaymentgatewaytype.php:20` | – | – |

### kernel/private (1)

[All patterns of kernel/private](area-kernel-private.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezpRestDbConfig` | class | `kernel/private/rest/classes/lazy.php:12` | – | – |

### kernel/private/classes/httpcache (1)

[All patterns of kernel/private/classes/httpcache](area-kernel-private-classes-httpcache.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezpHttpCacheListener` | class | `kernel/private/classes/httpcache/ezphttpcachelistener.php:27` | The kernel side of the role-aware HTTP cache: stores rendered content views for the early exit and Velocity's parent to serve, and turns the kernel's… | – |

### lib/ezdb (1)

[All patterns of lib/ezdb](area-lib-ezdb.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZDBQueryCache` | class | `lib/ezdb/classes/ezdbquerycache.php:37` | A result cache for the SQL drivers (MySQL/MariaDB, PostgreSQL, SQLite): the rows a SELECT returned, answered again until a write to one of the tables… | – |

### lib/ezi18n (1)

[All patterns of lib/ezi18n](area-lib-ezi18n.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZTranslatorManager` | class | `lib/ezi18n/classes/eztranslatormanager.php:31` | – | – |

### lib/ezimage (1)

[All patterns of lib/ezimage](area-lib-ezimage.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZImageInterface` | class | `lib/ezimage/classes/ezimageinterface.php:20` | – | – |

### lib/ezsession (1)

[All patterns of lib/ezsession](area-lib-ezsession.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZSession` | class | `lib/ezsession/classes/ezsession.php:67` | Exponential Session interface class | – |

### lib/ezutils (2)

[All patterns of lib/ezutils](area-lib-ezutils.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZExecution` | class | `lib/ezutils/classes/ezexecution.php:34` | – | – |
| `eZExpiryHandler` | class | `lib/ezutils/classes/ezexpiryhandler.php:16` | Keeps track of expiry keys and their timestamps | – |
