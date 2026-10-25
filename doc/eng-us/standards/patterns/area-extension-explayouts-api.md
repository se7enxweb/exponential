---
title: Patterns in extension/explayouts_api
audience: developer
category: standards
order: 415
summary: Every class, view, fetch function, operator and plugin point of extension/explayouts_api with its design patterns.
keywords: extension/explayouts_api, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/explayouts_api

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/explayouts_api` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (10)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expLayoutsEzContentContextProvider` | class | [Strategy / handler](strategy-handler.md) | `extension/explayouts_api/classes/explayoutsezcontentcontextprovider.php:2` | – |
| `expLayoutsEzContentProvider` | class | [Strategy / handler](strategy-handler.md) | `extension/explayouts_api/classes/explayoutsezcontentprovider.php:2` | – |
| `expLayoutsEzContentTypeMapper` | class | – | `extension/explayouts_api/classes/explayoutsezcontenttypemapper.php:2` | – |
| `expLayoutsEzContentValidator` | class | – | `extension/explayouts_api/classes/explayoutsezcontentvalidator.php:2` | – |
| `expLayoutsEzRemoteIdConverter` | class | [Strategy / handler](strategy-handler.md) | `extension/explayouts_api/classes/explayoutsezremoteidconverter.php:2` | – |
| `expLayoutsEzSectionMapper` | class | – | `extension/explayouts_api/classes/explayoutsezsectionmapper.php:2` | – |
| `expLayoutsEzValueConverter` | class | [Strategy / handler](strategy-handler.md) | `extension/explayouts_api/classes/explayoutsezvalueconverter.php:2` | – |
| `expLayoutsEzValueLoader` | class | – | `extension/explayouts_api/classes/explayoutsezvalueloader.php:2` | – |
| `expLayoutsEzValueUrlGenerator` | class | [Builder](builder.md) | `extension/explayouts_api/classes/explayoutsezvalueurlgenerator.php:2` | – |
| `explayouts_apiInfo` | class | [Value object / struct](value-object.md) | `extension/explayouts_api/ezinfo.php:12` | – |
