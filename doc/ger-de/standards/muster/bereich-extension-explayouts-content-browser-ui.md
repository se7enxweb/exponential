---
title: Muster in extension/explayouts_content_browser_ui
audience: developer
category: standards
order: 416
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/explayouts_content_browser_ui mit den Entwurfsmustern.
keywords: extension/explayouts_content_browser_ui, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/explayouts_content_browser_ui

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/explayouts_content_browser_ui` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (2)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `explayouts_content_browser_uiInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/explayouts_content_browser_ui/ezinfo.php:12` | – |
| `Exponential\View\Extension\ExplayoutsContentBrowserUi\ExplayoutsContentBrowserUi\Browser` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/explayouts_content_browser_ui/classes/runnable/views/explayouts_content_browser_ui/browser.php:10` | Der Code von extension/explayouts_content_browser_ui/modules/explayouts_content_browser_ui/browser.php, in eine Klasse verschoben (#207 Stufe 1). |

## Modulansichten (1)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/explayouts_content_browser_ui/browser` | `browser.php` | read | `extension/explayouts_content_browser_ui/modules/explayouts_content_browser_ui/module.php:7` |

## Richtlinienfunktionen (1)

| Funktion | Einschränkungen | Definiert in |
| --- | --- | --- |
| `explayouts_content_browser_ui/read` | – | `extension/explayouts_content_browser_ui/modules/explayouts_content_browser_ui/module.php:15` |
