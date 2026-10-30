---
title: Muster in extension/ezwebin
audience: developer
category: standards
order: 464
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/ezwebin mit den Entwurfsmustern.
keywords: extension/ezwebin, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/ezwebin

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/ezwebin` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (1)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `ezwebinInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/ezwebin/ezinfo.php:12` | – |

## Template-Operatoren (4)

| Operatoren | Klasse | Registriert in |
| --- | --- | --- |
| ezkeywordlist | `eZKeywordList` | `extension/ezwebin/autoloads/eztemplateautoload.php:30` |
| ezarchive | `eZArchive` | `extension/ezwebin/autoloads/eztemplateautoload.php:33` |
| eztagcloud | `eZTagCloud` | `extension/ezwebin/autoloads/eztemplateautoload.php:36` |
| ezpagedata, ezpagedata_set, ezpagedata_append | `eZPageData` | `extension/ezwebin/autoloads/eztemplateautoload.php:39` |
