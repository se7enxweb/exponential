---
title: Patterns in kernel/classes/apikey
audience: developer
category: standards
order: 479
summary: Every class, view, fetch function, operator and plugin point of kernel/classes/apikey with its design patterns.
keywords: kernel/classes/apikey, design patterns, classes, extension points
type: catalogue
---
# Patterns in kernel/classes/apikey

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `kernel/classes/apikey` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (3)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expApiKey` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/classes/apikey/expapikey.php:31` | A personal API key (publishing key) of a user: one row of expapikey (doc/guides/api-keys.md). |
| `expApiKeyFunctionCollection` | class | [Fetch function (Query Object, Facade)](fetch-function.md), [Utility (static helper)](utility.md) | `kernel/classes/apikey/expapikeyfunctioncollection.php:13` | The fetch functions of the apikey module (kernel/apikey/function_definition.php). |
| `expApiKeySchema` | class | [Utility (static helper)](utility.md) | `kernel/classes/apikey/expapikeyschema.php:16` | The expapikey table on any engine: whether it exists, and creating it from its definition in share/db_schema.dba through the engine's schema handler… |
