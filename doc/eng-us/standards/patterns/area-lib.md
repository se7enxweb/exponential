---
title: Patterns in lib
audience: developer
category: standards
order: 539
summary: Every class, view, fetch function, operator and plugin point of lib with its design patterns.
keywords: lib, design patterns, classes, extension points
type: catalogue
---
# Patterns in lib

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `lib` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (2)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `ExponentialSDK` | class | [Utility (static helper)](utility.md) | `lib/version.php:21` | – |
| `eZPublishSDK` | class extends ExponentialSDK | – | `lib/ezpublishsdk.php:20` | Former name of ExponentialSDK (lib/version.php), kept for compatibility. |
