---
title: Muster in extension/enhancedezbinaryfile
audience: developer
category: standards
order: 409
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/enhancedezbinaryfile mit den Entwurfsmustern.
keywords: extension/enhancedezbinaryfile, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/enhancedezbinaryfile

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Jede Klasse, Schnittstelle und jeder Trait von `extension/enhancedezbinaryfile` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (3)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `enhancedezbinaryfileInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/enhancedezbinaryfile/ezinfo.php:40` | – |
| `EnhancedeZBinaryFileType` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md) | `extension/enhancedezbinaryfile/datatypes/enhancedezbinaryfile/enhancedezbinaryfiletype.php:34` | – |
| `TemplateParseXMLOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `extension/enhancedezbinaryfile/autoloads/templateparsexmloperator.php:18` | – |

## Template-Operatoren (1)

| Operatoren | Klasse | Registriert in |
| --- | --- | --- |
| parsexml | `TemplateParseXMLOperator` | `extension/enhancedezbinaryfile/autoloads/eztemplateautoload.php:7` |
