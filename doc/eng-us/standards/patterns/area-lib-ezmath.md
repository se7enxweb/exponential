---
title: Patterns in lib/ezmath
audience: developer
category: standards
order: 547
summary: Every class, view, fetch function, operator and plugin point of lib/ezmath with its design patterns.
keywords: lib/ezmath, design patterns, classes, extension points
type: catalogue
---
# Patterns in lib/ezmath

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `lib/ezmath` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (2)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `eZBCMath` | class extends eZPHPMath | – | `lib/ezmath/classes/mathhandlers/ezbcmath.php:17` | – |
| `eZPHPMath` | class | [Factory and static factory methods](factory.md), [INI plugin point (extension point)](ini-plugin-point.md) | `lib/ezmath/classes/mathhandlers/ezphpmath.php:17` | – |
