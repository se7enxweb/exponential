---
title: Chain of Responsibility
audience: developer
category: standards
order: 332
summary: A request is passed along a list of handlers until one of them takes it: login handlers, URL matchers, router routes. 7 instances in Exponential.
keywords: design pattern, chain, behavior, pattern catalogue
type: catalogue
---
# Chain of Responsibility

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

A request is passed along a list of handlers until one of them takes it: login handlers, URL matchers, router routes.

| | |
|---|---|
| Group | Behavior |
| Instances | 7 |
| Recognized by | Class name contains LoginHandler, Chain, Pipeline, Middleware, Router or Matcher. |
| Extension point | site.ini [UserSettings] LoginHandler[] (tried in order), siteaccess MatchOrder. |

## Classes by area

### extension/ezjscore (1)

[All patterns of extension/ezjscore](area-extension-ezjscore.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezjscServerRouter` | class | `extension/ezjscore/classes/ezjscserverrouter.php:39` | – | – |

### extension/hcaptcha (1)

[All patterns of extension/hcaptcha](area-extension-hcaptcha.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `hcaptchaInfo` | class | `extension/hcaptcha/ezinfo.php:22` | hCaptcha extension for Exponential Written by 7x Based on works by: Bruce Morrison | – |

### extension/recaptcha (1)

[All patterns of extension/recaptcha](area-extension-recaptcha.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `recaptchaInfo` | class | `extension/recaptcha/ezinfo.php:22` | reCAPTCHA extension for Exponential Written by Bruce Morrison | – |

### kernel/classes/datatypes (1)

[All patterns of kernel/classes/datatypes](area-kernel-classes-datatypes.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZUserLoginHandler` | class | `kernel/classes/datatypes/ezuser/ezuserloginhandler.php:19` | – | – |

### kernel/classes/ini (1)

[All patterns of kernel/classes/ini](area-kernel-classes-ini.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expSettingsChain` | class | `kernel/classes/ini/expsettingschain.php:32` | The override chain of one INI file, built once from the files eZINI reads for it, in eZINI's own order. | – |

### kernel/private (1)

[All patterns of kernel/private](area-kernel-private.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezpRestRouter` | class extends ezcMvcRouter | `kernel/private/rest/classes/router.php:11` | – | – |

### kernel/private/classes/commands (1)

[All patterns of kernel/private/classes/commands](area-kernel-private-classes-commands.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\Command\Kernel\VelocityRouter` | class | `kernel/private/classes/commands/velocity-router.php:46` | The code of bin/php/velocity-router.php, moved into a class (#207). | – |
