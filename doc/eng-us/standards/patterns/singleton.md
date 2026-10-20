---
title: Singleton / instance()
audience: developer
category: standards
order: 320
summary: A class that hands out one shared instance per key, mostly through a static instance() method (often cached in $GLOBALS). 69 instances in Exponential.
keywords: design pattern, singleton, creation, pattern catalogue
type: catalogue
---
# Singleton / instance()

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

A class that hands out one shared instance per key, mostly through a static instance() method (often cached in $GLOBALS).

| | |
|---|---|
| Group | Creation |
| Instances | 69 |
| Recognized by | Has a static instance() or getInstance() method. |
| Extension point | Call Class::instance(); many of them take the settings file or database as the key. |

## Classes by area

### extension/bccie (1)

[All patterns of extension/bccie](area-extension-bccie.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `bccieExportFormatOutputHandler` | class | `extension/bccie/classes/bccieExportFormatOutputHandler.php:11` | – | – |

### extension/cjw_newsletter (1)

[All patterns of extension/cjw_newsletter](area-extension-cjw-newsletter.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `CjwNewsletterLog` | class extends ezcLog | `extension/cjw_newsletter/classes/cjwnewsletterlog.php:28` | Log handling of the Cjw Newsletter using ezcLog logmessage time [severity] [source] [category] ( element1: value1, .... | – |

### extension/explayouts (1)

[All patterns of extension/explayouts](area-extension-explayouts.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expLayoutsSite` | class | `extension/explayouts/classes/explayoutssite.php:9` | Exponential 4 Site registry compatible with the Exp Site API Site interface. | – |

### extension/ezfind (2)

[All patterns of extension/ezfind](area-extension-ezfind.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZFindExtendedAttributeFilterFactory` | class | `extension/ezfind/classes/ezfindextendedattributefilterfactory.php:10` | Extended attribute filters factory | – |
| `ezfSolrDocumentFieldBase` | class | `extension/ezfind/classes/ezfsolrdocumentfieldbase.php:21` | Class ezfSolrDocumentFieldBase. | – |

### extension/ezflow (1)

[All patterns of extension/ezflow](area-extension-ezflow.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZFlowAjaxContent` | class | `extension/ezflow/classes/ezflowajaxcontent.php:35` | – | – |

### extension/ezjscore (1)

[All patterns of extension/ezjscore](area-extension-ezjscore.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezjscServerRouter` | class | `extension/ezjscore/classes/ezjscserverrouter.php:39` | – | – |

### extension/ezupdate (1)

[All patterns of extension/ezupdate](area-extension-ezupdate.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZUpdateManager` | class | `extension/ezupdate/classes/ezupdatemanager.php:19` | Runs Composer for the installation this extension belongs to. | – |

### extension/git_manager (1)

[All patterns of extension/git_manager](area-extension-git-manager.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `GitManager` | class | `extension/git_manager/classes/git_manager.php:9` | – | – |

### extension/sevenx_authentication_2fa (1)

[All patterns of extension/sevenx_authentication_2fa](area-extension-sevenx-authentication-2fa.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `sevenxAuthentication2faHelper` | class | `extension/sevenx_authentication_2fa/classes/sevenxauthentication2fahelper.php:23` | – | – |

### extension/syndication (1)

[All patterns of extension/syndication](area-extension-syndication.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZImportEditWizard` | class extends eZWizardBase | `extension/syndication/modules/syndication/import_edit/ezimporteditwizard.php:47` | – | – |

### kernel/classes (12)

[All patterns of kernel/classes](area-kernel-classes.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expPasswordPolicy` | class | `kernel/classes/exppasswordpolicy.php:24` | The rules a new password must meet, and what happens after a password was changed. | – |
| `expScriptStatus` | class | `kernel/classes/expscriptstatus.php:18` | expScriptStatus - lightweight CLI progress/status reporter. | – |
| `expWeakPasswordList` | class | `kernel/classes/expweakpasswordlist.php:35` | The one list of well-known and common passwords, and the check every place that sets a new password uses: the setup wizard, the kickstarter and exp:i… | – |
| `eZBinaryFileHandler` | class | `kernel/classes/ezbinaryfilehandler.php:23` | – | – |
| `eZClusterFileHandler` | class | `kernel/classes/ezclusterfilehandler.php:11` | – | – |
| `eZCollaborationProfile` | class extends eZPersistentObject | `kernel/classes/ezcollaborationprofile.php:18` | – | – |
| `eZCollaborationViewHandler` | class | `kernel/classes/ezcollaborationviewhandler.php:18` | – | – |
| `eZConfirmOrderHandler` | class | `kernel/classes/ezconfirmorderhandler.php:18` | – | – |
| `eZPackageCreationHandler` | class | `kernel/classes/ezpackagecreationhandler.php:21` | – | – |
| `eZPackageInstallationHandler` | class | `kernel/classes/ezpackageinstallationhandler.php:19` | – | – |
| `eZScript` | class | `kernel/classes/ezscript.php:49` | – | – |
| `eZShopAccountHandler` | class | `kernel/classes/ezshopaccounthandler.php:12` | – | – |

### kernel/classes/datatypes (3)

[All patterns of kernel/classes/datatypes](area-kernel-classes-datatypes.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZUser` | class extends eZPersistentObject | `kernel/classes/datatypes/ezuser/ezuser.php:21` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZUserLoginHandler` | class | `kernel/classes/datatypes/ezuser/ezuserloginhandler.php:19` | – | – |
| `eZXMLSchema` | class | `kernel/classes/datatypes/ezxmltext/ezxmlschema.php:12` | – | – |

### kernel/classes/help (4)

[All patterns of kernel/classes/help](area-kernel-classes-help.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expHelpContentProvider` | class | `kernel/classes/help/content/exphelpcontentprovider.php:19` | Help maintained as content, delivered into the help system (exphelp): the topics the current user may see, rendered with the help's Markdown renderer… | settings/exphelp.ini [ContextHelp] Providers[] |
| `expHelpContentRepository` | class | `kernel/classes/help/content/exphelpcontentrepository.php:15` | Reads help maintained as content out of the database into plain arrays (the shapes expHelpContentValidator describes), without any permission filter:… | – |
| `expHelpContextRegistry` | class | `kernel/classes/help/exphelpcontextregistry.php:44` | Context help: which help page answers "help for this page", for a module, view and design. | – |
| `expHelpDocumentIndex` | class | `kernel/classes/help/exphelpdocumentindex.php:34` | The index of the help pages (doc/en/us/features/6.0/help-system.md, "The index"): one entry per document of the allowlist (expHelpAllowlist: git-tracked fi… | – |

### kernel/classes/logviewer (2)

[All patterns of kernel/classes/logviewer](area-kernel-classes-logviewer.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expLogManager` | class | `kernel/classes/logviewer/explogmanager.php:27` | Log management (doc/en/us/features/6.0/log-rotation.md): the logs of the installation with their archives, sizes, growth and limits; rotation, pruning, com… | – |
| `expLogViewerConfig` | class | `kernel/classes/logviewer/explogviewerconfig.php:11` | The settings of the log viewer (settings/logviewer.ini, doc/en/us/features/6.0/log-viewer.md, "Settings"), read once per request with safe bounds. | – |

### kernel/classes/mailpreferences (1)

[All patterns of kernel/classes/mailpreferences](area-kernel-classes-mailpreferences.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expMailCategoryRegistry` | class | `kernel/classes/mailpreferences/expmailcategoryregistry.php:26` | All categories of e-mail, in the order the preference page shows them: | – |

### kernel/classes/notification (1)

[All patterns of kernel/classes/notification](area-kernel-classes-notification.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZNotificationTransport` | class | `kernel/classes/notification/eznotificationtransport.php:17` | – | – |

### kernel/classes/requestclassifier (1)

[All patterns of kernel/classes/requestclassifier](area-kernel-classes-requestclassifier.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expRequestClassifier` | class | `kernel/classes/requestclassifier/exprequestclassifier.php:29` | The request classifier: tells a probe, a traversal, an injection, a scanner or a burst of not-found answers from an ordinary request, and writes one… | – |

### kernel/classes/subitems (1)

[All patterns of kernel/classes/subitems](area-kernel-classes-subitems.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expSubitemsColumnRegistry` | class | `kernel/classes/subitems/expsubitemscolumnregistry.php:23` | The registry of the admin subitems list columns ("Table options"). | – |

### kernel/common (1)

[All patterns of kernel/common](area-kernel-common.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZTemplateDesignResource` | class extends eZTemplateFileResource | `kernel/common/eztemplatedesignresource.php:18` | – | – |

### kernel/modules (1)

[All patterns of kernel/modules](area-kernel-modules.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZCurrencyConverter` | class | `kernel/shop/classes/ezcurrencyconverter.php:20` | – | – |

### kernel/private (5)

[All patterns of kernel/private](area-kernel-private.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezpContentPublishingQueueProcessor` | class | `kernel/private/classes/ezpcontentpublishingqueueprocessor.php:21` | This class manages the publishing queue through ezpContentPublishingProcess persistent objects | – |
| `ezpEvent` | class | `kernel/private/classes/ezpevent.php:20` | This class handles internal kernel events in Exponential, aka hooks. | – |
| `ezpExtension` | class | `kernel/private/classes/ezpextension.php:15` | Object representing an Exponential extension | – |
| `ezpKernel` | class | `kernel/private/classes/ezpkernel.php:16` | Base Exponential kernel class. | – |
| `ezpRestDebug` | class | `kernel/private/rest/classes/debug/debug.php:11` | – | – |

### kernel/private/classes/requestrules (1)

[All patterns of kernel/private/classes/requestrules](area-kernel-private-classes-requestrules.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezpRequestRuleEngine` | class | `kernel/private/classes/requestrules/ezprequestruleengine.php:32` | The request rules: an ordered list of rules, each a set of conditions and an action, asked about every module view the kernel is about to run. | – |

### lib/ezdb (1)

[All patterns of lib/ezdb](area-lib-ezdb.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZDB` | class | `lib/ezdb/classes/ezdb.php:80` | – | – |

### lib/ezdbschema (1)

[All patterns of lib/ezdbschema](area-lib-ezdbschema.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZDbSchema` | class | `lib/ezdbschema/classes/ezdbschema.php:19` | – | – |

### lib/ezfile (1)

[All patterns of lib/ezfile](area-lib-ezfile.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZFileHandler` | class | `lib/ezfile/classes/ezfilehandler.php:56` | – | – |

### lib/ezi18n (8)

[All patterns of lib/ezi18n](area-lib-ezi18n.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expI18nFallbackResolver` | class | `lib/ezi18n/classes/expi18nfallbackresolver.php:32` | Which translations a locale falls back to, in order: the one source of truth for the translator (eZTSTranslator), the interface language choice (expU… | – |
| `eZCharTransform` | class | `lib/ezi18n/classes/ezchartransform.php:21` | – | – |
| `eZCodePage` | class | `lib/ezi18n/classes/ezcodepage.php:19` | – | – |
| `eZCodePageMapper` | class | `lib/ezi18n/classes/ezcodepagemapper.php:18` | – | – |
| `eZMBStringMapper` | class | `lib/ezi18n/classes/ezmbstringmapper.php:25` | – | – |
| `eZTextCodec` | class | `lib/ezi18n/classes/eztextcodec.php:23` | – | – |
| `eZTranslatorManager` | class | `lib/ezi18n/classes/eztranslatormanager.php:31` | – | – |
| `eZUTF8Codec` | class | `lib/ezi18n/classes/ezutf8codec.php:22` | – | – |

### lib/ezimage (1)

[All patterns of lib/ezimage](area-lib-ezimage.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZImageManager` | class | `lib/ezimage/classes/ezimagemanager.php:85` | – | – |

### lib/ezlocale (1)

[All patterns of lib/ezlocale](area-lib-ezlocale.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZLocale` | class | `lib/ezlocale/classes/ezlocale.php:111` | – | – |

### lib/eztemplate (3)

[All patterns of lib/eztemplate](area-lib-eztemplate.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZTemplate` | class | `lib/eztemplate/classes/eztemplate.php:200` | – | – |
| `eZTemplateElementParser` | class | `lib/eztemplate/classes/eztemplateelementparser.php:18` | – | – |
| `eZTemplateMultiPassParser` | class extends eZTemplateParser | `lib/eztemplate/classes/eztemplatemultipassparser.php:18` | – | – |

### lib/ezutils (9)

[All patterns of lib/ezutils](area-lib-ezutils.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZCLI` | class | `lib/ezutils/classes/ezcli.php:31` | – | – |
| `eZDebug` | class | `lib/ezutils/classes/ezdebug.php:59` | – | – |
| `eZExpiryHandler` | class | `lib/ezutils/classes/ezexpiryhandler.php:16` | Keeps track of expiry keys and their timestamps | – |
| `eZHTTPTool` | class | `lib/ezutils/classes/ezhttptool.php:25` | – | – |
| `eZINI` | class | `lib/ezutils/classes/ezini.php:53` | ### EXP-MULTI-SITE-OVERRIDE-SETTINGS ### - lib/ezutils/classes/ezini.php - new ini setting ext-siteaccess-override - - new ini setting ext-siteaccess… | – |
| `eZMimeType` | class | `lib/ezutils/classes/ezmimetype.php:34` | – | – |
| `eZProcess` | class | `lib/ezutils/classes/ezprocess.php:18` | – | – |
| `eZSys` | class | `lib/ezutils/classes/ezsys.php:33` | Easy access to various system settings | – |
| `eZURI` | class | `lib/ezutils/classes/ezuri.php:32` | Provides access to the HTTP uri | – |
