---
title: Patterns in extension/owsimpleoperator
audience: developer
category: standards
order: 465
summary: Every class, view, fetch function, operator and plugin point of extension/owsimpleoperator with its design patterns.
keywords: extension/owsimpleoperator, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/owsimpleoperator

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/owsimpleoperator` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (5)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `ExampleOperator` | class extends OWSimpleOperator | – | `extension/owsimpleoperator/autoloads/exampleoperator.php:3` | – |
| `OWEzpAutoloader` | class extends ezpAutoloader | [Utility (static helper)](utility.md) | `extension/owsimpleoperator/autoloads/owezpautoloader.php:6` | – |
| `OWSimpleOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `extension/owsimpleoperator/autoloads/owsimpleoperator.php:21` | – |
| `owSimpleOperatorInfo` | class | [Value object / struct](value-object.md) | `extension/owsimpleoperator/ezinfo.php:11` | – |
| `PHPFunctionOperator` | class extends OWSimpleOperator | – | `extension/owsimpleoperator/autoloads/phpfunctionoperator.php:3` | – |
