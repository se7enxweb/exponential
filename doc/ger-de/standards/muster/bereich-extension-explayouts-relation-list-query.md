---
title: Muster in extension/explayouts_relation_list_query
audience: developer
category: standards
order: 421
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/explayouts_relation_list_query mit den Entwurfsmustern.
keywords: extension/explayouts_relation_list_query, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/explayouts_relation_list_query

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/explayouts_relation_list_query` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (4)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expLayoutsRelationListQuery` | class | – | `extension/explayouts_relation_list_query/classes/explayoutsrelationlistquery.php:2` | – |
| `expLayoutsRelationListQueryFactory` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts_relation_list_query/classes/explayoutsrelationlistqueryfactory.php:2` | – |
| `expLayoutsReverseRelationListQuery` | class | – | `extension/explayouts_relation_list_query/classes/explayoutsreverserelationlistquery.php:2` | – |
| `explayouts_relation_list_queryInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/explayouts_relation_list_query/ezinfo.php:12` | – |
