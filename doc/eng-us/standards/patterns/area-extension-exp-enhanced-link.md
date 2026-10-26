---
title: Patterns in extension/exp_enhanced_link
audience: developer
category: standards
order: 437
summary: Every class, view, fetch function, operator and plugin point of extension/exp_enhanced_link with its design patterns.
keywords: extension/exp_enhanced_link, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/exp_enhanced_link

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/exp_enhanced_link` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (3)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `exp_enhanced_linkInfo` | class | [Value object / struct](value-object.md) | `extension/exp_enhanced_link/ezinfo.php:12` | – |
| `ngEnhancedLinkType` | class extends eZDataType | [Datatype (Strategy for content fields)](datatype.md) | `extension/exp_enhanced_link/datatypes/ngenhancedlink/ngenhancedlinktype.php:5` | – |
| `ngEnhancedLinkValue` | class | – | `extension/exp_enhanced_link/classes/ngenhancedlinkvalue.php:3` | – |
