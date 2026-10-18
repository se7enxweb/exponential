---
title: Template Method / abstract base class
audience: developer
category: standards
order: 325
summary: An abstract class that fixes the steps of an algorithm and leaves some of them to its subclasses. 53 instances in Exponential.
keywords: design pattern, template-method, behavior, pattern catalogue
type: catalogue
---
# Template Method / abstract base class

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

An abstract class that fixes the steps of an algorithm and leaves some of them to its subclasses.

| | |
|---|---|
| Group | Behavior |
| Instances | 53 |
| Recognized by | An abstract class with both abstract and concrete methods. |
| Extension point | Extend the base class and implement its abstract methods. |

## Classes by area

### extension/cjw_newsletter (1)

[All patterns of extension/cjw_newsletter](area-extension-cjw-newsletter.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `CjwNewsletterSmsTransportBase` | abstract class | `extension/cjw_newsletter/classes/sms/cjwnewslettersmstransportbase.php:29` | What the shipped SMS transports share: the settings with their preset, the generic check of an inbound request (an HMAC of the body with a shared sec… | – |

### extension/explayouts (1)

[All patterns of extension/explayouts](area-extension-explayouts.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expLayoutsAbstractContentBlockHandler` | abstract class | `extension/explayouts/classes/explayoutsabstractcontentblockhandler.php:3` | – | – |

### extension/expquery_translator (2)

[All patterns of extension/expquery_translator](area-extension-expquery-translator.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `QueryTranslator\Languages\Galach\Generators\Lucene\Common\WordBase` | abstract class extends Visitor | `extension/expquery_translator/lib/Languages/Galach/Generators/Lucene/Common/WordBase.php:14` | Base Word Node Visitor implementation. | – |
| `QueryTranslator\Languages\Galach\TokenExtractor` | abstract class | `extension/expquery_translator/lib/Languages/Galach/TokenExtractor.php:14` | Token extractor is used by Tokenizer to extract tokens from the input string. | – |

### extension/expservices (5)

[All patterns of extension/expservices](area-extension-expservices.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expAttrServiceBase` | abstract class extends expServiceBase | `extension/expservices/classes/media/expattrservicebase.php:11` | Shared helpers of the media services (images, files, media): finding a node's attribute of one datatype, absolute URLs, and the paged listing of the… | – |
| `expCommunityBase` | abstract class extends expServiceBase | `extension/expservices/classes/community/expcommunitybase.php:12` | The shared work of the forum, topic, reply and comment services: they are content services over configurable content classes ([Community] in expservi… | – |
| `expContentServiceBase` | abstract class extends expServiceBase | `extension/expservices/classes/content/expcontentbase.php:12` | The shared part of the content domains of expservices: fetch helpers with the access checks, the exporters that turn content objects into plain array… | – |
| `expServiceBase` | abstract class extends ezjscServerFunctions | `extension/expservices/classes/expservicebase.php:29` | – | – |
| `expUsersBase` | abstract class extends expServiceBase | `extension/expservices/classes/users/expusersbase.php:21` | The shared part of the users and access domains of expservices: the compact declaration format and the helpers that encode users and groups without e… | – |

### extension/ezoe (1)

[All patterns of extension/ezoe](area-extension-ezoe.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expOEEditorEngineBase` | abstract class | `extension/ezoe/classes/expoeeditorenginebase.php:14` | Convenience base of editor engines: reads the engine's own ezoe.ini block [Engine_] (ToolbarMap[]=, ExternalPlugins[]=, Scripts[], Styles[], Version,… | – |

### extension/sevenx_authentication_2fa (1)

[All patterns of extension/sevenx_authentication_2fa](area-extension-sevenx-authentication-2fa.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZOAuthUser` | abstract class extends eZUser | `extension/sevenx_authentication_2fa/classes/ezoauthuser.php:26` | – | – |

### extension/sevenx_dse (8)

[All patterns of extension/sevenx_dse](area-extension-sevenx-dse.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `AdminNeo\Connection` | abstract class | `extension/sevenx_dse/adminneo/core/Connection.php:5` | – | – |
| `AdminNeo\Driver` | abstract class | `extension/sevenx_dse/adminneo/core/Driver.php:5` | – | – |
| `AdminNeo\MsSqlPdoConnection` | abstract class extends PdoConnection | `extension/sevenx_dse/adminneo/drivers/mssql.inc.php:236` | – | – |
| `AdminNeo\Origin` | abstract class extends Plugin | `extension/sevenx_dse/adminneo/core/Origin.php:7` | – | – |
| `AdminNeo\PdoConnection` | abstract class extends Connection | `extension/sevenx_dse/adminneo/include/pdo.inc.php:11` | – | – |
| `AdminNeo\Plugin` | abstract class | `extension/sevenx_dse/adminneo/core/Plugin.php:13` | Plugin parent class. | – |
| `AdminNeo\Result` | abstract class | `extension/sevenx_dse/adminneo/core/Result.php:7` | – | – |
| `AdminNeo\SqLiteConnectionBase` | abstract class extends Connection | `extension/sevenx_dse/adminneo/drivers/sqlite.inc.php:18` | – | – |

### extension/xrowextract (1)

[All patterns of extension/xrowextract](area-extension-xrowextract.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `XrowExtractTransport` | abstract class | `extension/xrowextract/classes/transports/xrowextracttransport.php:10` | One way of delivering a file to (or, for a scheduled import, fetching one from) a destination. | – |

### extension/xrowmetadata (1)

[All patterns of extension/xrowmetadata](area-extension-xrowmetadata.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `xrowSitemapItem` | abstract class extends ezcBaseStruct | `extension/xrowmetadata/classes/structs/xrowsitemapitem.php:3` | – | – |

### kernel/classes (1)

[All patterns of kernel/classes](area-kernel-classes.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZURLAliasFilter` | abstract class | `kernel/classes/ezurlaliasfilter.php:22` | The eZURLAliasFilter class the interface for all url alias filters, the filter implementation must implement the process method. | – |

### kernel/classes/audit (4)

[All patterns of kernel/classes/audit](area-kernel-classes-audit.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expAuditAlertRuleBase` | abstract class | `kernel/classes/audit/alerts/expauditalertrulebase.php:13` | Shared code of the built-in alert rule classes: the record fields a rule groups by, the time of a record, and the de-duplication (doc/bc/6.0/audit.md… | – |
| `expAuditFormatBase` | abstract class | `kernel/classes/audit/format/expauditformatbase.php:14` | Shared code of the archive format handlers (doc/bc/6.0/audit.md, "Rotation, archives and retention"): streaming compression through a PHP stream wrap… | – |
| `expAuditSinkBase` | abstract class | `kernel/classes/audit/sinks/expauditsinkbase.php:14` | Shared code of the audit sinks: the sink's settings block ([AuditSink_]), which records it wants (Events[] patterns and MinSeverity) and whether it d… | – |
| `expAuditSourceBase` | abstract class | `kernel/classes/audit/console/expauditsourcebase.php:11` | The base of an audit console source (expAuditSource): every method answers "nothing", so a source implements only what it shows. | – |

### kernel/classes/contentjob (1)

[All patterns of kernel/classes/contentjob](area-kernel-classes-contentjob.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expContentJobBatchType` | abstract class | `kernel/classes/contentjob/expcontentjobbatchtype.php:28` | The common ground of the content job types that work on a list of nodes: move, hide, reveal, section, state, addlocation and removelocation. | – |

### kernel/classes/ini (1)

[All patterns of kernel/classes/ini](area-kernel-classes-ini.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expIniActionBase` | abstract class | `kernel/classes/ini/actions/expiniactionbase.php:15` | What the built-in exp:ini actions share: the three describing methods from class constants, the printing of a value as INI lines, and the small check… | – |

### kernel/classes/requestclassifier (1)

[All patterns of kernel/classes/requestclassifier](area-kernel-classes-requestclassifier.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expRequestPatternRuleSet` | abstract class | `kernel/classes/requestclassifier/exprequestpatternruleset.php:29` | A rule set made of regular expressions: one per rule, all of them compiled into one combined expression, so a request costs one preg_match() per subj… | – |

### kernel/classes/subitems (2)

[All patterns of kernel/classes/subitems](area-kernel-classes-subitems.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expSubitemsColumn` | abstract class | `kernel/classes/subitems/expsubitemscolumn.php:19` | The base class of a column of the admin subitems list ("Table options"). | – |
| `expSubitemsFieldColumn` | abstract class extends expSubitemsColumn | `kernel/classes/subitems/columns/expsubitemsfieldcolumn.php:22` | The common base of the shipped column families of the admin subitems list. | – |

### kernel/classes/uiswitch (1)

[All patterns of kernel/classes/uiswitch](area-kernel-classes-uiswitch.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expUISwitchProvider` | abstract class | `kernel/classes/uiswitch/expuiswitchprovider.php:18` | The common part of a UI switch provider: its settings group uiswitch.ini [UISwitch_], the allow-list from AllowedValues[] (each value checked by the… | – |

### kernel/modules (2)

[All patterns of kernel/modules](area-kernel-modules.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expExtensionWizard` | abstract class | `kernel/setup/expextensionwizard.php:20` | What every extension wizard needs, whatever kind of extension it builds. | – |
| `ezpClusterGateway` | abstract class | `kernel/clustering/gateway.php:15` | Basis for a cluster gateway. | – |

### kernel/private (12)

[All patterns of kernel/private](area-kernel-private.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezpAsynchronousPublishingFilter` | abstract class | `kernel/private/classes/ezpasynchronouspublishingfilter.php:24` | This interface is to be used to create accept/reject filters for the Asynchronous Publishing feature Each filter class must implement the accept() me… | – |
| `ezpContentException` | abstract class extends ezcBaseException | `kernel/private/api/content/exceptions/content_exception.php:17` | This is the base exception for the Exponential content API | – |
| `ezpOauthBadRequestException` | abstract class extends ezpOauthException | `kernel/private/rest/classes/exceptions/bad_request.php:17` | This is the base exception for triggering BAD REQUEST response. | – |
| `ezpOauthException` | abstract class extends ezcBaseException | `kernel/private/rest/classes/exceptions/oauth.php:17` | This is the base exception for the Exponential oauth library. | – |
| `ezpOauthRequiredException` | abstract class extends ezpOauthException | `kernel/private/rest/classes/exceptions/auth.php:17` | This is the base exception for responsenses need authentication. | – |
| `ezpRestAuthenticationStyle` | abstract class | `kernel/private/rest/classes/auth/styles/abstract_auth_style.php:11` | – | – |
| `ezpRestCacheStorageCluster` | abstract class extends ezpRestCacheStorageFile | `kernel/private/rest/classes/cache/cluster.php:11` | – | – |
| `ezpRestException` | abstract class extends ezcBaseException | `kernel/private/rest/classes/exceptions/rest_exception.php:17` | This is the base exception for the Exponential REST layer | – |
| `ezpRestMvcController` | abstract class extends ezcMvcController | `kernel/private/rest/classes/controllers/rest_controller.php:15` | Abstract class that must be extended by every REST controller | – |
| `ezpRestPrefixFilterInterface` | abstract class | `kernel/private/rest/classes/prefix_filter.php:12` | – | – |
| `ezpRestRouteFilterInterface` | abstract class | `kernel/private/rest/classes/interfaces/route_filter.php:11` | – | – |
| `ezpUpdatedContent` | abstract class | `kernel/private/rest/classes/sync/updates.php:23` | This class provides API through which one can query for updated content items. | – |

### kernel/private/classes/runnable (4)

[All patterns of kernel/private/classes/runnable](area-kernel-private-classes-runnable.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\Runnable\Command` | abstract class extends Runnable | `kernel/private/classes/runnable/command.php:36` | A CLI command. | – |
| `Exponential\Runnable\CronjobPart` | abstract class extends Runnable | `kernel/private/classes/runnable/cronjobpart.php:21` | A cronjob part. | – |
| `Exponential\Runnable\ModuleView` | abstract class extends Runnable | `kernel/private/classes/runnable/moduleview.php:23` | A module view. | – |
| `Exponential\Runnable\Runnable` | abstract class | `kernel/private/classes/runnable/runnable.php:30` | The base of everything Exponential runs from a file: CLI commands (bin/), cronjob parts (cronjobs/) and module views (kernel//.php, extension//module… | – |

### kernel/private/classes/views (1)

[All patterns of kernel/private/classes/views](area-kernel-private-classes-views.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\View\Kernel\Mailpreferences\Page` | abstract class extends ModuleView | `kernel/private/classes/views/mailpreferences/page.php:19` | What the views of the mailpreferences module share: the variables of the including function, rendering a page of design:mailpreferences/, the answer… | – |

### lib/ezdb (1)

[All patterns of lib/ezdb](area-lib-ezdb.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZMySQLCharset` | abstract class | `lib/ezdb/classes/ezmysqlcharset.php:13` | – | – |

### lib/ezsession (1)

[All patterns of lib/ezsession](area-lib-ezsession.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezpSessionHandler` | abstract class | `lib/ezsession/classes/ezpsessionhandler.php:27` | Abstract session handler class to extend | – |
