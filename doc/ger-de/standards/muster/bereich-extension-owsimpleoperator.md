---
title: Muster in extension/owsimpleoperator
audience: developer
category: standards
order: 470
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/owsimpleoperator mit den Entwurfsmustern.
keywords: extension/owsimpleoperator, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/owsimpleoperator

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/owsimpleoperator` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (5)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `ExampleOperator` | class extends OWSimpleOperator | – | `extension/owsimpleoperator/autoloads/exampleoperator.php:3` | – |
| `OWEzpAutoloader` | class extends ezpAutoloader | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/owsimpleoperator/autoloads/owezpautoloader.php:6` | – |
| `OWSimpleOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `extension/owsimpleoperator/autoloads/owsimpleoperator.php:21` | – |
| `owSimpleOperatorInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/owsimpleoperator/ezinfo.php:11` | – |
| `PHPFunctionOperator` | class extends OWSimpleOperator | – | `extension/owsimpleoperator/autoloads/phpfunctionoperator.php:3` | – |
