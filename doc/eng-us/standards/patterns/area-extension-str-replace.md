---
title: Patterns in extension/str_replace
audience: developer
category: standards
order: 479
summary: Every class, view, fetch function, operator and plugin point of extension/str_replace with its design patterns.
keywords: extension/str_replace, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/str_replace

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/str_replace` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (2)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `MyStrReplaceOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `extension/str_replace/autoloads/str_replace_controloperator.php:3` | – |
| `str_replaceInfo` | class | [Value object / struct](value-object.md) | `extension/str_replace/ezinfo.php:2` | – |

## Template operators (1)

| Operators | Class | Registered in |
|---|---|---|
| ezstr_replace | `MyStrReplaceOperator` | `extension/str_replace/autoloads/eztemplateautoload.php:7` |
