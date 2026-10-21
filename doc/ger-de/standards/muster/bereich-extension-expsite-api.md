---
title: Muster in extension/expsite_api
audience: developer
category: standards
order: 426
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/expsite_api mit den Entwurfsmustern.
keywords: extension/expsite_api, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/expsite_api

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/expsite_api` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (7)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expSiteApi` | class | [Fassade / Dienst](fassade.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/expsite_api/classes/expsiteapi.php:2` | – |
| `expSiteApiContent` | class | – | `extension/expsite_api/classes/expsiteapicontent.php:2` | – |
| `expSiteApiContentService` | class | [Fassade / Dienst](fassade.md) | `extension/expsite_api/classes/expsiteapicontentservice.php:2` | – |
| `expSiteApiFilterService` | class | [Dekorierer / Filter](dekorierer.md), [Fassade / Dienst](fassade.md) | `extension/expsite_api/classes/expsiteapifilterservice.php:2` | – |
| `expSiteApiLocation` | class | – | `extension/expsite_api/classes/expsiteapilocation.php:2` | – |
| `expSiteApiLocationService` | class | [Fassade / Dienst](fassade.md) | `extension/expsite_api/classes/expsiteapilocationservice.php:2` | – |
| `expsite_apiInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/expsite_api/ezinfo.php:12` | – |
