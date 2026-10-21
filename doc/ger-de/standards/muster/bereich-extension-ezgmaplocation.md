---
title: Muster in extension/ezgmaplocation
audience: developer
category: standards
order: 441
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/ezgmaplocation mit den Entwurfsmustern.
keywords: extension/ezgmaplocation, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/ezgmaplocation

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Jede Klasse, Schnittstelle und jeder Trait von `extension/ezgmaplocation` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (3)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `eZGmapLocation` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `extension/ezgmaplocation/classes/ezgmaplocation.php:40` | Hilfsklasse, die zusammen mit dem Datentyp eZGmapLocationType verwendet wird. |
| `eZGmapLocationType` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md) | `extension/ezgmaplocation/datatypes/ezgmaplocation/ezgmaplocationtype.php:39` | Klasse, die den Datentyp eZGmapLocationType bereitstellt. |
| `ezgmlLocationFilter` | class | [Dekorierer / Filter](dekorierer.md), [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `extension/ezgmaplocation/classes/ezgmllocationfilter.php:32` | – |

## INI-Plugin-Punkte (1)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[ezgmlLocationFilter] ClassName` | `ezgmlLocationFilter` | `extension/ezgmaplocation/settings/extendedattributefilter.ini.append.php:5` |
