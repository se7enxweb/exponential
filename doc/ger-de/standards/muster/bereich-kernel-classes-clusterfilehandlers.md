---
title: Muster in kernel/classes/clusterfilehandlers
audience: developer
category: standards
order: 484
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von kernel/classes/clusterfilehandlers mit den Entwurfsmustern.
keywords: kernel/classes/clusterfilehandlers, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in kernel/classes/clusterfilehandlers

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `kernel/classes/clusterfilehandlers` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (2)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `eZFSFileHandler` | class implements eZClusterFileHandlerInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `kernel/classes/clusterfilehandlers/ezfsfilehandler.php:12` | – |
| `eZMySQLBackendError` | class | – | `kernel/classes/clusterfilehandlers/dbbackends/mysqlbackenderror.php:17` | – |
