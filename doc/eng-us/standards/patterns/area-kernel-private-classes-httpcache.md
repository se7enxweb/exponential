---
title: Patterns in kernel/private/classes/httpcache
audience: developer
category: standards
order: 520
summary: Every class, view, fetch function, operator and plugin point of kernel/private/classes/httpcache with its design patterns.
keywords: kernel/private/classes/httpcache, design patterns, classes, extension points
type: catalogue
---
# Patterns in kernel/private/classes/httpcache

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `kernel/private/classes/httpcache` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (2)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `ezpHttpCacheContract` | class | [Factory and static factory methods](factory.md) | `kernel/private/classes/httpcache/ezphttpcachecontract.php:38` | The role-aware HTTP cache contract: keys, context hashes, the entry and record formats, tag validation and placeholder substitution. |
| `ezpHttpCacheListener` | class | [Observer / listener / event](observer.md), [Registry](registry.md), [Utility (static helper)](utility.md) | `kernel/private/classes/httpcache/ezphttpcachelistener.php:27` | The kernel side of the role-aware HTTP cache: stores rendered content views for the early exit and Velocity's parent to serve, and turns the kernel's… |

## Event hooks (1)

| Event | Kind | Called in |
|---|---|---|
| `httpcache/store/headers` | filter | `kernel/private/classes/httpcache/ezphttpcachelistener.php:351` |
