---
title: Muster in extension/explayouts_api
audience: developer
category: standards
order: 413
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/explayouts_api mit den Entwurfsmustern.
keywords: extension/explayouts_api, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/explayouts_api

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/explayouts_api` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (10)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expLayoutsEzContentContextProvider` | class | [Strategie / Handler](strategie-handler.md) | `extension/explayouts_api/classes/explayoutsezcontentcontextprovider.php:2` | – |
| `expLayoutsEzContentProvider` | class | [Strategie / Handler](strategie-handler.md) | `extension/explayouts_api/classes/explayoutsezcontentprovider.php:2` | – |
| `expLayoutsEzContentTypeMapper` | class | – | `extension/explayouts_api/classes/explayoutsezcontenttypemapper.php:2` | – |
| `expLayoutsEzContentValidator` | class | – | `extension/explayouts_api/classes/explayoutsezcontentvalidator.php:2` | – |
| `expLayoutsEzRemoteIdConverter` | class | [Strategie / Handler](strategie-handler.md) | `extension/explayouts_api/classes/explayoutsezremoteidconverter.php:2` | – |
| `expLayoutsEzSectionMapper` | class | – | `extension/explayouts_api/classes/explayoutsezsectionmapper.php:2` | – |
| `expLayoutsEzValueConverter` | class | [Strategie / Handler](strategie-handler.md) | `extension/explayouts_api/classes/explayoutsezvalueconverter.php:2` | – |
| `expLayoutsEzValueLoader` | class | – | `extension/explayouts_api/classes/explayoutsezvalueloader.php:2` | – |
| `expLayoutsEzValueUrlGenerator` | class | [Builder](builder.md) | `extension/explayouts_api/classes/explayoutsezvalueurlgenerator.php:2` | – |
| `explayouts_apiInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/explayouts_api/ezinfo.php:12` | – |
