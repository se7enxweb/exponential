---
title: Muster in kernel/classes/contentfinder
audience: developer
category: standards
order: 488
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von kernel/classes/contentfinder mit den Entwurfsmustern.
keywords: kernel/classes/contentfinder, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in kernel/classes/contentfinder

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Jede Klasse, Schnittstelle und jeder Trait von `kernel/classes/contentfinder` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (3)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expContentFinder` | class | – | `kernel/classes/contentfinder/expcontentfinder.php:28` | Der Inhaltsfinder (Modul contentfinder, doc/eng-us/features/6.0/content-finder.md): findet Inhaltsobjekte über eine ID oder einen Pfad und beschreibt jedes mit dem, was… |
| `expContentFinderAction` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/contentfinder/expcontentfinderaction.php:33` | Die Aktionen des Content-Finders (doc/eng-us/features/6.0/content-finder.md, "Actions"): ein Objekt in den Papierkorb verschieben, es endgültig löschen, es entfernen aus… |
| `expContentFinderInput` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/contentfinder/expcontentfinderinput.php:28` | Was der Content Finder (Modul contentfinder, doc/eng-us/features/6.0/content-finder.md) aus seinem einen Suchfeld macht: die Arten von ID oder Pfad, die der Text ent… |
