---
title: Muster in lib/ezdb
audience: developer
category: standards
order: 528
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von lib/ezdb mit den Entwurfsmustern.
keywords: lib/ezdb, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in lib/ezdb

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `lib/ezdb` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (10)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expMongoDB` | class extends eZDBInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `lib/ezdb/classes/expmongodb.php:18` | – |
| `eZDB` | class | [Singleton / instance()](singleton.md) | `lib/ezdb/classes/ezdb.php:80` | – |
| `eZDBInterface` | class | – | `lib/ezdb/classes/ezdbinterface.php:21` | Die eZDBInterface definiert die Schnittstelle für alle Datenbankimplementierungen |
| `eZDBQueryCache` | class | [Registry](registry.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `lib/ezdb/classes/ezdbquerycache.php:37` | Ein Ergebnis-Cache für die SQL-Treiber (MySQL/MariaDB, PostgreSQL, SQLite): die Zeilen, die ein SELECT geliefert hat, werden erneut geliefert, bis ein Schreibvorgang in eine der Tabellen… |
| `eZDBTool` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `lib/ezdb/classes/ezdbtool.php:18` | – |
| `eZMySQLCharset` | abstract class | [Schablonenmethode / abstrakte Basisklasse](schablonenmethode.md) | `lib/ezdb/classes/ezmysqlcharset.php:13` | – |
| `eZMySQLiDB` | class extends eZDBInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `lib/ezdb/classes/ezmysqlidb.php:22` | – |
| `eZNullDB` | class extends eZDBInterface | – | `lib/ezdb/classes/eznulldb.php:20` | – |
| `eZPostgreSQLDB` | class extends eZDBInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `lib/ezdb/classes/ezpostgresqldb.php:23` | – |
| `eZSQLite3DB` | class extends eZDBInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `lib/ezdb/classes/ezsqlite3db.php:3` | – |
