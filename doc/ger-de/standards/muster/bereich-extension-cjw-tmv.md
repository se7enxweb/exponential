---
title: Muster in extension/cjw_tmv
audience: developer
category: standards
order: 408
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/cjw_tmv mit den Entwurfsmustern.
keywords: extension/cjw_tmv, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/cjw_tmv

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Jede Klasse, Schnittstelle und jeder Trait von `extension/cjw_tmv` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (7)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `cjwTmvClient` | class | – | `extension/cjw_tmv/classes/cjwtmvclient.php:13` | Der Client der TMV-Veranstaltungsdatenbank (Tourismusverband Mecklenburg-Vorpommern, infomax imxplatform JSON API). |
| `cjwTmvEventsBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/cjw_tmv/classes/cjwtmveventsblockhandler.php:11` | Der Layouts-Block „tmv_events“ (v2: Cjw\TmvBundle TmvEventsHandler): die Veranstaltungen eines tmv_container, mit der Filterleiste (Ansichtstyp Liste), nur d… |
| `cjwTmvFeed` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `extension/cjw_tmv/classes/cjwtmvfeed.php:14` | Die TMV-Ereignisse eines Ereigniscontainers (ein tmv_container-Knoten), gelesen aus den Inhaltsobjekten, die der Importer (cjwTmvImporter, Cronjob-Teil cjw_tmv)… |
| `cjwTmvFunctionCollection` | class | [Fetch-Funktion (Query Object, Fassade)](fetch-funktion.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/cjw_tmv/classes/cjwtmvfunctioncollection.php:10` | Die Fetch-Funktionen des Moduls cjw_tmv (modules/cjw_tmv/function_definition.php). |
| `cjwTmvHtml` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/cjw_tmv/classes/cjwtmvhtml.php:12` | Bereinigt das HTML eines TMV-Veranstaltungstextes, bevor es gecacht und gezeichnet wird: nur Absätze, Zeilenumbrüche, Hervorhebungen, Listen, kleine Überschriften und Links (http, htt… |
| `cjwTmvImporter` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `extension/cjw_tmv/classes/cjwtmvimporter.php:28` | Importiert die Veranstaltungen der TMV-Veranstaltungsdatenbank als Inhaltsobjekte unterhalb ihres tmv_container, wie es die alte Extension cjw_tmv_veranstdb tat (TmvHelper::… |
| `cjw_tmvInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/cjw_tmv/ezinfo.php:7` | – |

## Fetch-Funktionen (4)

| Fetch | Implementiert von | Definiert in |
| --- | --- | --- |
| `fetch( 'cjw_tmv', 'events' )` | `cjwTmvFunctionCollection::events()` | `extension/cjw_tmv/modules/cjw_tmv/function_definition.php:12` |
| `fetch( 'cjw_tmv', 'categories' )` | `cjwTmvFunctionCollection::categories()` | `extension/cjw_tmv/modules/cjw_tmv/function_definition.php:24` |
| `fetch( 'cjw_tmv', 'places' )` | `cjwTmvFunctionCollection::places()` | `extension/cjw_tmv/modules/cjw_tmv/function_definition.php:32` |
| `fetch( 'cjw_tmv', 'filter_params' )` | `cjwTmvFunctionCollection::filterParams()` | `extension/cjw_tmv/modules/cjw_tmv/function_definition.php:40` |

## Cronjob-Teile (1)

| Cronjob | Datei | Beschreibung |
| --- | --- | --- |
| `cjw_tmv_refresh` | `extension/cjw_tmv/cronjobs/cjw_tmv_refresh.php` | Cronjob-Teil cjw_tmv: importiert und aktualisiert die TMV-Veranstaltungen jedes tmv_container als Inhaltsobjekte (cjwTmvImporter) und entfernt die vergangenen. |

## INI-Plugin-Punkte (1)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[BlockDefinition_tmv_events] Handler` | `cjwTmvEventsBlockHandler` | `extension/cjw_tmv/settings/siteaccess/cjw_starter/explayouts.ini.append.php:10` |
