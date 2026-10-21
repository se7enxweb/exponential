---
title: Muster in kernel/private/classes/clusterfilehandlers
audience: developer
category: standards
order: 516
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von kernel/private/classes/clusterfilehandlers mit den Entwurfsmustern.
keywords: kernel/private/classes/clusterfilehandlers, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in kernel/private/classes/clusterfilehandlers

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Jede Klasse, Schnittstelle und jeder Trait von `kernel/private/classes/clusterfilehandlers` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (14)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `eZClusterEventListener` | interface | [Schnittstelle (Vertrag)](schnittstelle.md), [Beobachter / Listener / Ereignis](beobachter.md) | `kernel/private/classes/clusterfilehandlers/interfaces/ezclustereventlistener.php:14` | Schnittstelle, die jeder Cluster-Ereignis-Listener implementieren muss |
| `eZClusterEventLogger` | interface | [Schnittstelle (Vertrag)](schnittstelle.md) | `kernel/private/classes/clusterfilehandlers/interfaces/ezclustereventlogger.php:20` | Schnittstelle für eZClusterEvent-Logger. |
| `eZClusterEventLoggerEzdebug` | class implements eZClusterEventLogger | – | `kernel/private/classes/clusterfilehandlers/ezclustereventloggerezdebug.php:15` | Logger, der eZDebugSetting verwendet. |
| `eZClusterEventLoggerPhp` | class implements eZClusterEventLogger | – | `kernel/private/classes/clusterfilehandlers/ezclustereventloggerphp.php:14` | Logger, der das PHP-Log verwendet |
| `eZClusterEventNotifier` | interface | [Schnittstelle (Vertrag)](schnittstelle.md) | `kernel/private/classes/clusterfilehandlers/interfaces/ezclustereventnotifier.php:15` | Schnittstelle, die Cluster-Backends implementieren müssen, die Ereignisbenachrichtigungen unterstützen. |
| `eZDFSFileHandler` | class implements eZClusterFileHandlerInterface, ezpDatabaseBasedClusterFileHandler | [Strategie / Handler](strategie-handler.md) | `kernel/private/classes/clusterfilehandlers/ezdfsfilehandler.php:27` | Verarbeitet Dateioperationen für verteilte Dateisysteme (z. B. |
| `eZDFSFileHandlerBackendException` | class extends Exception | [Ausnahmehierarchie](ausnahme.md) | `kernel/private/classes/clusterfilehandlers/dfsbackends/ezdfsfilehandlerbackendexception.php:10` | Diese Datei ist Teil des Exponential-Pakets |
| `eZDFSFileHandlerBackendFactory` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/private/classes/clusterfilehandlers/ezdfsfilehandlerbackendfactory.php:14` | Instanziiert das DFSBackend anhand der INI-Einstellungen |
| `eZDFSFileHandlerDFSBackend` | class implements eZDFSFileHandlerDFSBackendInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `kernel/private/classes/clusterfilehandlers/dfsbackends/dfs.php:12` | – |
| `eZDFSFileHandlerDFSBackendFactoryInterface` | interface | [Fabrik und statische Fabrikmethoden](fabrik.md), [Schnittstelle (Vertrag)](schnittstelle.md) | `kernel/private/classes/clusterfilehandlers/dfsbackends/ezdfsfilehandlerdfsbackendfactoryinterface.php:14` | Macht einen DFSBackend-Handler über die statische Methode build instanziierbar. |
| `eZDFSFileHandlerDFSBackendFilterIterator` | class extends FilterIterator | [Dekorierer / Filter](dekorierer.md) | `kernel/private/classes/clusterfilehandlers/dfsbackends/dfs_filter_iterator.php:15` | Ein Filter-Iterator, der von eZDFSFileHandlerDFSBackend verwendet wird. |
| `eZDFSFileHandlerDFSBackendInterface` | interface | [Schnittstelle (Vertrag)](schnittstelle.md) | `kernel/private/classes/clusterfilehandlers/dfsbackends/ezdfsfilehandlerdfsbackendinterface.php:17` | Schnittstelle für einen DFS-FS-Handler, der CRUD-Unterstützung für Binärdateien bietet. |
| `eZDFSFileHandlerMySQLiBackend` | class implements eZClusterEventNotifier | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `kernel/private/classes/clusterfilehandlers/dfsbackends/mysqli.php:50` | – |
| `eZDFSMySQLBackendError` | class | – | `kernel/private/classes/clusterfilehandlers/dfsbackends/mysqlbackenderror.php:17` | – |
