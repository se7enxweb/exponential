---
title: Muster in extension/explayouts_content_browser_core
audience: developer
category: standards
order: 415
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/explayouts_content_browser_core mit den Entwurfsmustern.
keywords: extension/explayouts_content_browser_core, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/explayouts_content_browser_core

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Jede Klasse, Schnittstelle und jeder Trait von `extension/explayouts_content_browser_core` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (2)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expLayoutsContentBrowserCoreBackend` | class | [Strategie / Handler](strategie-handler.md) | `extension/explayouts_content_browser_core/classes/explayoutscontentbrowsercorebackend.php:2` | – |
| `explayouts_content_browser_coreInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/explayouts_content_browser_core/ezinfo.php:12` | – |
