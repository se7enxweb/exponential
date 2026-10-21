---
title: Muster in lib/ezfile
audience: developer
category: standards
order: 531
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von lib/ezfile mit den Entwurfsmustern.
keywords: lib/ezfile, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in lib/ezfile

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Jede Klasse, Schnittstelle und jeder Trait von `lib/ezfile` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (13)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expLogRotator` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `lib/ezfile/classes/explogrotator.php:28` | Die einzige Implementierung der Log-Rotation im Kernel (doc/eng-us/features/6.0/log-rotation.md). |
| `eZBZIP2Handler` | class extends eZCompressionHandler | [Strategie / Handler](strategie-handler.md) | `lib/ezfile/classes/ezbzip2compressionhandler.php:20` | – |
| `eZCompressionHandler` | class extends eZFileHandler | [Strategie / Handler](strategie-handler.md) | `lib/ezfile/classes/ezcompressionhandler.php:34` | – |
| `eZDir` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `lib/ezfile/classes/ezdir.php:17` | – |
| `eZFile` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `lib/ezfile/classes/ezfile.php:18` | – |
| `eZFileHandler` | class | [Singleton / instance()](singleton.md), [Strategie / Handler](strategie-handler.md) | `lib/ezfile/classes/ezfilehandler.php:56` | – |
| `eZForwardCompressionHandler` | class extends eZCompressionHandler | [Strategie / Handler](strategie-handler.md) | `lib/ezfile/classes/ezforwardcompressionhandler.php:20` | – |
| `eZGZIPCompressionHandler` | class extends eZForwardCompressionHandler | [Strategie / Handler](strategie-handler.md) | `lib/ezfile/classes/ezgzipcompressionhandler.php:22` | – |
| `eZGZIPShellCompressionHandler` | class extends eZCompressionHandler | [Strategie / Handler](strategie-handler.md) | `lib/ezfile/classes/ezgzipshellcompressionhandler.php:22` | – |
| `eZGZIPZLIBCompressionHandler` | class extends eZCompressionHandler | [Strategie / Handler](strategie-handler.md) | `lib/ezfile/classes/ezgzipzlibcompressionhandler.php:20` | – |
| `eZLog` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `lib/ezfile/classes/ezlog.php:21` | – |
| `eZMD5` | class | – | `lib/ezfile/classes/ezmd5.php:17` | – |
| `eZNoCompressionHandler` | class extends eZCompressionHandler | [Strategie / Handler](strategie-handler.md) | `lib/ezfile/classes/eznocompressionhandler.php:18` | – |
