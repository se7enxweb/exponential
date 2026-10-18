---
title: Patterns in kernel/classes/workflowtypes
audience: developer
category: standards
order: 511
summary: Every class, view, fetch function, operator and plugin point of kernel/classes/workflowtypes with its design patterns.
keywords: kernel/classes/workflowtypes, design patterns, classes, extension points
type: catalogue
---
# Patterns in kernel/classes/workflowtypes

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `kernel/classes/workflowtypes` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (9)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `eZApproveType` | class extends eZWorkflowEventType | [Workflow event type (Command, plugin)](workflow-event.md) | `kernel/classes/workflowtypes/event/ezapprove/ezapprovetype.php:24` | – |
| `eZFinishUserRegisterType` | class extends eZWorkflowEventType | [Workflow event type (Command, plugin)](workflow-event.md) | `kernel/classes/workflowtypes/event/ezfinishuserregister/ezfinishuserregistertype.php:17` | – |
| `eZMultiplexerType` | class extends eZWorkflowEventType | [Workflow event type (Command, plugin)](workflow-event.md) | `kernel/classes/workflowtypes/event/ezmultiplexer/ezmultiplexertype.php:24` | – |
| `eZPaymentGatewayType` | class extends eZWorkflowEventType | [Registry](registry.md), [Workflow event type (Command, plugin)](workflow-event.md) | `kernel/classes/workflowtypes/event/ezpaymentgateway/ezpaymentgatewaytype.php:20` | – |
| `eZPaymentLogger` | class | [Factory and static factory methods](factory.md) | `kernel/classes/workflowtypes/event/ezpaymentgateway/ezpaymentlogger.php:16` | – |
| `eZSimpleShippingType` | class extends eZWorkflowEventType | [Workflow event type (Command, plugin)](workflow-event.md) | `kernel/classes/workflowtypes/event/ezsimpleshipping/ezsimpleshippingtype.php:17` | – |
| `eZWaitUntilDate` | class | – | `kernel/classes/workflowtypes/event/ezwaituntildate/ezwaituntildate.php:17` | – |
| `eZWaitUntilDateType` | class extends eZWorkflowEventType | [Workflow event type (Command, plugin)](workflow-event.md) | `kernel/classes/workflowtypes/event/ezwaituntildate/ezwaituntildatetype.php:17` | – |
| `eZWaitUntilDateValue` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/classes/workflowtypes/event/ezwaituntildate/ezwaituntildatevalue.php:18` | – |
