---
title: Interface (contract)
audience: developer
category: standards
order: 326
summary: A contract that implementations follow; code depends on the interface, not on a class. 96 instances in Exponential.
keywords: design pattern, interface, structure, pattern catalogue
type: catalogue
---
# Interface (contract)

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

A contract that implementations follow; code depends on the interface, not on a class.

| | |
|---|---|
| Group | Structure |
| Instances | 96 |
| Recognized by | Every interface. |
| Extension point | Implement the interface and register the class where the interface is used. |

## Classes by area

### extension/cjw_newsletter (2)

[All patterns of extension/cjw_newsletter](area-extension-cjw-newsletter.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `CjwNewsletterScheduleConditionInterface` | interface | `extension/cjw_newsletter/classes/editorial/cjwnewsletterscheduleconditioninterface.php:19` | A condition a recurring send asks before each run (cjwnl_schedule.condition_handler). | – |
| `CjwNewsletterSmsTransport` | interface | `extension/cjw_newsletter/classes/sms/cjwnewslettersmstransport.php:25` | A way to send SMS and to read the SMS a provider hands back (the provider-neutral part of the SMS channel). | – |

### extension/expai (4)

[All patterns of extension/expai](area-extension-expai.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expAiFieldAction` | interface | `extension/expai/classes/fieldactions/expaifieldaction.php:16` | An action next to a field of the content edit form (write, rewrite, shorten, translate, suggest, ...). | extension/expai/settings/rad.ini [Registry_expai_field_actions] Entries[Handler]; extension/expai/settings/rad.ini [Registry_expai_presets] Entries[Handler] |
| `expAiProvider` | interface | `extension/expai/classes/expaiprovider.php:16` | A model backend of expai. | extension/expai/settings/rad.ini [Registry_expai_provider_types] Implements |
| `expAiRequestApplier` | interface | `extension/expai/classes/queue/expairequestapplier.php:18` | Applies the answer of a queued request (expai.request), e.g. | extension/expai/settings/rad.ini [Registry_expai_appliers] Entries[Appliers] |
| `expAiSegmenter` | interface | `extension/expai/classes/diff/expaisegmenter.php:28` | Cuts the value of one datatype into blocks and sentences, and puts values together again (doc/diff.md). | extension/expai/settings/rad.ini [Registry_expai_segmenters] Entries[FallbackSegmenter]; extension/expai/settings/rad.ini [Registry_expai_segmenters] Entries[Segmenters] |

### extension/explayouts (2)

[All patterns of extension/explayouts](area-extension-explayouts.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expLayoutsBlockHandlerInterface` | interface | `extension/explayouts/classes/explayoutsblockhandlerinterface.php:2` | – | – |
| `expLayoutsQueryHandlerInterface` | interface | `extension/explayouts/classes/explayoutsqueryhandlerinterface.php:2` | – | – |

### extension/expquery_translator (2)

[All patterns of extension/expquery_translator](area-extension-expquery-translator.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `QueryTranslator\Parsing` | interface | `extension/expquery_translator/lib/Parsing.php:10` | Interface for parsing a sequence of tokens into a syntax tree. | – |
| `QueryTranslator\Tokenizing` | interface | `extension/expquery_translator/lib/Tokenizing.php:8` | Interface for tokenizing a string into a sequence of tokens. | – |

### extension/expqueue (1)

[All patterns of extension/expqueue](area-extension-expqueue.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expQueueHandler` | interface | `extension/expqueue/classes/expqueuehandler.php:27` | A job handler: the code that runs the jobs of one type. | extension/expqueue/settings/rad.ini [Registry_expqueue_job_types] Entries[Handler] |

### extension/exp_oe_tiptap (1)

[All patterns of extension/exp_oe_tiptap](area-extension-exp-oe-tiptap.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expOETiptapAIProvider` | interface | `extension/exp_oe_tiptap/classes/ai/expoetiptapaiprovider.php:16` | A text generation backend of the AI hooks. | – |

### extension/ezfind (2)

[All patterns of extension/ezfind](area-extension-ezfind.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezfIndexPlugin` | interface | `extension/ezfind/classes/indexplugins/ezfindexplugin.php:18` | Description of ezfIndexPlugin. | – |
| `eZFindExtendedAttributeFilterInterface` | interface | `extension/ezfind/classes/ezfindextendedattributefilterinterface.php:10` | Extended attribute filter interface | – |

### extension/ezflow (2)

[All patterns of extension/ezflow](area-extension-ezflow.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZFlowFetchInterface` | interface | `extension/ezflow/classes/ezflowfetchinterface.php:28` | – | – |
| `eZHTTPCacheManagerInterface` | interface | `extension/ezflow/classes/ezhttpcachemanagerinterface.php:32` | Interface declaration | – |

### extension/ezie (1)

[All patterns of extension/ezie](area-extension-ezie.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZIEEzcConversions` | interface | `extension/ezie/classes/interfaces/conversions.php:11` | – | – |

### extension/ezjscore (1)

[All patterns of extension/ezjscore](area-extension-ezjscore.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezpAjaxUploaderHandlerInterface` | interface | `extension/ezjscore/classes/ajaxuploader/ezpajaxuploaderhandlerinterface.php:19` | Interface of the AJAX uploader handler | – |

### extension/ezmultiupload (1)

[All patterns of extension/ezmultiupload](area-extension-ezmultiupload.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZMultiuploadHandlerInterface` | interface | `extension/ezmultiupload/classes/ezmultiuploadhandlerinterface.php:12` | – | – |

### extension/ezoe (1)

[All patterns of extension/ezoe](area-extension-ezoe.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expOEEditorEngine` | interface | `extension/ezoe/classes/expoeeditorengine.php:21` | An editor engine of the online editor: the thing that turns an ezxmltext attribute into an editor in the browser. | extension/ezoe/settings/rad.ini [Registry_ezoeengines] Entries[Engines] |

### extension/ezurlmultisite (1)

[All patterns of extension/ezurlmultisite](area-extension-ezurlmultisite.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZURLMultisiteDataTypeHandler` | interface | `extension/ezurlmultisite/classes/ezurlmultisitedatatypehandler.php:3` | – | – |

### extension/sqliimport (14)

[All patterns of extension/sqliimport](area-extension-sqliimport.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expImportAiTransport` | interface | `extension/sqliimport/classes/exp/ai/expimportaitransport.php:16` | The way the AI assist of 7x SQLI Import (expImportAi) reaches a model. | – |
| `expImportBoltTransport` | interface | `extension/sqliimport/classes/exp/nosql/expimportbolttransport.php:13` | The byte stream the Bolt client (expImportBoltClient) talks over: a socket (expImportBoltSocket) or a test double. | – |
| `expImportCassandraTransport` | interface | `extension/sqliimport/classes/exp/nosql/expimportcassandratransport.php:21` | The connection the Cassandra/ScyllaDB source reads through: the PHP extension cassandra of DataStax (expImportCassandraExtension) where it is install… | – |
| `expImportHandlerInterface` | interface | `extension/sqliimport/classes/exp/engine/expimporthandlerinterface.php:21` | A handler of the 2.x engine: it turns records into content. | – |
| `expImportImapTransport` | interface | `extension/sqliimport/classes/exp/mail/expimportimaptransport.php:15` | The connection to an IMAP mailbox the IMAP source reads: the PHP extension imap where it is installed (expImportImapExtension), else a small client o… | – |
| `expImportLdapTransport` | interface | `extension/sqliimport/classes/exp/ldap/expimportldaptransport.php:15` | What the LDAP source needs of a directory server: connect, StartTLS, bind and search, nothing that writes. | – |
| `expImportMongoDbTransport` | interface | `extension/sqliimport/classes/exp/nosql/expimportmongodbtransport.php:15` | The connection the MongoDB source reads through: the PHP extension mongodb (expImportMongoDbExtension), or a test double. | – |
| `expImportRecordProviderInterface` | interface | `extension/sqliimport/classes/exp/engine/expimporthandlerinterface.php:52` | A handler that reads its records itself (a 1.x handler through the adapter): the engine takes the records from it instead of from a source. | – |
| `expImportRedisTransport` | interface | `extension/sqliimport/classes/exp/nosql/expimportredistransport.php:15` | The connection the Redis source reads through: the PHP extension redis (expImportRedisExtension), else a small RESP client of its own over a socket (… | – |
| `expImportSourceInterface` | interface | `extension/sqliimport/classes/exp/expimportsourceinterface.php:19` | A source type: it reads records from a file (or, for later source types, an endpoint). | – |
| `expImportTransformInterface` | interface | `extension/sqliimport/classes/exp/expimporttransforminterface.php:18` | A named transform of a mapping line: it turns a source value into the value a class attribute takes (the fromString() form of its datatype). | – |
| `expImportTriggerCallStore` | interface | `extension/sqliimport/classes/exp/expimporttriggerauth.php:14` | Where the trigger keeps its recent calls: the signatures already used (replay protection) and the calls counted by the rate limit. | – |
| `ISQLIDiffHandler` | interface | `extension/sqliimport/classes/content/diffhandlers/isqlidiffhandler.php:27` | Interface for diff handlers. | – |
| `ISQLIImportHandler` | interface | `extension/sqliimport/classes/sourcehandlers/isqliimporthandler.php:11` | Interface for import handlers Must be implemented by all import handlers | – |

### extension/xrowmetadata (3)

[All patterns of extension/xrowmetadata](area-extension-xrowmetadata.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `xrowSitemapImageConverterInterface` | interface | `extension/xrowmetadata/classes/interfaces/xrowsitemapimageconverterinterface.php:3` | – | – |
| `xrowSitemapNewsConverterInterface` | interface | `extension/xrowmetadata/classes/interfaces/xrowsitemapnewsconverterinterface.php:3` | – | – |
| `xrowSitemapVideoConverterInterface` | interface | `extension/xrowmetadata/classes/interfaces/xrowsitemapvideoconverterinterface.php:3` | – | – |

### kernel/classes (1)

[All patterns of kernel/classes](area-kernel-classes.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expNamePatternResolverInterface` | interface | `kernel/classes/expnamepatternresolverinterface.php:27` | A PHP class that makes the name of a content object, or the name its URL alias is made from, instead of the attribute tokens of the class's name patt… | – |

### kernel/classes/audit (5)

[All patterns of kernel/classes/audit](area-kernel-classes-audit.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expAuditAlertRule` | interface | `kernel/classes/audit/expauditalertrule.php:14` | An alert rule class, registered in audit.ini: [AuditAlertSettings] RuleClasses[]= Counted by the RAD survey (registry "auditalertrules"). | – |
| `expAuditFormatHandler` | interface | `kernel/classes/audit/expauditformathandler.php:14` | An archive format (gzip, bzip2, xz, zstd, zip), registered in audit.ini: [AuditArchiveSettings] FormatHandlers[]= Counted by the RAD survey (registry… | – |
| `expAuditSink` | interface | `kernel/classes/audit/expauditsink.php:15` | A copy of the audit records outside the file (syslog, webhook, mail), registered in audit.ini: [AuditSinkSettings] SinkClasses[]= Counted by the RAD… | – |
| `expAuditSource` | interface | `kernel/classes/audit/console/expauditsource.php:23` | An optional source of the audit console: an extension that writes its own events (usually into a channel of its own) and tells the console's views ho… | settings/rad.ini [Registry_auditsources] Entries[Sources] |
| `expAuditTaxonomyBranch` | interface | `kernel/classes/audit/expaudittaxonomybranch.php:15` | An extension's branch of the audit taxonomy, registered in audit.ini: [AuditEventSettings] Branches[myext]=myExtAuditBranch Counted by the RAD survey… | – |

### kernel/classes/contentcheck (1)

[All patterns of kernel/classes/contentcheck](area-kernel-classes-contentcheck.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expContentCheckFileHandler` | interface | `kernel/classes/contentcheck/expcontentcheckfilehandler.php:15` | Says which storage files a file-backed field stands for (doc/eng-us/features/6.0/content-checks.md, "Missing files"). | – |

### kernel/classes/contentjob (1)

[All patterns of kernel/classes/contentjob](area-kernel-classes-contentjob.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expContentJobType` | interface | `kernel/classes/contentjob/expcontentjobtype.php:22` | A kind of content job, registered in content.ini [ContentJobSettings] JobTypes[]=. | – |

### kernel/classes/datatable (1)

[All patterns of kernel/classes/datatable](area-kernel-classes-datatable.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expDataTableFieldSource` | interface | `kernel/classes/datatable/expdatatablefieldsource.php:33` | Something that can hand the fields of a new data table to the module table wizard (doc/eng-us/features/6.0/ data-tables.md, "The module table wizard"… | – |

### kernel/classes/debugbar (1)

[All patterns of kernel/classes/debugbar](area-kernel-classes-debugbar.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expDebugBarPanelInterface` | interface | `kernel/classes/debugbar/expdebugbarpanelinterface.php:26` | A panel of the Exp Debug bar from an extension: its own tab after Memory, optionally a chip in the summary and data in the page's JSON. | – |

### kernel/classes/help (2)

[All patterns of kernel/classes/help](area-kernel-classes-help.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expHelpContextPageInterface` | interface | `kernel/classes/help/exphelpcontextpageinterface.php:16` | A context help provider (expHelpContextProviderInterface) that also serves pages which are not files below doc/: help topics maintained as content, w… | – |
| `expHelpContextProviderInterface` | interface | `kernel/classes/help/exphelpcontextproviderinterface.php:15` | A class that answers "help for this page" in code, for what one Map[] entry per view cannot say (a view whose help depends on its parameters, an exte… | – |

### kernel/classes/ini (2)

[All patterns of kernel/classes/ini](area-kernel-classes-ini.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expIniAction` | interface | `kernel/classes/ini/actions/expiniaction.php:15` | One action of the exp:ini command (get, set, add, rem, ...). | – |
| `expIniScopeProvider` | interface | `kernel/classes/ini/expiniscopeprovider.php:20` | A source of INI scopes (places an INI file can be written). | – |

### kernel/classes/logviewer (1)

[All patterns of kernel/classes/logviewer](area-kernel-classes-logviewer.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expLogManagerInterface` | interface | `kernel/classes/logviewer/explogmanagerinterface.php:19` | Log management (doc/eng-us/features/6.0/log-rotation.md, "For developers"): what the command line, the cronjob part "logs" and the log viewer's actio… | – |

### kernel/classes/mailpreferences (1)

[All patterns of kernel/classes/mailpreferences](area-kernel-classes-mailpreferences.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expMailCategoryHandler` | interface | `kernel/classes/mailpreferences/expmailcategoryhandler.php:26` | The optional PHP class of a category ([Category_] HandlerClass): it reads data that existed before the preference system (subtree notifications, dige… | – |

### kernel/classes/reauth (1)

[All patterns of kernel/classes/reauth](area-kernel-classes-reauth.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expReauthCodeHandler` | interface | `kernel/classes/reauth/expreauthcodehandler.php:14` | A class that checks the code of a two-step sign-in method for "Verify it's you" (expReauth). | – |

### kernel/classes/requestclassifier (2)

[All patterns of kernel/classes/requestclassifier](area-kernel-classes-requestclassifier.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expRequestRuleSetInterface` | interface | `kernel/classes/requestclassifier/exprequestrulesetinterface.php:24` | A rule set of the request classifier (settings/requestclassifier.ini, RuleSets[] and [RuleSet-] Class=). | – |
| `expRequestVerdictListenerInterface` | interface | `kernel/classes/requestclassifier/exprequestverdictlistenerinterface.php:18` | Told about every verdict of the request classifier after its security.log line is written (settings/requestclassifier.ini [ClassifierSettings] Listen… | – |

### kernel/classes/syscard (1)

[All patterns of kernel/classes/syscard](area-kernel-classes-syscard.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expSysCardProvider` | interface | `kernel/classes/syscard/expsyscardprovider.php:19` | A provider of the system card: one class per group of facts (the machine, PHP, the engine, the database ...). | – |

### kernel/classes/transfer (1)

[All patterns of kernel/classes/transfer](area-kernel-classes-transfer.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expKernelTransferTask` | interface | `kernel/classes/transfer/expkerneltransfertask.php:38` | The work of one background run of an import or export tool, whatever engine runs it (expKernelTransferRunner: expqueue, the kernel's content jobs, th… | – |

### kernel/classes/uiswitch (1)

[All patterns of kernel/classes/uiswitch](area-kernel-classes-uiswitch.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expUISwitchProviderInterface` | interface | `kernel/classes/uiswitch/expuiswitchproviderinterface.php:19` | One switchable preference of the administration interface (the "UI chrome"): the display language, the design, or what an extension adds. | – |

### kernel/classes/user (1)

[All patterns of kernel/classes/user](area-kernel-classes-user.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expSignInSecondFactorHandler` | interface | `kernel/classes/user/expsigninsecondfactorhandler.php:18` | What a two-step sign-in extension tells the kernel about sign-ins outside the web login form (REST with a password, WebDAV, SOAP, the expservices log… | – |

### kernel/private (24)

[All patterns of kernel/private](area-kernel-private.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZClusterFileHandlerInterface` | interface | `kernel/private/interfaces/ezclusterfilehandlerinterface.php:15` | Cluster file handlers interface | – |
| `ezpAsynchronousPublisherOutput` | interface | `kernel/private/interfaces/asynchronouspublisheroutput.php:16` | This interface is used as the basis for the ezpasynchronouspublisher.php daemon | – |
| `ezpAsynchronousPublishingFilterInterface` | interface | `kernel/private/interfaces/asynchronouspublishingfilter.php:17` | Base interface for eZ Asynchronous Publishing filter classes | – |
| `ezpAttributeOperatorFormatterInterface` | interface | `kernel/private/eztemplate/ezpattributeoperatorformatterinterface.php:12` | – | – |
| `ezpAutoloadOutput` | interface | `kernel/private/interfaces/ezpautoloadoutput.php:17` | Interface for classes providing output for autoload generation. | – |
| `ezpContentCriteriaInterface` | interface | `kernel/private/api/interfaces/content_criteria.php:16` | This interface is the base one when it comes to implementing content query criteria | – |
| `ezpContentLimitationHandler` | interface | `kernel/private/interfaces/ezpcontentlimitationhandler.php:29` | Evaluates a policy limitation of the content module that the kernel does not know itself. | – |
| `ezpContentLimitationSolrHandler` | interface extends ezpContentLimitationHandler | `kernel/private/interfaces/ezpcontentlimitationsolrhandler.php:23` | A content limitation handler that can also restrict a search in a search engine that filters by the policies of the user itself, such as eZ Find with… | – |
| `ezpContentPublishingQueueReaderInterface` | interface | `kernel/private/interfaces/ezpcontentpublishingqueuereader.php:18` | This interface describes an asynchronous publishing queue reader Queue readers are meant to provide the asynchronous publishing system with the next… | – |
| `ezpDatabaseBasedClusterFileHandler` | interface | `kernel/private/interfaces/ezpdatabasebasedclusterfilehandler.php:20` | This interface describes a database based cluster file handler | – |
| `ezpKernelHandler` | interface | `kernel/private/interfaces/ezpkernelhandler.php:14` | Interface for kernel handlers | – |
| `ezpLanguageSwitcherCapable` | interface | `kernel/private/interfaces/ezplanguageswitchercapable.php:17` | Interface for classes providing language switcher functionality. | – |
| `ezpMobileDeviceDetectFilterInterface` | interface | `kernel/private/classes/ezpmobiledevicedetectfilterinterface.php:12` | – | – |
| `ezpMultivariateTestHandlerInterface` | interface | `kernel/private/classes/ezpmultivariatetesthandlerinterface.php:12` | – | – |
| `ezpRestAuthenticationStyleInterface` | interface | `kernel/private/rest/classes/auth/auth_style.php:12` | – | – |
| `ezpRestPreRoutingFilterInterface` | interface | `kernel/private/rest/classes/interfaces/prerouting_filter.php:12` | – | – |
| `ezpRestProviderInterface` | interface | `kernel/private/rest/classes/interfaces/rest_provider.php:12` | – | – |
| `ezpRestRequestFilterInterface` | interface | `kernel/private/rest/classes/interfaces/request_filter.php:12` | – | – |
| `ezpRestResponseFilterInterface` | interface | `kernel/private/rest/classes/interfaces/response_filter.php:12` | – | – |
| `ezpRestResultFilterInterface` | interface | `kernel/private/rest/classes/interfaces/result_filter.php:12` | – | – |
| `ezpRestViewControllerInterface` | interface | `kernel/private/rest/classes/interfaces/view_controller.php:12` | – | – |
| `ezpSearchEngine` | interface | `kernel/private/interfaces/ezpsearchengine.php:18` | This interface is used as the basis for the different search engine implementation | – |
| `ezpStaticCache` | interface | `kernel/private/interfaces/ezpstaticcache.php:16` | This interface is used as the basis for the different StaticCache engine implementation | – |
| `ezpWebBasedKernelHandler` | interface extends ezpKernelHandler | `kernel/private/interfaces/ezpwebbasedkernelhandler.php:14` | Interface for web-based kernel handlers | – |

### kernel/private/classes/clusterfilehandlers (5)

[All patterns of kernel/private/classes/clusterfilehandlers](area-kernel-private-classes-clusterfilehandlers.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZClusterEventListener` | interface | `kernel/private/classes/clusterfilehandlers/interfaces/ezclustereventlistener.php:14` | Interface to be implemented by any cluster event listener | – |
| `eZClusterEventLogger` | interface | `kernel/private/classes/clusterfilehandlers/interfaces/ezclustereventlogger.php:20` | Interface for eZClusterEvent loggers. | – |
| `eZClusterEventNotifier` | interface | `kernel/private/classes/clusterfilehandlers/interfaces/ezclustereventnotifier.php:15` | Interface that must be implemented by cluster backends supporting events notification. | – |
| `eZDFSFileHandlerDFSBackendFactoryInterface` | interface | `kernel/private/classes/clusterfilehandlers/dfsbackends/ezdfsfilehandlerdfsbackendfactoryinterface.php:14` | Makes a DFSBackend handler instantiable using the build static method. | – |
| `eZDFSFileHandlerDFSBackendInterface` | interface | `kernel/private/classes/clusterfilehandlers/dfsbackends/ezdfsfilehandlerdfsbackendinterface.php:17` | Interface for a DFS FS handler, that offers CRUD support for binary files. | – |

### kernel/private/classes/requestrules (5)

[All patterns of kernel/private/classes/requestrules](area-kernel-private-classes-requestrules.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezpRequestFactProvider` | interface | `kernel/private/classes/requestrules/ezprequestruleinterfaces.php:90` | Adds facts to every request's context before the rules run. | – |
| `ezpRequestRuleAction` | interface | `kernel/private/classes/requestrules/ezprequestruleinterfaces.php:57` | An action: what happens when every condition of a rule matches. | – |
| `ezpRequestRuleCondition` | interface | `kernel/private/classes/requestrules/ezprequestruleinterfaces.php:23` | A condition: one test on the facts of a request. | – |
| `ezpRequestRuleProvider` | interface | `kernel/private/classes/requestrules/ezprequestruleinterfaces.php:76` | Adds rules from PHP: an extension whose rules are computed (from a database table, from another INI file) rather than written in requestrules.ini. | – |
| `ezpRequestRuleStableCondition` | interface | `kernel/private/classes/requestrules/ezprequestruleinterfaces.php:46` | Marks a condition whose answer is the same for every request that the page caches would answer with one stored page: it reads only what their keys ho… | – |

### lib/ezi18n (1)

[All patterns of lib/ezi18n](area-lib-ezi18n.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expI18nFallbackRuleInterface` | interface | `lib/ezi18n/classes/expi18nfallbackruleinterface.php:27` | A translation fallback rule: says which translation a locale falls back to when a translation root (the kernel's share/translations, or an extension'… | – |
