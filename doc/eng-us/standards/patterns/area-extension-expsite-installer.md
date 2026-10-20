---
title: Patterns in extension/expsite_installer
audience: developer
category: standards
order: 430
summary: Every class, view, fetch function, operator and plugin point of extension/expsite_installer with its design patterns.
keywords: extension/expsite_installer, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/expsite_installer

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/expsite_installer` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (2)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expSiteInstaller` | class | [Utility (static helper)](utility.md) | `extension/expsite_installer/classes/expsiteinstaller.php:6` | Super installer for hourly site developers: one command to set everything up. |
| `expsite_installerInfo` | class | [Value object / struct](value-object.md) | `extension/expsite_installer/ezinfo.php:12` | – |
