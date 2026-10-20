---
title: Patterns in kernel/classes/logviewer
audience: developer
category: standards
order: 495
summary: Every class, view, fetch function, operator and plugin point of kernel/classes/logviewer with its design patterns.
keywords: kernel/classes/logviewer, design patterns, classes, extension points
type: catalogue
---
# Patterns in kernel/classes/logviewer

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `kernel/classes/logviewer` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (19)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expLogActions` | class | [Utility (static helper)](utility.md) | `kernel/classes/logviewer/explogactions.php:13` | The actions of the log viewer besides reading (doc/eng-us/features/6.0/log-viewer.md, "Actions"): download a range, rotate now, empty a file, remove an arch… |
| `expLogBundle` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `kernel/classes/logviewer/explogbundle.php:17` | "Download all logs" (Setup › Manage logs; doc/eng-us/features/6.0/log-viewer.md, "Downloading every log"): one archive of every log the viewer lists, option… |
| `expLogDigest` | class | – | `kernel/classes/logviewer/explogdigest.php:23` | The error digest of the log viewer (Setup › Log files › Error digest, setup/logsdigest; doc/eng-us/features/6.0/log-viewer.md, "Error digest"): the distinct… |
| `expLogFileLocator` | class | – | `kernel/classes/logviewer/explogfilelocator.php:21` | Which log files the viewer may open, and where they are (doc/eng-us/features/6.0/log-viewer.md, "Which files"). |
| `expLogFileStats` | class | – | `kernel/classes/logviewer/explogfilestats.php:16` | The line rate of the navigator: how many lines a minute each log file gains (doc/eng-us/features/6.0/log-viewer.md, "The navigator"). |
| `expLogHealth` | class | [Utility (static helper)](utility.md) | `kernel/classes/logviewer/exploghealth.php:17` | The health of the logs (Setup › Log files › Health, setup/logshealth; doc/eng-us/features/6.0/log-viewer.md, "Health"): the disk the logs and their archives… |
| `expLogManager` | class implements expLogManagerInterface | [Facade / service](facade.md), [Singleton / instance()](singleton.md) | `kernel/classes/logviewer/explogmanager.php:27` | Log management (doc/eng-us/features/6.0/log-rotation.md): the logs of the installation with their archives, sizes, growth and limits; rotation, pruning, com… |
| `expLogManagerFactory` | class | [Factory and static factory methods](factory.md) | `kernel/classes/logviewer/explogmanagerfactory.php:12` | The log manager the views of the log viewer use (doc/eng-us/features/6.0/log-viewer.md, "Managing logs"): the kernel's service expLogManager when it is ther… |
| `expLogManagerFallback` | class implements expLogManagerInterface | – | `kernel/classes/logviewer/explogmanagerfallback.php:20` | Log management on the log viewer's own classes (expLogManagerInterface; doc/eng-us/features/6.0/log-viewer.md, "Managing logs"), used while the kernel's ser… |
| `expLogManagerInterface` | interface | [Interface (contract)](interface.md) | `kernel/classes/logviewer/explogmanagerinterface.php:19` | Log management (doc/eng-us/features/6.0/log-rotation.md, "For developers"): what the command line, the cronjob part "logs" and the log viewer's actions use. |
| `expLogParser` | class | [Utility (static helper)](utility.md) | `kernel/classes/logviewer/explogparser.php:25` | Reads the lines of a log file as entries (doc/eng-us/features/6.0/log-viewer.md, "The line format"). |
| `expLogPermalink` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `kernel/classes/logviewer/explogpermalink.php:23` | The permalinks of the log viewer (doc/eng-us/features/6.0/log-viewer.md, "Permalinks"): every view of a log as a path without a query string, short enough t… |
| `expLogRateLimit` | class | [Factory and static factory methods](factory.md) | `kernel/classes/logviewer/explogratelimit.php:16` | The request limit of the log viewer's feed, per session ([RateLimitSettings], doc/eng-us/features/6.0/log-viewer.md, "Cost"), and the memory of which files… |
| `expLogRedactor` | class | [Factory and static factory methods](factory.md) | `kernel/classes/logviewer/explogredactor.php:16` | What the log viewer masks before a line leaves the server (doc/eng-us/features/6.0/log-viewer.md, "Privacy"). |
| `expLogSearch` | class | – | `kernel/classes/logviewer/explogsearch.php:26` | Searches log files and their rotations, within limits (doc/eng-us/features/6.0/log-viewer.md, "Searching"). |
| `expLogSearchLimitException` | class extends RuntimeException | [Exception hierarchy](exception.md) | `kernel/classes/logviewer/explogsearch.php:502` | A regular expression that exceeded pcre.backtrack_limit (or another PCRE limit) on a line. |
| `expLogTail` | class | – | `kernel/classes/logviewer/explogtail.php:26` | Reads what a log file gained since a byte offset (doc/eng-us/features/6.0/log-viewer.md, "Following a file"). |
| `expLogViewer` | class | [Factory and static factory methods](factory.md) | `kernel/classes/logviewer/explogviewer.php:16` | The log viewer (Setup › Log files, setup/logs; doc/eng-us/features/6.0/log-viewer.md): what its page, its feed and its command line use. |
| `expLogViewerConfig` | class | [Factory and static factory methods](factory.md), [Singleton / instance()](singleton.md) | `kernel/classes/logviewer/explogviewerconfig.php:11` | The settings of the log viewer (settings/logviewer.ini, doc/eng-us/features/6.0/log-viewer.md, "Settings"), read once per request with safe bounds. |
