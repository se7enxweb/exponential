---
title: Muster in extension/powercontent
audience: developer
category: standards
order: 466
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/powercontent mit den Entwurfsmustern.
keywords: extension/powercontent, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/powercontent

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/powercontent` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (3)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `Exponential\View\Extension\Powercontent\Powercontent\Action` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/powercontent/classes/runnable/views/powercontent/action.php:10` | Der Code von extension/powercontent/modules/powercontent/action.php, in eine Klasse verschoben (#207 Stufe 1). |
| `PowerContent` | class | – | `extension/powercontent/classes/powercontent.php:3` | – |
| `powercontentInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/powercontent/ezinfo.php:2` | – |

## Modulansichten (1)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/powercontent/action` | `action.php` | – | `extension/powercontent/modules/powercontent/module.php:6` |
