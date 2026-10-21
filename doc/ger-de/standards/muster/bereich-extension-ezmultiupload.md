---
title: Muster in extension/ezmultiupload
audience: developer
category: standards
order: 445
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/ezmultiupload mit den Entwurfsmustern.
keywords: extension/ezmultiupload, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/ezmultiupload

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Jede Klasse, Schnittstelle und jeder Trait von `extension/ezmultiupload` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (8)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expMultiuploadFunctionCollection` | class | [Fetch-Funktion (Query Object, Fassade)](fetch-funktion.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/ezmultiupload/classes/expmultiuploadfunctioncollection.php:10` | Die Template-Fetch-Funktionen von ezmultiupload (modules/ezmultiupload/function_definition.php). |
| `expMultiuploadService` | class | [Fassade / Dienst](fassade.md), [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/ezmultiupload/classes/expmultiuploadservice.php:20` | Die Serverseite der Upload-Seite: Prüfungen vor einem Upload (der Zielort, die Inhaltsklasse, zu der jede Datei wird, die Berechtigung zum Anlegen dafür… |
| `expMultiuploadZipImporter` | class | – | `extension/ezmultiupload/classes/expmultiuploadzipimporter.php:29` | Das ZIP-Import-Backend: inspect() listet auf, was ein Archiv enthält, extract() schreibt die gewählten Dateien in ein privates Verzeichnis, cleanup() entfernt dieses Verzeic… |
| `Exponential\View\Extension\Ezmultiupload\Ezmultiupload\Service` | class extends ModuleView | [Fassade / Dienst](fassade.md), [Modulansicht (Page Controller)](modulansicht.md) | `extension/ezmultiupload/classes/runnable/views/ezmultiupload/service.php:23` | ezmultiupload/service: die Prüfungen und Aktionen der Upload-Seite, als JSON beantwortet. |
| `Exponential\View\Extension\Ezmultiupload\Ezmultiupload\Upload` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/ezmultiupload/classes/runnable/views/ezmultiupload/upload.php:23` | Der Code von extension/ezmultiupload/modules/ezmultiupload/upload.php, in eine Klasse verschoben (#207 Stufe 1). |
| `eZMultiuploadHandler` | class | [Strategie / Handler](strategie-handler.md) | `extension/ezmultiupload/classes/ezmultiuploadhandler.php:16` | Klasse eZMultiuploadHandler |
| `eZMultiuploadHandlerInterface` | interface | [Schnittstelle (Vertrag)](schnittstelle.md) | `extension/ezmultiupload/classes/ezmultiuploadhandlerinterface.php:12` | – |
| `ezmultiuploadInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/ezmultiupload/ezinfo.php:7` | – |

## Modulansichten (2)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/ezmultiupload/upload` | `upload.php` | – | `extension/ezmultiupload/modules/ezmultiupload/module.php:15` |
| `/ezmultiupload/service` | `service.php` | – | `extension/ezmultiupload/modules/ezmultiupload/module.php:20` |

## Fetch-Funktionen (3)

| Fetch | Implementiert von | Definiert in |
| --- | --- | --- |
| `fetch( 'ezmultiupload', 'can_upload' )` | `expMultiuploadFunctionCollection::fetchCanUpload()` | `extension/ezmultiupload/modules/ezmultiupload/function_definition.php:26` |
| `fetch( 'ezmultiupload', 'dashboard_target' )` | `expMultiuploadFunctionCollection::fetchDashboardTarget()` | `extension/ezmultiupload/modules/ezmultiupload/function_definition.php:36` |
| `fetch( 'ezmultiupload', 'quick_locations' )` | `expMultiuploadFunctionCollection::fetchQuickLocations()` | `extension/ezmultiupload/modules/ezmultiupload/function_definition.php:44` |
