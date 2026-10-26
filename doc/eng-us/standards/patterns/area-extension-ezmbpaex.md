---
title: Patterns in extension/ezmbpaex
audience: developer
category: standards
order: 448
summary: Every class, view, fetch function, operator and plugin point of extension/ezmbpaex with its design patterns.
keywords: extension/ezmbpaex, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/ezmbpaex

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/ezmbpaex` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (4)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `ezmbpaexInfo` | class | [Value object / struct](value-object.md) | `extension/ezmbpaex/ezinfo.php:7` | – |
| `eZPaEx` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `extension/ezmbpaex/classes/ezpaex.php:11` | – |
| `ezpaextype` | class extends eZDataType | [Datatype (Strategy for content fields)](datatype.md) | `extension/ezmbpaex/datatypes/ezpaex/ezpaextype.php:16` | Handles the datatype used for password expiration. |
| `eZPaExUser` | class extends eZUser | [Active Record (eZPersistentObject)](active-record.md) | `extension/ezmbpaex/login_handler/ezpaexuser.php:23` | Handles logins for users using new Password Expiry constraints |

## Module views (2)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/userpaex/password` | `password.php` | password | `extension/ezmbpaex/modules/userpaex/module.php:16` |
| `/userpaex/forgotpassword` | `forgotpassword.php` | password | `extension/ezmbpaex/modules/userpaex/module.php:23` |

## Policy functions (2)

| Function | Limitations | Defined in |
|---|---|---|
| `userpaex/password` | – | `extension/ezmbpaex/modules/userpaex/module.php:37` |
| `userpaex/editpaex` | – | `extension/ezmbpaex/modules/userpaex/module.php:38` |

## Cronjob parts (2)

| Cronjob | File | Description |
|---|---|---|
| `ezmbpaex_sendexpirynotifications` | `extension/ezmbpaex/cronjobs/ezmbpaex_sendexpirynotifications.php` | – |
| `ezmbpaex_updatechildren` | `extension/ezmbpaex/cronjobs/ezmbpaex_updatechildren.php` | – |
