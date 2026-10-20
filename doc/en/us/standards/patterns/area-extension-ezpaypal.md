---
title: Patterns in extension/ezpaypal
audience: developer
category: standards
order: 449
summary: Every class, view, fetch function, operator and plugin point of extension/ezpaypal with its design patterns.
keywords: extension/ezpaypal, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/ezpaypal

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/ezpaypal` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (4)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `Exponential\View\Extension\Ezpaypal\Paypal\NotifyUrl` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/ezpaypal/classes/runnable/views/paypal/notify_url.php:38` | The code of extension/ezpaypal/modules/paypal/notify_url.php, moved into a class (#207 stage 1). |
| `eZPaypalChecker` | class extends eZPaymentCallbackChecker | – | `extension/ezpaypal/classes/ezpaypalchecker.php:45` | – |
| `eZPaypalGateway` | class extends eZRedirectGateway | – | `extension/ezpaypal/classes/ezpaypalgateway.php:45` | – |
| `ezpaypalInfo` | class | [Value object / struct](value-object.md) | `extension/ezpaypal/ezinfo.php:2` | – |

## Module views (1)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/paypal/notify_url` | `notify_url.php` | – | `extension/ezpaypal/modules/paypal/module.php:33` |
