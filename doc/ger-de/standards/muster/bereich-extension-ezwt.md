---
title: Muster in extension/ezwt
audience: developer
category: standards
order: 460
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/ezwt mit den Entwurfsmustern.
keywords: extension/ezwt, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/ezwt

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Jede Klasse, Schnittstelle und jeder Trait von `extension/ezwt` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (4)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `Exponential\View\Extension\Ezwt\Websitetoolbar\Sort` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/ezwt/classes/runnable/views/websitetoolbar/sort.php:34` | Der Code von extension/ezwt/modules/websitetoolbar/sort.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Ezwt\Websitetoolbar\Websitetoolbar` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/ezwt/classes/runnable/views/websitetoolbar/websitetoolbar.php:33` | Der Code von extension/ezwt/modules/websitetoolbar/websitetoolbar.php, in eine Klasse verschoben (#207 Stufe 1). |
| `eZCreateClassListGroups` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `extension/ezwt/autoloads/ezcreateclasslistgroups.php:28` | – |
| `ezwtServerCallFunctions` | class | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `extension/ezwt/classes/ezwtservercallfunctions.php:15` | Implementiert Methoden, die per XHR-Aufruf aus der Ferne aufgerufen werden |

## Modulansichten (1)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/websitetoolbar/sort` | `sort.php` | use | `extension/ezwt/modules/websitetoolbar/module.php:35` |

## Richtlinienfunktionen (1)

| Funktion | Einschränkungen | Definiert in |
| --- | --- | --- |
| `websitetoolbar/use` | – | `extension/ezwt/modules/websitetoolbar/module.php:44` |

## Template-Operatoren (1)

| Operatoren | Klasse | Registriert in |
| --- | --- | --- |
| ezcreateclasslistgroups | `eZCreateClassListGroups` | `extension/ezwt/autoloads/eztemplateautoload.php:29` |

## INI-Plugin-Punkte (1)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[ezjscServer_ezwt] Class` | `ezwtServerCallFunctions` | `extension/ezwt/settings/ezjscore.ini.append.php:4` |
