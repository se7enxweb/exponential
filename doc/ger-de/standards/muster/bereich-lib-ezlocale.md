---
title: Muster in lib/ezlocale
audience: developer
category: standards
order: 534
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von lib/ezlocale mit den Entwurfsmustern.
keywords: lib/ezlocale, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in lib/ezlocale

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Jede Klasse, Schnittstelle und jeder Trait von `lib/ezlocale` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (6)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `eZCurrency` | class | – | `lib/ezlocale/classes/ezcurrency.php:33` | – |
| `eZDate` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `lib/ezlocale/classes/ezdate.php:58` | – |
| `eZDateTime` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `lib/ezlocale/classes/ezdatetime.php:62` | – |
| `eZDateUtils` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `lib/ezlocale/classes/ezdateutils.php:18` | – |
| `eZLocale` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Singleton / instance()](singleton.md) | `lib/ezlocale/classes/ezlocale.php:111` | – |
| `eZTime` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `lib/ezlocale/classes/eztime.php:59` | – |
