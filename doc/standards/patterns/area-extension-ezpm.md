---
title: Patterns in extension/ezpm
audience: developer
category: standards
order: 450
summary: Every class, view, fetch function, operator and plugin point of extension/ezpm with its design patterns.
keywords: extension/ezpm, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/ezpm

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/ezpm` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (5)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `eZContact` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `extension/ezpm/classes/ezcontact.php:15` | – |
| `eZPm` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `extension/ezpm/classes/ezpm.php:15` | – |
| `eZPmBlackList` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `extension/ezpm/classes/ezpmblacklist.php:15` | – |
| `eZPmFunctionCollection` | class | [Fetch function (Query Object, Facade)](fetch-function.md) | `extension/ezpm/modules/pm/ezpmfunctioncollection.php:5` | – |
| `ezpmInfo` | class | [Value object / struct](value-object.md) | `extension/ezpm/ezinfo.php:25` | – |

## Module views (12)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/pm/message` | `view_message.php` | – | `extension/ezpm/modules/pm/module.php:7` |
| `/pm/list` | `list.php` | – | `extension/ezpm/modules/pm/module.php:11` |
| `/pm/list_inbox` | `list.php` | – | `extension/ezpm/modules/pm/module.php:15` |
| `/pm/list_sent` | `list_sent.php` | – | `extension/ezpm/modules/pm/module.php:19` |
| `/pm/list_drafts` | `list_drafts.php` | – | `extension/ezpm/modules/pm/module.php:23` |
| `/pm/create` | `create.php` | – | `extension/ezpm/modules/pm/module.php:27` |
| `/pm/reply` | `reply.php` | – | `extension/ezpm/modules/pm/module.php:31` |
| `/pm/edit` | `edit.php` | – | `extension/ezpm/modules/pm/module.php:35` |
| `/pm/add` | `add.php` | – | `extension/ezpm/modules/pm/module.php:39` |
| `/pm/action` | `action.php` | – | `extension/ezpm/modules/pm/module.php:46` |
| `/pm/blacklist` | `blacklist.php` | – | `extension/ezpm/modules/pm/module.php:59` |
| `/pm/contacts` | `contact_list.php` | – | `extension/ezpm/modules/pm/module.php:63` |

## Policy functions (6)

| Function | Limitations | Defined in |
|---|---|---|
| `pm/list` | – | `extension/ezpm/modules/pm/module.php:67` |
| `pm/blacklist` | – | `extension/ezpm/modules/pm/module.php:68` |
| `pm/contacts` | – | `extension/ezpm/modules/pm/module.php:69` |
| `pm/send_message` | – | `extension/ezpm/modules/pm/module.php:70` |
| `pm/add_contact` | – | `extension/ezpm/modules/pm/module.php:71` |
| `pm/stats` | – | `extension/ezpm/modules/pm/module.php:72` |

## Fetch functions (4)

| Fetch | Implemented by | Defined in |
|---|---|---|
| `fetch( 'pm', 'view_message' )` | `eZPm::fetchMessage()` | `extension/ezpm/modules/pm/function_definition.php:11` |
| `fetch( 'pm', 'list' )` | `eZPm::fetchMessageList()` | `extension/ezpm/modules/pm/function_definition.php:19` |
| `fetch( 'pm', 'messages_stats' )` | `eZPm::messagesStats()` | `extension/ezpm/modules/pm/function_definition.php:36` |
| `fetch( 'pm', 'check_contact' )` | `eZContact::checkIsOnContactList()` | `extension/ezpm/modules/pm/function_definition.php:45` |
