---
title: Patterns in extension/ngclasslist
audience: developer
category: standards
order: 467
summary: Every class, view, fetch function, operator and plugin point of extension/ngclasslist with its design patterns.
keywords: extension/ngclasslist, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/ngclasslist

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/ngclasslist` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (2)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `ngclasslistInfo` | class | [Value object / struct](value-object.md) | `extension/ngclasslist/ezinfo.php:3` | – |
| `NgClassListType` | class extends eZDataType | [Datatype (Strategy for content fields)](datatype.md) | `extension/ngclasslist/datatypes/ngclasslist/ngclasslisttype.php:6` | NgClassListType class implements the ngclasslist datatype |
