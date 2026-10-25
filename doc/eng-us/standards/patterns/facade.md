---
title: Facade / service
audience: developer
category: standards
order: 330
summary: One simple entry point in front of a subsystem, or a service class that holds the operations of one domain. 40 instances in Exponential.
keywords: design pattern, facade, structure, pattern catalogue
type: catalogue
---
# Facade / service

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

One simple entry point in front of a subsystem, or a service class that holds the operations of one domain.

| | |
|---|---|
| Group | Structure |
| Instances | 40 |
| Recognized by | Class name ends in Facade, Service, Manager, API or Api. |

## Classes by area

### extension/expai (1)

[All patterns of extension/expai](area-extension-expai.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expAiBatchApi` | abstract class | `extension/expai/classes/lanes/expaibatchapi.php:27` | A provider's batch API: submit many requests at once, poll the batch, fetch the results, cancel, delete. | extension/expai/settings/rad.ini [Registry_expai_batch_apis] Implements |

### extension/explayouts (2)

[All patterns of extension/explayouts](area-extension-explayouts.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expLayoutsExpSiteApi` | class | `extension/explayouts/classes/explayoutsexpsiteapi.php:3` | – | – |
| `expLayoutsSiteAPI` | class | `extension/explayouts/classes/explayoutssiteapi.php:10` | Exponential 4 / Exponential Site API compatibility layer. | – |

### extension/explayouts_core (6)

[All patterns of extension/explayouts_core](area-extension-explayouts-core.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expLayoutsCoreBlockService` | class | `extension/explayouts_core/classes/explayoutscoreblockservice.php:3` | – | – |
| `expLayoutsCoreCollectionService` | class | `extension/explayouts_core/classes/explayoutscorecollectionservice.php:3` | – | – |
| `expLayoutsCoreLayoutService` | class | `extension/explayouts_core/classes/explayoutscorelayoutservice.php:3` | – | – |
| `expLayoutsCoreRuleService` | class | `extension/explayouts_core/classes/explayoutscoreruleservice.php:3` | – | – |
| `expLayoutsCoreTransferService` | class | `extension/explayouts_core/classes/explayoutscoretransferservice.php:17` | Export and import of layouts, shared layouts and rules: the one entry point the admin pages (explayouts_ui/transfer_export, explayouts_ui/transfer_im… | – |
| `expLayoutsCoreZoneService` | class | `extension/explayouts_core/classes/explayoutscorezoneservice.php:3` | – | – |

### extension/explayouts_ui_api (1)

[All patterns of extension/explayouts_ui_api](area-extension-explayouts-ui-api.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expLayoutsUIApplicationApi` | class | `extension/explayouts_ui_api/classes/explayoutsuiapplicationapi.php:2` | – | – |

### extension/expqueue (1)

[All patterns of extension/expqueue](area-extension-expqueue.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expQueueService` | class | `extension/expqueue/classes/expqueueservice.php:22` | The systemd service of the queue's workers: ./console ext:expqueue:service install\|start\|stop\|status\|uninstall. | – |

### extension/expsite_api (4)

[All patterns of extension/expsite_api](area-extension-expsite-api.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expSiteApi` | class | `extension/expsite_api/classes/expsiteapi.php:2` | – | – |
| `expSiteApiContentService` | class | `extension/expsite_api/classes/expsiteapicontentservice.php:2` | – | – |
| `expSiteApiFilterService` | class | `extension/expsite_api/classes/expsiteapifilterservice.php:2` | – | – |
| `expSiteApiLocationService` | class | `extension/expsite_api/classes/expsiteapilocationservice.php:2` | – | – |

### extension/exp_oe_tiptap (1)

[All patterns of extension/exp_oe_tiptap](area-extension-exp-oe-tiptap.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expOETiptapAIService` | class | `extension/exp_oe_tiptap/classes/ai/expoetiptapaiservice.php:19` | The AI hooks on the server: checks, limits, prompt, provider call, answer. | – |

### extension/ezflow (2)

[All patterns of extension/ezflow](area-extension-ezflow.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZHTTPCacheManager` | class | `extension/ezflow/classes/ezhttpcachemanager.php:33` | eZHTTPCacheManager class implements methods required for loading user definied http cache handlers | – |
| `eZSquidCacheManager` | class | `extension/ezflow/classes/ezsquidcachemanager.php:30` | – | extension/ezflow/settings/http.ini [HTTPCacheHandlers] Handlers[] |

### extension/ezjscore (1)

[All patterns of extension/ezjscore](area-extension-ezjscore.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezjscCacheManager` | class | `extension/ezjscore/classes/ezjsccachemanager.php:11` | – | extension/ezjscore/settings/site.ini [Cache_ezjscore] class |

### extension/ezmultiupload (2)

[All patterns of extension/ezmultiupload](area-extension-ezmultiupload.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expMultiuploadService` | class | `extension/ezmultiupload/classes/expmultiuploadservice.php:20` | The server side of the upload page: checks before an upload (the target location, the content class each file becomes, the create permission for it t… | – |
| `Exponential\View\Extension\Ezmultiupload\Ezmultiupload\Service` | class extends ModuleView | `extension/ezmultiupload/classes/runnable/views/ezmultiupload/service.php:23` | ezmultiupload/service: the checks and actions of the upload page, answered as JSON. | – |

### extension/ezownerchange (1)

[All patterns of extension/ezownerchange](area-extension-ezownerchange.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expOwnerChangeService` | class | `extension/ezownerchange/classes/expownerchangeservice.php:23` | Changes the owner of content objects: one object, or every object with a location in a subtree. | – |

### extension/ezupdate (1)

[All patterns of extension/ezupdate](area-extension-ezupdate.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZUpdateManager` | class | `extension/ezupdate/classes/ezupdatemanager.php:19` | Runs Composer for the installation this extension belongs to. | – |

### extension/git_manager (2)

[All patterns of extension/git_manager](area-extension-git-manager.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `BackupManager` | class | `extension/git_manager/classes/backupmanager.php:9` | – | – |
| `GitManager` | class | `extension/git_manager/classes/git_manager.php:9` | – | – |

### extension/syndication (1)

[All patterns of extension/syndication](area-extension-syndication.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZSyndicationFeedCacheManager` | class | `extension/syndication/classes/ezsyndicationfeedcachemanager.php:48` | – | – |

### kernel/classes (7)

[All patterns of kernel/classes](area-kernel-classes.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expCacheManager` | class | `kernel/classes/expcachemanager.php:30` | Everything Setup › Cache (kernel/setup/cache.php) can do, as functions that return what happened instead of setting template variables, so the admini… | – |
| `expCollaborationGroupManager` | class | `kernel/classes/expcollaborationgroupmanager.php:14` | Creates, renames and deletes the collaboration groups of one user and moves items between them. | – |
| `expNotificationService` | class | `kernel/classes/expnotificationservice.php:26` | The one place that knows how the notification system is run and looked at: the cronjob part, the exp:notification:* commands, the status page and its… | – |
| `eZContentCacheManager` | class | `kernel/classes/ezcontentcachemanager.php:26` | – | – |
| `eZRedirectManager` | class | `kernel/classes/ezredirectmanager.php:22` | – | – |
| `eZShippingManager` | class | `kernel/classes/ezshippingmanager.php:17` | – | – |
| `eZVATManager` | class | `kernel/classes/ezvatmanager.php:18` | – | – |

### kernel/classes/logviewer (1)

[All patterns of kernel/classes/logviewer](area-kernel-classes-logviewer.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expLogManager` | class | `kernel/classes/logviewer/explogmanager.php:27` | Log management (doc/eng-us/features/6.0/log-rotation.md): the logs of the installation with their archives, sizes, growth and limits; rotation, pruni… | – |

### kernel/classes/mailpreferences (1)

[All patterns of kernel/classes/mailpreferences](area-kernel-classes-mailpreferences.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expMailPreferencesService` | class | `kernel/classes/mailpreferences/expmailpreferencesservice.php:18` | The flows around the preferences: the double opt-in mail and its confirmation, the one-click unsubscribe, "send me a link" for people without login,… | settings/mailpreferences.ini [GateSettings] EssentialSenders[] |

### kernel/classes/passkey (1)

[All patterns of kernel/classes/passkey](area-kernel-classes-passkey.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expPasskeyService` | class | `kernel/classes/passkey/exppasskeyservice.php:32` | The passkey ceremonies of the web pages (doc/eng-us/guides/passkeys.md): | – |

### kernel/private (2)

[All patterns of kernel/private](area-kernel-private.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezpAttributeOperatorManager` | class | `kernel/private/eztemplate/ezpattributeoperatormanager.php:12` | – | – |
| `ezpRestTokenManager` | class | `kernel/private/oauth/classes/tokenmanager.php:17` | This class handles application tokens | – |

### lib/ezi18n (1)

[All patterns of lib/ezi18n](area-lib-ezi18n.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZTranslatorManager` | class | `lib/ezi18n/classes/eztranslatormanager.php:31` | – | – |

### lib/ezimage (1)

[All patterns of lib/ezimage](area-lib-ezimage.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZImageManager` | class | `lib/ezimage/classes/ezimagemanager.php:85` | – | – |
