---
title: Muster in extension/explayouts_site_api
audience: developer
category: standards
order: 419
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/explayouts_site_api mit den Entwurfsmustern.
keywords: extension/explayouts_site_api, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/explayouts_site_api

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Jede Klasse, Schnittstelle und jeder Trait von `extension/explayouts_site_api` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (6)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expLayoutsSiteApiItemValueLoader` | class | – | `extension/explayouts_site_api/classes/explayoutssiteapiitemvalueloader.php:2` | – |
| `expLayoutsSiteApiItemValueUrlGenerator` | class | [Builder](builder.md) | `extension/explayouts_site_api/classes/explayoutssiteapiitemvalueurlgenerator.php:2` | – |
| `expLayoutsSiteApiValueConverter` | class | [Strategie / Handler](strategie-handler.md) | `extension/explayouts_site_api/classes/explayoutssiteapivalueconverter.php:2` | – |
| `expLayoutsSiteApiValueObjectProviderContent` | class | – | `extension/explayouts_site_api/classes/explayoutssiteapivalueobjectprovidercontent.php:2` | – |
| `expLayoutsSiteApiValueObjectProviderLocation` | class | – | `extension/explayouts_site_api/classes/explayoutssiteapivalueobjectproviderlocation.php:2` | – |
| `explayouts_site_apiInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/explayouts_site_api/ezinfo.php:12` | – |
