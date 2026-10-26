---
title: Patterns in extension/ezssp
audience: developer
category: standards
order: 459
summary: Every class, view, fetch function, operator and plugin point of extension/ezssp with its design patterns.
keywords: extension/ezssp, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/ezssp

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/ezssp` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (2)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `ezsspInfo` | class | [Value object / struct](value-object.md) | `extension/ezssp/ezinfo.php:2` | – |
| `SubtreeSkeletonPublishType` | class extends eZWorkflowEventType | [Workflow event type (Command, plugin)](workflow-event.md) | `extension/ezssp/eventtypes/event/subtreeskeletonpublish/subtreeskeletonpublishtype.php:27` | – |
