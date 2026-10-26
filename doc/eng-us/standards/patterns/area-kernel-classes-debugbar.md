---
title: Patterns in kernel/classes/debugbar
audience: developer
category: standards
order: 498
summary: Every class, view, fetch function, operator and plugin point of kernel/classes/debugbar with its design patterns.
keywords: kernel/classes/debugbar, design patterns, classes, extension points
type: catalogue
---
# Patterns in kernel/classes/debugbar

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `kernel/classes/debugbar` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (8)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expDebugBarIPList` | class | [Utility (static helper)](utility.md) | `kernel/classes/debugbar/expdebugbariplist.php:28` | One line of DebugIPList[] is an entry: |
| `expDebugBarLog` | class | – | `kernel/classes/debugbar/expdebugbarlog.php:19` | One JSON object per line in var//log/debugbar.log ([DebugBarSettings] LogFile in debugbar.ini): who (user id, login, address), what (setting, file, b… |
| `expDebugBarPanelInterface` | interface | [Interface (contract)](interface.md) | `kernel/classes/debugbar/expdebugbarpanelinterface.php:26` | A panel of the Exp Debug bar from an extension: its own tab after Memory, optionally a chip in the summary and data in the page's JSON. |
| `expDebugBarRegistry` | class | [Registry](registry.md) | `kernel/classes/debugbar/expdebugbarregistry.php:20` | Reads settings/debugbar.ini (and every extension's debugbar.ini.append.php, merged by eZINI): |
| `expDebugBarServerFunctions` | class extends ezjscServerFunctions | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `kernel/classes/debugbar/expdebugbarserverfunctions.php:28` | The ezjscore server functions of the Exp Debug bar, registered as [ezjscServer_expdebugbar] in extension/ezjscore/settings/ezjscore.ini: |
| `expDebugBarSettings` | class | – | `kernel/classes/debugbar/expdebugbarsettings.php:25` | For a siteaccess (the current one by default): |
| `expDebugBarSummary` | class | [Utility (static helper)](utility.md) | `kernel/classes/debugbar/expdebugbarsummary.php:20` | Collects what the request cost, from what eZDebug already measured: page time, SQL statements and their time (the *_query accumulators of the databas… |
| `expRenderStats` | class | [Utility (static helper)](utility.md) | `kernel/classes/debugbar/exprenderstats.php:19` | Collects, for the request being served, what the debug bar's Rendering tab shows: the controller (module, view, script and view class), what the cont… |
