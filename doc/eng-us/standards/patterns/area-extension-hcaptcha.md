---
title: Patterns in extension/hcaptcha
audience: developer
category: standards
order: 467
summary: Every class, view, fetch function, operator and plugin point of extension/hcaptcha with its design patterns.
keywords: extension/hcaptcha, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/hcaptcha

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/hcaptcha` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (5)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `ezRecaptureUtil` | class | – | `extension/hcaptcha/classes/ezrecaptureutil.php:10` | Utility class |
| `hcaptchaInfo` | class | [Chain of Responsibility](chain.md), [Value object / struct](value-object.md) | `extension/hcaptcha/ezinfo.php:22` | hCaptcha extension for Exponential Written by 7x Based on works by: Bruce Morrison |
| `hCaptchaLibrary` | class | [Utility (static helper)](utility.md) | `extension/hcaptcha/classes/hcaptchalib.php:44` | The hCAPTCHA server URL's |
| `hCaptchaTemplateOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `extension/hcaptcha/autoloads/hcaptchatemplateoperator.php:22` | hCaptcha extension for Exponential Written by 7x Based on works by: Bruce Morrison |
| `hcaptchaType` | class extends eZDataType | [Datatype (Strategy for content fields)](datatype.md) | `extension/hcaptcha/datatypes/hcaptcha/hcaptchatype.php:31` | hcaptcha extension for Exponential Written by Bruce Morrison |

## Policy functions (1)

| Function | Limitations | Defined in |
|---|---|---|
| `hcaptcha/bypass_captcha` | – | `extension/hcaptcha/modules/hcaptcha/module.php:8` |

## Template operators (1)

| Operators | Class | Registered in |
|---|---|---|
| – | `hCaptchaTemplateOperator` | `extension/hcaptcha/autoloads/eztemplateautoload.php:3` |
