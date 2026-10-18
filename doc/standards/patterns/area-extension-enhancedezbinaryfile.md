---
title: Patterns in extension/enhancedezbinaryfile
audience: developer
category: standards
order: 409
summary: Every class, view, fetch function, operator and plugin point of extension/enhancedezbinaryfile with its design patterns.
keywords: extension/enhancedezbinaryfile, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/enhancedezbinaryfile

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/enhancedezbinaryfile` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (3)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `enhancedezbinaryfileInfo` | class | [Value object / struct](value-object.md) | `extension/enhancedezbinaryfile/ezinfo.php:40` | – |
| `EnhancedeZBinaryFileType` | class extends eZDataType | [Datatype (Strategy for content fields)](datatype.md) | `extension/enhancedezbinaryfile/datatypes/enhancedezbinaryfile/enhancedezbinaryfiletype.php:34` | – |
| `TemplateParseXMLOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `extension/enhancedezbinaryfile/autoloads/templateparsexmloperator.php:18` | – |

## Template operators (1)

| Operators | Class | Registered in |
|---|---|---|
| parsexml | `TemplateParseXMLOperator` | `extension/enhancedezbinaryfile/autoloads/eztemplateautoload.php:7` |
