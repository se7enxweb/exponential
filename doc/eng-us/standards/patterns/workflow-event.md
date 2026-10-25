---
title: Workflow event type (Command, plugin)
audience: developer
category: standards
order: 312
summary: A step of a workflow that runs when a trigger fires (before or after publishing, reading, adding to the basket ...). 15 instances in Exponential.
keywords: design pattern, workflow-event, plugins, pattern catalogue
type: catalogue
---
# Workflow event type (Command, plugin)

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

A step of a workflow that runs when a trigger fires (before or after publishing, reading, adding to the basket ...).

| | |
|---|---|
| Group | Plugins |
| Instances | 15 |
| Recognized by | Extends eZWorkflowEventType. |
| Extension point | workflow.ini [EventSettings] ExtensionDirectories[] and AvailableEventTypes[]; triggers connect workflows to module operations. |

## Classes by area

### extension/autorss (1)

[All patterns of extension/autorss](area-extension-autorss.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `AutoRSSType` | class extends eZWorkflowEventType | `extension/autorss/eventtypes/event/autorss/autorsstype.php:27` | – | – |

### extension/bcwebsitestatistics (1)

[All patterns of extension/bcwebsitestatistics](area-extension-bcwebsitestatistics.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZReceiptType` | class extends eZWorkflowEventType | `extension/bcwebsitestatistics/workflowtypes/event/ezreceipt/ezreceipttype.php:38` | – | – |

### extension/expai (1)

[All patterns of extension/expai](area-extension-expai.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expAiTranslationSyncType` | class extends eZWorkflowEventType | `extension/expai/eventtypes/event/expaitranslationsync/expaitranslationsynctype.php:16` | The workflow event "Exponential Intelligence: translation sync" (event_expaitranslationsync). | – |

### extension/ezflow (1)

[All patterns of extension/ezflow](area-extension-ezflow.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZPageSwapType` | class extends eZWorkflowEventType | `extension/ezflow/eventtypes/event/ezpageswap/ezpageswaptype.php:28` | – | – |

### extension/ezssp (1)

[All patterns of extension/ezssp](area-extension-ezssp.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `SubtreeSkeletonPublishType` | class extends eZWorkflowEventType | `extension/ezssp/eventtypes/event/subtreeskeletonpublish/subtreeskeletonpublishtype.php:27` | – | – |

### extension/swark (2)

[All patterns of extension/swark](area-extension-swark.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `AutoPriorityType` | class extends eZWorkflowEventType | `extension/swark/eventtypes/event/autopriority/autoprioritytype.php:22` | – | – |
| `DeferToCronType` | class extends eZWorkflowEventType | `extension/swark/eventtypes/event/defertocron/defertocrontype.php:22` | – | – |

### kernel/classes/workflowtypes (8)

[All patterns of kernel/classes/workflowtypes](area-kernel-classes-workflowtypes.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expDataTableMirrorType` | class extends eZWorkflowEventType | `kernel/classes/workflowtypes/event/expdatatablemirror/expdatatablemirrortype.php:21` | The workflow event "Mirror to data tables" (doc/eng-us/features/6.0/data-tables.md, "Live mirror"): in a workflow on content/publish (after), the pub… | – |
| `expUserApproveType` | class extends eZApproveType | `kernel/classes/workflowtypes/event/expuserapprove/expuserapprovetype.php:36` | The workflow event "Approve user accounts" (doc/eng-us/bc/6.0/user-account-approval.md), modelled on the approval event (ezapprove): on content/publi… | – |
| `eZApproveType` | class extends eZWorkflowEventType | `kernel/classes/workflowtypes/event/ezapprove/ezapprovetype.php:24` | – | – |
| `eZFinishUserRegisterType` | class extends eZWorkflowEventType | `kernel/classes/workflowtypes/event/ezfinishuserregister/ezfinishuserregistertype.php:17` | – | – |
| `eZMultiplexerType` | class extends eZWorkflowEventType | `kernel/classes/workflowtypes/event/ezmultiplexer/ezmultiplexertype.php:24` | – | – |
| `eZPaymentGatewayType` | class extends eZWorkflowEventType | `kernel/classes/workflowtypes/event/ezpaymentgateway/ezpaymentgatewaytype.php:20` | – | – |
| `eZSimpleShippingType` | class extends eZWorkflowEventType | `kernel/classes/workflowtypes/event/ezsimpleshipping/ezsimpleshippingtype.php:17` | – | – |
| `eZWaitUntilDateType` | class extends eZWorkflowEventType | `kernel/classes/workflowtypes/event/ezwaituntildate/ezwaituntildatetype.php:17` | – | – |
