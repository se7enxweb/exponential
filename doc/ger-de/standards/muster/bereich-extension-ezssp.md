---
title: Muster in extension/ezssp
audience: developer
category: standards
order: 454
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/ezssp mit den Entwurfsmustern.
keywords: extension/ezssp, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/ezssp

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/ezssp` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (2)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `ezsspInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/ezssp/ezinfo.php:2` | – |
| `SubtreeSkeletonPublishType` | class extends eZWorkflowEventType | [Workflow-Ereignistyp (Befehl, Plugin)](workflow-ereignis.md) | `extension/ezssp/eventtypes/event/subtreeskeletonpublish/subtreeskeletonpublishtype.php:27` | – |
