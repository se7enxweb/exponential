---
title: Muster in extension/ezautosave
audience: developer
category: standards
order: 436
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/ezautosave mit den Entwurfsmustern.
keywords: extension/ezautosave, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/ezautosave

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Jede Klasse, Schnittstelle und jeder Trait von `extension/ezautosave` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (1)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `ezjscServerFunctionsAutosave` | class extends ezjscServerFunctions | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/ezautosave/classes/ezjscserverfunctionsautosave.php:16` | Diese Klasse verarbeitet AJAX-Aufrufe für die Funktion zum automatischen Speichern |

## Ereignis-Hooks (1)

| Ereignis | Art | Aufgerufen in |
| --- | --- | --- |
| `content/cache/version` | notify | `extension/ezautosave/classes/ezjscserverfunctionsautosave.php:225` |

## INI-Plugin-Punkte (1)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[ezjscServer_ezautosave] Class` | `ezjscServerFunctionsAutosave` | `extension/ezautosave/settings/ezjscore.ini.append.php:4` |
