---
title: Patterns in extension/swark
audience: developer
category: standards
order: 480
summary: Every class, view, fetch function, operator and plugin point of extension/swark with its design patterns.
keywords: extension/swark, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/swark

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/swark` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (43)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `AutoPriorityType` | class extends eZWorkflowEventType | [Workflow event type (Command, plugin)](workflow-event.md) | `extension/swark/eventtypes/event/autopriority/autoprioritytype.php:22` | – |
| `DeferToCronType` | class extends eZWorkflowEventType | [Workflow event type (Command, plugin)](workflow-event.md) | `extension/swark/eventtypes/event/defertocron/defertocrontype.php:22` | – |
| `SwarkAddViewParametersOperator` | class extends SwarkOperator | – | `extension/swark/autoloads/classes/SwarkAddViewParametersOperator.php:22` | – |
| `SwarkArraySearchOperator` | class extends SwarkOperator | – | `extension/swark/autoloads/classes/SwarkArraySearchOperator.php:22` | – |
| `SwarkARSortOperator` | class extends SwarkOperator | – | `extension/swark/autoloads/classes/SwarkARSortOperator.php:22` | – |
| `SwarkASortOperator` | class extends SwarkOperator | – | `extension/swark/autoloads/classes/SwarkASortOperator.php:22` | – |
| `SwarkCharsetOperator` | class extends SwarkOperator | – | `extension/swark/autoloads/classes/SwarkCharsetOperator.php:22` | – |
| `SwarkClearObjectCacheOperator` | class extends SwarkOperator | – | `extension/swark/autoloads/classes/SwarkClearObjectCacheOperator.php:22` | – |
| `SwarkCookieOperator` | class extends SwarkOperator | – | `extension/swark/autoloads/classes/SwarkCookieOperator.php:22` | – |
| `SwarkCurrentLayoutOperator` | class extends SwarkOperator | – | `extension/swark/autoloads/classes/SwarkCurrentLayoutOperator.php:22` | – |
| `SwarkCurrentSiteaccessOperator` | class extends SwarkOperator | – | `extension/swark/autoloads/classes/SwarkCurrentSiteaccessOperator.php:22` | – |
| `SwarkDebugAttributesOperator` | class extends SwarkOperator | – | `extension/swark/autoloads/classes/SwarkDebugAttributesOperator.php:22` | – |
| `SwarkDebugOperator` | class extends SwarkOperator | – | `extension/swark/autoloads/classes/SwarkDebugOperator.php:22` | – |
| `SwarkInfo` | class | [Value object / struct](value-object.md) | `extension/swark/ezinfo.php:22` | – |
| `SwarkIsPostRequestOperator` | class extends SwarkOperator | – | `extension/swark/autoloads/classes/SwarkIsPostRequestOperator.php:22` | – |
| `SwarkJSONEncodeOperator` | class extends SwarkOperator | – | `extension/swark/autoloads/classes/SwarkJSONEncodeOperator.php:22` | – |
| `SwarkKRSortOperator` | class extends SwarkOperator | – | `extension/swark/autoloads/classes/SwarkKRSortOperator.php:22` | – |
| `SwarkKSortOperator` | class extends SwarkOperator | – | `extension/swark/autoloads/classes/SwarkKSortOperator.php:22` | – |
| `SwarkLTrimOperator` | class extends SwarkOperator | – | `extension/swark/autoloads/classes/SwarkLTrimOperator.php:22` | – |
| `SwarkModifyViewParameterOperator` | class extends SwarkOperator | – | `extension/swark/autoloads/classes/SwarkModifyViewParameterOperator.php:22` | – |
| `SwarkOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `extension/swark/autoloads/classes/SwarkOperator.php:22` | – |
| `SwarkOperators` | class | – | `extension/swark/autoloads/SwarkOperators.php:22` | – |
| `SwarkPregMatchOperator` | class extends SwarkOperator | – | `extension/swark/autoloads/classes/SwarkPregMatchOperator.php:22` | – |
| `SwarkPregReplaceOperator` | class extends SwarkOperator | – | `extension/swark/autoloads/classes/SwarkPregReplaceOperator.php:22` | – |
| `SwarkRangeOperator` | class extends SwarkOperator | – | `extension/swark/autoloads/classes/SwarkRangeOperator.php:22` | – |
| `SwarkRedirectOperator` | class extends SwarkOperator | – | `extension/swark/autoloads/classes/SwarkRedirectOperator.php:22` | – |
| `SwarkRemoveArrayElementOperator` | class extends SwarkOperator | – | `extension/swark/autoloads/classes/SwarkRemoveArrayElementOperator.php:22` | – |
| `SwarkReturnOperator` | class extends SwarkOperator | – | `extension/swark/autoloads/classes/SwarkReturnOperator.php:22` | – |
| `SwarkRSortOperator` | class extends SwarkOperator | – | `extension/swark/autoloads/classes/SwarkRSortOperator.php:22` | – |
| `SwarkRTrimOperator` | class extends SwarkOperator | – | `extension/swark/autoloads/classes/SwarkRTrimOperator.php:22` | – |
| `SwarkServerOperator` | class extends SwarkOperator | – | `extension/swark/autoloads/classes/SwarkServerOperator.php:22` | – |
| `SwarkSetArrayElementOperator` | class extends SwarkOperator | – | `extension/swark/autoloads/classes/SwarkSetArrayElementOperator.php:22` | – |
| `SwarkShortenWOperator` | class extends SwarkOperator | – | `extension/swark/autoloads/classes/SwarkShortenWOperator.php:22` | – |
| `SwarkShuffleOperator` | class extends SwarkOperator | – | `extension/swark/autoloads/classes/SwarkShuffleOperator.php:22` | – |
| `SwarkSortOperator` | class extends SwarkOperator | – | `extension/swark/autoloads/classes/SwarkSortOperator.php:22` | – |
| `SwarkSplitByLengthOperator` | class extends SwarkOperator | – | `extension/swark/autoloads/classes/SwarkSplitByLengthOperator.php:22` | – |
| `SwarkStrPosOperator` | class extends SwarkOperator | – | `extension/swark/autoloads/classes/SwarkStrPosOperator.php:22` | – |
| `SwarkStrReplaceOperator` | class extends SwarkOperator | – | `extension/swark/autoloads/classes/SwarkStrReplaceOperator.php:22` | – |
| `SwarkStrRPosOperator` | class extends SwarkOperator | – | `extension/swark/autoloads/classes/SwarkStrRPosOperator.php:22` | – |
| `SwarkSubStrOperator` | class extends SwarkOperator | – | `extension/swark/autoloads/classes/SwarkSubStrOperator.php:22` | – |
| `SwarkURIPathSegmentOperator` | class extends SwarkOperator | – | `extension/swark/autoloads/classes/SwarkURIPathSegmentOperator.php:22` | – |
| `SwarkUserIDByLoginOperator` | class extends SwarkOperator | – | `extension/swark/autoloads/classes/SwarkUserIDByLoginOperator.php:22` | – |
| `SwarkVariableNamesOperator` | class extends SwarkOperator | – | `extension/swark/autoloads/classes/SwarkVariableNamesOperator.php:22` | – |
