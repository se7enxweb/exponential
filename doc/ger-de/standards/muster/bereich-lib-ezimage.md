---
title: Muster in lib/ezimage
audience: developer
category: standards
order: 546
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von lib/ezimage mit den Entwurfsmustern.
keywords: lib/ezimage, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in lib/ezimage

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `lib/ezimage` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (16)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expSvgSanitizer` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `lib/ezimage/classes/expsvgsanitizer.php:51` | Macht ein SVG-Bild per Erlaubnisliste sicher, damit es vom eigenen Ursprung der Site gespeichert und ausgeliefert werden kann. |
| `eZEXIFImageAnalyzer` | class | – | `lib/ezimage/classes/ezexifimageanalyzer.php:19` | – |
| `eZGIFImageAnalyzer` | class | – | `lib/ezimage/classes/ezgifimageanalyzer.php:19` | – |
| `eZImageAnalyzer` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `lib/ezimage/classes/ezimageanalyzer.php:23` | – |
| `eZImageFactory` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `lib/ezimage/classes/ezimagefactory.php:21` | – |
| `eZImageFont` | class | – | `lib/ezimage/classes/ezimagefont.php:30` | – |
| `eZImageGDFactory` | class extends eZImageFactory | [Fabrik und statische Fabrikmethoden](fabrik.md), [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `lib/ezimage/classes/ezimagegdfactory.php:12` | – |
| `eZImageGDHandler` | class extends eZImageHandler | [Fabrik und statische Fabrikmethoden](fabrik.md), [Strategie / Handler](strategie-handler.md) | `lib/ezimage/classes/ezimagegdhandler.php:24` | – |
| `eZImageHandler` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Strategie / Handler](strategie-handler.md) | `lib/ezimage/classes/ezimagehandler.php:19` | – |
| `eZImageInterface` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Registry](registry.md) | `lib/ezimage/classes/ezimageinterface.php:20` | – |
| `eZImageLayer` | class extends eZImageInterface | [Fabrik und statische Fabrikmethoden](fabrik.md) | `lib/ezimage/classes/ezimagelayer.php:19` | – |
| `eZImageManager` | class | [Fassade / Dienst](fassade.md), [Fabrik und statische Fabrikmethoden](fabrik.md), [Singleton / instance()](singleton.md) | `lib/ezimage/classes/ezimagemanager.php:85` | – |
| `eZImageObject` | class extends eZImageInterface | – | `lib/ezimage/classes/ezimageobject.php:19` | – |
| `eZImageShellFactory` | class extends eZImageFactory | [Fabrik und statische Fabrikmethoden](fabrik.md), [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `lib/ezimage/classes/ezimageshellfactory.php:12` | – |
| `eZImageShellHandler` | class extends eZImageHandler | [Fabrik und statische Fabrikmethoden](fabrik.md), [Strategie / Handler](strategie-handler.md) | `lib/ezimage/classes/ezimageshellhandler.php:19` | – |
| `eZImageTextLayer` | class extends eZImageLayer | [Fabrik und statische Fabrikmethoden](fabrik.md) | `lib/ezimage/classes/ezimagetextlayer.php:19` | – |

## Ereignis-Hooks (1)

| Ereignis | Art | Aufgerufen in |
| --- | --- | --- |
| `image/alias` | notify | `lib/ezimage/classes/ezimagemanager.php:989` |
