---
title: Muster in extension/ezauthorize
audience: developer
category: standards
order: 439
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/ezauthorize mit den Entwurfsmustern.
keywords: extension/ezauthorize, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/ezauthorize

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/ezauthorize` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (5)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `eZAuthorizeAIM` | class | – | `extension/ezauthorize/classes/ezauthorizeaim.php:32` | – |
| `eZAuthorizeGateway` | class extends eZCurlGateway | – | `extension/ezauthorize/classes/ezauthorizegateway.php:40` | – |
| `eZAuthorizeInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/ezauthorize/classes/ezauthorizeinfo.php:11` | – |
| `eZCurlGateway` | class extends eZPaymentGateway | – | `extension/ezauthorize/classes/ezcurlgateway.php:50` | – |
| `eZCustomShopAccountHandler` | class | [Strategie / Handler](strategie-handler.md) | `extension/ezauthorize/shopaccounthandlers/ezcustomshopaccounthandler.php:5` | – |

## Modulansichten (2)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/refund/refund` | `order.php` | – | `extension/ezauthorize/modules/refund/module.php:27` |
| `/refund/refund` | `order.php` | refund | `extension/ezauthorize/modules/refund/module.php:35` |
