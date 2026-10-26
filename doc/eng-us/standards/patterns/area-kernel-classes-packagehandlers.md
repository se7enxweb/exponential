---
title: Patterns in kernel/classes/packagehandlers
audience: developer
category: standards
order: 507
summary: Every class, view, fetch function, operator and plugin point of kernel/classes/packagehandlers with its design patterns.
keywords: kernel/classes/packagehandlers, design patterns, classes, extension points
type: catalogue
---
# Patterns in kernel/classes/packagehandlers

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `kernel/classes/packagehandlers` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (7)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `eZContentClassPackageHandler` | class extends eZPackageHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `kernel/classes/packagehandlers/ezcontentclass/ezcontentclasspackagehandler.php:18` | – |
| `eZContentObjectPackageHandler` | class extends eZPackageHandler | [Factory and static factory methods](factory.md), [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `kernel/classes/packagehandlers/ezcontentobject/ezcontentobjectpackagehandler.php:18` | – |
| `eZDBPackageHandler` | class extends eZPackageHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `kernel/classes/packagehandlers/ezdb/ezdbpackagehandler.php:18` | – |
| `eZExtensionPackageHandler` | class extends eZPackageHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `kernel/classes/packagehandlers/ezextension/ezextensionpackagehandler.php:18` | – |
| `eZFilePackageHandler` | class extends eZPackageHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `kernel/classes/packagehandlers/ezfile/ezfilepackagehandler.php:18` | – |
| `eZINIAddonPackageHandler` | class extends eZPackageHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `kernel/classes/packagehandlers/eziniaddon/eziniaddonpackagehandler.php:18` | – |
| `eZInstallScriptPackageHandler` | class extends eZPackageHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `kernel/classes/packagehandlers/ezinstallscript/ezinstallscriptpackagehandler.php:19` | – |
