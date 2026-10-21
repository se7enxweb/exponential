---
title: Muster in extension/bcwebsitestatistics
audience: developer
category: standards
order: 404
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/bcwebsitestatistics mit den Entwurfsmustern.
keywords: extension/bcwebsitestatistics, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/bcwebsitestatistics

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/bcwebsitestatistics` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (4)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `BCUsingOWMultisiteIni` | class | – | `extension/bcwebsitestatistics/autoloads/bcusingowmultisiteini.php:4` | – |
| `bcwebsitestatisticsInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/bcwebsitestatistics/ezinfo.php:3` | – |
| `BCWebsiteStatisticsOperators` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `extension/bcwebsitestatistics/autoloads/bcwebsitestatisticsoperators.php:30` | – |
| `eZReceiptType` | class extends eZWorkflowEventType | [Workflow-Ereignistyp (Befehl, Plugin)](workflow-ereignis.md) | `extension/bcwebsitestatistics/workflowtypes/event/ezreceipt/ezreceipttype.php:38` | – |

## Template-Operatoren (1)

| Operatoren | Klasse | Registriert in |
| --- | --- | --- |
| bc_ga_urchin, bc_ga_urchinOrder, bc_ga_xmlAttributeValue, bc_ga_jsEscapedString, bc_ga_formatNumericDecimal, bc_ga_script_url | `BCWebsiteStatisticsOperators` | `extension/bcwebsitestatistics/autoloads/eztemplateautoload.php:34` |
