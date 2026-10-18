---
title: Template function (Interpreter plugin)
audience: developer
category: standards
order: 314
summary: A class that adds a block or function to the template language ({def}, {foreach}, {cache-block} ...). 45 instances in Exponential.
keywords: design pattern, template-function, plugins, pattern catalogue
type: catalogue
---
# Template function (Interpreter plugin)

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

A class that adds a block or function to the template language ({def}, {foreach}, {cache-block} ...).

| | |
|---|---|
| Group | Plugins |
| Instances | 45 |
| Recognized by | Registered in an eztemplateautoload.php ($eZTemplateFunctionArray[]), or has functionList() and process(). |
| Extension point | autoloads/eztemplateautoload.php of an extension. |

## Classes by area

### extension/explayouts (1)

[All patterns of extension/explayouts](area-extension-explayouts.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ExplBlockFunction` | class | `extension/explayouts/classes/explblockfunction.php:9` | Template function {explblock name=... | – |

### kernel/common (1)

[All patterns of kernel/common](area-kernel-common.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZObjectForwarder` | class | `kernel/common/ezobjectforwarder.php:18` | – | – |

### lib/eztemplate (17)

[All patterns of lib/eztemplate](area-lib-eztemplate.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZTemplateBlockFunction` | class | `lib/eztemplate/classes/eztemplateblockfunction.php:50` | – | – |
| `eZTemplateCacheFunction` | class | `lib/eztemplate/classes/eztemplatecachefunction.php:19` | – | – |
| `eZTemplateDebugFunction` | class | `lib/eztemplate/classes/eztemplatedebugfunction.php:63` | – | – |
| `eZTemplateDefFunction` | class | `lib/eztemplate/classes/eztemplatedeffunction.php:38` | – | – |
| `eZTemplateDelimitFunction` | class | `lib/eztemplate/classes/eztemplatedelimitfunction.php:37` | – | – |
| `eZTemplateDoFunction` | class | `lib/eztemplate/classes/eztemplatedofunction.php:35` | – | – |
| `eZTemplateForeachFunction` | class | `lib/eztemplate/classes/eztemplateforeachfunction.php:46` | – | – |
| `eZTemplateForFunction` | class | `lib/eztemplate/classes/eztemplateforfunction.php:39` | – | – |
| `eZTemplateIfFunction` | class | `lib/eztemplate/classes/eztemplateiffunction.php:38` | – | – |
| `eZTemplateIncludeFunction` | class | `lib/eztemplate/classes/eztemplateincludefunction.php:33` | – | – |
| `eZTemplateMenuFunction` | class | `lib/eztemplate/classes/eztemplatemenufunction.php:18` | – | – |
| `eZTemplateSectionFunction` | class | `lib/eztemplate/classes/eztemplatesectionfunction.php:58` | – | – |
| `eZTemplateSequenceFunction` | class | `lib/eztemplate/classes/eztemplatesequencefunction.php:34` | – | – |
| `eZTemplateSetFunction` | class | `lib/eztemplate/classes/eztemplatesetfunction.php:37` | – | – |
| `eZTemplateSwitchFunction` | class | `lib/eztemplate/classes/eztemplateswitchfunction.php:43` | – | – |
| `eZTemplateToolbarFunction` | class | `lib/eztemplate/classes/eztemplatetoolbarfunction.php:18` | – | – |
| `eZTemplateWhileFunction` | class | `lib/eztemplate/classes/eztemplatewhilefunction.php:35` | – | – |

## Template functions (17)

### lib/eztemplate (17)

| Functions | Class | Registered in |
|---|---|---|
| section | `eZTemplateSectionFunction` | `lib/eztemplate/classes/eztemplateautoload.php:181` |
| ldelim, rdelim | `eZTemplateDelimitFunction` | `lib/eztemplate/classes/eztemplateautoload.php:187` |
| include | `eZTemplateIncludeFunction` | `lib/eztemplate/classes/eztemplateautoload.php:189` |
| switch | `eZTemplateSwitchFunction` | `lib/eztemplate/classes/eztemplateautoload.php:191` |
| sequence | `eZTemplateSequenceFunction` | `lib/eztemplate/classes/eztemplateautoload.php:194` |
| set, let, default | `eZTemplateSetFunction` | `lib/eztemplate/classes/eztemplateautoload.php:196` |
| set-block, append-block, run-once | `eZTemplateBlockFunction` | `lib/eztemplate/classes/eztemplateautoload.php:198` |
| debug-timing-point, debug-accumulator, debug-log, debug-trace | `eZTemplateDebugFunction` | `lib/eztemplate/classes/eztemplateautoload.php:201` |
| cache-block | `eZTemplateCacheFunction` | `lib/eztemplate/classes/eztemplateautoload.php:206` |
| tool_bar | `eZTemplateToolbarFunction` | `lib/eztemplate/classes/eztemplateautoload.php:209` |
| menu | `eZTemplateMenuFunction` | `lib/eztemplate/classes/eztemplateautoload.php:212` |
| if | `eZTemplateIfFunction` | `lib/eztemplate/classes/eztemplateautoload.php:216` |
| while | `eZTemplateWhileFunction` | `lib/eztemplate/classes/eztemplateautoload.php:220` |
| for | `eZTemplateForFunction` | `lib/eztemplate/classes/eztemplateautoload.php:226` |
| foreach | `eZTemplateForeachFunction` | `lib/eztemplate/classes/eztemplateautoload.php:233` |
| do | `eZTemplateDoFunction` | `lib/eztemplate/classes/eztemplateautoload.php:239` |
| def, undef | `eZTemplateDefFunction` | `lib/eztemplate/classes/eztemplateautoload.php:246` |
