---
title: Patterns in kernel/classes/mail
audience: developer
category: standards
order: 503
summary: Every class, view, fetch function, operator and plugin point of kernel/classes/mail with its design patterns.
keywords: kernel/classes/mail, design patterns, classes, extension points
type: catalogue
---
# Patterns in kernel/classes/mail

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `kernel/classes/mail` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (1)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expMailTemplateBody` | class | [Utility (static helper)](utility.md) | `kernel/classes/mail/expmailtemplatebody.php:16` | The body of a mail made from a template: the template's output, and what the template set at root scope. |
