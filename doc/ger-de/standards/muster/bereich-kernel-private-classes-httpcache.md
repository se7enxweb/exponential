---
title: Muster in kernel/private/classes/httpcache
audience: developer
category: standards
order: 520
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von kernel/private/classes/httpcache mit den Entwurfsmustern.
keywords: kernel/private/classes/httpcache, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in kernel/private/classes/httpcache

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Jede Klasse, Schnittstelle und jeder Trait von `kernel/private/classes/httpcache` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (2)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `ezpHttpCacheContract` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/private/classes/httpcache/ezphttpcachecontract.php:38` | Der rollenbewusste HTTP-Cache-Vertrag: Schlüssel, Kontext-Hashes, die Eintrags- und Datensatzformate, Tag-Validierung und Platzhalterersetzung. |
| `ezpHttpCacheListener` | class | [Beobachter / Listener / Ereignis](beobachter.md), [Registry](registry.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/private/classes/httpcache/ezphttpcachelistener.php:27` | Die Kernel-Seite des rollenbewussten HTTP-Caches: speichert gerenderte Inhaltsansichten, damit der frühe Ausstieg und der Elternprozess von Velocity sie ausliefern, und macht aus den… |

## Ereignis-Hooks (1)

| Ereignis | Art | Aufgerufen in |
| --- | --- | --- |
| `httpcache/store/headers` | filter | `kernel/private/classes/httpcache/ezphttpcachelistener.php:351` |
