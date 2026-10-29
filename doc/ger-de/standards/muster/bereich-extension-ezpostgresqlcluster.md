---
title: Muster in extension/ezpostgresqlcluster
audience: developer
category: standards
order: 456
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/ezpostgresqlcluster mit den Entwurfsmustern.
keywords: extension/ezpostgresqlcluster, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/ezpostgresqlcluster

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/ezpostgresqlcluster` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (2)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `eZDFSFileHandlerPostgresqlBackend` | class | [Strategie / Handler](strategie-handler.md) | `extension/ezpostgresqlcluster/clustering/dfs/ezpostsgresqlbackend.php:16` | Diese Klasse ermöglicht DFS-basiertes Clustering mit PostgreSQL |
| `ezpDfsPostgresqlClusterGateway` | class extends ezpClusterGateway | – | `extension/ezpostgresqlcluster/clustering/dfs/gateway.php:13` | DFS/PostgreSQL-Cluster-Gateway |
