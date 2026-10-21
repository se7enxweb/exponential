---
title: Muster in extension/explayouts_content_browser
audience: developer
category: standards
order: 414
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/explayouts_content_browser mit den Entwurfsmustern.
keywords: extension/explayouts_content_browser, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/explayouts_content_browser

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/explayouts_content_browser` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (4)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expLayoutsContentBrowser` | class | – | `extension/explayouts_content_browser/classes/explayoutscontentbrowser.php:2` | – |
| `expLayoutsContentBrowserItem` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/explayouts_content_browser/classes/explayoutscontentbrowseritem.php:2` | – |
| `expLayoutsContentBrowserProvider` | class | [Strategie / Handler](strategie-handler.md) | `extension/explayouts_content_browser/classes/explayoutscontentbrowserprovider.php:3` | – |
| `explayouts_content_browserInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/explayouts_content_browser/ezinfo.php:12` | – |
