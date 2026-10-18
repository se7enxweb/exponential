---
title: Front Controller
audience: developer
category: standards
order: 318
summary: One entry point receives every request, matches the siteaccess and dispatches it to a module view. 10 instances in Exponential.
keywords: design pattern, front-controller, request handling, pattern catalogue
type: catalogue
---
# Front Controller

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

One entry point receives every request, matches the siteaccess and dispatches it to a module view.

| | |
|---|---|
| Group | Request handling |
| Instances | 10 |
| Recognized by | The kernel classes ezpKernel, ezpKernelWeb, ezpKernelTreeMenu, ezpKernelRest, eZModule and their kind. |
| Extension point | index.php creates ezpKernel with ezpKernelWeb; Velocity and FrankenPHP keep the kernel in memory. |

## Classes by area

### kernel/classes (1)

[All patterns of kernel/classes](area-kernel-classes.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZSiteAccess` | class | `kernel/classes/ezsiteaccess.php:17` | Provides functions for siteaccess handling | – |

### kernel/private (7)

[All patterns of kernel/private](area-kernel-private.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezpKernel` | class | `kernel/private/classes/ezpkernel.php:16` | Base Exponential kernel class. | – |
| `ezpKernelHandler` | interface | `kernel/private/interfaces/ezpkernelhandler.php:14` | Interface for kernel handlers | – |
| `ezpKernelRest` | class | `kernel/private/rest/classes/ezpkernelrest.php:11` | – | – |
| `ezpKernelTreeMenu` | class | `kernel/private/classes/ezpkerneltreemenu.php:11` | – | – |
| `ezpKernelWeb` | class | `kernel/private/classes/ezpkernelweb.php:16` | Provides a kernel handler in web context | – |
| `ezpMvcRegexpRoute` | class extends ezcMvcRegexpRoute | `kernel/private/rest/classes/routes/regexp.php:16` | Override of ezcMvcRegexpRoute. | – |
| `ezpRestMvcController` | abstract class extends ezcMvcController | `kernel/private/rest/classes/controllers/rest_controller.php:15` | Abstract class that must be extended by every REST controller | – |

### lib/ezutils (2)

[All patterns of lib/ezutils](area-lib-ezutils.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZModule` | class | `lib/ezutils/classes/ezmodule.php:36` | The eZModule class is used to instanciate and use modules & views. | – |
| `eZURI` | class | `lib/ezutils/classes/ezuri.php:32` | Provides access to the HTTP uri | – |
