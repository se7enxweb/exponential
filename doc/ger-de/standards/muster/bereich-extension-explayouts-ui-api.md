---
title: Muster in extension/explayouts_ui_api
audience: developer
category: standards
order: 423
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/explayouts_ui_api mit den Entwurfsmustern.
keywords: extension/explayouts_ui_api, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/explayouts_ui_api

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Jede Klasse, Schnittstelle und jeder Trait von `extension/explayouts_ui_api` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (6)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expLayoutsUIApplicationApi` | class | [Fassade / Dienst](fassade.md), [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts_ui_api/classes/explayoutsuiapplicationapi.php:2` | – |
| `explayouts_ui_apiInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/explayouts_ui_api/ezinfo.php:12` | – |
| `Exponential\View\Extension\ExplayoutsUiApi\ExplayoutsUiApi\App` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/explayouts_ui_api/classes/runnable/views/explayouts_ui_api/app.php:10` | Der Code von extension/explayouts_ui_api/modules/explayouts_ui_api/app.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\ExplayoutsUiApi\ExplayoutsUiApi\Blocks` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/explayouts_ui_api/classes/runnable/views/explayouts_ui_api/blocks.php:38` | Der Code von extension/explayouts_ui_api/modules/explayouts_ui_api/blocks.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\ExplayoutsUiApi\ExplayoutsUiApi\Layouts` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/explayouts_ui_api/classes/runnable/views/explayouts_ui_api/layouts.php:31` | Der Code von extension/explayouts_ui_api/modules/explayouts_ui_api/layouts.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\ExplayoutsUiApi\ExplayoutsUiApi\Rules` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/explayouts_ui_api/classes/runnable/views/explayouts_ui_api/rules.php:50` | Der Code von extension/explayouts_ui_api/modules/explayouts_ui_api/rules.php, in eine Klasse verschoben (#207 Stufe 1). |

## Modulansichten (4)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/explayouts_ui_api/layouts` | `layouts.php` | read | `extension/explayouts_ui_api/modules/explayouts_ui_api/module.php:7` |
| `/explayouts_ui_api/rules` | `rules.php` | read | `extension/explayouts_ui_api/modules/explayouts_ui_api/module.php:14` |
| `/explayouts_ui_api/blocks` | `blocks.php` | read | `extension/explayouts_ui_api/modules/explayouts_ui_api/module.php:21` |
| `/explayouts_ui_api/app` | `app.php` | read | `extension/explayouts_ui_api/modules/explayouts_ui_api/module.php:28` |

## Richtlinienfunktionen (2)

| Funktion | Einschränkungen | Definiert in |
| --- | --- | --- |
| `explayouts_ui_api/read` | – | `extension/explayouts_ui_api/modules/explayouts_ui_api/module.php:36` |
| `explayouts_ui_api/edit` | – | `extension/explayouts_ui_api/modules/explayouts_ui_api/module.php:39` |
