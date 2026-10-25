---
title: Patterns in extension/explayouts_site_api
audience: developer
category: standards
order: 421
summary: Every class, view, fetch function, operator and plugin point of extension/explayouts_site_api with its design patterns.
keywords: extension/explayouts_site_api, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/explayouts_site_api

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/explayouts_site_api` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (6)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expLayoutsSiteApiItemValueLoader` | class | – | `extension/explayouts_site_api/classes/explayoutssiteapiitemvalueloader.php:2` | – |
| `expLayoutsSiteApiItemValueUrlGenerator` | class | [Builder](builder.md) | `extension/explayouts_site_api/classes/explayoutssiteapiitemvalueurlgenerator.php:2` | – |
| `expLayoutsSiteApiValueConverter` | class | [Strategy / handler](strategy-handler.md) | `extension/explayouts_site_api/classes/explayoutssiteapivalueconverter.php:2` | – |
| `expLayoutsSiteApiValueObjectProviderContent` | class | – | `extension/explayouts_site_api/classes/explayoutssiteapivalueobjectprovidercontent.php:2` | – |
| `expLayoutsSiteApiValueObjectProviderLocation` | class | – | `extension/explayouts_site_api/classes/explayoutssiteapivalueobjectproviderlocation.php:2` | – |
| `explayouts_site_apiInfo` | class | [Value object / struct](value-object.md) | `extension/explayouts_site_api/ezinfo.php:12` | – |
