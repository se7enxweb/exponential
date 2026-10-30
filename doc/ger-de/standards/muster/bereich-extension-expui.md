---
title: Muster in extension/expui
audience: developer
category: standards
order: 435
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/expui mit den Entwurfsmustern.
keywords: extension/expui, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/expui

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/expui` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (4)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `Exponential\View\Extension\Expui\Expui\Test` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/expui/classes/runnable/views/expui/test.php:23` | Der Code von extension/expui/modules/expui/test.php, in eine Klasse verschoben (#207 Stufe 1). |
| `expuiInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/expui/ezinfo.php:7` | – |
| `expUIServerFunctions` | class | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/expui/classes/expuiserverfunctions.php:30` | – |
| `expUITemplateOperators` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `extension/expui/classes/expuitemplateoperators.php:17` | – |

## Template-Operatoren (1)

| Operatoren | Klasse | Registriert in |
| --- | --- | --- |
| exp_config | `expUITemplateOperators` | `extension/expui/autoloads/eztemplateautoload.php:8` |

## INI-Plugin-Punkte (1)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[ezjscServer_exp] Class` | `expUIServerFunctions` | `extension/expui/settings/ezjscore.ini.append.php:5` |
