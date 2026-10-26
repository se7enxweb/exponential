---
title: Patterns in lib/ezfile
audience: developer
category: standards
order: 544
summary: Every class, view, fetch function, operator and plugin point of lib/ezfile with its design patterns.
keywords: lib/ezfile, design patterns, classes, extension points
type: catalogue
---
# Patterns in lib/ezfile

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `lib/ezfile` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (13)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expLogRotator` | class | [Utility (static helper)](utility.md) | `lib/ezfile/classes/explogrotator.php:28` | The one implementation of log rotation in the kernel (doc/eng-us/features/6.0/log-rotation.md). |
| `eZBZIP2Handler` | class extends eZCompressionHandler | [Strategy / handler](strategy-handler.md) | `lib/ezfile/classes/ezbzip2compressionhandler.php:20` | – |
| `eZCompressionHandler` | class extends eZFileHandler | [Strategy / handler](strategy-handler.md) | `lib/ezfile/classes/ezcompressionhandler.php:34` | – |
| `eZDir` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `lib/ezfile/classes/ezdir.php:17` | – |
| `eZFile` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `lib/ezfile/classes/ezfile.php:18` | – |
| `eZFileHandler` | class | [Singleton / instance()](singleton.md), [Strategy / handler](strategy-handler.md) | `lib/ezfile/classes/ezfilehandler.php:56` | – |
| `eZForwardCompressionHandler` | class extends eZCompressionHandler | [Strategy / handler](strategy-handler.md) | `lib/ezfile/classes/ezforwardcompressionhandler.php:20` | – |
| `eZGZIPCompressionHandler` | class extends eZForwardCompressionHandler | [Strategy / handler](strategy-handler.md) | `lib/ezfile/classes/ezgzipcompressionhandler.php:22` | – |
| `eZGZIPShellCompressionHandler` | class extends eZCompressionHandler | [Strategy / handler](strategy-handler.md) | `lib/ezfile/classes/ezgzipshellcompressionhandler.php:22` | – |
| `eZGZIPZLIBCompressionHandler` | class extends eZCompressionHandler | [Strategy / handler](strategy-handler.md) | `lib/ezfile/classes/ezgzipzlibcompressionhandler.php:20` | – |
| `eZLog` | class | [Utility (static helper)](utility.md) | `lib/ezfile/classes/ezlog.php:21` | – |
| `eZMD5` | class | – | `lib/ezfile/classes/ezmd5.php:17` | – |
| `eZNoCompressionHandler` | class extends eZCompressionHandler | [Strategy / handler](strategy-handler.md) | `lib/ezfile/classes/eznocompressionhandler.php:18` | – |
