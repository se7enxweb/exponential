---
title: Patterns in extension/expchangeclass
audience: developer
category: standards
order: 411
summary: Every class, view, fetch function, operator and plugin point of extension/expchangeclass with its design patterns.
keywords: extension/expchangeclass, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/expchangeclass

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/expchangeclass` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (7)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `conversionFunctions` | class | [Utility (static helper)](utility.md) | `extension/expchangeclass/classes/functions.php:39` | – |
| `expChangeClassConverters` | class | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expchangeclass/classes/converters.php:21` | – |
| `expChangeClassException` | class extends Exception | [Exception hierarchy](exception.md) | `extension/expchangeclass/classes/functions.php:35` | Raised when a conversion cannot be completed. |
| `expchangeclassInfo` | class | [Value object / struct](value-object.md) | `extension/expchangeclass/ezinfo.php:14` | – |
| `Exponential\View\Extension\Expchangeclass\Changeclass\Action` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/expchangeclass/classes/runnable/views/changeclass/action.php:43` | The code of extension/expchangeclass/modules/changeclass/action.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Expchangeclass\Changeclass\MapAttributes` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/expchangeclass/classes/runnable/views/changeclass/map_attributes.php:10` | The code of extension/expchangeclass/modules/changeclass/map_attributes.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Expchangeclass\Changeclass\SelectClass` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/expchangeclass/classes/runnable/views/changeclass/select_class.php:10` | The code of extension/expchangeclass/modules/changeclass/select_class.php, moved into a class (#207 stage 1). |

## Module views (3)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/changeclass/select_class` | `select_class.php` | convert | `extension/expchangeclass/modules/changeclass/module.php:34` |
| `/changeclass/map_attributes` | `map_attributes.php` | convert | `extension/expchangeclass/modules/changeclass/module.php:38` |
| `/changeclass/action` | `action.php` | convert | `extension/expchangeclass/modules/changeclass/module.php:42` |

## Policy functions (1)

| Function | Limitations | Defined in |
|---|---|---|
| `changeclass/convert` | – | `extension/expchangeclass/modules/changeclass/module.php:31` |

## Fetch functions (1)

| Fetch | Implemented by | Defined in |
|---|---|---|
| `fetch( 'changeclass', 'simple_conversion' )` | `conversionFunctions::fetchSimpleConversionArray()` | `extension/expchangeclass/modules/changeclass/function_definition.php:30` |

## INI plugin points (3)

| Setting | Class | Set in |
|---|---|---|
| `[ezstring] Class` | `expChangeClassConverters` | `extension/expchangeclass/settings/changeclass.ini.append.php:39` |
| `[eztext] Class` | `expChangeClassConverters` | `extension/expchangeclass/settings/changeclass.ini.append.php:48` |
| `[ezobjectrelationlist] Class` | `expChangeClassConverters` | `extension/expchangeclass/settings/changeclass.ini.append.php:55` |
