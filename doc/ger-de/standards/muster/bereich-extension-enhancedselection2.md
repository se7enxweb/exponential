---
title: Muster in extension/enhancedselection2
audience: developer
category: standards
order: 410
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/enhancedselection2 mit den Entwurfsmustern.
keywords: extension/enhancedselection2, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/enhancedselection2

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/enhancedselection2` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (6)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `enhancedselection2Info` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/enhancedselection2/ezinfo.php:14` | Extension Enhanced Selection |
| `Exponential\Command\Extension\Enhancedselection2\MigrateToDatabase` | class extends Command | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `extension/enhancedselection2/classes/runnable/commands/php_migrate_to_database.php:11` | Der Code von extension/enhancedselection2/bin/php/migrate_to_database.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\Command\Extension\Enhancedselection2\Updateenhancedselection` | class extends Command | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `extension/enhancedselection2/classes/runnable/commands/php_updateenhancedselection.php:10` | Der Code von extension/enhancedselection2/bin/php/updateenhancedselection.php, in eine Klasse verschoben (#207 Stufe 1). |
| `ezfSolrDocumentFieldSckEnhancedSelection` | class extends ezfSolrDocumentFieldBase | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `extension/enhancedselection2/classes/ezfsolrdocumentfieldsckenhancedselection.php:2` | – |
| `SckEnhancedSelection` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/enhancedselection2/classes/sckenhancedselection.php:3` | – |
| `SckEnhancedSelectionType` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md) | `extension/enhancedselection2/datatypes/sckenhancedselection/sckenhancedselectiontype.php:3` | – |

## INI-Plugin-Punkte (1)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[SolrFieldMapSettings] CustomMap[sckenhancedselection]` | `ezfSolrDocumentFieldSckEnhancedSelection` | `extension/enhancedselection2/settings/ezfind.ini.append.php:4` |
