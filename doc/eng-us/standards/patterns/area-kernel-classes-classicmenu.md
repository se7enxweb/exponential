---
title: Patterns in kernel/classes/classicmenu
audience: developer
category: standards
order: 489
summary: Every class, view, fetch function, operator and plugin point of kernel/classes/classicmenu with its design patterns.
keywords: kernel/classes/classicmenu, design patterns, classes, extension points
type: catalogue
---
# Patterns in kernel/classes/classicmenu

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `kernel/classes/classicmenu` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (2)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expClassicMenuSettings` | class | [Utility (static helper)](utility.md) | `kernel/classes/classicmenu/expclassicmenusettings.php:22` | Reads and writes the classic menu settings of one siteaccess: menu.ini [SelectedMenu] CurrentMenu, TopMenu and LeftMenu, which the page layouts of th… |
| `expClassicMenuSiteAccessInspector` | class | [Utility (static helper)](utility.md) | `kernel/classes/classicmenu/expclassicmenusiteaccessinspector.php:28` | Tells for a siteaccess whether its pages use the classic menu settings (menu.ini [SelectedMenu]). |
