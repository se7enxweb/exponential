---
title: Patterns in kernel/classes/packageinstallers
audience: developer
category: standards
order: 507
summary: Every class, view, fetch function, operator and plugin point of kernel/classes/packageinstallers with its design patterns.
keywords: kernel/classes/packageinstallers, design patterns, classes, extension points
type: catalogue
---
# Patterns in kernel/classes/packageinstallers

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `kernel/classes/packageinstallers` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (2)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `eZContentObjectPackageInstaller` | class extends eZPackageInstallationHandler | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/packageinstallers/ezcontentobject/ezcontentobjectpackageinstaller.php:18` | – |
| `eZInstallScriptPackageInstaller` | class extends eZPackageInstallationHandler | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/packageinstallers/ezinstallscript/ezinstallscriptpackageinstaller.php:17` | – |
