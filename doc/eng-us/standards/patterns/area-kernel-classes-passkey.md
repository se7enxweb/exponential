---
title: Patterns in kernel/classes/passkey
audience: developer
category: standards
order: 508
summary: Every class, view, fetch function, operator and plugin point of kernel/classes/passkey with its design patterns.
keywords: kernel/classes/passkey, design patterns, classes, extension points
type: catalogue
---
# Patterns in kernel/classes/passkey

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `kernel/classes/passkey` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (5)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expPasskey` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/classes/passkey/exppasskey.php:22` | A passkey of a user: one row of exppasskey (doc/eng-us/guides/passkeys.md). |
| `expPasskeyLogin` | class extends eZUser | [Active Record (eZPersistentObject)](active-record.md) | `kernel/classes/passkey/exppasskeylogin.php:16` | Signs in an account whose passkey expPasskeyService checked, through the same housekeeping as every password sign-in (eZUser::loginSucceeded(): last… |
| `expPasskeySchema` | class | [Utility (static helper)](utility.md) | `kernel/classes/passkey/exppasskeyschema.php:17` | The exppasskey table on any engine: whether it exists, and creating it from its definition in share/db_schema.dba through the engine's schema handler… |
| `expPasskeyService` | class | [Facade / service](facade.md) | `kernel/classes/passkey/exppasskeyservice.php:32` | The passkey ceremonies of the web pages (doc/eng-us/guides/passkeys.md): |
| `expPasskeySettings` | class | [Factory and static factory methods](factory.md) | `kernel/classes/passkey/exppasskeysettings.php:19` | site.ini [PasskeySettings], read for one request (doc/eng-us/guides/passkeys.md): whether passkeys are on, the relying party (id and name), the origi… |
