---
title: Patterns in kernel/private/classes/urlaliasfilters
audience: developer
category: standards
order: 524
summary: Every class, view, fetch function, operator and plugin point of kernel/private/classes/urlaliasfilters with its design patterns.
keywords: kernel/private/classes/urlaliasfilters, design patterns, classes, extension points
type: catalogue
---
# Patterns in kernel/private/classes/urlaliasfilters

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `kernel/private/classes/urlaliasfilters` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (1)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `eZURLAliasFilterAppendNodeID` | class extends eZURLAliasFilter | [Decorator / filter](decorator.md), [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/private/classes/urlaliasfilters/ezurlaliasfilterappendnodeid.php:18` | This class is an URL alias filter to be run with the eZURLAliasFilter system |
