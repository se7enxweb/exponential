---
title: SQL von Erweiterungen für jede Engine prüfen
audience: dba
category: database-administration
order: 45
summary: Eine Checkliste für Datenbankadministratoren, um das SQL einer Exponential-Erweiterung zu prüfen, bevor sie auf eine Website kommt, die nicht MySQL verwendet - Backticks, Tabellenaliase, Treiberhelfer, MySQL-Session-Anweisungen, leere Zeichenketten, Groß-/Kleinschreibung, Katalogabfragen, Indexnamen, Schemadateien je Engine und der MongoDB-Adapter.
keywords: Prüfung von Erweiterungen, portables SQL, engine-übergreifend, Backticks, AS-Alias, ORA-00933, subString, concatString, md5, bitAnd, bitOr, escapeString, lastSerialID, databaseName, SET NAMES, leere Zeichenkette NULL, lower, eZTableList, Indexnamen, sql/mysql, sql/postgresql, db_schema.dba, MONGO TODO, extension review, portable SQL, cross-engine, empty string NULL, index names
type: help
---
# SQL von Erweiterungen für jede Engine prüfen

Diese Seite richtet sich an Datenbankadministratoren, die eine Erweiterung freigeben müssen, bevor sie auf einer Website mit
PostgreSQL, SQLite, Oracle oder MongoDB installiert wird. Das eigene SQL von Exponential läuft auf jeder unterstützten Engine;
Erweiterungscode und handgeschriebenes SQL oft nicht, weil die meisten Erweiterungen gegen MySQL geschrieben wurden. Diese
Seite listet, worauf Sie achten und wo. Quelle: [9.8 Hinweise über Engines hinweg](../../installation/09-datenbanken.md#98-hinweise-über-engines-hinweg).

## Inhalt

1. [Wo das SQL einer Erweiterung liegt](#wo-das-sql-einer-erweiterung-liegt)
2. [Wonach Sie suchen](#wonach-sie-suchen)
3. [Treiberhelfer](#treiberhelfer)
4. [Abweichende Semantik](#abweichende-semantik)
5. [MongoDB](#mongodb)
6. [Testen](#testen)
7. [Checkliste](#checkliste)

## Wo das SQL einer Erweiterung liegt

- `share/db_schema.dba` und `share/db_data.dba`: das engine-neutrale Schema, der beste Fall; die Upgrade-Prüfung liest es.
- `sql/<engine>/*.sql`: DDL je Engine. Eine Erweiterung mit nur `sql/mysql/` hat noch keine Tabellen für andere Engines.
- PHP: Definitionen von `eZPersistentObject` (portabel) und handgeschriebene Anweisungen in Aufrufen von `query()` und
  `arrayQuery()` (die zu lesenden).
- Update-Dateien der eigenen Releases der Erweiterung.

## Wonach Sie suchen

```bash
grep -rn '`' extension/<name> --include=*.php | grep -i 'select\|insert\|update\|delete'     # backticks
grep -rniE 'FROM [a-z_]+ AS [a-z_]+' extension/<name> --include=*.php                        # AS before a table alias
grep -rniE 'SET (NAMES|FOREIGN_KEY_CHECKS|SQL_MODE|AUTOCOMMIT)' extension/<name>               # MySQL session statements
grep -rniE 'LIMIT [0-9]+ *, *[0-9]+|IFNULL|GROUP_CONCAT|ON DUPLICATE KEY|REPLACE INTO' extension/<name>   # MySQL dialect
```

| Befund | Problem | Lösung |
|---|---|---|
| Backticks um Namen | PostgreSQL und Oracle lehnen sie ab; der Parser des MongoDB-Adapters erwartet sie nicht | Tabellen und Spalten schlicht benennen |
| `AS` vor einem Tabellenalias | Oracle lehnt es ab (`ORA-00933`) | den Alias ohne `AS` schreiben; `AS` vor einem Spaltenalias ist überall in Ordnung |
| `SET NAMES`, `SET FOREIGN_KEY_CHECKS` und ähnliche | SQLite ignoriert sie (der Treiber akzeptiert und verwirft sie); PostgreSQL versteht nur `SET NAMES`; Oracle hat keine solche Anweisung | sie aus Code für jede Engine herauslassen |
| Reine MySQL-Funktionen und -Syntax | scheitern anderswo | die Helfer des Treibers oder die Parameter für Offset und Limit von `arrayQuery()` verwenden |

## Treiberhelfer

Die Treiber implementieren, was sich zwischen Engines unterscheidet; Code sollte sie aufrufen, statt das SQL zu schreiben:

| Helfer | Für |
|---|---|
| `$db->subString()`, `$db->concatString()` | Teilzeichenkette und Verkettung |
| `$db->md5()` | MD5 (PostgreSQL über `pgcrypto`, Oracle über `STANDARD_HASH`, SQLite über eine registrierte Funktion) |
| `$db->bitAnd()`, `$db->bitOr()` | bitweise Operationen (Oracle `BITAND`) |
| `$db->escapeString()` | Werte maskieren |
| `$db->lastSerialID()` | die ID des letzten Inserts (Sequenzen von PostgreSQL und Oracle) |
| `arrayQuery( $sql, array( 'offset' => ..., 'limit' => ... ) )` | Blättern |
| `$db->databaseName()` | `mysql`, `postgresql`, `sqlite`, `mongo` oder `oracle`, wenn eine Verzweigung unvermeidlich ist |

## Abweichende Semantik

- **Leere Zeichenketten**: Oracle gibt für `''` NULL zurück, sofern nicht `OracleEmptyStringForNull=enabled`.
- **Groß-/Kleinschreibung**: Die Standard-Sortierfolgen von MySQL ignorieren sie; PostgreSQL und Oracle vergleichen mit
  Beachtung; SQLite vergleicht `=` mit Beachtung und ignoriert ASCII-Schreibweise in `LIKE`. Vergleichen Sie `lower(...)` auf
  beiden Seiten, wo die Schreibweise keine Rolle spielen darf
  ([Zeichensätze und Sortierfolgen](zeichensaetze-und-sortierfolgen.md#groß-kleinschreibung-und-vergleiche)).
- **Scheitern gegenüber leerem Ergebnis**: Eine gescheiterte Anweisung und ein leeres Ergebnis werden nicht auf jeder Engine
  gleich gemeldet. Um zu fragen, ob eine Tabelle existiert, fragen Sie den Katalog (`eZTableList()` des Treibers), nicht ein
  `SELECT`, das scheitern kann.
- **Indexnamen** gelten auf SQLite und PostgreSQL datenbankweit; die Schema-Handler legen einen kollidierenden Namen als
  `<table>__<name>` an. Halten Sie Namen innerhalb der Lint-Grenzen ([Das .dba-Schemaformat](das-dba-schemaformat.md#der-lint-checker)).
- **Ganzzahlen**: Die Spaltenbereiche unterscheiden sich (`$db::MAX_INT`); `$db->clampInteger()` hält einen Wert im Bereich.
- **Fehlercodes** unterscheiden sich ([Fehlercodes und Meldungen der Datenbank](fehlercodes-und-meldungen.md)).

## MongoDB

Der Adapter führt nie SQL aus; er übersetzt, was er kann, und liefert für den Rest ein **leeres Ergebnis**, wobei er
`MONGO TODO` protokolliert. Eine Erweiterung mit handgeschriebenem SQL braucht entweder SQL, das der Adapter versteht, oder
einen eigenen MongoDB-Weg. Klicken Sie jede Funktion der Erweiterung auf einer MongoDB-Staging-Kopie durch und durchsuchen Sie
die Logs ([MongoDB: Grenzen und Betrieb](mongodb-grenzen-und-betrieb.md)).

## Testen

1. Installieren Sie die Erweiterung auf einer Staging-Kopie der Ziel-Engine.
2. Führen Sie ihr Schema über `ezsqlinsertschema.php` oder ihren Installer aus; führen Sie die Upgrade-Prüfung aus.
3. Verwenden Sie jede Funktion; beobachten Sie `var/log/error.log` und die Debug-Ausgabe mit `SQLOutput=enabled` für Ihre
   Adresse.
4. Führen Sie die eigenen Tests der Erweiterung aus, falls sie welche hat.

## Checkliste

- [ ] Die Erweiterung hat `share/db_schema.dba` oder SQL-Dateien für die Ziel-Engine.
- [ ] Keine Backticks, kein `AS` vor Tabellenaliasen, keine reinen MySQL-Session-Anweisungen.
- [ ] Treiberhelfer für Zeichenketten, Hashes, Bits, Blättern und IDs verwendet.
- [ ] Suchen ohne Beachtung der Groß-/Kleinschreibung verwenden `lower(...)`.
- [ ] Auf Staging auf der Ziel-Engine ohne neue Fehler getestet.

## Verwandte Seiten

- [Erweiterungsentwicklung](../erweiterungsentwicklung/README.md)
- [Eine Engine wählen](eine-engine-waehlen.md)
- [Datenbankunterstützung](../datenbankunterstuetzung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
