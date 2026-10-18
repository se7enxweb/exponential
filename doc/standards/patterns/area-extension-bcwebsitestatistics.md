---
title: Patterns in extension/bcwebsitestatistics
audience: developer
category: standards
order: 404
summary: Every class, view, fetch function, operator and plugin point of extension/bcwebsitestatistics with its design patterns.
keywords: extension/bcwebsitestatistics, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/bcwebsitestatistics

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/bcwebsitestatistics` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (4)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `BCUsingOWMultisiteIni` | class | – | `extension/bcwebsitestatistics/autoloads/bcusingowmultisiteini.php:4` | – |
| `bcwebsitestatisticsInfo` | class | [Value object / struct](value-object.md) | `extension/bcwebsitestatistics/ezinfo.php:3` | – |
| `BCWebsiteStatisticsOperators` | class | [Template operator (Interpreter plugin)](template-operator.md) | `extension/bcwebsitestatistics/autoloads/bcwebsitestatisticsoperators.php:30` | – |
| `eZReceiptType` | class extends eZWorkflowEventType | [Workflow event type (Command, plugin)](workflow-event.md) | `extension/bcwebsitestatistics/workflowtypes/event/ezreceipt/ezreceipttype.php:38` | – |

## Template operators (1)

| Operators | Class | Registered in |
|---|---|---|
| bc_ga_urchin, bc_ga_urchinOrder, bc_ga_xmlAttributeValue, bc_ga_jsEscapedString, bc_ga_formatNumericDecimal, bc_ga_script_url | `BCWebsiteStatisticsOperators` | `extension/bcwebsitestatistics/autoloads/eztemplateautoload.php:34` |
