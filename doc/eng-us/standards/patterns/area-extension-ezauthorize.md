---
title: Patterns in extension/ezauthorize
audience: developer
category: standards
order: 439
summary: Every class, view, fetch function, operator and plugin point of extension/ezauthorize with its design patterns.
keywords: extension/ezauthorize, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/ezauthorize

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/ezauthorize` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (5)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `eZAuthorizeAIM` | class | – | `extension/ezauthorize/classes/ezauthorizeaim.php:32` | – |
| `eZAuthorizeGateway` | class extends eZCurlGateway | – | `extension/ezauthorize/classes/ezauthorizegateway.php:40` | – |
| `eZAuthorizeInfo` | class | [Value object / struct](value-object.md) | `extension/ezauthorize/classes/ezauthorizeinfo.php:11` | – |
| `eZCurlGateway` | class extends eZPaymentGateway | – | `extension/ezauthorize/classes/ezcurlgateway.php:50` | – |
| `eZCustomShopAccountHandler` | class | [Strategy / handler](strategy-handler.md) | `extension/ezauthorize/shopaccounthandlers/ezcustomshopaccounthandler.php:5` | – |

## Module views (2)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/refund/refund` | `order.php` | – | `extension/ezauthorize/modules/refund/module.php:27` |
| `/refund/refund` | `order.php` | refund | `extension/ezauthorize/modules/refund/module.php:35` |
