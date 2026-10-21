---
title: Patterns in kernel/modules
audience: developer
category: standards
order: 513
summary: Every class, view, fetch function, operator and plugin point of kernel/modules with its design patterns.
keywords: kernel/modules, design patterns, classes, extension points
type: catalogue
---
# Patterns in kernel/modules

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `kernel/modules` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (76)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expContentExtensionWizard` | class extends expExtensionWizard | [Utility (static helper)](utility.md) | `kernel/setup/expcontentextensionwizard.php:27` | The content extension wizard: what authors, editors and translators see. |
| `expCronjobRunner` | class | [Utility (static helper)](utility.md) | `kernel/setup/expcronjobrunner.php:38` | Lists the cronjob parts this installation defines and launches one from the administration interface, leaving it to run after the request has finishe… |
| `expDatatypeWizard` | class extends expExtensionWizard | [Utility (static helper)](utility.md) | `kernel/setup/expdatatypewizard.php:22` | The datatype wizard: builds a working content datatype extension. |
| `expDesignExtensionWizard` | class extends expExtensionWizard | [Utility (static helper)](utility.md) | `kernel/setup/expdesignextensionwizard.php:21` | Builds a design extension: the directories, the settings and the templates that a working design needs, ready to switch on. |
| `expExtensionWizard` | abstract class | [Template Method / abstract base class](template-method.md) | `kernel/setup/expextensionwizard.php:20` | What every extension wizard needs, whatever kind of extension it builds. |
| `expHandlerWizard` | class extends expExtensionWizard | [Utility (static helper)](utility.md) | `kernel/setup/exphandlerwizard.php:29` | Builds a handler: a class named by an ini setting that the kernel loads in place of its own. |
| `expKernelOverrideWizard` | class extends expExtensionWizard | [Utility (static helper)](utility.md) | `kernel/setup/expkerneloverridewizard.php:24` | The kernel override wizard: replacing a kernel class outright. |
| `expModuleExtensionWizard` | class extends expExtensionWizard | [Utility (static helper)](utility.md) | `kernel/setup/expmoduleextensionwizard.php:31` | Builds a module extension over tables that already exist. |
| `expModuleWizard` | class extends expExtensionWizard | [Utility (static helper)](utility.md) | `kernel/setup/expmodulewizard.php:28` | The module wizard: a module, its views, and the policies that reach them. |
| `expPreloadRunner` | class | – | `kernel/setup/exppreloadrunner.php:28` | The preloader as a library: it warms the site's page caches and reports what it did through a callback, so the same code can drive a terminal, an adm… |
| `expRADCatalogue` | class | [Utility (static helper)](utility.md) | `kernel/setup/expradcatalogue.php:29` | Every point this system can be extended at, and what it takes to extend it. |
| `expRADHealth` | class | [Utility (static helper)](utility.md) | `kernel/setup/expradhealth.php:23` | What is configured on this installation and cannot work. |
| `expRADSurvey` | class | [Utility (static helper)](utility.md) | `kernel/setup/expradsurvey.php:26` | A live survey of what this installation can be extended at. |
| `expSettingsExtensionWizard` | class extends expExtensionWizard | [Utility (static helper)](utility.md) | `kernel/setup/expsettingsextensionwizard.php:26` | The settings extension wizard: an extension that is mostly ini files. |
| `expStaticCacheRunner` | class | – | `kernel/setup/expstaticcacherunner.php:33` | Generates the static cache for one site at a time and reports every page as it is written, so the administration interface shows a run instead of a b… |
| `expTemplateExtensionWizard` | class extends expExtensionWizard | [Utility (static helper)](utility.md) | `kernel/setup/exptemplateextensionwizard.php:25` | The template extension wizard: everything a template can be taught to call. |
| `expWorkflowEventWizard` | class extends expExtensionWizard | [Utility (static helper)](utility.md) | `kernel/setup/expworkfloweventwizard.php:27` | Builds a workflow event type: a step a workflow can take when something is published, moved, removed, registered or bought. |
| `eZCheckClasses` | class | [Utility (static helper)](utility.md) | `kernel/setup/expclassloadcheck.php:26` | The parts of the check worth having in one place. |
| `eZClassFunctionCollection` | class | [Fetch function (Query Object, Facade)](fetch-function.md) | `kernel/class/ezclassfunctioncollection.php:20` | – |
| `eZClassFunctions` | class | [Utility (static helper)](utility.md) | `kernel/class/ezclassfunctions.php:14` | – |
| `eZCollaborationFunctionCollection` | class | [Fetch function (Query Object, Facade)](fetch-function.md) | `kernel/collaboration/ezcollaborationfunctioncollection.php:20` | – |
| `eZCurrencyConverter` | class | [Singleton / instance()](singleton.md), [Strategy / handler](strategy-handler.md) | `kernel/shop/classes/ezcurrencyconverter.php:20` | – |
| `eZCurrencyData` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/shop/classes/ezcurrencydata.php:12` | – |
| `eZECBHandler` | class extends eZExchangeRatesUpdateHandler | [Strategy / handler](strategy-handler.md) | `kernel/shop/classes/exchangeratehandlers/ezecb/ezecbhandler.php:12` | – |
| `eZExchangeRatesUpdateHandler` | class | [Factory and static factory methods](factory.md), [Strategy / handler](strategy-handler.md) | `kernel/shop/classes/exchangeratehandlers/ezexchangeratesupdatehandler.php:12` | – |
| `eZInfocollectorFunctionCollection` | class | [Fetch function (Query Object, Facade)](fetch-function.md), [Utility (static helper)](utility.md) | `kernel/infocollector/ezinfocollectorfunctioncollection.php:20` | – |
| `eZLayoutFunctionCollection` | class | [Fetch function (Query Object, Facade)](fetch-function.md) | `kernel/layout/ezlayoutfunctioncollection.php:20` | – |
| `eZMultiEdit` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `kernel/content/multiedit_functions.php:16` | The working parts of content/multiedit. |
| `eZMultiPriceData` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `kernel/shop/classes/ezmultipricedata.php:12` | – |
| `eZNotificationFunctionCollection` | class | [Fetch function (Query Object, Facade)](fetch-function.md) | `kernel/notification/eznotificationfunctioncollection.php:20` | – |
| `eZPackageFunctionCollection` | class | [Fetch function (Query Object, Facade)](fetch-function.md) | `kernel/package/ezpackagefunctioncollection.php:20` | – |
| `eZPaymentCallbackChecker` | class | – | `kernel/shop/classes/ezpaymentcallbackchecker.php:19` | – |
| `eZPaymentGateway` | class | – | `kernel/shop/classes/ezpaymentgateway.php:17` | – |
| `eZPaymentObject` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/shop/classes/ezpaymentobject.php:22` | – |
| `ezpClusterGateway` | abstract class | [Template Method / abstract base class](template-method.md) | `kernel/clustering/gateway.php:15` | Basis for a cluster gateway. |
| `ezpDfsMySQLiClusterGateway` | class extends ezpClusterGateway | – | `kernel/clustering/dfsmysqli.php:13` | DFS/MySQLi cluster gateway |
| `eZRedirectGateway` | class extends eZPaymentGateway | – | `kernel/shop/classes/ezredirectgateway.php:21` | – |
| `eZRoleFunctionCollection` | class | [Fetch function (Query Object, Facade)](fetch-function.md) | `kernel/role/ezrolefunctioncollection.php:20` | – |
| `eZRSSEditFunction` | class | [Utility (static helper)](utility.md) | `kernel/rss/edit_functions.php:14` | – |
| `eZRSSFunctionCollection` | class | [Fetch function (Query Object, Facade)](fetch-function.md), [Utility (static helper)](utility.md) | `kernel/rss/ezrssfunctioncollection.php:14` | – |
| `eZRSSListPager` | class | [Utility (static helper)](utility.md) | `kernel/rss/ezrsslistpager.php:24` | Works out one page of a list, and the links that reach the other pages. |
| `eZSearchEngine` | class implements ezpSearchEngine | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `kernel/search/plugins/ezsearchengine/ezsearchengine.php:18` | – |
| `eZSearchFunctionCollection` | class | [Fetch function (Query Object, Facade)](fetch-function.md) | `kernel/search/ezsearchfunctioncollection.php:20` | – |
| `eZSectionFunctionCollection` | class | [Fetch function (Query Object, Facade)](fetch-function.md) | `kernel/section/ezsectionfunctioncollection.php:20` | – |
| `eZSetupFunctionCollection` | class | [Fetch function (Query Object, Facade)](fetch-function.md) | `kernel/setup/ezsetupfunctioncollection.php:20` | – |
| `eZSetupSummary` | class | – | `kernel/setup/ezsetup_summary.php:20` | – |
| `eZShopFunctionCollection` | class | [Fetch function (Query Object, Facade)](fetch-function.md) | `kernel/shop/ezshopfunctioncollection.php:20` | – |
| `eZShopFunctions` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `kernel/shop/classes/ezshopfunctions.php:12` | – |
| `eZShopOperationCollection` | class | – | `kernel/shop/ezshopoperationcollection.php:19` | – |
| `eZShopReceipt` | class | [Utility (static helper)](utility.md) | `kernel/shop/classes/ezshopreceipt.php:35` | A permanent, bookmarkable address for a placed order: its receipt. |
| `eZSimplePrice` | class | – | `kernel/shop/classes/ezsimpleprice.php:37` | – |
| `eZStepCreateSites` | class extends eZStepInstaller | [Factory and static factory methods](factory.md) | `kernel/setup/steps/ezstep_create_sites.php:52` | – |
| `eZStepData` | class | – | `kernel/setup/steps/ezstep_data.php:18` | – |
| `eZStepDatabaseChoice` | class extends eZStepInstaller | – | `kernel/setup/steps/ezstep_database_choice.php:21` | – |
| `eZStepDatabaseInit` | class extends eZStepInstaller | – | `kernel/setup/steps/ezstep_database_init.php:18` | – |
| `eZStepEmailSettings` | class extends eZStepInstaller | – | `kernel/setup/steps/ezstep_email_settings.php:18` | – |
| `eZStepFinal` | class extends eZStepInstaller | – | `kernel/setup/steps/ezstep_final.php:18` | – |
| `eZStepInstaller` | class | – | `kernel/setup/steps/ezstep_installer.php:17` | – |
| `eZStepLanguageOptions` | class extends eZStepInstaller | – | `kernel/setup/steps/ezstep_language_options.php:18` | – |
| `eZStepPackageLanguageOptions` | class extends eZStepInstaller | – | `kernel/setup/steps/ezstep_package_language_options.php:18` | – |
| `eZStepRegistration` | class extends eZStepInstaller | – | `kernel/setup/steps/ezstep_registration.php:18` | – |
| `eZStepSecurity` | class extends eZStepInstaller | – | `kernel/setup/steps/ezstep_security.php:18` | – |
| `eZStepSiteAccess` | class extends eZStepInstaller | – | `kernel/setup/steps/ezstep_site_access.php:18` | – |
| `eZStepSiteAdmin` | class extends eZStepInstaller | – | `kernel/setup/steps/ezstep_site_admin.php:18` | – |
| `eZStepSiteDetails` | class extends eZStepInstaller | – | `kernel/setup/steps/ezstep_site_details.php:18` | – |
| `eZStepSitePackages` | class extends eZStepInstaller | – | `kernel/setup/steps/ezstep_site_packages.php:18` | – |
| `eZStepSiteTemplates` | class extends eZStepInstaller | – | `kernel/setup/steps/ezstep_site_templates.php:18` | – |
| `eZStepSiteTypes` | class extends eZStepInstaller | – | `kernel/setup/steps/ezstep_site_types.php:18` | – |
| `eZStepSystemCheck` | class extends eZStepInstaller | – | `kernel/setup/steps/ezstep_system_check.php:18` | – |
| `eZStepSystemFinetune` | class extends eZStepInstaller | – | `kernel/setup/steps/ezstep_system_finetune.php:18` | – |
| `eZStepWelcome` | class extends eZStepInstaller | – | `kernel/setup/steps/ezstep_welcome.php:17` | – |
| `eZURLFunctionCollection` | class | [Fetch function (Query Object, Facade)](fetch-function.md) | `kernel/url/ezurlfunctioncollection.php:20` | – |
| `eZUserFunctionCollection` | class | [Fetch function (Query Object, Facade)](fetch-function.md) | `kernel/user/ezuserfunctioncollection.php:20` | – |
| `eZUserOperationCollection` | class | [Utility (static helper)](utility.md) | `kernel/user/ezuseroperationcollection.php:20` | – |
| `eZWorkflowFunctionCollection` | class | [Fetch function (Query Object, Facade)](fetch-function.md) | `kernel/workflow/ezworkflowfunctioncollection.php:20` | – |
| `eZWorkflowFunctions` | class | [Utility (static helper)](utility.md) | `kernel/workflow/ezworkflowfunctions.php:14` | – |

## Module views (234)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/apikey/list` | `list.php` | – | `kernel/apikey/module.php:24` |
| `/audit/dashboard` | `dashboard.php` | read | `kernel/audit/module.php:27` |
| `/audit/console` | `console.php` | read | `kernel/audit/module.php:33` |
| `/audit/event` | `event.php` | read | `kernel/audit/module.php:40` |
| `/audit/charts` | `charts.php` | read | `kernel/audit/module.php:47` |
| `/audit/alerts` | `alerts.php` | read | `kernel/audit/module.php:54` |
| `/audit/export` | `export.php` | read | `kernel/audit/module.php:61` |
| `/audit/archives` | `archives.php` | manage | `kernel/audit/module.php:68` |
| `/audit/settings` | `settings.php` | manage | `kernel/audit/module.php:75` |
| `/audit/recent` | `recent.php` | read | `kernel/audit/module.php:81` |
| `/class/edit` | `edit.php` | – | `kernel/class/module.php:13` |
| `/class/view` | `view.php` | – | `kernel/class/module.php:20` |
| `/class/copy` | `copy.php` | – | `kernel/class/module.php:26` |
| `/class/down` | `edit.php` | – | `kernel/class/module.php:31` |
| `/class/up` | `edit.php` | – | `kernel/class/module.php:35` |
| `/class/removeclass` | `removeclass.php` | – | `kernel/class/module.php:39` |
| `/class/removegroup` | `removegroup.php` | – | `kernel/class/module.php:43` |
| `/class/classlist` | `classlist.php` | – | `kernel/class/module.php:47` |
| `/class/grouplist` | `grouplist.php` | – | `kernel/class/module.php:51` |
| `/class/groupedit` | `groupedit.php` | – | `kernel/class/module.php:55` |
| `/class/translation` | `translation.php` | – | `kernel/class/module.php:60` |
| `/collaboration/action` | `action.php` | – | `kernel/collaboration/module.php:13` |
| `/collaboration/view` | `view.php` | – | `kernel/collaboration/module.php:24` |
| `/collaboration/item` | `item.php` | – | `kernel/collaboration/module.php:34` |
| `/collaboration/group` | `group.php` | – | `kernel/collaboration/module.php:40` |
| `/content/multiedit` | `multiedit.php` | edit | `kernel/content/module.php:23` |
| `/content/edit` | `edit.php` | edit or create | `kernel/content/module.php:34` |
| `/content/removenode` | `removenode.php` | edit | `kernel/content/module.php:70` |
| `/content/removeassignment` | `removeassignment.php` | edit | `kernel/content/module.php:79` |
| `/content/pdf` | `pdf.php` | pdf | `kernel/content/module.php:89` |
| `/content/view` | `view.php` | read | `kernel/content/module.php:101` |
| `/content/copy` | `copy.php` | read | `kernel/content/module.php:113` |
| `/content/copysubtree` | `copysubtree.php` | create | `kernel/content/module.php:124` |
| `/content/versionview` | `versionview.php` | versionread | `kernel/content/module.php:138` |
| `/content/restore` | `restore.php` | restore | `kernel/content/module.php:158` |
| `/content/search` | `search.php` | read | `kernel/content/module.php:169` |
| `/content/urlalias` | `urlalias.php` | edit | `kernel/content/module.php:176` |
| `/content/urltranslator` | `urlalias_global.php` | urltranslator | `kernel/content/module.php:190` |
| `/content/urlwildcards` | `urlalias_wildcard.php` | urltranslator | `kernel/content/module.php:210` |
| `/content/advancedsearch` | `advancedsearch.php` | read | `kernel/content/module.php:227` |
| `/content/browse` | `browse.php` | read | `kernel/content/module.php:234` |
| `/content/upload` | `upload.php` | create | `kernel/content/module.php:242` |
| `/content/removeobject` | `removeobject.php` | read | `kernel/content/module.php:252` |
| `/content/removeuserobject` | `removeobject.php` | read | `kernel/content/module.php:258` |
| `/content/removemediaobject` | `removeobject.php` | read | `kernel/content/module.php:264` |
| `/content/removeeditversion` | `removeeditversion.php` | read | `kernel/content/module.php:270` |
| `/content/download` | `download.php` | read | `kernel/content/module.php:277` |
| `/content/action` | `action.php` | read | `kernel/content/module.php:284` |
| `/content/collectinformation` | `collectinformation.php` | read | `kernel/content/module.php:356` |
| `/content/draft` | `draft.php` | edit | `kernel/content/module.php:366` |
| `/content/history` | `history.php` | read or edit | `kernel/content/module.php:373` |
| `/content/trash` | `trash.php` | restore | `kernel/content/module.php:389` |
| `/content/translations` | `translations.php` | translations | `kernel/content/module.php:396` |
| `/content/tipafriend` | `tipafriend.php` | tipafriend, read | `kernel/content/module.php:412` |
| `/content/keyword` | `keyword.php` | read | `kernel/content/module.php:418` |
| `/content/collectedinfo` | `collectedinfo.php` | read | `kernel/content/module.php:425` |
| `/content/bookmark` | `bookmark.php` | bookmark | `kernel/content/module.php:431` |
| `/content/pendinglist` | `pendinglist.php` | pendinglist | `kernel/content/module.php:442` |
| `/content/new` | `newcontent.php` | read | `kernel/content/module.php:449` |
| `/content/hide` | `hide.php` | hide | `kernel/content/module.php:455` |
| `/content/move` | `move.php` | edit | `kernel/content/module.php:463` |
| `/content/reverserelatedlist` | `reverserelatedlist.php` | reverserelatedlist | `kernel/content/module.php:469` |
| `/content/translation` | `translation.php` | read | `kernel/content/module.php:476` |
| `/content/treemenu` | `treemenu.php` | read | `kernel/content/module.php:505` |
| `/content/subitemsexport` | `subitemsexport.php` | read | `kernel/content/module.php:513` |
| `/content/dashboard` | `dashboard.php` | dashboard | `kernel/content/module.php:519` |
| `/content/queued` | `queued.php` | edit | `kernel/content/module.php:526` |
| `/content/job` | `job.php` | read | `kernel/content/module.php:535` |
| `/content/jobs` | `jobs.php` | read | `kernel/content/module.php:542` |
| `/contentcheck/unreferenced` | `unreferenced.php` | unreferenced | `kernel/contentcheck/module.php:26` |
| `/contentcheck/trash` | `trash.php` | unreferenced, trash | `kernel/contentcheck/module.php:34` |
| `/contentcheck/brokenrelations` | `brokenrelations.php` | brokenrelations | `kernel/contentcheck/module.php:42` |
| `/contentcheck/fixfiles` | `fixfiles.php` | brokenrelations, fixfiles | `kernel/contentcheck/module.php:49` |
| `/contentcheck/scope` | `scope.php` | – | `kernel/contentcheck/module.php:57` |
| `/contentcheck/storage` | `storage.php` | storage | `kernel/contentcheck/module.php:64` |
| `/contentcheck/storageaction` | `storageaction.php` | storage | `kernel/contentcheck/module.php:72` |
| `/contentfinder/search` | `search.php` | search | `kernel/contentfinder/module.php:24` |
| `/contentfinder/action` | `action.php` | search, delete | `kernel/contentfinder/module.php:31` |
| `/csp/report` | `report.php` | – | `kernel/csp/module.php:22` |
| `/error/view` | `view.php` | – | `kernel/error/module.php:13` |
| `/exphelp/dashboard` | `dashboard.php` | read | `kernel/exphelp/module.php:28` |
| `/exphelp/viewer` | `viewer.php` | read | `kernel/exphelp/module.php:35` |
| `/exphelp/contents` | `contents.php` | read | `kernel/exphelp/module.php:42` |
| `/exphelp/search` | `search.php` | read | `kernel/exphelp/module.php:51` |
| `/exphelp/asset` | `asset.php` | read | `kernel/exphelp/module.php:65` |
| `/exphelp/pdf` | `pdf.php` | read | `kernel/exphelp/module.php:72` |
| `/exphelp/report` | `report.php` | read | `kernel/exphelp/module.php:79` |
| `/exphelp/topic` | `topic.php` | read | `kernel/exphelp/module.php:87` |
| `/exphelp/manual` | `manual.php` | read | `kernel/exphelp/module.php:94` |
| `/exphelp/context` | `context.php` | context | `kernel/exphelp/module.php:102` |
| `/ezinfo/copyright` | `copyright.php` | read | `kernel/ezinfo/module.php:14` |
| `/ezinfo/about` | `about.php` | read | `kernel/ezinfo/module.php:21` |
| `/ezinfo/is_alive` | `isalive.php` | read | `kernel/ezinfo/module.php:27` |
| `/infocollector/overview` | `overview.php` | read | `kernel/infocollector/module.php:13` |
| `/infocollector/collectionlist` | `collectionlist.php` | read | `kernel/infocollector/module.php:23` |
| `/infocollector/view` | `view.php` | read | `kernel/infocollector/module.php:34` |
| `/infocollector/export` | `export.php` | read | `kernel/infocollector/module.php:43` |
| `/layout/set` | `set.php` | – | `kernel/layout/module.php:14` |
| `/mailpreferences/settings` | `settings.php` | – | `kernel/mailpreferences/module.php:22` |
| `/mailpreferences/manage` | `manage.php` | – | `kernel/mailpreferences/module.php:30` |
| `/mailpreferences/unsubscribe` | `unsubscribe.php` | – | `kernel/mailpreferences/module.php:37` |
| `/mailpreferences/confirm` | `confirm.php` | – | `kernel/mailpreferences/module.php:43` |
| `/mailpreferences/request` | `request.php` | – | `kernel/mailpreferences/module.php:49` |
| `/mailpreferences/export` | `export.php` | – | `kernel/mailpreferences/module.php:55` |
| `/mailpreferences/admin` | `admin.php` | administrate | `kernel/mailpreferences/module.php:62` |
| `/notification/settings` | `settings.php` | use | `kernel/notification/module.php:15` |
| `/notification/runfilter` | `runfilter.php` | administrate | `kernel/notification/module.php:23` |
| `/notification/status` | `status.php` | administrate | `kernel/notification/module.php:31` |
| `/notification/job` | `job.php` | administrate | `kernel/notification/module.php:39` |
| `/notification/addtonotification` | `addtonotification.php` | use | `kernel/notification/module.php:46` |
| `/package/list` | `list.php` | list | `kernel/package/module.php:13` |
| `/package/upload` | `upload.php` | import | `kernel/package/module.php:29` |
| `/package/create` | `create.php` | create | `kernel/package/module.php:37` |
| `/package/viewfile` | `viewfile.php` | read | `kernel/package/module.php:84` |
| `/package/compare` | `compare.php` | read | `kernel/package/module.php:95` |
| `/package/install` | `install.php` | install | `kernel/package/module.php:108` |
| `/package/uninstall` | `uninstall.php` | install | `kernel/package/module.php:132` |
| `/pdf/edit` | `edit.php` | edit | `kernel/pdf/module.php:13` |
| `/pdf/list` | `list.php` | edit | `kernel/pdf/module.php:48` |
| `/role/list` | `list.php` | – | `kernel/role/module.php:13` |
| `/role/edit` | `edit.php` | – | `kernel/role/module.php:19` |
| `/role/copy` | `copy.php` | – | `kernel/role/module.php:24` |
| `/role/policyedit` | `policyedit.php` | – | `kernel/role/module.php:29` |
| `/role/view` | `view.php` | – | `kernel/role/module.php:34` |
| `/role/assign` | `assign.php` | – | `kernel/role/module.php:39` |
| `/rss/list` | `list.php` | edit | `kernel/rss/module.php:13` |
| `/rss/edit_export` | `edit_export.php` | edit | `kernel/rss/module.php:28` |
| `/rss/edit_import` | `edit_import.php` | edit | `kernel/rss/module.php:41` |
| `/rss/feed` | `feed.php` | feed | `kernel/rss/module.php:55` |
| `/search/stats` | `stats.php` | – | `kernel/search/module.php:15` |
| `/section/list` | `list.php` | view or edit or assign | `kernel/section/module.php:13` |
| `/section/view` | `view.php` | view or assign | `kernel/section/module.php:20` |
| `/section/edit` | `edit.php` | edit | `kernel/section/module.php:28` |
| `/section/assign` | `assign.php` | assign | `kernel/section/module.php:35` |
| `/settings/view` | `view.php` | – | `kernel/settings/module.php:14` |
| `/settings/edit` | `edit.php` | – | `kernel/settings/module.php:18` |
| `/setup/init` | `ezsetup.php` | install | `kernel/setup/module.php:18` |
| `/setup/cache` | `cache.php` | managecache | `kernel/setup/module.php:25` |
| `/setup/cronjobsstream` | `cronjobsstream.php` | managecronjobs | `kernel/setup/module.php:303` |
| `/setup/staticcachestream` | `staticcachestream.php` | managecache | `kernel/setup/module.php:315` |
| `/setup/systemupgrade` | `systemupgrade.php` | setup | `kernel/setup/module.php:326` |
| `/setup/logs` | `logs.php` | logs | `kernel/setup/module.php:342` |
| `/setup/logsmanage` | `logsmanage.php` | managelogs | `kernel/setup/module.php:357` |
| `/setup/logshealth` | `logshealth.php` | logs | `kernel/setup/module.php:367` |
| `/setup/logsdigest` | `logsdigest.php` | logs | `kernel/setup/module.php:376` |
| `/setup/logsfeed` | `logsfeed.php` | logs | `kernel/setup/module.php:384` |
| `/setup/logsaction` | `logsaction.php` | logs | `kernel/setup/module.php:391` |
| `/setup/toolbarlist` | `toolbarlist.php` | setup | `kernel/setup/module.php:400` |
| `/setup/toolbar` | `toolbar.php` | setup | `kernel/setup/module.php:406` |
| `/setup/menuconfig` | `menuconfig.php` | setup | `kernel/setup/module.php:414` |
| `/setup/templatelist` | `templatelist.php` | setup | `kernel/setup/module.php:422` |
| `/setup/templateview` | `templateview.php` | setup | `kernel/setup/module.php:429` |
| `/setup/templateedit` | `templateedit.php` | setup | `kernel/setup/module.php:439` |
| `/setup/templatecreate` | `templatecreate.php` | setup | `kernel/setup/module.php:448` |
| `/shop/add` | `add.php` | buy | `kernel/shop/module.php:14` |
| `/shop/orderview` | `orderview.php` | buy | `kernel/shop/module.php:20` |
| `/shop/orderreceipt` | `orderreceipt.php` | – | `kernel/shop/module.php:28` |
| `/shop/orderreciept` | `orderreciept.php` | – | `kernel/shop/module.php:35` |
| `/shop/updatebasket` | `updatebasket.php` | buy | `kernel/shop/module.php:41` |
| `/shop/basket` | `basket.php` | buy | `kernel/shop/module.php:47` |
| `/shop/register` | `register.php` | buy | `kernel/shop/module.php:69` |
| `/shop/userregister` | `userregister.php` | buy | `kernel/shop/module.php:79` |
| `/shop/wishlist` | `wishlist.php` | buy | `kernel/shop/module.php:89` |
| `/shop/dashboard` | `dashboard.php` | administrate | `kernel/shop/module.php:98` |
| `/shop/orderlist` | `orderlist.php` | administrate | `kernel/shop/module.php:104` |
| `/shop/archivelist` | `archivelist.php` | administrate | `kernel/shop/module.php:111` |
| `/shop/removeorder` | `removeorder.php` | administrate | `kernel/shop/module.php:118` |
| `/shop/archiveorder` | `archiveorder.php` | administrate | `kernel/shop/module.php:125` |
| `/shop/unarchiveorder` | `unarchiveorder.php` | administrate | `kernel/shop/module.php:132` |
| `/shop/customerlist` | `customerlist.php` | administrate | `kernel/shop/module.php:140` |
| `/shop/customerorderview` | `customerorderview.php` | administrate | `kernel/shop/module.php:147` |
| `/shop/statistics` | `orderstatistics.php` | administrate | `kernel/shop/module.php:153` |
| `/shop/confirmorder` | `confirmorder.php` | buy | `kernel/shop/module.php:159` |
| `/shop/checkout` | `checkout.php` | buy | `kernel/shop/module.php:165` |
| `/shop/vattype` | `vattype.php` | setup | `kernel/shop/module.php:171` |
| `/shop/vatrules` | `vatrules.php` | setup | `kernel/shop/module.php:184` |
| `/shop/editvatrule` | `editvatrule.php` | setup | `kernel/shop/module.php:190` |
| `/shop/productcategories` | `productcategories.php` | setup | `kernel/shop/module.php:208` |
| `/shop/discountgroup` | `discountgroup.php` | setup | `kernel/shop/module.php:221` |
| `/shop/discountgroupedit` | `discountgroupedit.php` | setup | `kernel/shop/module.php:227` |
| `/shop/discountruleedit` | `discountruleedit.php` | setup | `kernel/shop/module.php:234` |
| `/shop/discountgroupview` | `discountgroupmembershipview.php` | setup | `kernel/shop/module.php:242` |
| `/shop/status` | `status.php` | edit_status | `kernel/shop/module.php:249` |
| `/shop/setstatus` | `setstatus.php` | setstatus | `kernel/shop/module.php:255` |
| `/shop/currencylist` | `currencylist.php` | setup | `kernel/shop/module.php:261` |
| `/shop/editcurrency` | `editcurrency.php` | setup | `kernel/shop/module.php:278` |
| `/shop/preferredcurrency` | `preferredcurrency.php` | buy | `kernel/shop/module.php:291` |
| `/shop/productsoverview` | `productsoverview.php` | administrate | `kernel/shop/module.php:297` |
| `/shop/setpreferredcurrency` | `setpreferredcurrency.php` | buy | `kernel/shop/module.php:311` |
| `/shop/setusercountry` | `setusercountry.php` | buy | `kernel/shop/module.php:320` |
| `/state/assign` | `assign.php` | assign | `kernel/state/module.php:15` |
| `/state/groups` | `groups.php` | administrate | `kernel/state/module.php:26` |
| `/state/group` | `group.php` | administrate | `kernel/state/module.php:37` |
| `/state/group_edit` | `group_edit.php` | administrate | `kernel/state/module.php:50` |
| `/state/view` | `view.php` | administrate | `kernel/state/module.php:60` |
| `/state/edit` | `edit.php` | administrate | `kernel/state/module.php:68` |
| `/trigger/list` | `list.php` | – | `kernel/trigger/module.php:13` |
| `/uiswitch/set` | `set.php` | – | `kernel/uiswitch/module.php:24` |
| `/url/list` | `list.php` | – | `kernel/url/module.php:13` |
| `/url/view` | `view.php` | – | `kernel/url/module.php:22` |
| `/url/edit` | `edit.php` | – | `kernel/url/module.php:28` |
| `/user/logout` | `logout.php` | login | `kernel/user/module.php:14` |
| `/user/login` | `login.php` | login | `kernel/user/module.php:20` |
| `/user/setting` | `setting.php` | preferences | `kernel/user/module.php:34` |
| `/user/preferences` | `preferences.php` | login | `kernel/user/module.php:40` |
| `/user/megamenu` | `megamenu.php` | login | `kernel/user/module.php:52` |
| `/user/password` | `password.php` | password | `kernel/user/module.php:58` |
| `/user/forgotpassword` | `forgotpassword.php` | password | `kernel/user/module.php:65` |
| `/user/edit` | `edit.php` | login | `kernel/user/module.php:76` |
| `/user/register` | `register.php` | register | `kernel/user/module.php:86` |
| `/user/activate` | `activate.php` | login | `kernel/user/module.php:96` |
| `/user/success` | `success.php` | register | `kernel/user/module.php:103` |
| `/user/unactivated` | `unactivated.php` | activation | `kernel/user/module.php:110` |
| `/user/hygiene` | `hygiene.php` | hygiene | `kernel/user/module.php:135` |
| `/user/deactivate` | `deactivate.php` | deactivate | `kernel/user/module.php:148` |
| `/user/registration` | `registration.php` | login | `kernel/user/module.php:161` |
| `/visual/toolbarlist` | `toolbarlist.php` | – | `kernel/visual/module.php:15` |
| `/visual/toolbar` | `toolbar.php` | – | `kernel/visual/module.php:20` |
| `/visual/menuconfig` | `menuconfig.php` | – | `kernel/visual/module.php:33` |
| `/visual/templatelist` | `templatelist.php` | – | `kernel/visual/module.php:42` |
| `/visual/templateview` | `templateview.php` | – | `kernel/visual/module.php:48` |
| `/visual/templateedit` | `templateedit.php` | – | `kernel/visual/module.php:57` |
| `/visual/templatecreate` | `templatecreate.php` | – | `kernel/visual/module.php:66` |
| `/workflow/view` | `view.php` | – | `kernel/workflow/module.php:13` |
| `/workflow/edit` | `edit.php` | – | `kernel/workflow/module.php:17` |
| `/workflow/groupedit` | `groupedit.php` | – | `kernel/workflow/module.php:22` |
| `/workflow/down` | `edit.php` | – | `kernel/workflow/module.php:27` |
| `/workflow/up` | `edit.php` | – | `kernel/workflow/module.php:31` |
| `/workflow/workflowlist` | `workflowlist.php` | – | `kernel/workflow/module.php:35` |
| `/workflow/grouplist` | `grouplist.php` | – | `kernel/workflow/module.php:39` |
| `/workflow/process` | `process.php` | – | `kernel/workflow/module.php:43` |
| `/workflow/run` | `run.php` | – | `kernel/workflow/module.php:47` |
| `/workflow/event` | `event.php` | – | `kernel/workflow/module.php:51` |
| `/workflow/processlist` | `processlist.php` | – | `kernel/workflow/module.php:55` |

## Policy functions (87)

| Function | Limitations | Defined in |
|---|---|---|
| `apikey/create` | values | `kernel/apikey/module.php:47` |
| `audit/read` | – | `kernel/audit/module.php:90` |
| `audit/manage` | – | `kernel/audit/module.php:121` |
| `content/bookmark` | – | `kernel/content/module.php:661` |
| `content/move` | – | `kernel/content/module.php:663` |
| `content/read` | Class, Section, Owner, Group, Node, Subtree | `kernel/content/module.php:665` |
| `content/diff` | Class, Section, Owner, Node, Subtree | `kernel/content/module.php:672` |
| `content/view_embed` | Class, Section, Owner, Node, Subtree | `kernel/content/module.php:677` |
| `content/create` | Class, Section, ParentOwner, ParentGroup, ParentClass, ParentDepth, Subtree, Language | `kernel/content/module.php:682` |
| `content/edit` | Class, Section, Owner, Group, Node, Subtree, Language | `kernel/content/module.php:692` |
| `content/publish` | Class, Section, Owner, Group, Node, Subtree, Language | `kernel/content/module.php:701` |
| `content/manage_locations` | Class, Section, Owner, Subtree | `kernel/content/module.php:710` |
| `content/hide` | Class, Section, Owner, Group, Node, Subtree, Language | `kernel/content/module.php:716` |
| `content/reverserelatedlist` | – | `kernel/content/module.php:724` |
| `content/translate` | Class, Section, Owner, Node, Subtree, Language | `kernel/content/module.php:726` |
| `content/remove` | Class, Section, Owner, Node, Subtree | `kernel/content/module.php:732` |
| `content/versionread` | Class, Section, Owner, Status, Node, Subtree | `kernel/content/module.php:740` |
| `content/versionremove` | Class, Section, Owner, Status, Node, Subtree | `kernel/content/module.php:747` |
| `content/pdf` | Class, Section, Owner, Node, Subtree | `kernel/content/module.php:754` |
| `content/translations` | – | `kernel/content/module.php:760` |
| `content/urltranslator` | – | `kernel/content/module.php:761` |
| `content/pendinglist` | – | `kernel/content/module.php:762` |
| `content/restore` | – | `kernel/content/module.php:763` |
| `content/cleantrash` | – | `kernel/content/module.php:764` |
| `content/tipafriend` | – | `kernel/content/module.php:765` |
| `content/dashboard` | – | `kernel/content/module.php:766` |
| `content/view_system_url` | – | `kernel/content/module.php:770` |
| `content/jobs` | – | `kernel/content/module.php:772` |
| `content/publish_without_notification` | – | `kernel/content/module.php:776` |
| `contentcheck/unreferenced` | – | `kernel/contentcheck/module.php:80` |
| `contentcheck/trash` | – | `kernel/contentcheck/module.php:82` |
| `contentcheck/brokenrelations` | – | `kernel/contentcheck/module.php:84` |
| `contentcheck/fixfiles` | – | `kernel/contentcheck/module.php:87` |
| `contentcheck/storage` | – | `kernel/contentcheck/module.php:89` |
| `contentcheck/quarantine` | – | `kernel/contentcheck/module.php:91` |
| `contentcheck/permissions` | – | `kernel/contentcheck/module.php:93` |
| `contentfinder/search` | – | `kernel/contentfinder/module.php:39` |
| `contentfinder/delete` | – | `kernel/contentfinder/module.php:41` |
| `exphelp/read` | – | `kernel/exphelp/module.php:110` |
| `exphelp/context` | – | `kernel/exphelp/module.php:112` |
| `ezinfo/read` | – | `kernel/ezinfo/module.php:33` |
| `infocollector/read` | – | `kernel/infocollector/module.php:50` |
| `mailpreferences/administrate` | – | `kernel/mailpreferences/module.php:71` |
| `mailpreferences/export` | – | `kernel/mailpreferences/module.php:72` |
| `notification/use` | – | `kernel/notification/module.php:53` |
| `notification/administrate` | – | `kernel/notification/module.php:54` |
| `package/read` | Type | `kernel/package/module.php:170` |
| `package/list` | Type | `kernel/package/module.php:171` |
| `package/create` | Type, CreatorType, Role | `kernel/package/module.php:172` |
| `package/edit` | Type | `kernel/package/module.php:175` |
| `package/remove` | Type | `kernel/package/module.php:176` |
| `package/install` | Type | `kernel/package/module.php:177` |
| `package/import` | Type | `kernel/package/module.php:178` |
| `package/export` | Type | `kernel/package/module.php:179` |
| `package/export_extension` | – | `kernel/package/module.php:182` |
| `pdf/create` | – | `kernel/pdf/module.php:59` |
| `pdf/edit` | – | `kernel/pdf/module.php:60` |
| `rss/feed` | – | `kernel/rss/module.php:62` |
| `rss/edit` | – | `kernel/rss/module.php:63` |
| `section/assign` | Class, Section, Owner, NewSection | `kernel/section/module.php:79` |
| `section/edit` | – | `kernel/section/module.php:83` |
| `section/view` | – | `kernel/section/module.php:84` |
| `setup/administrate` | – | `kernel/setup/module.php:459` |
| `setup/install` | – | `kernel/setup/module.php:460` |
| `setup/logs` | – | `kernel/setup/module.php:462` |
| `setup/managelogs` | – | `kernel/setup/module.php:463` |
| `setup/unmasklogs` | – | `kernel/setup/module.php:464` |
| `setup/managecache` | – | `kernel/setup/module.php:465` |
| `setup/managecronjobs` | – | `kernel/setup/module.php:466` |
| `setup/preload` | – | `kernel/setup/module.php:467` |
| `setup/setup` | – | `kernel/setup/module.php:468` |
| `setup/system_info` | – | `kernel/setup/module.php:469` |
| `shop/setup` | – | `kernel/shop/module.php:349` |
| `shop/administrate` | – | `kernel/shop/module.php:350` |
| `shop/buy` | – | `kernel/shop/module.php:351` |
| `shop/edit_status` | – | `kernel/shop/module.php:352` |
| `shop/setstatus` | FromStatus, ToStatus | `kernel/shop/module.php:353` |
| `state/administrate` | – | `kernel/state/module.php:132` |
| `state/assign` | Class, Section, Owner, Group, Node, Subtree | `kernel/state/module.php:134` |
| `user/login` | SiteAccess | `kernel/user/module.php:182` |
| `user/password` | – | `kernel/user/module.php:183` |
| `user/preferences` | – | `kernel/user/module.php:184` |
| `user/register` | – | `kernel/user/module.php:185` |
| `user/selfedit` | – | `kernel/user/module.php:186` |
| `user/activation` | – | `kernel/user/module.php:187` |
| `user/hygiene` | – | `kernel/user/module.php:188` |
| `user/deactivate` | – | `kernel/user/module.php:189` |

## Fetch functions (173)

| Fetch | Implemented by | Defined in |
|---|---|---|
| `fetch( 'apikey', 'can_create' )` | `expApiKeyFunctionCollection::fetchCanCreate()` | `kernel/apikey/function_definition.php:17` |
| `fetch( 'apikey', 'counts' )` | `expApiKeyFunctionCollection::fetchCounts()` | `kernel/apikey/function_definition.php:22` |
| `fetch( 'audit', 'events' )` | `expAuditFunctionCollection::fetchEvents()` | `kernel/audit/function_definition.php:14` |
| `fetch( 'audit', 'count' )` | `expAuditFunctionCollection::fetchCount()` | `kernel/audit/function_definition.php:35` |
| `fetch( 'audit', 'event' )` | `expAuditFunctionCollection::fetchEvent()` | `kernel/audit/function_definition.php:54` |
| `fetch( 'audit', 'chain_status' )` | `expAuditFunctionCollection::fetchChainStatus()` | `kernel/audit/function_definition.php:59` |
| `fetch( 'audit', 'can_read' )` | `expAuditFunctionCollection::fetchCanRead()` | `kernel/audit/function_definition.php:66` |
| `fetch( 'audit', 'hidden_menu_links' )` | `expAuditFunctionCollection::fetchHiddenMenuLinks()` | `kernel/audit/function_definition.php:72` |
| `fetch( 'class', 'list' )` | `eZClassFunctionCollection::fetchClassList()` | `kernel/class/function_definition.php:11` |
| `fetch( 'class', 'list_by_groups' )` | `eZClassFunctionCollection::fetchClassListByGroups()` | `kernel/class/function_definition.php:25` |
| `fetch( 'class', 'latest_list' )` | `eZClassFunctionCollection::fetchLatestClassList()` | `kernel/class/function_definition.php:39` |
| `fetch( 'class', 'attribute_list' )` | `eZClassFunctionCollection::fetchClassAttributeList()` | `kernel/class/function_definition.php:52` |
| `fetch( 'class', 'override_template_list' )` | `eZClassFunctionCollection::fetchOverrideTemplateList()` | `kernel/class/function_definition.php:63` |
| `fetch( 'collaboration', 'participant' )` | `eZCollaborationFunctionCollection::fetchParticipant()` | `kernel/collaboration/function_definition.php:11` |
| `fetch( 'collaboration', 'participant_list' )` | `eZCollaborationFunctionCollection::fetchParticipantList()` | `kernel/collaboration/function_definition.php:22` |
| `fetch( 'collaboration', 'participant_map' )` | `eZCollaborationFunctionCollection::fetchParticipantMap()` | `kernel/collaboration/function_definition.php:39` |
| `fetch( 'collaboration', 'message_list' )` | `eZCollaborationFunctionCollection::fetchMessageList()` | `kernel/collaboration/function_definition.php:59` |
| `fetch( 'collaboration', 'item_list' )` | `eZCollaborationFunctionCollection::fetchItemList()` | `kernel/collaboration/function_definition.php:76` |
| `fetch( 'collaboration', 'inbox' )` | `eZCollaborationFunctionCollection::fetchInbox()` | `kernel/collaboration/function_definition.php:102` |
| `fetch( 'collaboration', 'inbox_row' )` | `eZCollaborationFunctionCollection::fetchInboxRow()` | `kernel/collaboration/function_definition.php:125` |
| `fetch( 'collaboration', 'item_count' )` | `eZCollaborationFunctionCollection::fetchItemCount()` | `kernel/collaboration/function_definition.php:132` |
| `fetch( 'collaboration', 'group_tree' )` | `eZCollaborationFunctionCollection::fetchGroupTree()` | `kernel/collaboration/function_definition.php:149` |
| `fetch( 'collaboration', 'tree_count' )` | `eZContentFunctionCollection::fetchObjectTreeCount()` | `kernel/collaboration/function_definition.php:169` |
| `fetch( 'content', 'object' )` | `eZContentFunctionCollection::fetchContentObject()` | `kernel/content/function_definition.php:12` |
| `fetch( 'content', 'version' )` | `eZContentFunctionCollection::fetchContentVersion()` | `kernel/content/function_definition.php:25` |
| `fetch( 'content', 'node' )` | `eZContentFunctionCollection::fetchContentNode()` | `kernel/content/function_definition.php:37` |
| `fetch( 'content', 'locale_list' )` | `eZContentFunctionCollection::fetchLocaleList()` | `kernel/content/function_definition.php:58` |
| `fetch( 'content', 'locale' )` | `eZContentFunctionCollection::fetchLocale()` | `kernel/content/function_definition.php:67` |
| `fetch( 'content', 'prioritized_languages' )` | `eZContentFunctionCollection::fetchPrioritizedLanguages()` | `kernel/content/function_definition.php:77` |
| `fetch( 'content', 'prioritized_language_codes' )` | `eZContentFunctionCollection::fetchPrioritizedLanguageCodes()` | `kernel/content/function_definition.php:83` |
| `fetch( 'content', 'translation_list' )` | `eZContentFunctionCollection::fetchTranslationList()` | `kernel/content/function_definition.php:89` |
| `fetch( 'content', 'language_states' )` | `expContentLanguageStates::fetchForObject()` | `kernel/content/function_definition.php:96` |
| `fetch( 'content', 'non_translation_list' )` | `eZContentFunctionCollection::fetchNonTranslationList()` | `kernel/content/function_definition.php:104` |
| `fetch( 'content', 'class' )` | `eZContentFunctionCollection::fetchClass()` | `kernel/content/function_definition.php:115` |
| `fetch( 'content', 'class_attribute_list' )` | `eZContentFunctionCollection::fetchClassAttributeList()` | `kernel/content/function_definition.php:123` |
| `fetch( 'content', 'class_attribute' )` | `eZContentFunctionCollection::fetchClassAttribute()` | `kernel/content/function_definition.php:135` |
| `fetch( 'content', 'calendar' )` | `eZContentFunctionCollection::calendar()` | `kernel/content/function_definition.php:147` |
| `fetch( 'content', 'list' )` | `eZContentFunctionCollection::fetchObjectTree()` | `kernel/content/function_definition.php:208` |
| `fetch( 'content', 'list_count' )` | `eZContentFunctionCollection::fetchObjectTreeCount()` | `kernel/content/function_definition.php:292` |
| `fetch( 'content', 'tree' )` | `eZContentFunctionCollection::fetchObjectTree()` | `kernel/content/function_definition.php:348` |
| `fetch( 'content', 'tree_count' )` | `eZContentFunctionCollection::fetchObjectTreeCount()` | `kernel/content/function_definition.php:434` |
| `fetch( 'content', 'search' )` | `eZContentFunctionCollection::fetchContentSearch()` | `kernel/content/function_definition.php:491` |
| `fetch( 'content', 'trash_count' )` | `eZContentFunctionCollection::fetchTrashObjectCount()` | `kernel/content/function_definition.php:544` |
| `fetch( 'content', 'trash_object_list' )` | `eZContentFunctionCollection::fetchTrashObjectList()` | `kernel/content/function_definition.php:558` |
| `fetch( 'content', 'draft_count' )` | `eZContentFunctionCollection::fetchDraftVersionCount()` | `kernel/content/function_definition.php:588` |
| `fetch( 'content', 'draft_version_list' )` | `eZContentFunctionCollection::fetchDraftVersionList()` | `kernel/content/function_definition.php:595` |
| `fetch( 'content', 'pending_count' )` | `eZContentFunctionCollection::fetchPendingCount()` | `kernel/content/function_definition.php:609` |
| `fetch( 'content', 'pending_list' )` | `eZContentFunctionCollection::fetchPendingList()` | `kernel/content/function_definition.php:616` |
| `fetch( 'content', 'version_count' )` | `eZContentFunctionCollection::fetchVersionCount()` | `kernel/content/function_definition.php:630` |
| `fetch( 'content', 'version_list' )` | `eZContentFunctionCollection::fetchVersionList()` | `kernel/content/function_definition.php:639` |
| `fetch( 'content', 'can_instantiate_class_list' )` | `eZContentFunctionCollection::canInstantiateClassList()` | `kernel/content/function_definition.php:662` |
| `fetch( 'content', 'class_list' )` | `eZContentFunctionCollection::fetchClassList()` | `kernel/content/function_definition.php:692` |
| `fetch( 'content', 'can_instantiate_classes' )` | `eZContentFunctionCollection::canInstantiateClasses()` | `kernel/content/function_definition.php:714` |
| `fetch( 'content', 'contentobject_attributes' )` | `eZContentFunctionCollection::contentobjectAttributes()` | `kernel/content/function_definition.php:723` |
| `fetch( 'content', 'bookmarks' )` | `eZContentFunctionCollection::fetchBookmarks()` | `kernel/content/function_definition.php:737` |
| `fetch( 'content', 'bookmark_rows' )` | `eZContentFunctionCollection::fetchBookmarkRows()` | `kernel/content/function_definition.php:751` |
| `fetch( 'content', 'bookmark_folders' )` | `eZContentFunctionCollection::fetchBookmarkFolders()` | `kernel/content/function_definition.php:761` |
| `fetch( 'content', 'recent' )` | `eZContentFunctionCollection::fetchRecent()` | `kernel/content/function_definition.php:768` |
| `fetch( 'content', 'section_list' )` | `eZContentFunctionCollection::fetchSectionList()` | `kernel/content/function_definition.php:775` |
| `fetch( 'content', 'tipafriend_top_list' )` | `eZContentFunctionCollection::fetchTipafriendTopList()` | `kernel/content/function_definition.php:782` |
| `fetch( 'content', 'view_top_list' )` | `eZContentFunctionCollection::fetchMostViewedTopList()` | `kernel/content/function_definition.php:816` |
| `fetch( 'content', 'collected_info_count' )` | `eZContentFunctionCollection::fetchCollectedInfoCount()` | `kernel/content/function_definition.php:838` |
| `fetch( 'content', 'collected_info_count_list' )` | `eZContentFunctionCollection::fetchCollectedInfoCountList()` | `kernel/content/function_definition.php:864` |
| `fetch( 'content', 'collected_info_collection' )` | `eZContentFunctionCollection::fetchCollectedInfoCollection()` | `kernel/content/function_definition.php:875` |
| `fetch( 'content', 'collected_info_list' )` | `eZContentFunctionCollection::fetchCollectionsList()` | `kernel/content/function_definition.php:889` |
| `fetch( 'content', 'object_by_attribute' )` | `eZContentFunctionCollection::fetchObjectByAttribute()` | `kernel/content/function_definition.php:919` |
| `fetch( 'content', 'object_count_by_user_id' )` | `eZContentFunctionCollection::fetchObjectCountByUserID()` | `kernel/content/function_definition.php:929` |
| `fetch( 'content', 'same_classattribute_node' )` | `eZContentFunctionCollection::fetchSameClassAttributeNodeList()` | `kernel/content/function_definition.php:947` |
| `fetch( 'content', 'keyword' )` | `eZContentFunctionCollection::fetchKeyword()` | `kernel/content/function_definition.php:962` |
| `fetch( 'content', 'keyword_count' )` | `eZContentFunctionCollection::fetchKeywordCount()` | `kernel/content/function_definition.php:1008` |
| `fetch( 'content', 'assigned_nodes' )` | `eZContentFunctionCollection::fetchAssignedNodes()` | `kernel/content/function_definition.php:1048` |
| `fetch( 'content', 'assigned_node_count' )` | `eZContentFunctionCollection::fetchAssignedNodeCount()` | `kernel/content/function_definition.php:1073` |
| `fetch( 'content', 'access' )` | `eZContentFunctionCollection::checkAccess()` | `kernel/content/function_definition.php:1081` |
| `fetch( 'content', 'navigation_parts' )` | `eZContentFunctionCollection::fetchNavigationParts()` | `kernel/content/function_definition.php:1106` |
| `fetch( 'content', 'navigation_part' )` | `eZContentFunctionCollection::fetchNavigationPart()` | `kernel/content/function_definition.php:1114` |
| `fetch( 'content', 'related_objects' )` | `eZContentFunctionCollection::fetchRelatedObjects()` | `kernel/content/function_definition.php:1124` |
| `fetch( 'content', 'related_objects_count' )` | `eZContentFunctionCollection::fetchRelatedObjectsCount()` | `kernel/content/function_definition.php:1173` |
| `fetch( 'content', 'reverse_related_objects' )` | `eZContentFunctionCollection::fetchReverseRelatedObjects()` | `kernel/content/function_definition.php:1190` |
| `fetch( 'content', 'reverse_related_objects_count' )` | `eZContentFunctionCollection::fetchReverseRelatedObjectsCount()` | `kernel/content/function_definition.php:1234` |
| `fetch( 'content', 'available_sort_fields' )` | `eZContentFunctionCollection::fetchAvailableSortFieldList()` | `kernel/content/function_definition.php:1254` |
| `fetch( 'content', 'country_list' )` | `eZContentFunctionCollection::fetchCountryList()` | `kernel/content/function_definition.php:1261` |
| `fetch( 'content', 'country_groups' )` | `eZContentFunctionCollection::fetchCountryGroups()` | `kernel/content/function_definition.php:1275` |
| `fetch( 'content', 'related_objects_ids' )` | `eZContentFunctionCollection::fetchRelatedObjectsID()` | `kernel/content/function_definition.php:1285` |
| `fetch( 'content', 'reverse_related_objects_ids' )` | `eZContentFunctionCollection::fetchReverseRelatedObjectsID()` | `kernel/content/function_definition.php:1302` |
| `fetch( 'content', 'site_data' )` | `eZContentFunctionCollection::fetchSiteData()` | `kernel/content/function_definition.php:1321` |
| `fetch( 'content', 'content_tree_menu_expiry' )` | `eZContentFunctionCollection::fetchContentTreeMenuExpiry()` | `kernel/content/function_definition.php:1330` |
| `fetch( 'exphelp', 'context' )` | `expHelpFunctionCollection::fetchContext()` | `kernel/exphelp/function_definition.php:16` |
| `fetch( 'exphelp', 'license' )` | `expHelpFunctionCollection::fetchLicense()` | `kernel/exphelp/function_definition.php:25` |
| `fetch( 'exphelp', 'context_menu' )` | `expHelpFunctionCollection::fetchContextMenu()` | `kernel/exphelp/function_definition.php:33` |
| `fetch( 'infocollector', 'collected_info_count' )` | `eZInfocollectorFunctionCollection::fetchCollectedInfoCount()` | `kernel/infocollector/function_definition.php:13` |
| `fetch( 'infocollector', 'collected_info_count_list' )` | `eZInfocollectorFunctionCollection::fetchCollectedInfoCountList()` | `kernel/infocollector/function_definition.php:39` |
| `fetch( 'infocollector', 'collected_info_collection' )` | `eZInfocollectorFunctionCollection::fetchCollectedInfoCollection()` | `kernel/infocollector/function_definition.php:50` |
| `fetch( 'infocollector', 'collected_info_list' )` | `eZInfocollectorFunctionCollection::fetchCollectionsList()` | `kernel/infocollector/function_definition.php:64` |
| `fetch( 'layout', 'sitedesign_list' )` | `eZLayoutFunctionCollection::fetchSitedesignList()` | `kernel/layout/function_definition.php:11` |
| `fetch( 'notification', 'handler_list' )` | `eZNotificationFunctionCollection::handlerList()` | `kernel/notification/function_definition.php:11` |
| `fetch( 'notification', 'digest_handlers' )` | `eZNotificationFunctionCollection::digestHandlerList()` | `kernel/notification/function_definition.php:18` |
| `fetch( 'notification', 'digest_items' )` | `eZNotificationFunctionCollection::digestItems()` | `kernel/notification/function_definition.php:31` |
| `fetch( 'notification', 'event_content' )` | `eZNotificationFunctionCollection::eventContent()` | `kernel/notification/function_definition.php:47` |
| `fetch( 'notification', 'subscribed_nodes' )` | `eZNotificationFunctionCollection::subscribedNodes()` | `kernel/notification/function_definition.php:56` |
| `fetch( 'notification', 'subscribed_nodes_count' )` | `eZNotificationFunctionCollection::subscribedNodesCount()` | `kernel/notification/function_definition.php:70` |
| `fetch( 'package', 'list' )` | `eZPackageFunctionCollection::fetchList()` | `kernel/package/function_definition.php:11` |
| `fetch( 'package', 'list_count' )` | `eZPackageFunctionCollection::fetchListCount()` | `kernel/package/function_definition.php:31` |
| `fetch( 'package', 'maintainer_role_list' )` | `eZPackageFunctionCollection::fetchMaintainerRoleList()` | `kernel/package/function_definition.php:43` |
| `fetch( 'package', 'can_create' )` | `eZPackageFunctionCollection::canCreate()` | `kernel/package/function_definition.php:55` |
| `fetch( 'package', 'can_edit' )` | `eZPackageFunctionCollection::canEdit()` | `kernel/package/function_definition.php:61` |
| `fetch( 'package', 'can_import' )` | `eZPackageFunctionCollection::canImport()` | `kernel/package/function_definition.php:67` |
| `fetch( 'package', 'can_install' )` | `eZPackageFunctionCollection::canInstall()` | `kernel/package/function_definition.php:73` |
| `fetch( 'package', 'can_export' )` | `eZPackageFunctionCollection::canExport()` | `kernel/package/function_definition.php:79` |
| `fetch( 'package', 'content_export_access' )` | `eZPackageFunctionCollection::contentExportAccess()` | `kernel/package/function_definition.php:87` |
| `fetch( 'package', 'can_read' )` | `eZPackageFunctionCollection::canRead()` | `kernel/package/function_definition.php:93` |
| `fetch( 'package', 'can_list' )` | `eZPackageFunctionCollection::canList()` | `kernel/package/function_definition.php:99` |
| `fetch( 'package', 'can_remove' )` | `eZPackageFunctionCollection::canRemove()` | `kernel/package/function_definition.php:105` |
| `fetch( 'package', 'item' )` | `eZPackageFunctionCollection::fetchPackage()` | `kernel/package/function_definition.php:112` |
| `fetch( 'package', 'dependent_list' )` | `eZPackageFunctionCollection::fetchDependentPackageList()` | `kernel/package/function_definition.php:124` |
| `fetch( 'package', 'repository_list' )` | `eZPackageFunctionCollection::fetchRepositoryList()` | `kernel/package/function_definition.php:139` |
| `fetch( 'role', 'role' )` | `eZRoleFunctionCollection::fetchRole()` | `kernel/role/function_definition.php:11` |
| `fetch( 'role', 'policies' )` | `eZRoleFunctionCollection::fetchRolePolicies()` | `kernel/role/function_definition.php:20` |
| `fetch( 'role', 'policy_count' )` | `eZRoleFunctionCollection::fetchRolePolicyCount()` | `kernel/role/function_definition.php:37` |
| `fetch( 'rss', 'has_export_by_node' )` | `eZRSSFunctionCollection::hasExportByNode()` | `kernel/rss/function_definition.php:14` |
| `fetch( 'rss', 'export_by_node' )` | `eZRSSFunctionCollection::exportByNode()` | `kernel/rss/function_definition.php:22` |
| `fetch( 'search', 'list_count' )` | `eZSearchFunctionCollection::fetchSearchListCount()` | `kernel/search/function_definition.php:11` |
| `fetch( 'search', 'list' )` | `eZSearchFunctionCollection::fetchSearchList()` | `kernel/search/function_definition.php:17` |
| `fetch( 'section', 'object' )` | `eZSectionFunctionCollection::fetchSectionObject()` | `kernel/section/function_definition.php:12` |
| `fetch( 'section', 'list' )` | `eZSectionFunctionCollection::fetchSectionList()` | `kernel/section/function_definition.php:25` |
| `fetch( 'section', 'object_list' )` | `eZSectionFunctionCollection::fetchObjectList()` | `kernel/section/function_definition.php:31` |
| `fetch( 'section', 'object_list_count' )` | `eZSectionFunctionCollection::fetchObjectListCount()` | `kernel/section/function_definition.php:55` |
| `fetch( 'section', 'roles' )` | `eZSectionFunctionCollection::fetchRoles()` | `kernel/section/function_definition.php:67` |
| `fetch( 'section', 'user_roles' )` | `eZSectionFunctionCollection::fetchUserRoles()` | `kernel/section/function_definition.php:75` |
| `fetch( 'setup', 'version' )` | `eZSetupFunctionCollection::fetchFullVersionString()` | `kernel/setup/function_definition.php:12` |
| `fetch( 'setup', 'alias' )` | `eZSetupFunctionCollection::fetchVersionAlias()` | `kernel/setup/function_definition.php:18` |
| `fetch( 'setup', 'major_version' )` | `eZSetupFunctionCollection::fetchMajorVersion()` | `kernel/setup/function_definition.php:24` |
| `fetch( 'setup', 'minor_version' )` | `eZSetupFunctionCollection::fetchMinorVersion()` | `kernel/setup/function_definition.php:30` |
| `fetch( 'setup', 'release' )` | `eZSetupFunctionCollection::fetchRelease()` | `kernel/setup/function_definition.php:36` |
| `fetch( 'setup', 'state' )` | `eZSetupFunctionCollection::fetchState()` | `kernel/setup/function_definition.php:42` |
| `fetch( 'setup', 'is_development' )` | `eZSetupFunctionCollection::fetchIsDevelopment()` | `kernel/setup/function_definition.php:48` |
| `fetch( 'setup', 'database_version' )` | `eZSetupFunctionCollection::fetchDatabaseVersion()` | `kernel/setup/function_definition.php:54` |
| `fetch( 'setup', 'database_release' )` | `eZSetupFunctionCollection::fetchDatabaseRelease()` | `kernel/setup/function_definition.php:63` |
| `fetch( 'setup', 'edition' )` | `eZSetupFunctionCollection::fetchEdition()` | `kernel/setup/function_definition.php:70` |
| `fetch( 'setup', 'extension_counts' )` | `eZSetupFunctionCollection::fetchExtensionCounts()` | `kernel/setup/function_definition.php:76` |
| `fetch( 'setup', 'cronjob_pauses' )` | `eZSetupFunctionCollection::fetchCronjobPauses()` | `kernel/setup/function_definition.php:86` |
| `fetch( 'setup', 'log_files' )` | `eZSetupFunctionCollection::fetchLogFiles()` | `kernel/setup/function_definition.php:94` |
| `fetch( 'shop', 'basket' )` | `eZShopFunctionCollection::fetchBasket()` | `kernel/shop/function_definition.php:11` |
| `fetch( 'shop', 'best_sell_list' )` | `eZShopFunctionCollection::fetchBestSellList()` | `kernel/shop/function_definition.php:18` |
| `fetch( 'shop', 'related_purchase' )` | `eZShopFunctionCollection::fetchRelatedPurchaseList()` | `kernel/shop/function_definition.php:54` |
| `fetch( 'shop', 'wish_list' )` | `eZShopFunctionCollection::fetchWishList()` | `kernel/shop/function_definition.php:66` |
| `fetch( 'shop', 'wish_list_count' )` | `eZShopFunctionCollection::fetchWishListCount()` | `kernel/shop/function_definition.php:83` |
| `fetch( 'shop', 'current_wish_list' )` | `eZShopFunctionCollection::fetchCurrentWishList()` | `kernel/shop/function_definition.php:92` |
| `fetch( 'shop', 'order' )` | `eZShopFunctionCollection::fetchOrder()` | `kernel/shop/function_definition.php:99` |
| `fetch( 'shop', 'order_status_history_count' )` | `eZShopFunctionCollection::fetchOrderStatusHistoryCount()` | `kernel/shop/function_definition.php:107` |
| `fetch( 'shop', 'order_status_history' )` | `eZShopFunctionCollection::fetchOrderStatusHistory()` | `kernel/shop/function_definition.php:115` |
| `fetch( 'shop', 'currency_list' )` | `eZShopFunctionCollection::fetchCurrencyList()` | `kernel/shop/function_definition.php:123` |
| `fetch( 'shop', 'currency' )` | `eZShopFunctionCollection::fetchCurrency()` | `kernel/shop/function_definition.php:132` |
| `fetch( 'shop', 'preferred_currency_code' )` | `eZShopFunctionCollection::fetchPreferredCurrencyCode()` | `kernel/shop/function_definition.php:141` |
| `fetch( 'shop', 'user_country' )` | `eZShopFunctionCollection::fetchUserCountry()` | `kernel/shop/function_definition.php:148` |
| `fetch( 'shop', 'product_category_list' )` | `eZShopFunctionCollection::fetchProductCategoryList()` | `kernel/shop/function_definition.php:155` |
| `fetch( 'shop', 'product_category' )` | `eZShopFunctionCollection::fetchProductCategory()` | `kernel/shop/function_definition.php:163` |
| `fetch( 'uiswitch', 'toolbar' )` | `expUISwitch::fetchToolbar()` | `kernel/uiswitch/function_definition.php:13` |
| `fetch( 'url', 'list' )` | `eZURLFunctionCollection::fetchList()` | `kernel/url/function_definition.php:11` |
| `fetch( 'url', 'list_count' )` | `eZURLFunctionCollection::fetchListCount()` | `kernel/url/function_definition.php:28` |
| `fetch( 'user', 'current_user' )` | `eZUserFunctionCollection::fetchCurrentUser()` | `kernel/user/function_definition.php:11` |
| `fetch( 'user', 'is_logged_in' )` | `eZUserFunctionCollection::fetchIsLoggedIn()` | `kernel/user/function_definition.php:18` |
| `fetch( 'user', 'logged_in_count' )` | `eZUserFunctionCollection::fetchLoggedInCount()` | `kernel/user/function_definition.php:27` |
| `fetch( 'user', 'anonymous_count' )` | `eZUserFunctionCollection::fetchAnonymousCount()` | `kernel/user/function_definition.php:34` |
| `fetch( 'user', 'logged_in_list' )` | `eZUserFunctionCollection::fetchLoggedInList()` | `kernel/user/function_definition.php:41` |
| `fetch( 'user', 'logged_in_users' )` | `eZUserFunctionCollection::fetchLoggedInUsers()` | `kernel/user/function_definition.php:56` |
| `fetch( 'user', 'user_role' )` | `eZUserFunctionCollection::fetchUserRole()` | `kernel/user/function_definition.php:70` |
| `fetch( 'user', 'member_of' )` | `eZUserFunctionCollection::fetchMemberOf()` | `kernel/user/function_definition.php:80` |
| `fetch( 'user', 'has_access_to' )` | `eZUserFunctionCollection::hasAccessTo()` | `kernel/user/function_definition.php:89` |
| `fetch( 'user', 'can_open' )` | `eZUserFunctionCollection::canOpen()` | `kernel/user/function_definition.php:108` |
| `fetch( 'user', 'account_status' )` | `eZUserFunctionCollection::fetchAccountStatus()` | `kernel/user/function_definition.php:121` |
| `fetch( 'user', 'deactivation_offers' )` | `eZUserFunctionCollection::fetchDeactivationOffers()` | `kernel/user/function_definition.php:132` |
| `fetch( 'workflow', 'workflow_statuses' )` | `eZWorkflowFunctionCollection::fetchWorkflowStatuses()` | `kernel/workflow/function_definition.php:12` |
| `fetch( 'workflow', 'workflow_type_statuses' )` | `eZWorkflowFunctionCollection::fetchWorkflowTypeStatuses()` | `kernel/workflow/function_definition.php:19` |

## Event hooks (6)

| Event | Kind | Called in |
|---|---|---|
| `content/cache/version` | notify | `kernel/content/attribute_edit.php:325` |
| `content/notification/create` | filter | `kernel/content/ezcontentoperationcollection.php:920` |
| `content/cache` | filter | `kernel/content/ezcontentoperationcollection.php:1414` |
| `content/cache` | notify | `kernel/content/ezcontentoperationcollection.php:1530` |
| `content/state/assign` | notify | `kernel/content/ezcontentoperationcollection.php:2034` |
| `content/cache/version` | notify | `kernel/content/multiedit_functions.php:496` |
