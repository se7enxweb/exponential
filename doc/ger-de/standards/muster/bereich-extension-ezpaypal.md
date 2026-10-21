---
title: Muster in extension/ezpaypal
audience: developer
category: standards
order: 449
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/ezpaypal mit den Entwurfsmustern.
keywords: extension/ezpaypal, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/ezpaypal

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Jede Klasse, Schnittstelle und jeder Trait von `extension/ezpaypal` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (4)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `Exponential\View\Extension\Ezpaypal\Paypal\NotifyUrl` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/ezpaypal/classes/runnable/views/paypal/notify_url.php:38` | Der Code von extension/ezpaypal/modules/paypal/notify_url.php, in eine Klasse verschoben (#207 Stufe 1). |
| `eZPaypalChecker` | class extends eZPaymentCallbackChecker | – | `extension/ezpaypal/classes/ezpaypalchecker.php:45` | – |
| `eZPaypalGateway` | class extends eZRedirectGateway | – | `extension/ezpaypal/classes/ezpaypalgateway.php:45` | – |
| `ezpaypalInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/ezpaypal/ezinfo.php:2` | – |

## Modulansichten (1)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/paypal/notify_url` | `notify_url.php` | – | `extension/ezpaypal/modules/paypal/module.php:33` |
