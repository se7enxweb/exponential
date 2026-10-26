---
title: Patterns in kernel/private/classes/clusterfilehandlers
audience: developer
category: standards
order: 529
summary: Every class, view, fetch function, operator and plugin point of kernel/private/classes/clusterfilehandlers with its design patterns.
keywords: kernel/private/classes/clusterfilehandlers, design patterns, classes, extension points
type: catalogue
---
# Patterns in kernel/private/classes/clusterfilehandlers

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `kernel/private/classes/clusterfilehandlers` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (14)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `eZClusterEventListener` | interface | [Interface (contract)](interface.md), [Observer / listener / event](observer.md) | `kernel/private/classes/clusterfilehandlers/interfaces/ezclustereventlistener.php:14` | Interface to be implemented by any cluster event listener |
| `eZClusterEventLogger` | interface | [Interface (contract)](interface.md) | `kernel/private/classes/clusterfilehandlers/interfaces/ezclustereventlogger.php:20` | Interface for eZClusterEvent loggers. |
| `eZClusterEventLoggerEzdebug` | class implements eZClusterEventLogger | – | `kernel/private/classes/clusterfilehandlers/ezclustereventloggerezdebug.php:15` | Logger using eZDebugSetting. |
| `eZClusterEventLoggerPhp` | class implements eZClusterEventLogger | – | `kernel/private/classes/clusterfilehandlers/ezclustereventloggerphp.php:14` | Logger using PHP log |
| `eZClusterEventNotifier` | interface | [Interface (contract)](interface.md) | `kernel/private/classes/clusterfilehandlers/interfaces/ezclustereventnotifier.php:15` | Interface that must be implemented by cluster backends supporting events notification. |
| `eZDFSFileHandler` | class implements eZClusterFileHandlerInterface, ezpDatabaseBasedClusterFileHandler | [Strategy / handler](strategy-handler.md) | `kernel/private/classes/clusterfilehandlers/ezdfsfilehandler.php:27` | Handles file operations for Distributed File Systems (f.e. |
| `eZDFSFileHandlerBackendException` | class extends Exception | [Exception hierarchy](exception.md) | `kernel/private/classes/clusterfilehandlers/dfsbackends/ezdfsfilehandlerbackendexception.php:10` | This file is part of the Exponential package |
| `eZDFSFileHandlerBackendFactory` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `kernel/private/classes/clusterfilehandlers/ezdfsfilehandlerbackendfactory.php:14` | Instantiates the DFSBackend based on INI settings |
| `eZDFSFileHandlerDFSBackend` | class implements eZDFSFileHandlerDFSBackendInterface | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `kernel/private/classes/clusterfilehandlers/dfsbackends/dfs.php:12` | – |
| `eZDFSFileHandlerDFSBackendFactoryInterface` | interface | [Factory and static factory methods](factory.md), [Interface (contract)](interface.md) | `kernel/private/classes/clusterfilehandlers/dfsbackends/ezdfsfilehandlerdfsbackendfactoryinterface.php:14` | Makes a DFSBackend handler instantiable using the build static method. |
| `eZDFSFileHandlerDFSBackendFilterIterator` | class extends FilterIterator | [Decorator / filter](decorator.md) | `kernel/private/classes/clusterfilehandlers/dfsbackends/dfs_filter_iterator.php:15` | A filter iterator used by eZDFSFileHandlerDFSBackend. |
| `eZDFSFileHandlerDFSBackendInterface` | interface | [Interface (contract)](interface.md) | `kernel/private/classes/clusterfilehandlers/dfsbackends/ezdfsfilehandlerdfsbackendinterface.php:17` | Interface for a DFS FS handler, that offers CRUD support for binary files. |
| `eZDFSFileHandlerMySQLiBackend` | class implements eZClusterEventNotifier | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `kernel/private/classes/clusterfilehandlers/dfsbackends/mysqli.php:50` | – |
| `eZDFSMySQLBackendError` | class | – | `kernel/private/classes/clusterfilehandlers/dfsbackends/mysqlbackenderror.php:17` | – |
