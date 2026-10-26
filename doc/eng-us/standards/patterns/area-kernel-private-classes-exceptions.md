---
title: Patterns in kernel/private/classes/exceptions
audience: developer
category: standards
order: 532
summary: Every class, view, fetch function, operator and plugin point of kernel/private/classes/exceptions with its design patterns.
keywords: kernel/private/classes/exceptions, design patterns, classes, extension points
type: catalogue
---
# Patterns in kernel/private/classes/exceptions

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `kernel/private/classes/exceptions` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (14)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `eZClusterHandlerDBNoConnectionException` | class extends eZDBException | [Exception hierarchy](exception.md) | `kernel/private/classes/exceptions/cluster/noconnection.php:19` | Class representing a cluster no connection exception |
| `eZClusterHandlerDBNoDatabaseException` | class extends eZDBException | [Exception hierarchy](exception.md) | `kernel/private/classes/exceptions/cluster/nodatabase.php:19` | Class representing a no database exception |
| `eZDBException` | class extends ezcBaseException | [Exception hierarchy](exception.md) | `kernel/private/classes/exceptions/database/exception.php:26` | Class representing a the top class for any database related exception |
| `eZDBNoConnectionException` | class extends eZDBException | [Exception hierarchy](exception.md) | `kernel/private/classes/exceptions/database/noconnection.php:19` | Class representing a no connection exception |
| `eZDFSFileHandlerNFSMountPointNotFoundException` | class extends ezcBaseException | [Exception hierarchy](exception.md) | `kernel/private/classes/exceptions/cluster/mount_point_not_found.php:19` | Class representing a cluster mount point not found exception |
| `eZDFSFileHandlerNFSMountPointNotWriteableException` | class extends ezcBaseException | [Exception hierarchy](exception.md) | `kernel/private/classes/exceptions/cluster/mount_point_not_writeable.php:19` | Class representing a cluster mount point not writeable exception |
| `eZDFSFileHandlerTableNotFoundException` | class extends ezcBaseException | [Exception hierarchy](exception.md) | `kernel/private/classes/exceptions/cluster/table_not_found.php:19` | Class handling a cluster table not found exception |
| `ezpAccessDenied` | class extends Exception | [Exception hierarchy](exception.md) | `kernel/private/classes/exceptions/kernel/accessdenied.php:17` | Exception occuring when access is denied. |
| `ezpFormTokenException` | class extends Exception | [Exception hierarchy](exception.md) | `kernel/private/classes/exceptions/kernel/formtoken.php:23` | A POST was refused because its form token (CSRF token) was missing or did not belong to the session: the form was loaded in another session, before a… |
| `ezpLanguageNotFound` | class extends Exception | [Exception hierarchy](exception.md) | `kernel/private/classes/exceptions/kernel/languagenotfound.php:17` | Exception occuring when an unknown language is used. |
| `ezpModuleDisabled` | class extends Exception | [Exception hierarchy](exception.md) | `kernel/private/classes/exceptions/kernel/moduledisabled.php:17` | Exception occuring when a module is disabled. |
| `ezpModuleNotFound` | class extends Exception | [Exception hierarchy](exception.md) | `kernel/private/classes/exceptions/kernel/modulenotfound.php:17` | Exception occuring when a module is not found. |
| `ezpModuleViewDisabled` | class extends Exception | [Exception hierarchy](exception.md) | `kernel/private/classes/exceptions/kernel/moduleviewdisabled.php:17` | Exception occuring when a module/view is disabled. |
| `ezpModuleViewNotFound` | class extends Exception | [Exception hierarchy](exception.md) | `kernel/private/classes/exceptions/kernel/moduleviewnotfound.php:17` | Exception occuring when a module/view is not found. |
