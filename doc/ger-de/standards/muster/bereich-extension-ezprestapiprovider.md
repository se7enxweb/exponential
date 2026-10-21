---
title: Muster in extension/ezprestapiprovider
audience: developer
category: standards
order: 453
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/ezprestapiprovider mit den Entwurfsmustern.
keywords: extension/ezprestapiprovider, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/ezprestapiprovider

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Jede Klasse, Schnittstelle und jeder Trait von `extension/ezprestapiprovider` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (2)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `ezpRestApiProvider` | class extends ezp7xRestApiProvider | [Strategie / Handler](strategie-handler.md) | `extension/ezprestapiprovider/classes/rest_provider.php:19` | Veraltet: der Inhaltsanbieter der REST-API v1 liegt jetzt in der Extension ezprestapi, die die Lesezugriffe unter v1 und v2 und die Schreibzugriffe unter v2 bedient, aus… |
| `ezpRestContentController` | class extends ezp7xRestContentController | – | `extension/ezprestapiprovider/classes/controllers/content.php:19` | Veraltet: Der Content-Controller von REST API v1 liegt jetzt in der Extension ezprestapi (ezp7xRestContentController), die dieselben v1-Lesezugriffe beantwortet… |

## INI-Plugin-Punkte (2)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[ApiProvider] ProviderClass[ezp]` | `ezp7xRestApiProvider` | `extension/ezprestapiprovider/settings/rest.ini.append.php:7` |
| `[ApiProvider] ProviderClass[ezpl]` | `ezp7xRestApiProvider` | `extension/ezprestapiprovider/settings/rest.ini.append.php:8` |
