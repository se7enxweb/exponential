---
title: Patterns in extension/recaptcha
audience: developer
category: standards
order: 472
summary: Every class, view, fetch function, operator and plugin point of extension/recaptcha with its design patterns.
keywords: extension/recaptcha, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/recaptcha

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/recaptcha` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (4)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `recaptchaInfo` | class | [Chain of Responsibility](chain.md), [Value object / struct](value-object.md) | `extension/recaptcha/ezinfo.php:22` | reCAPTCHA extension for Exponential Written by Bruce Morrison |
| `ReCaptchaResponse` | class | – | `extension/recaptcha/classes/recaptchalib.php:142` | A ReCaptchaResponse is returned from recaptcha_check_answer() |
| `reCAPTCHATemplateOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `extension/recaptcha/autoloads/recaptchatemplateoperator.php:22` | reCAPTCHA extension for Exponential Written by Bruce Morrison |
| `recaptchaType` | class extends eZDataType | [Datatype (Strategy for content fields)](datatype.md) | `extension/recaptcha/datatypes/recaptcha/recaptchatype.php:31` | reCAPTCHA extension for Exponential Written by Bruce Morrison |

## Policy functions (1)

| Function | Limitations | Defined in |
|---|---|---|
| `recaptcha/bypass_captcha` | – | `extension/recaptcha/modules/recaptcha/module.php:8` |

## Template operators (1)

| Operators | Class | Registered in |
|---|---|---|
| – | `reCAPTCHATemplateOperator` | `extension/recaptcha/autoloads/eztemplateautoload.php:23` |
