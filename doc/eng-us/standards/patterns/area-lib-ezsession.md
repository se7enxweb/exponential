---
title: Patterns in lib/ezsession
audience: developer
category: standards
order: 550
summary: Every class, view, fetch function, operator and plugin point of lib/ezsession with its design patterns.
keywords: lib/ezsession, design patterns, classes, extension points
type: catalogue
---
# Patterns in lib/ezsession

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `lib/ezsession` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (6)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `ezpSessionHandler` | abstract class | [Strategy / handler](strategy-handler.md), [Template Method / abstract base class](template-method.md) | `lib/ezsession/classes/ezpsessionhandler.php:27` | Abstract session handler class to extend |
| `ezpSessionHandlerAdapter` | class implements SessionHandlerInterface | [Adapter / bridge / wrapper](adapter.md), [Strategy / handler](strategy-handler.md) | `lib/ezsession/classes/ezpsessionhandleradapter.php:24` | Gives an ezpSessionHandler to PHP as an object implementing SessionHandlerInterface. |
| `ezpSessionHandlerDB` | class extends ezpSessionHandler | – | `lib/ezsession/classes/ezpsessionhandlerdb.php:20` | DB session handler class |
| `ezpSessionHandlerPHP` | class extends ezpSessionHandler | – | `lib/ezsession/classes/ezpsessionhandlerphp.php:21` | PHP session handler class Does not register it self as opposed to most other handler, as the point is to let PHP handle most things |
| `ezpSessionHandlerSymfony` | class extends ezpSessionHandler | – | `lib/ezsession/classes/ezpsessionhandlersymfony.php:22` | Symfony session handler. |
| `eZSession` | class | [Registry](registry.md) | `lib/ezsession/classes/ezsession.php:67` | Exponential Session interface class |

## Event hooks (11)

| Event | Kind | Called in |
|---|---|---|
| `session/destroy` | notify | `lib/ezsession/classes/ezpsessionhandlerdb.php:144` |
| `session/regenerate` | notify | `lib/ezsession/classes/ezpsessionhandlerdb.php:174` |
| `session/gc` | notify | `lib/ezsession/classes/ezpsessionhandlerdb.php:205` |
| `session/cleanup` | notify | `lib/ezsession/classes/ezpsessionhandlerdb.php:270` |
| `session/destroy` | notify | `lib/ezsession/classes/ezpsessionhandlerphp.php:72` |
| `session/regenerate` | notify | `lib/ezsession/classes/ezpsessionhandlerphp.php:85` |
| `session/gc` | notify | `lib/ezsession/classes/ezpsessionhandlerphp.php:104` |
| `session/cleanup` | notify | `lib/ezsession/classes/ezpsessionhandlerphp.php:116` |
| `session/destroy` | notify | `lib/ezsession/classes/ezpsessionhandlersymfony.php:57` |
| `session/regenerate` | notify | `lib/ezsession/classes/ezpsessionhandlersymfony.php:81` |
| `session/gc` | notify | `lib/ezsession/classes/ezpsessionhandlersymfony.php:102` |
