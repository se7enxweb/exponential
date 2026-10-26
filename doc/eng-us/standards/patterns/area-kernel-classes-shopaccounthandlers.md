---
title: Patterns in kernel/classes/shopaccounthandlers
audience: developer
category: standards
order: 516
summary: Every class, view, fetch function, operator and plugin point of kernel/classes/shopaccounthandlers with its design patterns.
keywords: kernel/classes/shopaccounthandlers, design patterns, classes, extension points
type: catalogue
---
# Patterns in kernel/classes/shopaccounthandlers

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `kernel/classes/shopaccounthandlers` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (4)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `bcusauserShopAccountHandler` | class extends eZUserShopAccountHandler | [Strategy / handler](strategy-handler.md) | `kernel/classes/shopaccounthandlers/bcusausershopaccounthandler.php:21` | Shop account handler for the US address model: the address line is stored as rather than , and a telephone number is collected and kept as . |
| `eZDefaultShopAccountHandler` | class | [Strategy / handler](strategy-handler.md) | `kernel/classes/shopaccounthandlers/ezdefaultshopaccounthandler.php:12` | – |
| `eZSimpleShopAccountHandler` | class | [Strategy / handler](strategy-handler.md) | `kernel/classes/shopaccounthandlers/ezsimpleshopaccounthandler.php:12` | – |
| `eZUserShopAccountHandler` | class | [Strategy / handler](strategy-handler.md) | `kernel/classes/shopaccounthandlers/ezusershopaccounthandler.php:12` | – |
