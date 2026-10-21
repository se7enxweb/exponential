---
title: Muster in lib/ezmath
audience: developer
category: standards
order: 535
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von lib/ezmath mit den Entwurfsmustern.
keywords: lib/ezmath, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in lib/ezmath

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Jede Klasse, Schnittstelle und jeder Trait von `lib/ezmath` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (2)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `eZBCMath` | class extends eZPHPMath | – | `lib/ezmath/classes/mathhandlers/ezbcmath.php:17` | – |
| `eZPHPMath` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `lib/ezmath/classes/mathhandlers/ezphpmath.php:17` | – |
