---
title: Langsame Abfragen finden
audience: dba
category: database-administration
order: 39
summary: Die Werkzeuge, die Exponential einem Datenbankadministrator gibt, um langsames und wiederholtes SQL zu finden - SQLOutput und SlowQueriesOutput in der Debug-Ausgabe, MySQLs EXPLAIN über QueryAnalysisOutput, DebugTransactions, das Log des SQL-Profilers, das Log langsamer Abfragen von Oracle, exp:benchmark - und wie sie zu den eigenen Logs langsamer Abfragen der Engine passen.
keywords: langsame Abfragen, SQLOutput, SlowQueriesOutput, QueryAnalysisOutput, EXPLAIN, DebugTransactions, Debug-Ausgabe, DebugByIP, SQL-Profiler, sql_profile.on, sql_profile.log, oracle-slow.log, SlowQueryThreshold, exp:benchmark, Slow Query Log, pg_stat_statements, Leistung, slow queries, debug output, SQL profiler, slow query log, performance
type: help
---
# Langsame Abfragen finden

Diese Seite richtet sich an Datenbankadministratoren, die das SQL suchen, das eine Exponential-Seite langsam macht. Sie
beantwortet: welche Schalter die Anweisungen einer Anfrage zeigen, wie Sie sie auf die langsamen begrenzen, wie Sie Anweisungen
je Anfrage über die Zeit zählen, was Oracle hinzufügt und wie das zu den eigenen Werkzeugen Ihrer Engine passt.

## Inhalt

1. [Zuerst: Liegt es an der Datenbank?](#zuerst-liegt-es-an-der-datenbank)
2. [Anweisungen in der Debug-Ausgabe](#anweisungen-in-der-debug-ausgabe)
3. [EXPLAIN auf MySQL und MariaDB](#explain-auf-mysql-und-mariadb)
4. [Unausgeglichene Transaktionen](#unausgeglichene-transaktionen)
5. [Der SQL-Profiler](#der-sql-profiler)
6. [Das Log langsamer Abfragen von Oracle](#das-log-langsamer-abfragen-von-oracle)
7. [Die eigenen Werkzeuge der Engine](#die-eigenen-werkzeuge-der-engine)
8. [Checkliste](#checkliste)

## Zuerst: Liegt es an der Datenbank?

Die meisten Anfragen werden von Caches beantwortet, bevor sie die Datenbank erreichen
([Leistung und Skalierung](leistung-und-skalierung.md)). Messen Sie zuerst:

```bash
php bin/php/console exp:benchmark --help
```

`exp:benchmark` misst Seitenzeiten über HTTP, vergleicht Server und führt Kernel-Proben aus, darunter den Weg zur Datenbank und
zurück; `--save` und `--baseline` erkennen eine Verlangsamung zwischen zwei Läufen ([Benchmark](../../funktionen/6.0/benchmark.md)).

## Anweisungen in der Debug-Ausgabe

Schalten Sie die Debug-Ausgabe nur für Ihre eigene Adresse ein, nie für alle:

```ini
# settings/override/site.ini.append.php
[DebugSettings]
DebugOutput=enabled
DebugByIP=enabled
DebugIPList[]=203.0.113.10

[DatabaseSettings]
SQLOutput=enabled
# only statements slower than this many milliseconds (0: all)
SlowQueriesOutput=50
```

Leeren Sie den INI-Cache (`php bin/php/ezcache.php --clear-tag=ini --allow-root-user`), starten Sie Velocity neu, wenn es die
Website ausliefert, und laden Sie die Seite: Die Debug-Ausgabe listet die Anweisungen mit ihren Zeiten, auf jeder Engine
([12.4 Debug-Ausgabe](../../installation/12-fehlerbehebung.md#124-debug-ausgabe)). Schalten Sie sie wieder aus, wenn Sie fertig sind;
die Ausgabe zeigt Pfade, Abfragen und Einstellungen.

## EXPLAIN auf MySQL und MariaDB

```ini
[DatabaseSettings]
SQLOutput=enabled
QueryAnalysisOutput=enabled
```

fügt der Debug-Ausgabe MySQLs `EXPLAIN` jeder Anweisung hinzu. Es funktioniert nur auf MySQL und MariaDB. Auf anderen Engines
kopieren Sie eine Anweisung aus der Debug-Ausgabe und führen selbst `EXPLAIN` (PostgreSQL, SQLite: `EXPLAIN QUERY PLAN`) oder
`EXPLAIN PLAN FOR` (Oracle) aus.

## Unausgeglichene Transaktionen

`[DatabaseSettings] DebugTransactions=enabled` hält für jedes Begin und Commit einen Stacktrace fest, um eine Transaktion zu
finden, die geöffnet und nie geschlossen wurde. Verwenden Sie es nur, während Sie untersuchen.

## Der SQL-Profiler

`eZDBInterface::profileSQL()` schreibt je Anfrage eine Zeile nach `var/tmp/sql_profile.log`: Anweisungen, `SELECT`s,
verschiedene `SELECT`s, exakte Wiederholungen, Zeit in der Datenbank und in den Wiederholungen. Er ist aus, solange
`var/tmp/sql_profile.on` nicht existiert; der Schalter unter **Setup > Systeminformationen** (Feld **Datenbankabfragen**,
Richtlinie `setup/managecache`) legt diese Datei an und entfernt sie. In 6.0.15 ruft ihn nur der MySQL-Treiber (`eZMySQLiDB`)
auf, er zählt also Anweisungen auf MySQL und MariaDB.

Er zählt die Anweisungen, die **die Datenbank erreicht haben**: Mit eingeschaltetem Abfrage-Cache ist eine gecachte Antwort
nicht darin, was ihn zum Werkzeug macht, um zu sehen, was der Cache übrig lässt ([Der SQL-Abfrage-Cache](sql-abfrage-cache.md)).
Viele exakte Wiederholungen auf einer Seite deuten auf Code, der dieselbe Abfrage in einer Schleife ausführt.

## Das Log langsamer Abfragen von Oracle

Die Erweiterung `ezoracle` schreibt langsame Anweisungen nach `var/log/oracle-slow.log`, wenn
`ezoracle.ini [LogSettings] SlowQueryThreshold` gesetzt ist; `MaskLiterals` maskiert Zeichenkettenliterale, sodass das Log keine
Inhalte enthält. `[TraceSettings]` kennzeichnet jede Session in `V$SESSION` mit Siteaccess, Modul und View, sodass AWR- und
ASH-Berichte zeigen, welche Seite eine Anweisung ausgeführt hat
([Oracle: persistente Verbindungen, DRCP und Tuning](oracle-verbindungen-und-tuning.md)). `ext:ezoracle:report --top-sql`
listet die schwersten Anweisungen aus `V$SQL`.

## Die eigenen Werkzeuge der Engine

Das Log langsamer Abfragen von MySQL und MariaDB, `pg_stat_statements` und `log_min_duration_statement` auf PostgreSQL, der
Profiler von MongoDB und AWR von Oracle sind Funktionen der Server, nicht von Exponential. Sie sehen jede Anweisung jedes
Prozesses (Web, Velocity, Cron), ohne die Einstellungen der Website anzufassen, und sind die richtigen Werkzeuge für die
Produktion. Verwenden Sie die Debug-Ausgabe, um eine Anweisung einer Seite zuzuordnen.

## Checkliste

- [ ] Vor und nach einer Änderung mit `exp:benchmark` gemessen.
- [ ] Debug-Ausgabe auf Ihre Adresse beschränkt und danach ausgeschaltet.
- [ ] Das eigene Log langsamer Abfragen der Engine mit einem sinnvollen Schwellenwert eingeschaltet.
- [ ] Wiederholte Anweisungen an diejenigen gemeldet, die die Erweiterung oder das Template pflegen.

## Verwandte Seiten

- [Leistung und Skalierung](leistung-und-skalierung.md)
- [Eine gehostete Website messen](../hosting/leistung/benchmark-einer-gehosteten-website.md)
- [Betriebsanleitung für eine langsame Website](../hosting/runbooks/langsame-website.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
