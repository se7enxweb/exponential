---
title: Patterns in kernel/classes/secrets
audience: developer
category: standards
order: 514
summary: Every class, view, fetch function, operator and plugin point of kernel/classes/secrets with its design patterns.
keywords: kernel/classes/secrets, design patterns, classes, extension points
type: catalogue
---
# Patterns in kernel/classes/secrets

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `kernel/classes/secrets` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (3)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expSecret` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `kernel/classes/secrets/expsecret.php:18` | One secret of the kernel's secret store (table exp_secret): its name, the tool it belongs to (scope), a description, and its value encrypted with XCh… |
| `expSecretKey` | class | [Utility (static helper)](utility.md) | `kernel/classes/secrets/expsecretkey.php:31` | The key of the kernel's secret store (expSecrets, doc/eng-us/features/6.0/secret-store.md): |
| `expSecrets` | class | [Utility (static helper)](utility.md) | `kernel/classes/secrets/expsecrets.php:34` | The kernel's secret store (doc/eng-us/features/6.0/secret-store.md): one place for every password, token and API key a tool needs (the data table con… |
