---
title: Patterns in kernel/classes/packagecreators
audience: developer
category: standards
order: 499
summary: Every class, view, fetch function, operator and plugin point of kernel/classes/packagecreators with its design patterns.
keywords: kernel/classes/packagecreators, design patterns, classes, extension points
type: catalogue
---
# Patterns in kernel/classes/packagecreators

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `kernel/classes/packagecreators` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (4)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `eZContentClassPackageCreator` | class extends eZPackageCreationHandler | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/packagecreators/ezcontentclass/ezcontentclasspackagecreator.php:19` | – |
| `eZContentObjectPackageCreator` | class extends eZPackageCreationHandler | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/packagecreators/ezcontentobject/ezcontentobjectpackagecreator.php:18` | – |
| `eZExtensionPackageCreator` | class extends eZPackageCreationHandler | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/packagecreators/ezextension/ezextensionpackagecreator.php:19` | – |
| `eZStylePackageCreator` | class extends eZPackageCreationHandler | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/packagecreators/ezstyle/ezstylepackagecreator.php:21` | – |
