---
title: Muster in kernel/classes/shopaccounthandlers
audience: developer
category: standards
order: 506
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von kernel/classes/shopaccounthandlers mit den Entwurfsmustern.
keywords: kernel/classes/shopaccounthandlers, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in kernel/classes/shopaccounthandlers

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `kernel/classes/shopaccounthandlers` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (4)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `bcusauserShopAccountHandler` | class extends eZUserShopAccountHandler | [Strategie / Handler](strategie-handler.md) | `kernel/classes/shopaccounthandlers/bcusausershopaccounthandler.php:21` | Shop-Konto-Handler für das US-Adressmodell: die Adresszeile wird als statt gespeichert, und eine Telefonnummer wird erfasst und als gespeichert. |
| `eZDefaultShopAccountHandler` | class | [Strategie / Handler](strategie-handler.md) | `kernel/classes/shopaccounthandlers/ezdefaultshopaccounthandler.php:12` | – |
| `eZSimpleShopAccountHandler` | class | [Strategie / Handler](strategie-handler.md) | `kernel/classes/shopaccounthandlers/ezsimpleshopaccounthandler.php:12` | – |
| `eZUserShopAccountHandler` | class | [Strategie / Handler](strategie-handler.md) | `kernel/classes/shopaccounthandlers/ezusershopaccounthandler.php:12` | – |
