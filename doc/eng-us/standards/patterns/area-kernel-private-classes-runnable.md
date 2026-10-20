---
title: Patterns in kernel/private/classes/runnable
audience: developer
category: standards
order: 522
summary: Every class, view, fetch function, operator and plugin point of kernel/private/classes/runnable with its design patterns.
keywords: kernel/private/classes/runnable, design patterns, classes, extension points
type: catalogue
---
# Patterns in kernel/private/classes/runnable

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `kernel/private/classes/runnable` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (4)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `Exponential\Runnable\Command` | abstract class extends Runnable | [Command (console, scripts, cronjobs)](command.md), [Template Method / abstract base class](template-method.md) | `kernel/private/classes/runnable/command.php:36` | A CLI command. |
| `Exponential\Runnable\CronjobPart` | abstract class extends Runnable | [Command (console, scripts, cronjobs)](command.md), [Template Method / abstract base class](template-method.md) | `kernel/private/classes/runnable/cronjobpart.php:21` | A cronjob part. |
| `Exponential\Runnable\ModuleView` | abstract class extends Runnable | [Command (console, scripts, cronjobs)](command.md), [Template Method / abstract base class](template-method.md) | `kernel/private/classes/runnable/moduleview.php:23` | A module view. |
| `Exponential\Runnable\Runnable` | abstract class | [Factory and static factory methods](factory.md), [Template Method / abstract base class](template-method.md) | `kernel/private/classes/runnable/runnable.php:30` | The base of everything Exponential runs from a file: CLI commands (bin/), cronjob parts (cronjobs/) and module views (kernel//.php, extension//module… |
