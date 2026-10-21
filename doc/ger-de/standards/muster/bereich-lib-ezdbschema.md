---
title: Muster in lib/ezdbschema
audience: developer
category: standards
order: 529
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von lib/ezdbschema mit den Entwurfsmustern.
keywords: lib/ezdbschema, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in lib/ezdbschema

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Jede Klasse, Schnittstelle und jeder Trait von `lib/ezdbschema` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (8)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expMongoSchema` | class extends eZDBSchemaInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `lib/ezdbschema/classes/expmongoschema.php:11` | MongoDB-Schema-Handler für eZDbSchema. |
| `eZDbSchema` | class | [Singleton / instance()](singleton.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `lib/ezdbschema/classes/ezdbschema.php:19` | – |
| `eZDbSchemaChecker` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `lib/ezdbschema/classes/ezdbschemachecker.php:19` | – |
| `eZDBSchemaInterface` | class | – | `lib/ezdbschema/classes/ezdbschemainterface.php:62` | – |
| `eZLintSchema` | class extends eZDBSchemaInterface | – | `lib/ezdbschema/classes/ezlintschema.php:41` | – |
| `eZMysqlSchema` | class extends eZDBSchemaInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `lib/ezdbschema/classes/ezmysqlschema.php:19` | – |
| `eZPgsqlSchema` | class extends eZDBSchemaInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `lib/ezdbschema/classes/ezpgsqlschema.php:19` | – |
| `eZSQLiteSchema` | class extends eZDBSchemaInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `lib/ezdbschema/classes/ezsqliteschema.php:3` | – |
