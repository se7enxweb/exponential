---
title: Patterns in extension/expsite_api
audience: developer
category: standards
order: 429
summary: Every class, view, fetch function, operator and plugin point of extension/expsite_api with its design patterns.
keywords: extension/expsite_api, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/expsite_api

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/expsite_api` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (7)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expSiteApi` | class | [Facade / service](facade.md), [Utility (static helper)](utility.md) | `extension/expsite_api/classes/expsiteapi.php:2` | – |
| `expSiteApiContent` | class | – | `extension/expsite_api/classes/expsiteapicontent.php:2` | – |
| `expSiteApiContentService` | class | [Facade / service](facade.md) | `extension/expsite_api/classes/expsiteapicontentservice.php:2` | – |
| `expSiteApiFilterService` | class | [Decorator / filter](decorator.md), [Facade / service](facade.md) | `extension/expsite_api/classes/expsiteapifilterservice.php:2` | – |
| `expSiteApiLocation` | class | – | `extension/expsite_api/classes/expsiteapilocation.php:2` | – |
| `expSiteApiLocationService` | class | [Facade / service](facade.md) | `extension/expsite_api/classes/expsiteapilocationservice.php:2` | – |
| `expsite_apiInfo` | class | [Value object / struct](value-object.md) | `extension/expsite_api/ezinfo.php:12` | – |
