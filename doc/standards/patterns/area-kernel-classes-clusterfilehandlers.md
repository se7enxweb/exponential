---
title: Patterns in kernel/classes/clusterfilehandlers
audience: developer
category: standards
order: 484
summary: Every class, view, fetch function, operator and plugin point of kernel/classes/clusterfilehandlers with its design patterns.
keywords: kernel/classes/clusterfilehandlers, design patterns, classes, extension points
type: catalogue
---
# Patterns in kernel/classes/clusterfilehandlers

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `kernel/classes/clusterfilehandlers` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (2)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `eZFSFileHandler` | class implements eZClusterFileHandlerInterface | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `kernel/classes/clusterfilehandlers/ezfsfilehandler.php:12` | – |
| `eZMySQLBackendError` | class | – | `kernel/classes/clusterfilehandlers/dbbackends/mysqlbackenderror.php:17` | – |
