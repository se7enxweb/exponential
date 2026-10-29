---
title: Muster in extension/ezoracle
audience: developer
category: standards
order: 452
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/ezoracle mit den Entwurfsmustern.
keywords: extension/ezoracle, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/ezoracle

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/ezoracle` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (8)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `eZDBFileHandlerOracleBackend` | class | [Strategie / Handler](strategie-handler.md) | `extension/ezoracle/clusterfilehandlers/dbbackends/oracle.php:68` | – |
| `eZDFSFileHandlerOracleBackend` | class | [Strategie / Handler](strategie-handler.md) | `extension/ezoracle/clusterfilehandlers/dfsbackends/oracle.php:55` | – |
| `eZOracleDB` | class extends eZDBInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `extension/ezoracle/ezdb/dbms-drivers/ezoracledb.php:50` | \class eZOracleDB ezoracledb.php \ingroup eZDB \brief Stellt Oracle-Datenbankfunktionen für das eZDB-Subsystem bereit |
| `ezoracleInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/ezoracle/ezinfo.php:11` | – |
| `eZOracleSchema` | class extends eZDBSchemaInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `extension/ezoracle/ezdb/dbms-schema/ezoracleschema.php:39` | – |
| `eZOracleTools` | class | – | `extension/ezoracle/classes/ezoracletools.php:22` | Wartung und Diagnose für eine Exponential-Installation auf Oracle, gemeinsam genutzt von den Konsolenbefehlen (extension/ezoracle/bin/php, ext:ezoracle:*) und… |
| `ezpDbOracleClusterGateway` | class extends ezpClusterGateway | – | `extension/ezoracle/clusterfilehandlers/gateways/db.php:13` | DB/Oracle-Cluster-Gateway |
| `ezpDfsOracleClusterGateway` | class extends ezpClusterGateway | – | `extension/ezoracle/clusterfilehandlers/gateways/dfs.php:13` | DFS/Oracle-Cluster-Gateway |

## Cronjob-Teile (4)

| Cronjob | Datei | Beschreibung |
| --- | --- | --- |
| `ezoracle_health` | `extension/ezoracle/cronjobs/ezoracle_health.php` | – |
| `ezoracle_recyclebin` | `extension/ezoracle/cronjobs/ezoracle_recyclebin.php` | – |
| `ezoracle_sequences` | `extension/ezoracle/cronjobs/ezoracle_sequences.php` | – |
| `ezoracle_statistics` | `extension/ezoracle/cronjobs/ezoracle_statistics.php` | – |

## INI-Plugin-Punkte (3)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[SchemaSettings] SchemaHandlerClasses[oracle]` | `eZOracleSchema` | `extension/ezoracle/settings/dbschema.ini.append.php:4` |
| `[DatabaseSettings] ImplementationAlias[oracle]` | `eZOracleDB` | `extension/ezoracle/settings/site.ini.append.php:3` |
| `[DatabaseSettings] ImplementationAlias[ezoracle]` | `eZOracleDB` | `extension/ezoracle/settings/site.ini.append.php:4` |
