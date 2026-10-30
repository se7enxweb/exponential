---
title: Muster in extension/explayouts_content_browser_core
audience: developer
category: standards
order: 418
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/explayouts_content_browser_core mit den Entwurfsmustern.
keywords: extension/explayouts_content_browser_core, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/explayouts_content_browser_core

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/explayouts_content_browser_core` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (3)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expLayoutsContentBrowserCoreBackend` | class | [Strategie / Handler](strategie-handler.md) | `extension/explayouts_content_browser_core/classes/explayoutscontentbrowsercorebackend.php:2` | – |
| `expLayoutsContentBrowserCoreSearchFilter` | class | [Dekorierer / Filter](dekorierer.md), [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `extension/explayouts_content_browser_core/classes/explayoutscontentbrowsercoresearchfilter.php:10` | Der erweiterte Attributfilter der Suche des Content-Browsers (extendedattributefilter.ini [explayouts_content_browser_search]): jedes Wort muss in… |
| `explayouts_content_browser_coreInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/explayouts_content_browser_core/ezinfo.php:12` | – |

## INI-Plugin-Punkte (1)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[explayouts_content_browser_search] ClassName` | `expLayoutsContentBrowserCoreSearchFilter` | `extension/explayouts_content_browser_core/settings/extendedattributefilter.ini.append.php:7` |
