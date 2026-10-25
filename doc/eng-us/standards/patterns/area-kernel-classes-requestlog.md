---
title: Patterns in kernel/classes/requestlog
audience: developer
category: standards
order: 510
summary: Every class, view, fetch function, operator and plugin point of kernel/classes/requestlog with its design patterns.
keywords: kernel/classes/requestlog, design patterns, classes, extension points
type: catalogue
---
# Patterns in kernel/classes/requestlog

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `kernel/classes/requestlog` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (1)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expRequestLog` | class | [Utility (static helper)](utility.md) | `kernel/classes/requestlog/exprequestlog.php:32` | The request log: at most one line per web request for a page that does not exist (404.log), with the reason the kernel found, and the hand-over to th… |
