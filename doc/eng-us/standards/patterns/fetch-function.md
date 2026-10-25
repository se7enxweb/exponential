---
title: Fetch function (Query Object, Facade)
audience: developer
category: standards
order: 315
summary: A named query that templates call with fetch( module, function, hash(...) ); the kernel maps it to a method of a function collection class. 352 instances in Exponential.
keywords: design pattern, fetch-function, plugins, pattern catalogue
type: catalogue
---
# Fetch function (Query Object, Facade)

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

A named query that templates call with fetch( module, function, hash(...) ); the kernel maps it to a method of a function collection class.

| | |
|---|---|
| Group | Plugins |
| Instances | 352 |
| Recognized by | An entry of a module's function_definition.php, or a class named *FunctionCollection. |
| Extension point | function_definition.php in the module directory of an extension (module.ini ModuleRepositories[]). |

## Classes by area

### extension/cjw_newsletter (1)

[All patterns of extension/cjw_newsletter](area-extension-cjw-newsletter.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `CjwNewsletterFunctionCollection` | class | `extension/cjw_newsletter/modules/newsletter/cjwnewsletterfunctioncollection.php:21` | Class description here | – |

### extension/cjw_tmv (1)

[All patterns of extension/cjw_tmv](area-extension-cjw-tmv.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `cjwTmvFunctionCollection` | class | `extension/cjw_tmv/classes/cjwtmvfunctioncollection.php:10` | The fetch functions of the cjw_tmv module (modules/cjw_tmv/function_definition.php). | – |

### extension/expai (6)

[All patterns of extension/expai](area-extension-expai.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expAiAltTextFunctionCollection` | class | `extension/expai/classes/alttext/expaialttextfunctioncollection.php:14` | fetch( 'expai', 'alt_text_model', hash( 'object', $object ) ): the vision model the "Write alt text" button uses for the images of this object, or wh… | – |
| `expAiBudgetFunctionCollection` | class | `extension/expai/classes/budget/expaibudgetfunctioncollection.php:14` | The template fetch function fetch( 'expai', 'budget_status' ): the monthly site budget of queued requests (expAiBudget::status()), for users with exp… | – |
| `expAiChainFunctionCollection` | class | `extension/expai/classes/chain/expaichainfunctioncollection.php:18` | Template fetch functions of AI chains: | – |
| `expAiFunctionCollection` | class | `extension/expai/classes/expaifunctioncollection.php:14` | The template fetch functions of expai (modules/expai/function_definition.php). | – |
| `expAiPresetFunctionCollection` | class | `extension/expai/classes/fieldactions/expaipresetfunctioncollection.php:15` | fetch( 'expai', 'edit_presets', hash( 'object', $object, 'attributes', $content_attributes ) ): the presets, the Improve menu and the defaults line o… | – |
| `expAiThreadFunctionCollection` | class | `extension/expai/classes/vector/expaithreadfunctioncollection.php:17` | Template fetch functions of threads (function_definition.php): | – |

### extension/expimageeditor (1)

[All patterns of extension/expimageeditor](area-extension-expimageeditor.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expImageEditorFunctionCollection` | class | `extension/expimageeditor/classes/expimageeditorfunctioncollection.php:15` | The fetch functions of the expimageeditor module. | – |

### extension/explayouts (1)

[All patterns of extension/explayouts](area-extension-explayouts.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expLayoutsFunctionCollection` | class | `extension/explayouts/modules/explayouts/functioncollection.php:4` | – | – |

### extension/ezfind (1)

[All patterns of extension/ezfind](area-extension-ezfind.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezfModuleFunctionCollection` | class | `extension/ezfind/classes/ezfmodulefunctioncollection.php:12` | The ezfModuleFunctionCollection contains methods for functions defined in the module ezfind. | – |

### extension/ezflow (1)

[All patterns of extension/ezflow](area-extension-ezflow.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZFlowFunctionCollection` | class | `extension/ezflow/modules/ezflow/functioncollection.php:30` | – | – |

### extension/ezmultiupload (1)

[All patterns of extension/ezmultiupload](area-extension-ezmultiupload.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expMultiuploadFunctionCollection` | class | `extension/ezmultiupload/classes/expmultiuploadfunctioncollection.php:10` | The template fetch functions of ezmultiupload (modules/ezmultiupload/function_definition.php). | – |

### extension/ezpm (1)

[All patterns of extension/ezpm](area-extension-ezpm.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZPmFunctionCollection` | class | `extension/ezpm/modules/pm/ezpmfunctioncollection.php:5` | – | – |

### extension/eztags (1)

[All patterns of extension/eztags](area-extension-eztags.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZTagsFunctionCollection` | class | `extension/eztags/modules/tags/eztagsfunctioncollection.php:9` | eZTagsFunctionCollection class implements fetch functions for eztags | – |

### extension/nxc_powercontent (1)

[All patterns of extension/nxc_powercontent](area-extension-nxc-powercontent.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZContentFunctionCollection` | class | `extension/nxc_powercontent/modules/content/ezcontentfunctioncollection.php:20` | – | – |

### extension/sqliimport (1)

[All patterns of extension/sqliimport](area-extension-sqliimport.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expImportFunctionCollection` | class | `extension/sqliimport/classes/exp/expimportfunctioncollection.php:11` | The template fetch functions of the module sqliimport (modules/sqliimport/function_definition.php). | – |

### extension/xrowextract (1)

[All patterns of extension/xrowextract](area-extension-xrowextract.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `XrowExtractFunctionCollection` | class | `extension/xrowextract/classes/xrowextractfunctioncollection.php:4` | The xrowextract module's template fetch functions (modules/xrowextract/function_definition.php). | – |

### kernel/classes/apikey (1)

[All patterns of kernel/classes/apikey](area-kernel-classes-apikey.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expApiKeyFunctionCollection` | class | `kernel/classes/apikey/expapikeyfunctioncollection.php:13` | The fetch functions of the apikey module (kernel/apikey/function_definition.php). | – |

### kernel/classes/audit (1)

[All patterns of kernel/classes/audit](area-kernel-classes-audit.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expAuditFunctionCollection` | class | `kernel/classes/audit/console/expauditfunctioncollection.php:22` | The fetch functions of the audit module (kernel/audit/function_definition.php; doc/eng-us/bc/6.0/audit.md, "Template operator and fetch"): | – |

### kernel/classes/datatable (1)

[All patterns of kernel/classes/datatable](area-kernel-classes-datatable.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expDataTableFunctionCollection` | class | `kernel/classes/datatable/expdatatablefunctioncollection.php:28` | The template fetch functions of the datatable module (kernel/datatable/function_definition.php, doc/eng-us/features/6.0/data-tables.md, "In templates… | – |

### kernel/classes/help (1)

[All patterns of kernel/classes/help](area-kernel-classes-help.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expHelpFunctionCollection` | class | `kernel/classes/help/exphelpfunctioncollection.php:10` | The exphelp fetch functions (kernel/exphelp/function_definition.php). | – |

### kernel/modules (15)

[All patterns of kernel/modules](area-kernel-modules.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZClassFunctionCollection` | class | `kernel/class/ezclassfunctioncollection.php:20` | – | – |
| `eZCollaborationFunctionCollection` | class | `kernel/collaboration/ezcollaborationfunctioncollection.php:20` | – | – |
| `eZInfocollectorFunctionCollection` | class | `kernel/infocollector/ezinfocollectorfunctioncollection.php:20` | – | – |
| `eZLayoutFunctionCollection` | class | `kernel/layout/ezlayoutfunctioncollection.php:20` | – | – |
| `eZNotificationFunctionCollection` | class | `kernel/notification/eznotificationfunctioncollection.php:20` | – | – |
| `eZPackageFunctionCollection` | class | `kernel/package/ezpackagefunctioncollection.php:20` | – | – |
| `eZRoleFunctionCollection` | class | `kernel/role/ezrolefunctioncollection.php:20` | – | – |
| `eZRSSFunctionCollection` | class | `kernel/rss/ezrssfunctioncollection.php:14` | – | – |
| `eZSearchFunctionCollection` | class | `kernel/search/ezsearchfunctioncollection.php:20` | – | – |
| `eZSectionFunctionCollection` | class | `kernel/section/ezsectionfunctioncollection.php:20` | – | – |
| `eZSetupFunctionCollection` | class | `kernel/setup/ezsetupfunctioncollection.php:20` | – | – |
| `eZShopFunctionCollection` | class | `kernel/shop/ezshopfunctioncollection.php:20` | – | – |
| `eZURLFunctionCollection` | class | `kernel/url/ezurlfunctioncollection.php:20` | – | – |
| `eZUserFunctionCollection` | class | `kernel/user/ezuserfunctioncollection.php:20` | – | – |
| `eZWorkflowFunctionCollection` | class | `kernel/workflow/ezworkflowfunctioncollection.php:20` | – | – |

### kernel/private (1)

[All patterns of kernel/private](area-kernel-private.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezpLanguageSwitcherFunctionCollection` | class | `kernel/private/modules/switchlanguage/ezpLanguageSwitcherFunctionCollection.php:14` | – | – |

## Fetch functions (314)

### extension/cjw_newsletter (16)

| Fetch | Implemented by | Defined in |
|---|---|---|
| `fetch( 'newsletter', 'subscription_list' )` | `CjwNewsletterFunctionCollection::fetchSubscriptionList()` | `extension/cjw_newsletter/modules/newsletter/function_definition.php:16` |
| `fetch( 'newsletter', 'subscription_list_count' )` | `CjwNewsletterFunctionCollection::fetchSubscriptionListCount()` | `extension/cjw_newsletter/modules/newsletter/function_definition.php:47` |
| `fetch( 'newsletter', 'import_subscription_list' )` | `CjwNewsletterFunctionCollection::fetchImportSubscriptionList()` | `extension/cjw_newsletter/modules/newsletter/function_definition.php:69` |
| `fetch( 'newsletter', 'import_subscription_list_count' )` | `CjwNewsletterFunctionCollection::fetchImportSubscriptionListCount()` | `extension/cjw_newsletter/modules/newsletter/function_definition.php:93` |
| `fetch( 'newsletter', 'user_list' )` | `CjwNewsletterFunctionCollection::fetchUserList()` | `extension/cjw_newsletter/modules/newsletter/function_definition.php:109` |
| `fetch( 'newsletter', 'user_list_count' )` | `CjwNewsletterFunctionCollection::fetchUserListCount()` | `extension/cjw_newsletter/modules/newsletter/function_definition.php:137` |
| `fetch( 'newsletter', 'edition_send_item_list' )` | `CjwNewsletterFunctionCollection::fetchEditonSendItemList()` | `extension/cjw_newsletter/modules/newsletter/function_definition.php:152` |
| `fetch( 'newsletter', 'edition_send_item_list_count' )` | `CjwNewsletterFunctionCollection::fetchEditonSendItemListCount()` | `extension/cjw_newsletter/modules/newsletter/function_definition.php:177` |
| `fetch( 'newsletter', 'test_group_list' )` | `CjwNewsletterDeliverabilityFetch::fetchTestGroupList()` | `extension/cjw_newsletter/modules/newsletter/function_definition.php:193` |
| `fetch( 'newsletter', 'article_statistics' )` | `CjwNewsletterStatistics::fetchArticleStatistics()` | `extension/cjw_newsletter/modules/newsletter/function_definition.php:205` |
| `fetch( 'newsletter', 'send_statistics' )` | `CjwNewsletterStatistics::fetchSendStatistics()` | `extension/cjw_newsletter/modules/newsletter/function_definition.php:214` |
| `fetch( 'newsletter', 'approval_state' )` | `CjwNewsletterEditorialFetch::fetchApprovalState()` | `extension/cjw_newsletter/modules/newsletter/function_definition.php:225` |
| `fetch( 'newsletter', 'edition_articles' )` | `CjwNewsletterEditorialFetch::fetchEditionArticles()` | `extension/cjw_newsletter/modules/newsletter/function_definition.php:233` |
| `fetch( 'newsletter', 'list_article_pool' )` | `CjwNewsletterEditorialFetch::fetchListArticlePool()` | `extension/cjw_newsletter/modules/newsletter/function_definition.php:240` |
| `fetch( 'newsletter', 'article_pool_list' )` | `CjwNewsletterEditorialFetch::fetchArticlePoolList()` | `extension/cjw_newsletter/modules/newsletter/function_definition.php:247` |
| `fetch( 'newsletter', 'schedule_list' )` | `CjwNewsletterEditorialFetch::fetchScheduleList()` | `extension/cjw_newsletter/modules/newsletter/function_definition.php:254` |

### extension/cjw_tmv (4)

| Fetch | Implemented by | Defined in |
|---|---|---|
| `fetch( 'cjw_tmv', 'events' )` | `cjwTmvFunctionCollection::events()` | `extension/cjw_tmv/modules/cjw_tmv/function_definition.php:12` |
| `fetch( 'cjw_tmv', 'categories' )` | `cjwTmvFunctionCollection::categories()` | `extension/cjw_tmv/modules/cjw_tmv/function_definition.php:24` |
| `fetch( 'cjw_tmv', 'places' )` | `cjwTmvFunctionCollection::places()` | `extension/cjw_tmv/modules/cjw_tmv/function_definition.php:32` |
| `fetch( 'cjw_tmv', 'filter_params' )` | `cjwTmvFunctionCollection::filterParams()` | `extension/cjw_tmv/modules/cjw_tmv/function_definition.php:40` |

### extension/expai (12)

| Fetch | Implemented by | Defined in |
|---|---|---|
| `fetch( 'expai', 'my_drafts' )` | `expAiFunctionCollection::fetchMyDrafts()` | `extension/expai/modules/expai/function_definition.php:16` |
| `fetch( 'expai', 'my_draft_count' )` | `expAiFunctionCollection::fetchMyDraftCount()` | `extension/expai/modules/expai/function_definition.php:22` |
| `fetch( 'expai', 'waiting_for_my_approval' )` | `expAiFunctionCollection::fetchWaitingForMyApproval()` | `extension/expai/modules/expai/function_definition.php:27` |
| `fetch( 'expai', 'budget_status' )` | `expAiBudgetFunctionCollection::fetchStatus()` | `extension/expai/modules/expai/function_definition.php:33` |
| `fetch( 'expai', 'chains' )` | `expAiChainFunctionCollection::fetchChains()` | `extension/expai/modules/expai/function_definition.php:40` |
| `fetch( 'expai', 'chain_runs' )` | `expAiChainFunctionCollection::fetchRuns()` | `extension/expai/modules/expai/function_definition.php:45` |
| `fetch( 'expai', 'models' )` | `expAiFunctionCollection::fetchModels()` | `extension/expai/modules/expai/function_definition.php:51` |
| `fetch( 'expai', 'my_threads' )` | `expAiThreadFunctionCollection::fetchMyThreads()` | `extension/expai/modules/expai/function_definition.php:57` |
| `fetch( 'expai', 'switched_on' )` | `expAiGate::fetchSwitchedOn()` | `extension/expai/modules/expai/function_definition.php:64` |
| `fetch( 'expai', 'alt_text_model' )` | `expAiAltTextFunctionCollection::fetchModel()` | `extension/expai/modules/expai/function_definition.php:72` |
| `fetch( 'expai', 'alt_text_required' )` | `expAiAltTextFunctionCollection::fetchAltTextRequired()` | `extension/expai/modules/expai/function_definition.php:79` |
| `fetch( 'expai', 'edit_presets' )` | `expAiPresetFunctionCollection::fetchEditPresets()` | `extension/expai/modules/expai/function_definition.php:87` |

### extension/expchangeclass (1)

| Fetch | Implemented by | Defined in |
|---|---|---|
| `fetch( 'changeclass', 'simple_conversion' )` | `conversionFunctions::fetchSimpleConversionArray()` | `extension/expchangeclass/modules/changeclass/function_definition.php:30` |

### extension/expimageeditor (1)

| Fetch | Implemented by | Defined in |
|---|---|---|
| `fetch( 'expimageeditor', 'aspect_ratios' )` | `expImageEditorFunctionCollection::fetchAspectRatios()` | `extension/expimageeditor/modules/expimageeditor/function_definition.php:13` |

### extension/explayouts (9)

| Fetch | Implemented by | Defined in |
|---|---|---|
| `fetch( 'explayouts', 'layout' )` | `expLayoutsFunctionCollection::fetchLayout()` | `extension/explayouts/modules/explayouts/function_definition.php:4` |
| `fetch( 'explayouts', 'resolve_layout' )` | `expLayoutsFunctionCollection::resolveLayout()` | `extension/explayouts/modules/explayouts/function_definition.php:18` |
| `fetch( 'explayouts', 'resolve_route_layout' )` | `expLayoutsFunctionCollection::resolveRouteLayout()` | `extension/explayouts/modules/explayouts/function_definition.php:33` |
| `fetch( 'explayouts', 'resolve_admin_layout' )` | `expLayoutsFunctionCollection::resolveAdminLayout()` | `extension/explayouts/modules/explayouts/function_definition.php:45` |
| `fetch( 'explayouts', 'admin_layout_cache_key' )` | `expLayoutsFunctionCollection::adminLayoutCacheKey()` | `extension/explayouts/modules/explayouts/function_definition.php:60` |
| `fetch( 'explayouts', 'admin_layouts_enabled' )` | `expLayoutsFunctionCollection::adminLayoutsEnabled()` | `extension/explayouts/modules/explayouts/function_definition.php:75` |
| `fetch( 'explayouts', 'layout_summary_for_node' )` | `expLayoutsFunctionCollection::layoutSummaryForNode()` | `extension/explayouts/modules/explayouts/function_definition.php:87` |
| `fetch( 'explayouts', 'resolve_layout_for_node' )` | `expLayoutsFunctionCollection::resolveLayoutForNode()` | `extension/explayouts/modules/explayouts/function_definition.php:101` |
| `fetch( 'explayouts', 'rules_for_node' )` | `expLayoutsFunctionCollection::rulesForNode()` | `extension/explayouts/modules/explayouts/function_definition.php:115` |

### extension/ezfind (7)

| Fetch | Implemented by | Defined in |
|---|---|---|
| `fetch( 'ezfind', 'search' )` | `ezfModuleFunctionCollection::search()` | `extension/ezfind/modules/ezfind/function_definition.php:10` |
| `fetch( 'ezfind', 'getDefaultSearchFacets' )` | `ezfModuleFunctionCollection::getDefaultSearchFacets()` | `extension/ezfind/modules/ezfind/function_definition.php:112` |
| `fetch( 'ezfind', 'facetParameters' )` | `ezfModuleFunctionCollection::getFacetParameters()` | `extension/ezfind/modules/ezfind/function_definition.php:120` |
| `fetch( 'ezfind', 'filterParameters' )` | `ezfModuleFunctionCollection::getFilterParameters()` | `extension/ezfind/modules/ezfind/function_definition.php:128` |
| `fetch( 'ezfind', 'rawSolrRequest' )` | `ezfModuleFunctionCollection::rawSolrRequest()` | `extension/ezfind/modules/ezfind/function_definition.php:138` |
| `fetch( 'ezfind', 'moreLikeThis' )` | `ezfModuleFunctionCollection::moreLikeThis()` | `extension/ezfind/modules/ezfind/function_definition.php:156` |
| `fetch( 'ezfind', 'elevateConfiguration' )` | `ezfModuleFunctionCollection::fetchElevateConfiguration()` | `extension/ezfind/modules/ezfind/function_definition.php:211` |

### extension/ezflow (6)

| Fetch | Implemented by | Defined in |
|---|---|---|
| `fetch( 'ezflow', 'waiting' )` | `eZFlowFunctionCollection::fetchWaiting()` | `extension/ezflow/modules/ezflow/function_definition.php:30` |
| `fetch( 'ezflow', 'valid' )` | `eZFlowFunctionCollection::fetchValid()` | `extension/ezflow/modules/ezflow/function_definition.php:42` |
| `fetch( 'ezflow', 'archived' )` | `eZFlowFunctionCollection::fetchArchived()` | `extension/ezflow/modules/ezflow/function_definition.php:54` |
| `fetch( 'ezflow', 'valid_nodes' )` | `eZFlowFunctionCollection::fetchValidNodes()` | `extension/ezflow/modules/ezflow/function_definition.php:66` |
| `fetch( 'ezflow', 'block' )` | `eZFlowFunctionCollection::fetchBlock()` | `extension/ezflow/modules/ezflow/function_definition.php:78` |
| `fetch( 'ezflow', 'allowed_zones' )` | `eZFlowFunctionCollection::fetchAllowedZones()` | `extension/ezflow/modules/ezflow/function_definition.php:90` |

### extension/ezmultiupload (3)

| Fetch | Implemented by | Defined in |
|---|---|---|
| `fetch( 'ezmultiupload', 'can_upload' )` | `expMultiuploadFunctionCollection::fetchCanUpload()` | `extension/ezmultiupload/modules/ezmultiupload/function_definition.php:26` |
| `fetch( 'ezmultiupload', 'dashboard_target' )` | `expMultiuploadFunctionCollection::fetchDashboardTarget()` | `extension/ezmultiupload/modules/ezmultiupload/function_definition.php:36` |
| `fetch( 'ezmultiupload', 'quick_locations' )` | `expMultiuploadFunctionCollection::fetchQuickLocations()` | `extension/ezmultiupload/modules/ezmultiupload/function_definition.php:44` |

### extension/ezpm (4)

| Fetch | Implemented by | Defined in |
|---|---|---|
| `fetch( 'pm', 'view_message' )` | `eZPm::fetchMessage()` | `extension/ezpm/modules/pm/function_definition.php:11` |
| `fetch( 'pm', 'list' )` | `eZPm::fetchMessageList()` | `extension/ezpm/modules/pm/function_definition.php:19` |
| `fetch( 'pm', 'messages_stats' )` | `eZPm::messagesStats()` | `extension/ezpm/modules/pm/function_definition.php:36` |
| `fetch( 'pm', 'check_contact' )` | `eZContact::checkIsOnContactList()` | `extension/ezpm/modules/pm/function_definition.php:45` |

### extension/eztags (9)

| Fetch | Implemented by | Defined in |
|---|---|---|
| `fetch( 'tags', 'tag' )` | `eZTagsFunctionCollection::fetchTag()` | `extension/eztags/modules/tags/function_definition.php:5` |
| `fetch( 'tags', 'tags_by_keyword' )` | `eZTagsFunctionCollection::fetchTagsByKeyword()` | `extension/eztags/modules/tags/function_definition.php:16` |
| `fetch( 'tags', 'tag_by_remote_id' )` | `eZTagsFunctionCollection::fetchTagByRemoteID()` | `extension/eztags/modules/tags/function_definition.php:27` |
| `fetch( 'tags', 'tag_by_url' )` | `eZTagsFunctionCollection::fetchTagByUrl()` | `extension/eztags/modules/tags/function_definition.php:38` |
| `fetch( 'tags', 'list' )` | `eZTagsFunctionCollection::fetchTagTree()` | `extension/eztags/modules/tags/function_definition.php:49` |
| `fetch( 'tags', 'list_count' )` | `eZTagsFunctionCollection::fetchTagTreeCount()` | `extension/eztags/modules/tags/function_definition.php:84` |
| `fetch( 'tags', 'tree' )` | `eZTagsFunctionCollection::fetchTagTree()` | `extension/eztags/modules/tags/function_definition.php:107` |
| `fetch( 'tags', 'tree_count' )` | `eZTagsFunctionCollection::fetchTagTreeCount()` | `extension/eztags/modules/tags/function_definition.php:142` |
| `fetch( 'tags', 'latest_tags' )` | `eZTagsFunctionCollection::fetchLatestTags()` | `extension/eztags/modules/tags/function_definition.php:165` |

### extension/nxc_powercontent (56)

| Fetch | Implemented by | Defined in |
|---|---|---|
| `fetch( 'content', 'object' )` | `eZContentFunctionCollection::fetchContentObject()` | `extension/nxc_powercontent/modules/content/function_definition.php:12` |
| `fetch( 'content', 'version' )` | `eZContentFunctionCollection::fetchContentVersion()` | `extension/nxc_powercontent/modules/content/function_definition.php:25` |
| `fetch( 'content', 'node' )` | `eZContentFunctionCollection::fetchContentNode()` | `extension/nxc_powercontent/modules/content/function_definition.php:37` |
| `fetch( 'content', 'locale_list' )` | `eZContentFunctionCollection::fetchLocaleList()` | `extension/nxc_powercontent/modules/content/function_definition.php:58` |
| `fetch( 'content', 'locale' )` | `eZContentFunctionCollection::fetchLocale()` | `extension/nxc_powercontent/modules/content/function_definition.php:67` |
| `fetch( 'content', 'prioritized_languages' )` | `eZContentFunctionCollection::fetchPrioritizedLanguages()` | `extension/nxc_powercontent/modules/content/function_definition.php:77` |
| `fetch( 'content', 'prioritized_language_codes' )` | `eZContentFunctionCollection::fetchPrioritizedLanguageCodes()` | `extension/nxc_powercontent/modules/content/function_definition.php:83` |
| `fetch( 'content', 'translation_list' )` | `eZContentFunctionCollection::fetchTranslationList()` | `extension/nxc_powercontent/modules/content/function_definition.php:89` |
| `fetch( 'content', 'non_translation_list' )` | `eZContentFunctionCollection::fetchNonTranslationList()` | `extension/nxc_powercontent/modules/content/function_definition.php:95` |
| `fetch( 'content', 'class' )` | `eZContentFunctionCollection::fetchClass()` | `extension/nxc_powercontent/modules/content/function_definition.php:106` |
| `fetch( 'content', 'class_attribute_list' )` | `eZContentFunctionCollection::fetchClassAttributeList()` | `extension/nxc_powercontent/modules/content/function_definition.php:114` |
| `fetch( 'content', 'class_attribute' )` | `eZContentFunctionCollection::fetchClassAttribute()` | `extension/nxc_powercontent/modules/content/function_definition.php:126` |
| `fetch( 'content', 'calendar' )` | `eZContentFunctionCollection::calendar()` | `extension/nxc_powercontent/modules/content/function_definition.php:138` |
| `fetch( 'content', 'list' )` | `eZContentFunctionCollection::fetchObjectTree()` | `extension/nxc_powercontent/modules/content/function_definition.php:199` |
| `fetch( 'content', 'list_count' )` | `eZContentFunctionCollection::fetchObjectTreeCount()` | `extension/nxc_powercontent/modules/content/function_definition.php:283` |
| `fetch( 'content', 'tree' )` | `eZContentFunctionCollection::fetchObjectTree()` | `extension/nxc_powercontent/modules/content/function_definition.php:339` |
| `fetch( 'content', 'tree_count' )` | `eZContentFunctionCollection::fetchObjectTreeCount()` | `extension/nxc_powercontent/modules/content/function_definition.php:425` |
| `fetch( 'content', 'search' )` | `eZContentFunctionCollection::fetchContentSearch()` | `extension/nxc_powercontent/modules/content/function_definition.php:482` |
| `fetch( 'content', 'trash_count' )` | `eZContentFunctionCollection::fetchTrashObjectCount()` | `extension/nxc_powercontent/modules/content/function_definition.php:535` |
| `fetch( 'content', 'trash_object_list' )` | `eZContentFunctionCollection::fetchTrashObjectList()` | `extension/nxc_powercontent/modules/content/function_definition.php:549` |
| `fetch( 'content', 'draft_count' )` | `eZContentFunctionCollection::fetchDraftVersionCount()` | `extension/nxc_powercontent/modules/content/function_definition.php:579` |
| `fetch( 'content', 'draft_version_list' )` | `eZContentFunctionCollection::fetchDraftVersionList()` | `extension/nxc_powercontent/modules/content/function_definition.php:586` |
| `fetch( 'content', 'pending_count' )` | `eZContentFunctionCollection::fetchPendingCount()` | `extension/nxc_powercontent/modules/content/function_definition.php:600` |
| `fetch( 'content', 'pending_list' )` | `eZContentFunctionCollection::fetchPendingList()` | `extension/nxc_powercontent/modules/content/function_definition.php:607` |
| `fetch( 'content', 'version_count' )` | `eZContentFunctionCollection::fetchVersionCount()` | `extension/nxc_powercontent/modules/content/function_definition.php:621` |
| `fetch( 'content', 'version_list' )` | `eZContentFunctionCollection::fetchVersionList()` | `extension/nxc_powercontent/modules/content/function_definition.php:630` |
| `fetch( 'content', 'can_instantiate_class_list' )` | `eZContentFunctionCollection::canInstantiateClassList()` | `extension/nxc_powercontent/modules/content/function_definition.php:653` |
| `fetch( 'content', 'class_list' )` | `eZContentFunctionCollection::fetchClassList()` | `extension/nxc_powercontent/modules/content/function_definition.php:683` |
| `fetch( 'content', 'can_instantiate_classes' )` | `eZContentFunctionCollection::canInstantiateClasses()` | `extension/nxc_powercontent/modules/content/function_definition.php:705` |
| `fetch( 'content', 'contentobject_attributes' )` | `eZContentFunctionCollection::contentobjectAttributes()` | `extension/nxc_powercontent/modules/content/function_definition.php:714` |
| `fetch( 'content', 'bookmarks' )` | `eZContentFunctionCollection::fetchBookmarks()` | `extension/nxc_powercontent/modules/content/function_definition.php:728` |
| `fetch( 'content', 'recent' )` | `eZContentFunctionCollection::fetchRecent()` | `extension/nxc_powercontent/modules/content/function_definition.php:742` |
| `fetch( 'content', 'section_list' )` | `eZContentFunctionCollection::fetchSectionList()` | `extension/nxc_powercontent/modules/content/function_definition.php:749` |
| `fetch( 'content', 'tipafriend_top_list' )` | `eZContentFunctionCollection::fetchTipafriendTopList()` | `extension/nxc_powercontent/modules/content/function_definition.php:756` |
| `fetch( 'content', 'view_top_list' )` | `eZContentFunctionCollection::fetchMostViewedTopList()` | `extension/nxc_powercontent/modules/content/function_definition.php:790` |
| `fetch( 'content', 'collected_info_count' )` | `eZContentFunctionCollection::fetchCollectedInfoCount()` | `extension/nxc_powercontent/modules/content/function_definition.php:812` |
| `fetch( 'content', 'collected_info_count_list' )` | `eZContentFunctionCollection::fetchCollectedInfoCountList()` | `extension/nxc_powercontent/modules/content/function_definition.php:838` |
| `fetch( 'content', 'collected_info_collection' )` | `eZContentFunctionCollection::fetchCollectedInfoCollection()` | `extension/nxc_powercontent/modules/content/function_definition.php:849` |
| `fetch( 'content', 'collected_info_list' )` | `eZContentFunctionCollection::fetchCollectionsList()` | `extension/nxc_powercontent/modules/content/function_definition.php:863` |
| `fetch( 'content', 'object_by_attribute' )` | `eZContentFunctionCollection::fetchObjectByAttribute()` | `extension/nxc_powercontent/modules/content/function_definition.php:893` |
| `fetch( 'content', 'object_count_by_user_id' )` | `eZContentFunctionCollection::fetchObjectCountByUserID()` | `extension/nxc_powercontent/modules/content/function_definition.php:903` |
| `fetch( 'content', 'same_classattribute_node' )` | `eZContentFunctionCollection::fetchSameClassAttributeNodeList()` | `extension/nxc_powercontent/modules/content/function_definition.php:921` |
| `fetch( 'content', 'keyword' )` | `eZContentFunctionCollection::fetchKeyword()` | `extension/nxc_powercontent/modules/content/function_definition.php:936` |
| `fetch( 'content', 'keyword_count' )` | `eZContentFunctionCollection::fetchKeywordCount()` | `extension/nxc_powercontent/modules/content/function_definition.php:982` |
| `fetch( 'content', 'access' )` | `eZContentFunctionCollection::checkAccess()` | `extension/nxc_powercontent/modules/content/function_definition.php:1015` |
| `fetch( 'content', 'navigation_parts' )` | `eZContentFunctionCollection::fetchNavigationParts()` | `extension/nxc_powercontent/modules/content/function_definition.php:1040` |
| `fetch( 'content', 'navigation_part' )` | `eZContentFunctionCollection::fetchNavigationPart()` | `extension/nxc_powercontent/modules/content/function_definition.php:1048` |
| `fetch( 'content', 'related_objects' )` | `eZContentFunctionCollection::fetchRelatedObjects()` | `extension/nxc_powercontent/modules/content/function_definition.php:1058` |
| `fetch( 'content', 'related_objects_count' )` | `eZContentFunctionCollection::fetchRelatedObjectsCount()` | `extension/nxc_powercontent/modules/content/function_definition.php:1107` |
| `fetch( 'content', 'reverse_related_objects' )` | `eZContentFunctionCollection::fetchReverseRelatedObjects()` | `extension/nxc_powercontent/modules/content/function_definition.php:1124` |
| `fetch( 'content', 'reverse_related_objects_count' )` | `eZContentFunctionCollection::fetchReverseRelatedObjectsCount()` | `extension/nxc_powercontent/modules/content/function_definition.php:1168` |
| `fetch( 'content', 'available_sort_fields' )` | `eZContentFunctionCollection::fetchAvailableSortFieldList()` | `extension/nxc_powercontent/modules/content/function_definition.php:1188` |
| `fetch( 'content', 'country_list' )` | `eZContentFunctionCollection::fetchCountryList()` | `extension/nxc_powercontent/modules/content/function_definition.php:1195` |
| `fetch( 'content', 'related_objects_ids' )` | `eZContentFunctionCollection::fetchRelatedObjectsID()` | `extension/nxc_powercontent/modules/content/function_definition.php:1207` |
| `fetch( 'content', 'reverse_related_objects_ids' )` | `eZContentFunctionCollection::fetchReverseRelatedObjectsID()` | `extension/nxc_powercontent/modules/content/function_definition.php:1224` |
| `fetch( 'content', 'content_tree_menu_expiry' )` | `eZContentFunctionCollection::fetchContentTreeMenuExpiry()` | `extension/nxc_powercontent/modules/content/function_definition.php:1241` |

### extension/sqliimport (3)

| Fetch | Implemented by | Defined in |
|---|---|---|
| `fetch( 'sqliimport', 'recent_runs' )` | `expImportFunctionCollection::fetchRecentRuns()` | `extension/sqliimport/modules/sqliimport/function_definition.php:17` |
| `fetch( 'sqliimport', 'failed_runs_24h' )` | `expImportFunctionCollection::fetchFailedRuns24h()` | `extension/sqliimport/modules/sqliimport/function_definition.php:22` |
| `fetch( 'sqliimport', 'notices' )` | `expImportFunctionCollection::fetchNotices()` | `extension/sqliimport/modules/sqliimport/function_definition.php:27` |

### extension/xrowextract (2)

| Fetch | Implemented by | Defined in |
|---|---|---|
| `fetch( 'xrowextract', 'schedule_alerts' )` | `XrowExtractFunctionCollection::fetchScheduleAlerts()` | `extension/xrowextract/modules/xrowextract/function_definition.php:10` |
| `fetch( 'xrowextract', 'requirement_notices' )` | `XrowExtractFunctionCollection::fetchRequirementNotices()` | `extension/xrowextract/modules/xrowextract/function_definition.php:17` |

### kernel/modules (180)

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
| `fetch( 'content', 'view_bookmark' )` | `eZContentFunctionCollection::fetchViewBookmark()` | `kernel/content/function_definition.php:761` |
| `fetch( 'content', 'bookmark_folders' )` | `eZContentFunctionCollection::fetchBookmarkFolders()` | `kernel/content/function_definition.php:774` |
| `fetch( 'content', 'recent' )` | `eZContentFunctionCollection::fetchRecent()` | `kernel/content/function_definition.php:781` |
| `fetch( 'content', 'section_list' )` | `eZContentFunctionCollection::fetchSectionList()` | `kernel/content/function_definition.php:788` |
| `fetch( 'content', 'tipafriend_top_list' )` | `eZContentFunctionCollection::fetchTipafriendTopList()` | `kernel/content/function_definition.php:795` |
| `fetch( 'content', 'view_top_list' )` | `eZContentFunctionCollection::fetchMostViewedTopList()` | `kernel/content/function_definition.php:829` |
| `fetch( 'content', 'collected_info_count' )` | `eZContentFunctionCollection::fetchCollectedInfoCount()` | `kernel/content/function_definition.php:851` |
| `fetch( 'content', 'collected_info_count_list' )` | `eZContentFunctionCollection::fetchCollectedInfoCountList()` | `kernel/content/function_definition.php:877` |
| `fetch( 'content', 'collected_info_collection' )` | `eZContentFunctionCollection::fetchCollectedInfoCollection()` | `kernel/content/function_definition.php:888` |
| `fetch( 'content', 'collected_info_list' )` | `eZContentFunctionCollection::fetchCollectionsList()` | `kernel/content/function_definition.php:902` |
| `fetch( 'content', 'object_by_attribute' )` | `eZContentFunctionCollection::fetchObjectByAttribute()` | `kernel/content/function_definition.php:932` |
| `fetch( 'content', 'object_count_by_user_id' )` | `eZContentFunctionCollection::fetchObjectCountByUserID()` | `kernel/content/function_definition.php:942` |
| `fetch( 'content', 'same_classattribute_node' )` | `eZContentFunctionCollection::fetchSameClassAttributeNodeList()` | `kernel/content/function_definition.php:960` |
| `fetch( 'content', 'keyword' )` | `eZContentFunctionCollection::fetchKeyword()` | `kernel/content/function_definition.php:975` |
| `fetch( 'content', 'keyword_count' )` | `eZContentFunctionCollection::fetchKeywordCount()` | `kernel/content/function_definition.php:1021` |
| `fetch( 'content', 'assigned_nodes' )` | `eZContentFunctionCollection::fetchAssignedNodes()` | `kernel/content/function_definition.php:1061` |
| `fetch( 'content', 'assigned_node_count' )` | `eZContentFunctionCollection::fetchAssignedNodeCount()` | `kernel/content/function_definition.php:1086` |
| `fetch( 'content', 'access' )` | `eZContentFunctionCollection::checkAccess()` | `kernel/content/function_definition.php:1094` |
| `fetch( 'content', 'navigation_parts' )` | `eZContentFunctionCollection::fetchNavigationParts()` | `kernel/content/function_definition.php:1119` |
| `fetch( 'content', 'navigation_part' )` | `eZContentFunctionCollection::fetchNavigationPart()` | `kernel/content/function_definition.php:1127` |
| `fetch( 'content', 'related_objects' )` | `eZContentFunctionCollection::fetchRelatedObjects()` | `kernel/content/function_definition.php:1137` |
| `fetch( 'content', 'related_objects_count' )` | `eZContentFunctionCollection::fetchRelatedObjectsCount()` | `kernel/content/function_definition.php:1186` |
| `fetch( 'content', 'reverse_related_objects' )` | `eZContentFunctionCollection::fetchReverseRelatedObjects()` | `kernel/content/function_definition.php:1203` |
| `fetch( 'content', 'reverse_related_objects_count' )` | `eZContentFunctionCollection::fetchReverseRelatedObjectsCount()` | `kernel/content/function_definition.php:1247` |
| `fetch( 'content', 'available_sort_fields' )` | `eZContentFunctionCollection::fetchAvailableSortFieldList()` | `kernel/content/function_definition.php:1267` |
| `fetch( 'content', 'country_list' )` | `eZContentFunctionCollection::fetchCountryList()` | `kernel/content/function_definition.php:1274` |
| `fetch( 'content', 'country_groups' )` | `eZContentFunctionCollection::fetchCountryGroups()` | `kernel/content/function_definition.php:1288` |
| `fetch( 'content', 'related_objects_ids' )` | `eZContentFunctionCollection::fetchRelatedObjectsID()` | `kernel/content/function_definition.php:1298` |
| `fetch( 'content', 'reverse_related_objects_ids' )` | `eZContentFunctionCollection::fetchReverseRelatedObjectsID()` | `kernel/content/function_definition.php:1315` |
| `fetch( 'content', 'site_data' )` | `eZContentFunctionCollection::fetchSiteData()` | `kernel/content/function_definition.php:1334` |
| `fetch( 'content', 'content_tree_menu_expiry' )` | `eZContentFunctionCollection::fetchContentTreeMenuExpiry()` | `kernel/content/function_definition.php:1343` |
| `fetch( 'datatable', 'list' )` | `expDataTableFunctionCollection::fetchList()` | `kernel/datatable/function_definition.php:13` |
| `fetch( 'datatable', 'count' )` | `expDataTableFunctionCollection::fetchCount()` | `kernel/datatable/function_definition.php:23` |
| `fetch( 'datatable', 'row' )` | `expDataTableFunctionCollection::fetchRow()` | `kernel/datatable/function_definition.php:30` |
| `fetch( 'datatable', 'pointing_at' )` | `expDataTableFunctionCollection::fetchPointingAt()` | `kernel/datatable/function_definition.php:37` |
| `fetch( 'datatable', 'can' )` | `expDataTableFunctionCollection::fetchCan()` | `kernel/datatable/function_definition.php:44` |
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
| `fetch( 'setup', 'cronjob_status' )` | `eZSetupFunctionCollection::fetchCronjobStatus()` | `kernel/setup/function_definition.php:94` |
| `fetch( 'setup', 'log_files' )` | `eZSetupFunctionCollection::fetchLogFiles()` | `kernel/setup/function_definition.php:102` |
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

### kernel/private (1)

| Fetch | Implemented by | Defined in |
|---|---|---|
| `fetch( 'switchlanguage', 'url_alias' )` | `ezpLanguageSwitcherFunctionCollection::fetchUrlAlias()` | `kernel/private/modules/switchlanguage/function_definition.php:13` |
