---
title: Muster in extension/ezstarrating
audience: developer
category: standards
order: 455
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/ezstarrating mit den Entwurfsmustern.
keywords: extension/ezstarrating, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/ezstarrating

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/ezstarrating` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (7)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `ezsrRatingDataObject` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `extension/ezstarrating/classes/ezsrratingdataobject.php:27` | – |
| `ezsrRatingFilter` | class | [Dekorierer / Filter](dekorierer.md), [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `extension/ezstarrating/classes/ezsrratingfilter.php:32` | – |
| `ezsrRatingObject` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `extension/ezstarrating/classes/ezsrratingobject.php:27` | – |
| `ezsrRatingObjectTreeNode` | class extends eZContentObjectTreeNode | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `extension/ezstarrating/classes/ezsrratingobjecttreenode.php:36` | Nur von {@link ezsrRatingObject::fetchNodeByRating()} zu verwenden, da andere Fetch-Funktionen die benötigten Bewertungswerte nicht in den zurückgegebenen Daten haben. |
| `ezsrRatingType` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md) | `extension/ezstarrating/datatypes/ezsrrating/ezsrratingtype.php:27` | – |
| `ezsrServerFunctions` | class extends ezjscServerFunctions | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/ezstarrating/classes/ezsrserverfunctions.php:36` | – |
| `ezsrTemplateOperators` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `extension/ezstarrating/autoloads/ezsrtemplateoperators.php:27` | – |

## Template-Operatoren (1)

| Operatoren | Klasse | Registriert in |
| --- | --- | --- |
| fetch_starrating_data, fetch_starrating_stats, fetch_by_starrating | `ezsrTemplateOperators` | `extension/ezstarrating/autoloads/eztemplateautoload.php:14` |

## INI-Plugin-Punkte (2)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[ezsrRatingFilter] ClassName` | `ezsrRatingFilter` | `extension/ezstarrating/settings/extendedattributefilter.ini.append.php:6` |
| `[ezjscServer_ezstarrating] Class` | `ezsrServerFunctions` | `extension/ezstarrating/settings/ezjscore.ini.append.php:11` |
