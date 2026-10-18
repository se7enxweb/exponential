---
title: Patterns in extension/autorss
audience: developer
category: standards
order: 401
summary: Every class, view, fetch function, operator and plugin point of extension/autorss with its design patterns.
keywords: extension/autorss, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/autorss

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/autorss` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (2)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `autorssInfo` | class | [Value object / struct](value-object.md) | `extension/autorss/ezinfo.php:3` | – |
| `AutoRSSType` | class extends eZWorkflowEventType | [Factory and static factory methods](factory.md), [Workflow event type (Command, plugin)](workflow-event.md) | `extension/autorss/eventtypes/event/autorss/autorsstype.php:27` | – |
