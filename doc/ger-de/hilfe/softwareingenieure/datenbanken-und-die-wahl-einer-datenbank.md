---
title: "Datenbanken: eine wählen und betreiben"
audience: software-engineer, admin
category: software-engineering
order: 200
summary: Die sechs Datenbanken, die Exponential 6.0.x unterstützt, in der Reihenfolge der Präferenz des Projekts - SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL - so verglichen, wie ein Ingenieur sie vergleicht - Treiber, CI-Abdeckung, Betrieb mit mehreren Knoten, Transaktionen, Zeichensätze und Grenzen - mit einer Entscheidungstabelle und den Befehlen, die zeigen, welche eine Website verwendet.
keywords: Datenbank, SQLite, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL, DatabaseImplementation, Treiber, CI, Transaktionen, Zeichensatz, mehrere Knoten, Wahl, Entscheidung, Softwareingenieur, database, driver, transactions, character set, multi-node, choice, decision
type: help
---
# Datenbanken: eine wählen und betreiben

Diese Seite richtet sich an Ingenieure, die die Datenbank für ein Projekt wählen oder diese Wahl prüfen. Die
Unterstützungsaussage mit Versionen, Einstellungen, Sicherung und Migration für jede Engine ist
[Datenbankunterstützung](../datenbankunterstuetzung.md); diese Seite verdichtet sie auf die Fragen, die eine
Architekturentscheidung braucht.

## Inhalt

1. [Die offizielle Reihenfolge](#die-offizielle-reihenfolge)
2. [Für Ingenieure verglichen](#für-ingenieure-verglichen)
3. [Eine Entscheidungstabelle](#eine-entscheidungstabelle)
4. [Was die Wahl im Code ändert](#was-die-wahl-im-code-ändert)
5. [Welche Datenbank verwendet eine Website?](#welche-datenbank-verwendet-eine-website)

## Die offizielle Reihenfolge

> **Unterstützte Datenbanken (Exponential 6.0.x), in der Reihenfolge der Präferenz:** 1. SQLite 3 (der Standard) ·
> 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL. Microsoft SQL Server ist bekanntermaßen mit
> zusätzlicher Arbeit möglich.

Alle sechs haben einen Treiber im Kernel oder in einer mitgelieferten Erweiterung, einen Eintrag im Einrichtungsassistenten
und einen Weg in den Kommandozeilen-Installern. Die Reihenfolge ist eine Empfehlung für neue Websites, keine Rangfolge
der Qualität ([Datenbankunterstützung](../datenbankunterstuetzung.md#die-unterstützten-datenbanken-in-ihrer-reihenfolge)).

## Für Ingenieure verglichen

| | SQLite 3 | PostgreSQL | MongoDB | MariaDB | Oracle | MySQL |
|---|---|---|---|---|---|---|
| `DatabaseImplementation` | `sqlite3` | `ezpostgresql` | `mongodb` | `ezmysqli` | `ezoracle` | `ezmysqli` |
| Treiberklasse | `eZSQLite3DB` | `eZPostgreSQLDB` | `expMongoDB` | `eZMySQLiDB` | `eZOracleDB` | `eZMySQLiDB` |
| Serverprozess | keiner (eine Datei) | ja | ja | ja | ja | ja |
| Mehrere Webserver auf einer Datenbank | nein | ja | ja | ja | ja | ja |
| Transaktionen | ja, Schreibende serialisiert | ja | nein: jede Operation wird für sich festgeschrieben | ja | ja | ja |
| In der CI des Projekts getestet | Unit-Tests des Treibers | ja (PostgreSQL 15) | Unit-Tests des Adapters | nein | nein | ja (MySQL 8.0) |
| SQL-Abfrage-Cache | ja | ja | nicht verwendet | ja | ja (Hooks von ezoracle) | ja |

Quelle: [Datenbankunterstützung, auf einen Blick](../datenbankunterstuetzung.md#auf-einen-blick),
[Standards für Datenbanken und SQL](../../standards/datenbanken-und-sql.md) und
[Der SQL-Abfrage-Cache](../../bc/6.0/sql-abfrage-cache.md).

## Eine Entscheidungstabelle

| Situation | Passt gut | Warum |
|---|---|---|
| Ein Rechner, kein Datenbankadministrator | SQLite 3 | kein Server zu betreiben; eine vollwertige Produktionsdatenbank für eine Website auf einem Rechner |
| Mehrere Webknoten oder ein Team, das den am besten getesteten Server möchte | PostgreSQL | bei jedem Push in der CI getestet; striktes SQL; Replikation und Wiederherstellung auf einen Zeitpunkt |
| Die Organisation hat sich auf MongoDB festgelegt | MongoDB | über einen Adapter unterstützt; lesen Sie zuerst seine Grenzen ([MongoDB als Datenbank](../../funktionen/6.0/mongodb-datenbankunterstuetzung.md)) |
| Ein MySQL-kompatibler Server ist vorgegeben | MariaDB, dann MySQL | derselbe Treiber (`mysqli`); MySQL wird in der CI getestet |
| Die Organisation hat sich auf Oracle festgelegt | Oracle 19c oder später | über `ezoracle`, das `composer.json` verlangt |

Gewichten Sie die CI-Spalte: MariaDB und Oracle haben im Kernel-Repository keinen CI-Job, eine für sie spezifische
Regression wird also später gefunden
([Standards für Datenbanken und SQL, bekannte Lücken](../../standards/datenbanken-und-sql.md#bekannte-lücken)).

## Was die Wahl im Code ändert

- Kernel- und Erweiterungscode muss auf jeder Engine funktionieren: maskieren und casten, Limits als Parameter übergeben,
  die Funktionen einer Engine nicht ohne Rückfall verwenden ([Portables SQL schreiben](portables-sql-schreiben.md)).
- Schemaänderungen müssen für jeden Treiber ausgeliefert werden ([Schemaänderungen und Migrationen](schemaaenderungen-und-migrationen.md)).
- Unter MySQL und MariaDB können Zeichen außerhalb der Basic Multilingual Plane (zum Beispiel Emoji) die Verbindung
  selbst mit `utf8mb4`-Tabellen nicht passieren ([Standards zur Internationalisierung](../../standards/internationalisierung.md)).
- Der MongoDB-Adapter braucht PHP 8.2 oder später.

## Welche Datenbank verwendet eine Website?

```bash
./console exp:ini where site.ini/DatabaseSettings/DatabaseImplementation
```

Die Administration zeigt sie unter Setup > Systeminformationen. Den Umzug einer Website auf eine andere Engine
beschreiben [Datenbankunterstützung, eine Website auf eine andere Datenbank umziehen](../datenbankunterstuetzung.md#eine-site-auf-eine-andere-datenbank-umziehen)
und [Die Datenbank-Engine wechseln](../hosting/migrationen/die-datenbank-engine-einer-kunden-website-wechseln.md).

## Verwandte Seiten

- [Kapitel 9 der Installationsanleitung: Datenbanken](../../installation/09-datenbanken.md)
- [Persistenz und die Datenbankschicht](persistenz-und-die-datenbankschicht.md)
- [Datenbanken in der offiziellen Reihenfolge, für Hoster](../hosting/grundlagen/datenbanken-in-der-offiziellen-reihenfolge.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
