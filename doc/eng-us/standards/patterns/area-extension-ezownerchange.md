---
title: Patterns in extension/ezownerchange
audience: developer
category: standards
order: 452
summary: Every class, view, fetch function, operator and plugin point of extension/ezownerchange with its design patterns.
keywords: extension/ezownerchange, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/ezownerchange

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/ezownerchange` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (5)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expOwnerChangeAuditBranch` | class implements expAuditTaxonomyBranch | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/ezownerchange/classes/expownerchangeauditbranch.php:12` | ezownerchange's branch of the audit taxonomy, registered in settings/audit.ini.append.php ([AuditEventSettings] Branches[ezownerchange]). |
| `expOwnerChangePlan` | class | – | `extension/ezownerchange/classes/expownerchangeplan.php:20` | The plan of an owner change: every object the request reaches, with what happens to it. |
| `expOwnerChangeRequest` | class | [Factory and static factory methods](factory.md) | `extension/ezownerchange/classes/expownerchangerequest.php:14` | One owner change request, checked before anything is read or written: the new owner, the scope (one object or a subtree), the start (object or node),… |
| `expOwnerChangeService` | class | [Facade / service](facade.md) | `extension/ezownerchange/classes/expownerchangeservice.php:23` | Changes the owner of content objects: one object, or every object with a location in a subtree. |
| `ezownerchangeInfo` | class | [Value object / struct](value-object.md) | `extension/ezownerchange/ezinfo.php:10` | The about information of ezownerchange (ezinfo/about). |

## Module views (1)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/owner/change` | `change.php` | change | `extension/ezownerchange/modules/owner/module.php:22` |

## Policy functions (2)

| Function | Limitations | Defined in |
|---|---|---|
| `owner/change` | – | `extension/ezownerchange/modules/owner/module.php:39` |
| `owner/subtree` | – | `extension/ezownerchange/modules/owner/module.php:40` |

## INI plugin points (1)

| Setting | Class | Set in |
|---|---|---|
| `[AuditEventSettings] Branches[ezownerchange]` | `expOwnerChangeAuditBranch` | `extension/ezownerchange/settings/audit.ini.append.php:6` |
