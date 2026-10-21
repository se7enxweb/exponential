---
title: Muster in extension/expchangeclass
audience: developer
category: standards
order: 411
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/expchangeclass mit den Entwurfsmustern.
keywords: extension/expchangeclass, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/expchangeclass

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/expchangeclass` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (7)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `conversionFunctions` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/expchangeclass/classes/functions.php:39` | – |
| `expChangeClassConverters` | class | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/expchangeclass/classes/converters.php:21` | – |
| `expChangeClassException` | class extends Exception | [Ausnahmehierarchie](ausnahme.md) | `extension/expchangeclass/classes/functions.php:35` | Wird ausgelöst, wenn eine Konvertierung nicht abgeschlossen werden kann. |
| `expchangeclassInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/expchangeclass/ezinfo.php:14` | – |
| `Exponential\View\Extension\Expchangeclass\Changeclass\Action` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/expchangeclass/classes/runnable/views/changeclass/action.php:43` | Der Code von extension/expchangeclass/modules/changeclass/action.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Expchangeclass\Changeclass\MapAttributes` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/expchangeclass/classes/runnable/views/changeclass/map_attributes.php:10` | Der Code von extension/expchangeclass/modules/changeclass/map_attributes.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Expchangeclass\Changeclass\SelectClass` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/expchangeclass/classes/runnable/views/changeclass/select_class.php:10` | Der Code von extension/expchangeclass/modules/changeclass/select_class.php, in eine Klasse verschoben (#207 Stufe 1). |

## Modulansichten (3)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/changeclass/select_class` | `select_class.php` | convert | `extension/expchangeclass/modules/changeclass/module.php:34` |
| `/changeclass/map_attributes` | `map_attributes.php` | convert | `extension/expchangeclass/modules/changeclass/module.php:38` |
| `/changeclass/action` | `action.php` | convert | `extension/expchangeclass/modules/changeclass/module.php:42` |

## Richtlinienfunktionen (1)

| Funktion | Einschränkungen | Definiert in |
| --- | --- | --- |
| `changeclass/convert` | – | `extension/expchangeclass/modules/changeclass/module.php:31` |

## Fetch-Funktionen (1)

| Fetch | Implementiert von | Definiert in |
| --- | --- | --- |
| `fetch( 'changeclass', 'simple_conversion' )` | `conversionFunctions::fetchSimpleConversionArray()` | `extension/expchangeclass/modules/changeclass/function_definition.php:30` |

## INI-Plugin-Punkte (3)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[ezstring] Class` | `expChangeClassConverters` | `extension/expchangeclass/settings/changeclass.ini.append.php:39` |
| `[eztext] Class` | `expChangeClassConverters` | `extension/expchangeclass/settings/changeclass.ini.append.php:48` |
| `[ezobjectrelationlist] Class` | `expChangeClassConverters` | `extension/expchangeclass/settings/changeclass.ini.append.php:55` |
