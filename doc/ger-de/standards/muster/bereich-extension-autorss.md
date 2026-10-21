---
title: Muster in extension/autorss
audience: developer
category: standards
order: 401
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/autorss mit den Entwurfsmustern.
keywords: extension/autorss, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/autorss

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/autorss` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (2)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `autorssInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/autorss/ezinfo.php:3` | – |
| `AutoRSSType` | class extends eZWorkflowEventType | [Fabrik und statische Fabrikmethoden](fabrik.md), [Workflow-Ereignistyp (Befehl, Plugin)](workflow-ereignis.md) | `extension/autorss/eventtypes/event/autorss/autorsstype.php:27` | – |
