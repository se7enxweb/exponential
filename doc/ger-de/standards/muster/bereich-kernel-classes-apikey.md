---
title: Muster in kernel/classes/apikey
audience: developer
category: standards
order: 485
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von kernel/classes/apikey mit den Entwurfsmustern.
keywords: kernel/classes/apikey, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in kernel/classes/apikey

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `kernel/classes/apikey` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (3)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expApiKey` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/apikey/expapikey.php:31` | Ein persönlicher API-Schlüssel (Veröffentlichungsschlüssel) eines Benutzers: eine Zeile von expapikey (doc/eng-us/guides/api-keys.md). |
| `expApiKeyFunctionCollection` | class | [Fetch-Funktion (Query Object, Fassade)](fetch-funktion.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/apikey/expapikeyfunctioncollection.php:13` | Die Fetch-Funktionen des Moduls apikey (kernel/apikey/function_definition.php). |
| `expApiKeySchema` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/apikey/expapikeyschema.php:16` | Die Tabelle expapikey auf jeder Engine: ob sie existiert, und ihr Anlegen aus ihrer Definition in share/db_schema.dba über den Schema-Handler der Engine… |
