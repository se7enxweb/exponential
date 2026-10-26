---
title: Patterns in extension/ezprestapiprovider
audience: developer
category: standards
order: 458
summary: Every class, view, fetch function, operator and plugin point of extension/ezprestapiprovider with its design patterns.
keywords: extension/ezprestapiprovider, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/ezprestapiprovider

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/ezprestapiprovider` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (2)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `ezpRestApiProvider` | class extends ezp7xRestApiProvider | [Strategy / handler](strategy-handler.md) | `extension/ezprestapiprovider/classes/rest_provider.php:19` | Deprecated: the content provider of REST API v1 now lives in the ezprestapi extension, which serves the reads at v1 and v2 and the writes at v2 from… |
| `ezpRestContentController` | class extends ezp7xRestContentController | – | `extension/ezprestapiprovider/classes/controllers/content.php:19` | Deprecated: the content controller of REST API v1 now lives in the ezprestapi extension (ezp7xRestContentController), which answers the same v1 reads… |

## INI plugin points (2)

| Setting | Class | Set in |
|---|---|---|
| `[ApiProvider] ProviderClass[ezp]` | `ezp7xRestApiProvider` | `extension/ezprestapiprovider/settings/rest.ini.append.php:7` |
| `[ApiProvider] ProviderClass[ezpl]` | `ezp7xRestApiProvider` | `extension/ezprestapiprovider/settings/rest.ini.append.php:8` |
